<?php

declare(strict_types=1);

namespace Cacing69\Cquery;

use Cacing69\Cquery\CqueryException;
use Cacing69\Cquery\Loader\DOMCrawlerLoader;
use Closure;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * An implementation Cquery of a Loader to wrap all loader available.
 *
 * @author Ibnul Mutaki <ibnuu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Cquery
{
    /**
     * The base Loader instance.
     * loader should be an instance of Cacing69\Loader\Loader
     * Available loader DOMCrawlerLoader
     *
     * @var \Cacing69\Cquery\Loader
     *
     * The default loader is null, u need to specify when create Cquery instance.
     */
    private $loader;

    /**
    * A variable used to store the results of a query
    *
    * @var \Doctrine\Common\Collections\ArrayCollection
    *
    * The default results is null
    */
    private $results;

    /**
     * Create a new Cquery instance.
     *
     * @param \DOMNodeList|\DOMNode|string|null $source A source to use as the the source data
     * u can put html content/url page to scrape default is null
     *
     * @param string $contentType Type of Data Content to be Used as Data Source default is 'html'
     */
    public function __construct(string $source = null, $contentType = "html")
    {
        if($source !== null) {
            if (filter_var($source, FILTER_VALIDATE_URL) && $contentType === "html") {
                $remote = true;
                $this->loader = new DOMCrawlerLoader($source, $remote);
            } else {
                if($contentType === "html") {
                    $this->loader = new DOMCrawlerLoader($source);
                }
            }
        }
    }

    /**
     * Adds a source based on data given.
     * This method is used to determine the HTML element selector
     * that will serve as a property in each array element.
     *
     * @param string $value set a source element selector to activate query
     * @return \Cacing69\Cquery\Cquery
     */
    public function from(string $value)
    {
        $this->loader->from($value);
        return $this;
    }

    /**
     * Adds a definer to the current source.
     *
     * This method is used to determine the HTML element selector
     * that will serve as a property in each array element.
     *
     * @param \Cacing69\Cquery\Definer|string $picks a selector to grab on element
     * @return \Cacing69\Cquery\Cquery
     * @throws \Cacing69\Cquery\CqueryException when the provided parameter is incorrect."
     */
    public function define(...$defines): Cquery
    {
        $this->loader->define(...$defines);
        return $this;
    }

    /**
     * Add limit amount when scraping.
     * This method is used to limit the total length of the data.
     *
     * @param int $limit set a limit
     * @return \Cacing69\Cquery\Cquery
     *
     */
    public function limit(int $limit)
    {
        $this->loader->limit($limit);
        return $this;
    }

    /**
     * Take a first result from query result collection
     *
     * @return array
     */
    public function first()
    {
        return $this->loader->first();
    }

    private function makeFilter($node, $operator = null, $value = null): Filter
    {

        if($node instanceof Filter) {
            $filter = $node;
        } else {
            $filter = new Filter($node, $operator);
            if($node instanceof Closure) {
                throw new CqueryException("when used closure, u need to place it on second parameter");
            }

            if(!($operator instanceof Closure) && empty($value)) {
                throw new CqueryException("non closure operator need a value for comparison");
            } else {
                $filter->setValue($value);
            }
        }

        return $filter;
    }

    /**
     * The filter method is used to add filter criteria with 'AND' logic
     *
     * @return \Cacing69\Cquery\Cquery;
     */
    public function filter($node, $operator = null, $value = null): Cquery
    {
        $filter = $this->makeFilter($node, $operator, $value);
        $this->loader->addFilter($filter, "and");
        return $this;
    }

    /**
     * The filter method is used to add filter criteria with 'OR' logic
     *
     * @return \Cacing69\Cquery\Cquery;
     */
    public function orFilter($node, $operator = null, $value = null): Cquery
    {
        $filter = $this->makeFilter($node, $operator, $value);
        $this->loader->addFilter($filter, "or");
        return $this;
    }

    /**
    * Take a result query from loader
    *
    * @return ArrayCollection
    */
    public function get()
    {
        $this->results = $this->loader->get();

        return $this->results;
    }

    public function onContentLoaded($closure)
    {
        $this->loader->setCallbackOnContentLoaded($closure);
        return $this;
    }

    /**
     * Used to access or manipulate each item/element present at array results.
     * @param Closure(array): $closure
     * @return \Cacing69\Cquery\Cquery;
     */
    public function eachItem(Closure $closure)
    {
        $this->loader->setCallbackEachItem($closure);
        return $this;
    }

    /**
     * Used to access or manipulate array results.
     * @param Closure(array): $closure
     * @return \Cacing69\Cquery\Cquery;
     */
    public function onObtainedResults($closure)
    {
        $this->loader->setOnObtainedResults($closure);
        return $this;
    }

    /**
     * Used to get source cquery.
     *
     * @return \Cacing69\Cquery\Source;
     */
    public function getSource(): Source
    {
        return $this->loader->getSource();
    }

    /**
     * Used to set http client type.
     *
     * @param string $clientType
     * @return \Cacing69\Cquery\Cquery;
     */
    public function client($clientType)
    {
        $this->loader->setClientType($clientType);
        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Cacing69\Cquery;

use Cacing69\Cquery\Exception\CqueryException;
use Cacing69\Cquery\Loader\HTMLLoader;
use Closure;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

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
     * Available loader HTMLLoader, JSONLoader(), CSVLoader
     *
     * @var \Cacing69\Cquery\Loader
     *
     * The default loader is null, u need to specify when create Cquery instance.
     */
    private $loader;

    /**
     * Create a new Cquery instance.
     *
     * @param \DOMNodeList|\DOMNode|string|null $source A source to use as the the source data, u can put html content/url page to scrape default is null
     * @param string $contentType Type of Data Content to be Used as Data Source default is 'html'
     * @param string $encoding Encoding Used in the Content default is 'UTF-8'
     */
    public function __construct(string $source = null, $contentType = "html", string $encoding = "UTF-8")
    {
        if($source !== null) {
            if (filter_var($source, FILTER_VALIDATE_URL)) {
                $remote = true;
                $this->loader = new HTMLLoader($source, $remote);
            } else {
                if($contentType === "html") {
                    $this->loader = new HTMLLoader($source);
                }
            }
        }
    }

    /**
     * Adds a definer to the current source.
     *
     * This method is used to determine the HTML element selector
     * that will serve as a property in each array element.
     *
     * @param \Cacing69\Cquery\Definer|string $picks a selector to grab on element
     * @return \Cacing69\Cquery\Cquery
     * @throws \Cacing69\Cquery\Exception\CqueryException when the provided parameter is incorrect."
     */
    public function define(...$defines): Cquery
    {
        $this->loader->define(...$defines);
        return $this;
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
     * Take a first reesult from result collection
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

    public function filter($node, $operator = null, $value = null): Cquery
    {
        $filter = $this->makeFilter($node, $operator, $value);
        $this->loader->filter($filter);
        return $this;
    }

    public function orFilter($node, $operator = null, $value = null): Cquery
    {
        $filter = $this->makeFilter($node, $operator, $value);
        $this->loader->orFilter($filter);
        return $this;
    }

    /**
    * Take a result from query
    *
    * @return ArrayCollection
    */
    public function get(): ArrayCollection
    {
        return $this->loader->get();
    }

    protected function validateSource()
    {
        $this->loader->validateSource();
    }

    public function getActiveSource()
    {
        if(get_class($this->loader) === HTMLLoader::class) {
            return $this->loader->getActiveDom();
        }

        return null;
    }

    public function setContent(string $args)
    {
        $this->loader->setContent($args);
        return $this;
    }
}

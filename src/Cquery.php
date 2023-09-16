<?php

/**
 * This file is part of Cquery.
 *
 * (c) 2023 Ibnul Mutaki <ibnuul@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cacing69\Cquery;

use Cacing69\Cquery\Loader\DOMCrawlerLoader;
use Cacing69\Cquery\Writer\CSVWriter;
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
class Cquery extends AbstractCqueryLoader
{
    private $loaderName;

    /**
     * The base Loader instance.
     * loader should be an instance of Cacing69\Loader\Loader
     * Available loader DOMCrawlerLoader.
     *
     * @var \Cacing69\Cquery\AbstractCqueryLoader
     *
     * The default loader is null, u need to specify when create Cquery instance.
     */
    private $loader;

    /**
     * Create a new Cquery instance.
     *
     * @param \DOMNodeList|\DOMNode|string|null $source     A source to use as the the source data
     *                                                      u can put html content/url page to scrape default is null
     * @param string                            $loaderName
     */
    public function __construct(string $source = null, $loaderName = DOMCrawlerLoader::class)
    {
        $this->loaderName = $loaderName;
        if ($source !== null) {
            if (filter_var($source, FILTER_VALIDATE_URL)) {
                $remote = true;
                $this->loader = new $loaderName($source, $remote);
            } else {
                $this->loader = new $loaderName($source);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function from(string $value)
    {
        $this->loader->from($value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function define(...$defines): Cquery
    {
        $this->loader->define(...$defines);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function limit(int $limit)
    {
        $this->loader->limit($limit);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function first()
    {
        return $this->loader->first();
    }

    /**
     * {@inheritDoc}
     */
    public function last()
    {
        return $this->loader->last();
    }

    public static function makeFilter($node, $operator = null, $value = null): Filter
    {
        if ($node instanceof Filter) {
            $filter = $node;
        } else {
            $filter = new Filter($node, $operator);
            if ($node instanceof Closure) {
                if ($operator === null) {
                    // BEGIN NESTED
                    throw new CqueryException('nested filter, still not available');
                    // END NESTED
                } else {
                    throw new CqueryException('when used closure, u need to place it on second parameter');
                }
            }

            if (!($operator instanceof Closure) && empty($value)) {
                throw new CqueryException('non closure operator need a value for comparison');
            } else {
                $filter->setValue($value);
            }
        }

        return $filter;
    }

    /**
     * The filter method is used to add filter criteria with 'AND' logic.
     *
     * @return $this;
     */
    public function filter($node, $operator = null, $value = null): Cquery
    {
        if (count($this->loader->getFilters()) > 0) {
            throw new CqueryException('use `andFilter` or `orFilter` after filter declared');
        }

        $filter = Cquery::makeFilter($node, $operator, $value);
        $this->loader->addFilter($filter, 'and');

        return $this;
    }

    /**
     * The filter method is used to add filter criteria with 'AND' logic.
     *
     * @return $this;
     */
    public function andFilter($node, $operator = null, $value = null): Cquery
    {
        $filter = Cquery::makeFilter($node, $operator, $value);
        $this->loader->addFilter($filter, 'and');

        return $this;
    }

    /**
     * The filter method is used to add filter criteria with 'OR' logic.
     *
     * @return $this;
     */
    public function orFilter($node, $operator = null, $value = null): Cquery
    {
        $filter = Cquery::makeFilter($node, $operator, $value);
        $this->loader->addFilter($filter, 'or');

        return $this;
    }

    /**
     * Take a result query from loader.
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
     *
     * @param Closure(array): $closure
     *
     * @return $this;
     */
    public function eachItem(Closure $closure)
    {
        $this->loader->setCallbackEachItem($closure);

        return $this;
    }

    /**
     * Used to access or manipulate array results.
     *
     * @param Closure(array): $closure
     *
     * @return $this;
     */
    public function onObtainedResults($closure)
    {
        $this->loader->setCallbackOnObtainedResults($closure);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource(): Source
    {
        return $this->loader->getSource();
    }

    /**
     * Used to query with raw expression.
     *
     * @param string $query
     *
     * @return \Cacing69\Cquery\Support\Collection;
     */
    public function raw($query = '')
    {
        $parser = new Parser($query);

        $this->loader->setSource($parser->getSource());

        $this->loader->define(...$parser->getDefiners());

        foreach ($parser->getFilters() as $_operator => $_filter) {
            foreach ($_filter as $filter) {
                $this->loader->addFilter($filter, $_operator);
            }
        }

        return $this->get();
    }

    public function save($path, $writer = CSVWriter::class)
    {
        $writer = new $writer();
        $writer->setData($this->get());

        return $writer->save($path);
    }
}

<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Support;

use Cacing69\Cquery\Adapter\HTML\AttributeCallbackAdapter;
use Cacing69\Cquery\Trait\HasSelectorProperty;
use Cacing69\Cquery\Trait\HasSourceProperty;
use Cacing69\Cquery\Adapter\HTML\ClosureCallbackAdapter;
use Cacing69\Cquery\Adapter\HTML\DefaultCallbackAdapter;
use Cacing69\Cquery\Adapter\HTML\LengthCallbackAdapter;
use Cacing69\Cquery\Adapter\HTML\ReverseCallbackAdapter;
use Cacing69\Cquery\Adapter\HTML\UpperCallbackAdapter;
use Cacing69\Cquery\Exception\CqueryException;
use Cacing69\Cquery\Extractor\DefinerExtractor;
use Closure;
use Symfony\Component\DomCrawler\Crawler;

class DOMManipulator {
    // use HasSelectorProperty;
    use HasSourceProperty;
    private $crawler;
    private $definer = [];
    private $results = [];
    private $filter = [];
    private $limit = null;

    public function __construct($content, $source)
    {
        $this->crawler = new Crawler($content);
        $this->source = $source;
    }

    public function addFilter($filter, $operator = "and")
    {
        $adapter = null;

        if($filter[0] instanceof Closure) {
            $adapter = new ClosureCallbackAdapter($filter[0], $this->source);

            if(!array_key_exists(1, $filter)) {
                throw new CqueryException("error processing filter, when used callback filter, please set selector on second parameter");

            }

            $adapter->setNode($filter[1]);
        } else {
            if(preg_match(CqueryRegex::IS_ATTRIBUTE, $filter[0])) {
                $adapter = new AttributeCallbackAdapter($filter[0], $this->source);
            } else if (preg_match(CqueryRegex::IS_LENGTH, $filter[0])) {
                $adapter = new LengthCallbackAdapter($filter[0], $this->source);
            } else if (preg_match(CqueryRegex::IS_UPPER, $filter[0])) {
                $adapter = new UpperCallbackAdapter($filter[0], $this->source);
            } else if (preg_match(CqueryRegex::IS_REVERSE, $filter[0])) {
                $adapter = new ReverseCallbackAdapter($filter[0], $this->source);
            } else {
                $adapter = new DefaultCallbackAdapter($filter[0], $this->source);
            }
        }

        $adapter->setOperator($operator);
        $adapter->setFilter($filter);

        $this->filter[] = $adapter;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function getCrawler()
    {
        return $this->crawler;
    }
    public function addDefiner($definer)
    {
        array_push($this->definer, new DefinerExtractor($definer, $this->source));
        return $this;
    }

    public function resetFilter()
    {
        $this->filter = [];
        return $this;
    }

    public function getDefiner()
    {
        return $this->definer;
    }
}

<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Support;

use Cacing69\Cquery\Adapter\AttributeCallbackAdapter;
use Cacing69\Cquery\Trait\HasSelectorProperty;
use Cacing69\Cquery\Adapter\DefaultCallbackAdapter;
use Cacing69\Cquery\Adapter\LengthCallbackAdapter;
use Cacing69\Cquery\Extractor\DefinerExtractor;
use Symfony\Component\DomCrawler\Crawler;

class DOMManipulator {
    use HasSelectorProperty;
    private $crawler;
    private $definer = [];
    // private $definerV2 = [];
    private $results = [];
    private $filter = [];
    private $limit = null;

    public function __construct($content, $selector)
    {
        $this->crawler = new Crawler($content);
        $this->selector = $selector;
    }

    // public function addFilter($filter)
    // {
    //     $this->filter[] = $filter
    //                         ->setSelector($this->selector)
    //                         ->extract();
    // }

    public function addFilter($filter, $operator = "and")
    {
        $adapter = null;

        if(preg_match('/^attr\(.*\s?,\s.*\s?\)$/', $filter[0])) {
            $adapter = new AttributeCallbackAdapter($filter[0], $this->selector);
        } else if (preg_match("/^length(\s?.*\s?)$/is", $filter[0])) {
            $adapter = new LengthCallbackAdapter($filter[0], $this->selector);
        } else {
            $adapter = new DefaultCallbackAdapter($filter[0], $this->selector);
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

    // public function addDefiner($definer)
    // {
    //     array_push($this->definer, new ColumnExtractor($definer));
    //     return $this;
    // }
    public function addDefiner($definer)
    {
        // dd($definer);
        array_push($this->definer, new DefinerExtractor($definer, $this->selector));
        return $this;
    }
    // public function getDefiner()
    // {
    //     return $this->definer;
    // }

    public function getDefiner()
    {
        return $this->definer;
    }
}

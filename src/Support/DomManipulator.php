<?php

namespace Cacing69\Cquery\Support;

use Symfony\Component\DomCrawler\Crawler;

class DomManipulator {
    use HasSelectorProperty;
    private $crawler;
    private $column = [];
    private $results = [];
    private $filter = [];
    private $limit = null;

    public function __construct($content, $selector)
    {
        $this->crawler = new Crawler($content);
        $this->selector = $selector;
    }

    public function addFilter($filter)
    {
        $this->filter[] = $filter
                            ->setSelector($this->selector)
                            ->extract();
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function getCrawler()
    {
        return $this->crawler;
    }

    public function addColumn($column)
    {
        array_push($this->column, $column);
        return $this;
    }
    public function getColumn()
    {
        return $this->column;
    }
}

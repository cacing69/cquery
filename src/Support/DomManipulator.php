<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Support;

use Cacing69\Cquery\Extractor\ColumnExtractor;
use Cacing69\Cquery\Support\HasSelectorProperty;
use Symfony\Component\DomCrawler\Crawler;

class DomManipulator {
    use HasSelectorProperty;
    private $crawler;
    private $definer = [];
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

    public function addDefiner($definer)
    {
        array_push($this->definer, new ColumnExtractor($definer));
        return $this;
    }
    public function getDefiner()
    {
        return $this->definer;
    }
}

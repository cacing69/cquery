<?php

namespace Cacing69\Cquery\Support;

use Symfony\Component\DomCrawler\Crawler;

class DomManipulator {
    use HasSelectorProperty;
    private $crawler;
    private $where = [];
    private $limit = null;

    public function __construct($content, $selector)
    {
        $this->crawler = new Crawler($content);
        $this->selector = $selector;
    }

    public function addWhere($where)
    {
        $this->where[] = $where
                        ->setSelector($this->selector)
                        ->extract();
    }

    public function getWhere()
    {
        return $this->where;
    }
}

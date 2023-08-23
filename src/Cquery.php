<?php

namespace Cacing69\Cquery;

use Symfony\Component\CssSelector\CssSelectorConverter;
use Symfony\Component\DomCrawler\Crawler;

class Cquery {
    private $crawler;
    private $converter;
    private $selects = [];
    private $from;
    private $where = [];
    private $results = [];

    public function __construct($content = null, $encoding = "UTF-8")
    {
        $this->converter = new CssSelectorConverter();

        if($content !== null) {
            $this->crawler = new Crawler($content);
        }

        return $this;
    }

    public function select(...$selects)
    {
        foreach ($selects as $select) {
            $decodeSelect = explode(" as ", $select);
            $this->selects[] = [
                "selector" => trim($decodeSelect[0]),
                "key" => trim($decodeSelect[1]),
            ];
        }

        return $this;
    }

    public function from($from)
    {
        $this->from = $from;
        return $this;
    }

    public function first()
    {
        return $this->get()->first();
    }

    public function where(...$where)
    {
        $this->where[] = $where;
        return $this;
    }

    public function get()
    {
        // WHERE CHECKING DISINI
        $_remove = [];
        $cssToXpathWhere = $this->converter->toXPath($this->from . " a");

        $this->crawler->filterXPath($cssToXpathWhere)->each(function (Crawler $node, $i) use (&$_remove) {
            if (!preg_match('/^vip|\svip|\svip$/im', $node->attr('class'))) { // regex khusus like %vip%
                array_push($_remove, $i);
            }
        });

        $parentXPath = $this->converter->toXPath($this->from);

        $this->crawler->filterXPath($parentXPath)->each(function (Crawler $crawler, $i) use (&$_remove) {
            if(in_array($i, $_remove)) {
                $node = $crawler->getNode(0);
                $crawler->getNode(0)->parentNode->removeChild($node);
            }
        });

        // PROSES DOM DISINI
        foreach ($this->selects as $select) {

            if(preg_match("/^attr(.*, .*)$/is", $select["selector"])){
                preg_match('/^attr\(\s*?(.*?),\s*?.*\)$/is', $select["selector"], $attr);
                preg_match('/^attr\(\s*?.*\s?,\s*?(.*?)\)$/is', $select["selector"], $pick);

                $cssToXpath = $this->converter->toXPath($this->from . " " . $pick[1]);

                $this->crawler->filterXPath($cssToXpath)->each(function (Crawler $node, $i) use ($select, $attr) {
                    $this->results[$i][$select["key"]] = $node->attr($attr[1]);
                });
            } else {
                $cssToXpath = $this->converter->toXPath($this->from." ". $select["selector"]);

                $this->crawler->filterXPath($cssToXpath)->each(function (Crawler $node, $i) use ($select){
                    $this->results[$i][$select["key"]] = $node->innerText(false);
                });
            }
        }

        return collect($this->results);
    }
}

<?php
namespace Cacing69\Cquery;

use Cacing69\Cquery\Adapter\WhereAttributeAdapter;
use Cacing69\Cquery\Extractor\WhereExtractor;
use Cacing69\Cquery\Extractor\SelectorExtractor;
use Cacing69\Cquery\Support\DomManipulator;
use Cacing69\Cquery\Support\HasSelectorProperty;
use Symfony\Component\CssSelector\CssSelectorConverter;
use Symfony\Component\DomCrawler\Crawler;

class Cquery {
    // use HasSelectorProperty; // will remove

    private $crawler;
    private $content;
    private $converter;
    private $column = [];
    private $limit = null; // delete prop
    // private $where = []; // delete prop
    private $results = [];
    private $from;
    private $backpack = []; // create prop

    public function __construct($content = null, $encoding = "UTF-8")
    {
        $this->converter = new CssSelectorConverter();

        if($content !== null) {
            $this->crawler = new Crawler($content);
            $this->content = $content;
        }

        return $this;
    }

    public function select(...$selects)
    {
        foreach ($selects as $select) {
            if(preg_match('/.+\s+?as\s+?.+/im', $select)) {
                $decodeSelect = explode(" as ", $select);
                $this->column[] = [
                    "selector" => trim($decodeSelect[0]),
                    "key" => trim($decodeSelect[1]),
                ];
            } else {
                $this->column[] = [
                    "selector" => $select,
                    "key" => $select,
                ];
            }
        }

        return $this;
    }

    public function from($value)
    {
        $selector = new SelectorExtractor($value);
        // $this->selector = $selector;
        $this->from = $selector->getXpath();
        $this->backpack[$this->from] = new DomManipulator($this->content, $selector);
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function first()
    {
        return $this
                ->limit(1)
                ->get()
                ->first();
    }

    public function where(...$where)
    {
        $where = new WhereExtractor($where);

        // $this->where[] = $where
        //                 ->setSelector($this->selector)
        //                 ->extract();

        $this->backpack[$this->from]->addWhere($where);

        return $this;
    }

    public function get()
    {
        // WHERE CHECKING DISINI
        // if(count($this->where) > 0) {
        $dom = $this->backpack[$this->from];

        if(count($dom->getWhere()) > 0) {
            $_keep = [];
            foreach ($dom->getWhere() as $value) {
                $cssToXpathWhere = $this->converter->toXPath($dom->getSelector()->getValue() . $value->getNode());

                $this->crawler->filterXPath($cssToXpathWhere)->each(function (Crawler $node, $i) use (&$_keep, $value) {
                    if ($value instanceof WhereAttributeAdapter) {
                        if (preg_match($value->getPattern(), $node->attr($value->getRef()))) { // regex khusus like %vip%
                            array_push($_keep, $i);
                        }
                    }
                });
            }

            // $parentXPath = $this->converter->toXPath($this->selector);
            // $parentXPath = $this->converter->toXPath($dom->getSelector()->getXpath());

            $this->crawler->filterXPath($dom->getSelector()->getXpath())->each(function (Crawler $crawler, $i) use (&$_keep) {
                if (!in_array($i, $_keep)) {
                    $node = $crawler->getNode(0);
                    $node->parentNode->removeChild($node);
                }
            });
        }

        // PROSES DOM DISINI
        $limit = $this->limit;
        foreach ($this->column as $column) {
            if(preg_match("/^attr(.*, .*)$/is", $column["selector"])){
                preg_match('/^attr\(\s*?(.*?),\s*?.*\)$/is', $column["selector"], $attr);
                preg_match('/^attr\(\s*?.*\s?,\s*?(.*?)\)$/is', $column["selector"], $pick);

                // $cssToXpath = $this->converter->toXPath($dom->getSelector() . " " . $pick[1]);
                $cssToXpath = $this->converter->toXPath($dom->getSelector() . " " . $pick[1]);

                $this->crawler->filterXPath($cssToXpath)->each(function (Crawler $node, $i) use ($column, $attr, $limit) {
                    if($limit === null) {
                        $this->results[$i][$column["key"]] = $node->attr($attr[1]);
                    } else if ($limit - 1 <= $i) {
                        $this->results[$i][$column["key"]] = $node->attr($attr[1]);
                        return false;
                    }
                });
            } else {
                $cssToXpath = $this->converter->toXPath($dom->getSelector()." ". $column["selector"]);

                $this->crawler->filterXPath($cssToXpath)->each(function (Crawler $node, $i) use ($column, $limit){
                    if ($limit === null) {
                        $this->results[$i][$column["key"]] = $node->innerText(false);
                    } else if ($limit - 1 <= $i) {
                        $this->results[$i][$column["key"]] = $node->innerText(false);
                        return false;
                    }
                });
            }
        }

        return collect($this->results);
    }

    public function getActiveSelector()
    {
        return $this->backpack[$this->from]->getSelector();
    }
}

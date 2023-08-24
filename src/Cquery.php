<?php
namespace Cacing69\Cquery;

use Cacing69\Cquery\Adapter\FilterAttributeAdapter;
use Cacing69\Cquery\Exception\CqueryException;
use Cacing69\Cquery\Extractor\FilterExtractor;
use Cacing69\Cquery\Extractor\SelectExtractor;
use Cacing69\Cquery\Support\DomManipulator;
use Symfony\Component\CssSelector\CssSelectorConverter;
use Symfony\Component\DomCrawler\Crawler;

class Cquery {
    private $content;
    private $converter;
    // private $column = [];
    private $limit = null;
    private $results = [];
    private $source;
    private $dom = [];

    public function __construct($content = null, $encoding = "UTF-8")
    {
        $this->converter = new CssSelectorConverter();

        if($content !== null) {
            $this->content = $content;
        }
    }
    private function validateSource() {
        if (count($this->dom) === 0) {
            throw new CqueryException("No source defined");
        }
    }

    private function addColumnToActiveDom($column) {
        $this->dom[$this->source]->addColumn($column);
    }

    public function pick(...$selects)
    {
        $this->validateSource();
        $column = null;
        foreach ($selects as $select) {
            if (preg_match('/.+\s+?as\s+?.+/im', $select)) {
                $decodeSelect = explode(" as ", $select);
                $column = [
                    "selector" => trim($decodeSelect[0]),
                    "key" => trim($decodeSelect[1]),
                ];
            } else {
                $column = [
                    "selector" => $select,
                    "key" => $select,
                ];
            }

            if($column === null) {
                throw new CqueryException("wrong column defined");
            }
            $this->addColumnToActiveDom($column);
        }

        return $this;
    }

    public function source($value)
    {
        $selector = new SelectExtractor($value);

        $this->source = $selector->getXpath();

        $this->dom[$this->source] = new DomManipulator($this->content, $selector);
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

    public function filter(...$where)
    {
        $this->validateSource();

        $where = new FilterExtractor($where);
        $this->dom[$this->source]->addFilter($where);

        return $this;
    }

    public function get()
    {
        $this->validateSource();

        // WHERE CHECKING
        $dom = $this->getActiveDom();

        if(count($dom->getFilter()) > 0) {
            $_keep = [];
            foreach ($dom->getFilter() as $value) {
                $cssToXpathWhere = $this->converter->toXPath($dom->getSelector()->getValue() . $value->getNode());
                $dom->getCrawler()->filterXPath($cssToXpathWhere)->each(function (Crawler $node, $i) use (&$_keep, $value) {
                    if ($value instanceof FilterAttributeAdapter) {
                        if (preg_match($value->getPattern(), $node->attr($value->getRef()))) { // regex khusus like %vip%
                            array_push($_keep, $i);
                        }
                    }
                });
            }

            $dom->getCrawler()->filterXPath($dom->getSelector()->getXpath())->each(function (Crawler $crawler, $i) use (&$_keep) {
                if (!in_array($i, $_keep)) {
                    $node = $crawler->getNode(0);
                    $node->parentNode->removeChild($node);
                }
            });
        }

        // PROCESS DOM HERE
        $limit = $this->limit;

        foreach ($this->getActiveDom()->getColumn() as $column) {
            if(preg_match("/^attr(.*, .*)$/is", $column["selector"])){
                preg_match('/^attr\(\s*?(.*?),\s*?.*\)$/is', $column["selector"], $attr);
                preg_match('/^attr\(\s*?.*\s?,\s*?(.*?)\)$/is', $column["selector"], $pick);

                $cssToXpath = $this->converter->toXPath($dom->getSelector() . " " . $pick[1]);
                $dom->getCrawler()->filterXPath($cssToXpath)->each(function (Crawler $node, $i) use ($column, $attr, $limit) {
                    if($limit === null) {
                        $this->results[$this->source][$i][$column["key"]] = $node->attr($attr[1]);
                    } else if ($limit - 1 <= $i) {
                        $this->results[$this->source][$i][$column["key"]] = $node->attr($attr[1]);
                        return false;
                    }
                });
            } else {
                $columnSelector = str_replace($dom->getSelector()->getAlias(), "", $column["selector"]);
                $cssToXpath = $this->converter->toXPath($dom->getSelector()." ". trim($columnSelector));

                $dom->getCrawler()->filterXPath($cssToXpath)->each(function (Crawler $node, $i) use ($column, $limit){
                    if ($limit === null) {
                        $this->results[$this->source][$i][$column["key"]] = $node->innerText();
                    } else if ($limit - 1 <= $i) {
                        $this->results[$this->source][$i][$column["key"]] = $node->innerText();
                        return false;
                    }
                });
            }
        }
        return collect($this->results[@$this->source]);
    }

    public function getActiveSelector()
    {
        return $this
                ->getActiveDom()
                ->getSelector();
    }
    public function getActiveDom()
    {
        return $this->dom[$this->source];
    }
}

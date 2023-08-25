<?php
namespace Cacing69\Cquery;

use Cacing69\Cquery\Adapter\FilterAttributeAdapter;
use Cacing69\Cquery\Exception\CqueryException;
use Cacing69\Cquery\Extractor\FilterExtractor;
use Cacing69\Cquery\Extractor\SelectorExtractor;
use Cacing69\Cquery\Support\DomManipulator;
use Symfony\Component\CssSelector\CssSelectorConverter;
use Symfony\Component\DomCrawler\Crawler;

class Cquery {
    private $content;
    private $converter;
    // private $column = [];
    private $limit = null;
    private $results = [];
    private $element;
    private $dom = [];

    public function __construct($content = null, $encoding = "UTF-8")
    {
        $this->converter = new CssSelectorConverter();

        if($content !== null) {
            $this->content = $content;
        }
    }
    private function validateElement() {
        if (count($this->dom) === 0) {
            throw new CqueryException("no element defined");
        }
    }

    private function addColumnToActiveDom($column) {
        $this->dom[$this->element]->addColumn($column);
    }

    public function pick(...$selects)
    {
        $this->validateElement();
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

    public function from($value)
    {
        $selector = new SelectorExtractor($value);

        $this->element = $selector->getXpath();

        $this->dom[$this->element] = new DomManipulator($this->content, $selector);
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
        $this->validateElement();

        $where = new FilterExtractor($where);
        $this->dom[$this->element]->addFilter($where);

        return $this;
    }

    public function OrFilter(...$where)
    {
        $this->validateElement();

        $where = new FilterExtractor($where, "or");
        $this->dom[$this->element]->addFilter($where);

        return $this;
    }

    public function get()
    {
        $this->validateElement();

        // WHERE CHECKING
        $dom = $this->getActiveDom();

        if(count($dom->getFilter()) > 0) {
            $_affect = [
                "and" => [],
                "or" => [],
            ];
            foreach ($dom->getFilter() as $key => $value) {
                $cssToXpathWhere = $this->converter->toXPath($dom->getSelector()->getValue() . $value->getNode());
                $dom->getCrawler()->filterXPath($cssToXpathWhere)->each(function (Crawler $node, $i) use (&$_affect, $key, $value) {
                    if ($value instanceof FilterAttributeAdapter) {
                        if (preg_match($value->getPattern(), $node->attr($value->getRef()))) { // regex khusus like %vip%
                            $_affect[$value->getOperator()][$key][] = $i;
                        }
                    }
                });
            }

            $_filtered = $this->getResultFilter($_affect);

            $dom->getCrawler()->filterXPath($dom->getSelector()->getXpath())->each(function (Crawler $crawler, $i) use ($_filtered) {
                if (!in_array($i, $_filtered)) {
                    $node = $crawler->getNode(0);
                    $node->parentNode->removeChild($node);
                }
            });
        }

        // PROCESS DOM HERE
        $limit = $this->limit;

        foreach ($this->getActiveDom()->getColumn() as $column) {
            if(preg_match("/^attr(.*,\s?.*)$/is", $column["selector"])){
                preg_match('/^attr\(\s*?(.*?),\s*?.*\)$/is', $column["selector"], $attr);
                preg_match('/^attr\(\s*?.*\s?,\s*?(.*?)\)$/is', $column["selector"], $pick);

                $cssToXpath = $this->converter->toXPath($dom->getSelector() . " " . $pick[1]);
                $dom->getCrawler()->filterXPath($cssToXpath)->each(function (Crawler $node, $i) use ($column, $attr, $limit) {
                    if($limit === null) {
                        $this->results[$this->element][$i][$column["key"]] = $node->attr($attr[1]);
                    } else if ($limit - 1 <= $i) {
                        $this->results[$this->element][$i][$column["key"]] = $node->attr($attr[1]);
                        return false;
                    }
                });
            } else {
                $columnSelector = str_replace($dom->getSelector()->getAlias(), "", $column["selector"]);
                $cssToXpath = $this->converter->toXPath($dom->getSelector()." ". trim($columnSelector));

                $dom->getCrawler()->filterXPath($cssToXpath)->each(function (Crawler $node, $i) use ($column, $limit){
                    if ($limit === null) {
                        $this->results[$this->element][$i][$column["key"]] = $node->innerText();
                    } else if ($limit - 1 <= $i) {
                        $this->results[$this->element][$i][$column["key"]] = $node->innerText();
                        return false;
                    }
                });
            }
        }
        return collect($this->results[@$this->element]);
    }

    public static function getResultFilter($filtered) {
        $result = [
            "and" => [],
            "or" => [],
        ];

        if(array_key_exists("and", $filtered) && count($filtered["and"]) > 0){
            $result["and"] = array_intersect(...$filtered["and"]);
        }

        if (array_key_exists("or", $filtered) && count($filtered["or"]) > 0) {
            $result["or"] = array_unique(array_merge(...$filtered["or"]));
        }

        $filterResult = array_unique(array_merge($result["and"], $result["or"]));

        sort($filterResult, SORT_NUMERIC);

        return $filterResult;
    }

    public function getActiveDom()
    {
        return $this->dom[$this->element];
    }
}

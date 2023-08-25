<?php
declare(strict_types=1);

namespace Cacing69\Cquery;

use Cacing69\Cquery\Adapter\AttributeAdapter;
use Cacing69\Cquery\Adapter\FilterAttributeAdapter;
use Cacing69\Cquery\Exception\CqueryException;
use Cacing69\Cquery\Extractor\FilterExtractor;
use Cacing69\Cquery\Extractor\SourceExtractor;
use Cacing69\Cquery\Support\DomManipulator;
use Symfony\Component\CssSelector\CssSelectorConverter;
use Symfony\Component\DomCrawler\Crawler;
use Tightenco\Collect\Support\Collection;

class Cquery {
    private $content;
    private $converter;
    private $limit = null;
    private $results = [];
    private $source;
    private $dom = [];

    public function __construct(string $content = null, string $encoding = "UTF-8")
    {
        $this->converter = new CssSelectorConverter();

        if($content !== null) {
            $this->content = $content;
        }
    }
    private function validatesource() {
        if (count($this->dom) === 0) {
            throw new CqueryException("no source defined");
        }
    }

    public function pick(string ...$picks): Cquery
    {
        $this->validatesource();
        // $column = null;
        foreach ($picks as $pick) {
        //     if (preg_match('/.+\s+?as\s+?.+/im', $select)) {
        //         $decodeSelect = explode(" as ", $select);
        //         $column = [
        //             "selector" => trim($decodeSelect[0]),
        //             "key" => trim($decodeSelect[1]),
        //         ];
        //     } else {
        //         $column = [
        //             "selector" => $select,
        //             "key" => $select, "_",
        //         ];
        //     }

            // if($pick === null) {
            //     throw new CqueryException("wrong column defined");
            // }

            $this->dom[$this->source]->addDefiner($pick);
        }

        return $this;
    }

    public function from(string $value): Cquery
    {
        $selector = new SourceExtractor($value);

        $this->source = $selector->getXpath();

        $this->dom[$this->source] = new DomManipulator($this->content, $selector);
        return $this;
    }

    public function limit(int $limit): Cquery
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

    public function filter(...$filter): Cquery
    {
        $this->validatesource();

        $filter = new FilterExtractor($filter);
        $this->dom[$this->source]->addFilter($filter);

        return $this;
    }

    public function OrFilter(...$filter) : Cquery
    {
        $this->validatesource();

        $filter = new FilterExtractor($filter, "or");
        $this->dom[$this->source]->addFilter($filter);

        return $this;
    }

    public function get() : Collection
    {
        $this->validatesource();

        // WHERE CHECKING
        $dom = $this->getActiveDom();

        if(count($dom->getFilter()) > 0) {
            $_affect = [
                "and" => [],
                "or" => [],
            ];

            foreach ($dom->getFilter() as $key => $value) {
                $cssToXpathWhere = $this->converter->toXPath($dom->getSelector()->getValue() . $value->getNode());
                $dom->getCrawler()->filterXPath($cssToXpathWhere)->each(function (Crawler $node, $i) use (&$_affect, &$_remove, $key, $value) {
                    if ($value instanceof FilterAttributeAdapter) {
                        if($node->attr($value->getRef()) !== null) {
                            if($value->getPattern()) {
                                if (preg_match($value->getPattern(), $node->attr($value->getRef()))) {
                                    $_affect[$value->getOperator()][$key][] = $i;
                                }
                            }

                            if ($value->getCallback() !== null) {
                                $callback = $value->getCallback();
                                if($callback($node->attr($value->getRef()))) {
                                    $_affect[$value->getOperator()][$key][] = $i;
                                }
                            }
                        }
                    }
                });
            }

            $_filtered = $this->getResultFilter($_affect);

            if(count($_filtered) === 0) {
                return collect([]);
            }

            if(count($_filtered) > 0){
                $dom->getCrawler()->filterXPath($dom->getSelector()->getXpath())->each(function (Crawler $crawler, $i) use ($_filtered) {
                    if (!in_array($i, $_filtered)) {
                        $node = $crawler->getNode(0);
                        $node->parentNode->removeChild($node);
                    }
                });
            }
        }

        // PROCESS DOM HERE
        $limit = $this->limit;

        foreach ($this->getActiveDom()->getDefiner() as $column) {
            if($column->getColumn() instanceof AttributeAdapter){
                $cssToXpath = $this->converter->toXPath($dom->getSelector() . " " . $column->getColumn()->getNode());
                $dom->getCrawler()->filterXPath($cssToXpath)->each(function (Crawler $node, $i) use ($column, $limit) {
                    if($limit === null) {
                        $this->results[$this->source][$i][$column->getAlias()] = $node->attr($column->getColumn()->getRef());
                    } else if ($limit - 1 <= $i) {
                        $this->results[$this->source][$i][$column->getAlias()] = $node->attr($column->getColumn()->getRef());
                        return false;
                    }
                });
            } else {
                $columnSelector = str_replace($dom->getSelector()->getAlias(), "", $column["selector"]);
                $cssToXpath = $this->converter->toXPath($dom->getSelector()." ". trim($columnSelector));

                $dom->getCrawler()->filterXPath($cssToXpath)->each(function (Crawler $node, $i) use ($column, $limit){
                    if ($limit === null) {
                        $this->results[$this->source][$i][$column["key"]] = $node->text();
                    } else if ($limit - 1 <= $i) {
                        $this->results[$this->source][$i][$column["key"]] = $node->text();
                        return false;
                    }
                });
            }
        }
        return collect($this->results[@$this->source]);
    }

    public static function getResultFilter(array $filtered) : array {
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

    public function getActiveDom() : DomManipulator
    {
        return $this->dom[$this->source];
    }
}

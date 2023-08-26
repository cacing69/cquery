<?php
declare(strict_types=1);

namespace Cacing69\Cquery;

use Cacing69\Cquery\Exception\CqueryException;
use Cacing69\Cquery\Extractor\SourceExtractor;
use Cacing69\Cquery\Support\DOMManipulator;
use Symfony\Component\DomCrawler\Crawler;
use Tightenco\Collect\Support\Collection;

class Cquery {
    private $content;
    private $limit = null;
    private $results = [];
    private $source;
    private $dom = [];

    public function __construct(string $content = null, string $encoding = "UTF-8")
    {
        if($content !== null) {
            $this->content = $content;
        }
    }
    private function validateSource() {
        if (count($this->dom) === 0) {
            throw new CqueryException("no source defined");
        }
    }

    public function pick(string ...$picks): Cquery
    {
        $this->validateSource();
        foreach ($picks as $pick) {
            $this->dom[$this->source]->addDefiner($pick);
        }

        return $this;
    }

    public function from(string $value): Cquery
    {
        $selector = new SourceExtractor($value);
        $this->source = $selector->getXpath();

        $this->dom[$this->source] = new DOMManipulator($this->content, $selector);
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
        $this->validateSource();
        $this->dom[$this->source]->addFilter($filter, "and");

        return $this;
    }

    public function OrFilter(...$filter) : Cquery
    {
        $this->validateSource();
        $this->dom[$this->source]->addFilter($filter, "or");

        return $this;
    }
    public function get() : Collection
    {
        $this->validateSource();

        // WHERE CHECKING
        $dom = $this->getActiveDom();

        if(count($dom->getFilter()) > 0) {
            $_affect = [
                "and" => [],
                "or" => [],
            ];

            foreach ($dom->getFilter() as $key => $value) {
                $dom->getCrawler()->filterXPath($dom->getSelector()->getXpath())->each(function (Crawler $node, $index) use (&$_affect, $key, $value) {
                    $node->filter($value->getNode())->each(function (Crawler $childNode) use (&$_affect, $key, $value, $index) {
                        $callback = $value->getCallback();
                        if ($value->extract($callback($childNode))) {
                            $_affect[$value->getOperator()][$key][] = $index;
                        }
                    });
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

        foreach ($this->getActiveDom()->getDefiner() as $definer) {
            $dom->getCrawler()->filterXPath($dom->getSelector()->getXpath())->each(function (Crawler $node, $index) use ($definer, $limit) {
                $adapter = $definer->getAdapter();

                if (count($node->filter($adapter->getNode())) === 0) {
                    $this->results[$this->source][$index][$definer->getAlias()] = null;
                }

                $node->filter($adapter->getNode())->each(function (Crawler $childNode) use ($definer, $index, $limit) {
                    $callback = $definer->getAdapter()->getCallback();
                    $this->results[$this->source][$index][$definer->getAlias()] = $callback($childNode);
                });

                if ($limit !== null && $limit - 1 <= $index) {
                    return false;
                }
            });
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

    public function getActiveDom() : DOMManipulator
    {
        return $this->dom[$this->source];
    }
}

<?php
declare(strict_types = 1);
namespace Cacing69\Cquery\Loader;

use Cacing69\Cquery\Exception\CqueryException;
use Cacing69\Cquery\Extractor\SourceExtractor;
use Cacing69\Cquery\DOMManipulator;
use Cacing69\Cquery\Loader;
use Cacing69\Cquery\Support\Str;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DomCrawler\Crawler;

class HTMLLoader extends Loader
{
    private $dom = [];

    public function __construct(string $content = null, string $encoding = "UTF-8")
    {
        if ($content !== null) {
            $this->content = $content;
        }
    }

    public function setContent(string $content)
    {
        $this->content = $content;

        return $this;
    }

    protected function validateSource()
    {
        if (count($this->dom) === 0) {
            throw new CqueryException("no source defined");
        }
    }

    public function define(...$defines)
    {
        $this->validateSource();
        foreach ($defines as $define) {
            $this->dom[$this->source]->addDefiner($define);
        }

        return $this;
    }

    public function from(string $value)
    {
        $selector = new SourceExtractor($value);
        $this->source = Str::slug($selector->getXpath());

        $this->dom[$this->source] = new DOMManipulator($this->content, $selector);
        return $this;
    }

    public function filter(...$filter)
    {
        $this->validateSource();
        $this->dom[$this->source]->addFilter($filter, "and");

        return $this;
    }

    public function OrFilter(...$filter)
    {
        $this->validateSource();
        $this->dom[$this->source]->addFilter($filter, "or");

        return $this;
    }

    public function get(): ArrayCollection
    {
        $this->validateSource();
        $this->results[$this->source] = [];

        // WHERE CHECKING
        $dom = $this->getActiveDom();
        $_filtered = null;

        $bound = null;

        if (count($dom->getFilter()) > 0) {
            $_affect = [
                "and" => [],
                "or" => [],
            ];

            foreach ($dom->getFilter() as $key => $filterAdapter) {
                $_data = $dom->getCrawler()
                    ->filterXPath($dom->getSource()->getXpath())
                    ->filterXPath($filterAdapter->getNodeXpath());

                if ($filterAdapter->getCall() === "extract") {
                    $_data = $_data->extract($filterAdapter->getCallParameter());
                } else if ($filterAdapter->getCall() === "filter") {
                    dd($filterAdapter);
                }

                if ($filterAdapter->getAfterCall() !== null) {
                    $_afterCall = $filterAdapter->getAfterCall();

                    $_data = array_map(function ($_mapValue) use ($_afterCall) {
                        return $_afterCall($_mapValue);
                    }, $_data);
                }

                foreach ($_data as $_key => $_value) {
                    if (!is_numeric($_value)) {
                        $_value = trim(preg_replace('/\s+/', ' ', (string) $_value));
                    }


                    if($filterAdapter->filterExecutor($_value)) {
                        $_affect[$filterAdapter->getOperator()][$key][] = $_key;
                    }
                }
            }

            $_filtered = $this->getResultFilter($_affect);

            if (count($_filtered) === 0) {
                return new ArrayCollection([]);
            }
        }

        // PROCESS DOM HERE
        $limit = $this->limit;

        $_hold_data = [];

        foreach ($this->getActiveDom()->getDefiner() as $key => $definer) {
            $_data = null;
            if($definer->getAdapter()->getCall() === "extract"){
                $_data = $dom->getCrawler()
                        ->filterXPath($dom->getSource()->getXpath())
                        ->filterXPath($definer->getAdapter()->getNodeXpath());


                if($_filtered !== null) {
                    $_data = $_data->reduce(function (Crawler $node, $i) use ($_filtered) {
                        return in_array($i, $_filtered);
                    });
                }

                $_data = $_data->extract($definer->getAdapter()->getCallParameter());
            } else if($definer->getAdapter()->getCall() === "filter.extract"){
                $_data = [];
                $dom->getCrawler()
                    ->filterXPath($dom->getSource()->getXpath())
                    ->filterXPath($definer->getAdapter()->getNodeXpath())
                    ->each(function (Crawler $node, $i) use (&$_data){
                    //    dump($node->text());$node-
                        $node->filter("a")->each(function (Crawler $_node, $_i) use ($i, &$_data) {
                            $_data[$i][] = $_node->text();
                        });
                    });
            }

            if($key === 0) {
                $bound = count($_data);
            } else {
                if(count($_data) !== $bound) {
                    throw new CqueryException("error query definer, it looks like an error occurred while attempting to pick the column, It's because there are no matching rows in each column.");
                }
            }

            if($definer->getAdapter()->getAfterCall() !== null) {
                $_afterCall = $definer->getAdapter()->getAfterCall();
                $_data = array_map(function ($_mapValue) use ($_afterCall) {
                    return $_afterCall($_mapValue);
                }, $_data);
            }

            foreach ($_data as $_key => $_value) {
                if(is_string($_value)) {
                    $_value = trim(preg_replace('/\s+/', ' ', (string) $_value));
                }

                if ($limit !== null) {
                    if ($_key === $limit) {
                        break;
                    } else {
                        $_hold_data[$_key][$definer->getAlias()] = $_value;
                    }
                } else {
                    $_hold_data[$_key][$definer->getAlias()] = $_value;
                }
            }
        }
        return new ArrayCollection($_hold_data);
    }

    public function getActiveDom(): DOMManipulator
    {
        return $this->dom[$this->source];
    }
}

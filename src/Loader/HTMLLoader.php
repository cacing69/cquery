<?php
declare(strict_types = 1);
namespace Cacing69\Cquery\Loader;

use Cacing69\Cquery\Adapter\HTML\ClosureCallbackAdapter;
use Cacing69\Cquery\Exception\CqueryException;
use Cacing69\Cquery\Extractor\SourceExtractor;
use Cacing69\Cquery\Support\DOMManipulator;
use Cacing69\Cquery\Support\StringHelper;
use Symfony\Component\DomCrawler\Crawler;
use Tightenco\Collect\Support\Collection;

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

    public function pick(...$picks)
    {
        $this->validateSource();
        foreach ($picks as $pick) {
            $this->dom[$this->source]->addDefiner($pick);
        }

        return $this;
    }

    public function from(string $value)
    {
        $selector = new SourceExtractor($value);
        $this->source = StringHelper::slug($selector->getXpath());

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

    public function get(): Collection
    {
        $this->validateSource();
        $this->results[$this->source] = [];

        // WHERE CHECKING
        $dom = $this->getActiveDom();
        $_filtered = null;

        if (count($dom->getFilter()) > 0) {
            $_affect = [
                "and" => [],
                "or" => [],
            ];

            foreach ($dom->getFilter() as $key => $adapter) {
                $dom->getCrawler()->filterXPath($dom->getSource()->getXpath())->each(function (Crawler $node, $index) use (&$_affect, $key, $adapter, $dom) {
                    if($adapter->getNode() !== null){
                        if ($adapter instanceof ClosureCallbackAdapter) {
                            $node->filterXPath($adapter->getNodeXpath())->each(function (Crawler $childNode, $indexChild) use (&$_affect, $key, $adapter, $index, $dom) {
                                $callback = $adapter->getRaw();
                                if ($callback($childNode)) {
                                    $_index = $index;
                                    if ($dom->getCrawler()->filterXPath($dom->getSource()->getXpath())->count() === 1) {
                                        $_index = $indexChild;
                                    }

                                    $_affect[$adapter->getOperator()][$key][] = $_index;
                                }
                            });
                        } else {
                            $node->filterXPath($adapter->getNodeXpath())->each(function (Crawler $childNode, $indexChild) use (&$_affect, $key, $adapter, $index, $dom) {
                                $callback = $adapter->getCallback();
                                if ($adapter->extract($callback($childNode))) {

                                    $_index = $index;
                                    if ($dom->getCrawler()->filter($dom->getSource()->getValue())->count() === 1) {
                                        $_index = $indexChild;
                                    }

                                    $_affect[$adapter->getOperator()][$key][] = $_index;
                                }
                            });
                        }
                    }
                });
            }

            $_filtered = $this->getResultFilter($_affect);

            // dump(count($_filtered));

            if (count($_filtered) === 0) {
                return collect([]);
            }

            // if (count($_filtered) > 0) {
            //     $dom->getCrawler()->filter($dom->getSource()->getValue())->each(function (Crawler $node, $index) use ($_filtered, $dom) {

            //         if($dom->getCrawler()->filter($dom->getSource()->getValue())->count() === 1){
            //             foreach ($dom->getFilter() as $key => $adapter) {
            //                 if ($adapter->getNode() !== null) {
            //                     // dd($ge);
            //                     $node->filter($adapter->getNode())->each(
            //                         function (Crawler $childNode, $indexChild) use (&$_affect, $key, $adapter, $index, $dom, $_filtered) {
            //                             // dd($childNode->nodeName());
            //                             if(!in_array($indexChild, $_filtered)) {
            //                                 foreach ($childNode as $__node) {
            //                                     dump($__node);
            //                                     $__node->parentNode->removeChild($__node);
            //                                 }
            //                                 // $currentNode = $childNode->getNode(0);
            //                                 // $currentNode->parentNode->removeChild($currentNode);
            //                             }
            //                          }
            //                     );
            //                 }
            //             }
            //         } else {
            //             if (!in_array($index, $_filtered)) {
            //                 $currentNode = $node->getNode(0);
            //                 $currentNode->parentNode->removeChild($currentNode);
            //             }
            //         }
            //         // }
            //     });
            // }
        }

        // PROCESS DOM HERE
        $limit = $this->limit;

        $_hold_data = [];

        foreach ($this->getActiveDom()->getDefiner() as $key => $definer) {
            // if($dom->getCrawler()->filterXPath($dom->getSource()->getXpath())->count() === 1) {
            // if(true) {
                $_data = $dom->getCrawler()->filterXPath($dom->getSource()->getXpath())
                    ->filterXPath($definer->getAdapter()->getNodeXpath());

                if($_filtered !== null) {
                $_data = $_data->reduce(function (Crawler $node, $i) use ($_filtered) {
                        return in_array($i, $_filtered);
                    });
                }

                // if ($limit !== null) {
                //     $_data = $_data->reduce(function (Crawler $node) use ($limit, $_hold_data, $key) {
                //         // dump($limit." ". count($_hold_data));
                //         return $limit == count($_hold_data);
                //     });
                // }

                // dd($definer);

                if($definer->getAdapter()->getCall() === "extract"){
                    $_data = $_data->{$definer->getAdapter()->getCall()}($definer->getAdapter()->getCallParameter());
                } else if($definer->getAdapter()->getCall() === "filter"){
                    dd($definer);
                }

                if($definer->getAdapter()->getAfterCall() !== null) {
                    $_afterCall = $definer->getAdapter()->getAfterCall();
                    // dd($_afterCall);
                    $_data = array_map(function ($_mapValue) use ($_afterCall) {
                        return $_afterCall($_mapValue);
                    }, $_data);
                }

                foreach ($_data as $_key => $_value) {

                    if(!is_numeric($_value)) {
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

                // dump();

                // if($key === count($this->getActiveDom()->getDefiner())) {
                //     if($limit !== null) {
                //         if($limit === count($_hold_data)) {
                //             break;
                //         }
                //     }
                // }

                // if($limit !== null && count($_hold_data) === $limit && ($key + 1) === count($this->getActiveDom()->getDefiner())) {
                //     break;
                // }
            // }

            // OLD VERSION, ITS RUNNING, BUT VERY SLOW
            // $dom->getCrawler()->filter($dom->getSource()->getValue())->each(function (Crawler $node, $index) use ($dom, $definer, $limit, $_filtered, $key) {

            //     if ($definer->getAdapter()->getNode() !== null) {
            //         if (count($node->filter($definer->getAdapter()->getNode())) === 0) {
            //             // if ($limit !== null && $limit > count($this->results[$this->source])) {
            //             //     return false;
            //             // }

            //             if ($_filtered !== null) {
            //                 if (in_array($index, $_filtered)) {
            //                     $this->results[$this->source][$index][$definer->getAlias()] = null;
            //                 }
            //             } else {
            //                 $this->results[$this->source][$index][$definer->getAlias()] = null;
            //             }
            //         }


            //         $node->filter($definer->getAdapter()->getNode())->each(function (Crawler $childNode, $indexChild) use ($dom, $definer, $index, $_filtered, $limit, $key) {
            //             $callback = $definer->getAdapter()->getCallback();


            //             $_index = $index;
            //             if ($dom->getCrawler()->filter($dom->getSource()->getValue())->count() === 1) {
            //                 $_index = $indexChild;
            //             }


            //             if ($_filtered !== null) {
            //                 if (in_array($_index, $_filtered)) {
            //                     $this->results[$this->source][$_index][$definer->getAlias()] = $callback($childNode);
            //                 }
            //             } else {
            //                 dump(count($this->results[$this->source]));
            //                 $this->results[$this->source][$_index][$definer->getAlias()] = $callback($childNode);
            //             }
            //         });
            //     }
            // });
        }

        // CLEANSING DATA
        // $_clean_data = [];
        // foreach ($variable as $key => $value) {
        //     # code...
        // }
        // dd($_hold_data);

        return collect($_hold_data);

        // if($limit !== null){
        //     $result_collect = $result_collect->take($limit);
        // }

        // return $result_collect;
    }

    public function getActiveDom(): DOMManipulator
    {
        return $this->dom[$this->source];
    }
}

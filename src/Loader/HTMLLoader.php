<?php
declare(strict_types = 1);
namespace Cacing69\Cquery\Loader;

use Cacing69\Cquery\Adapter\HTML\ClosureCallbackAdapter;
use Cacing69\Cquery\Exception\CqueryException;
use Cacing69\Cquery\Extractor\SourceExtractor;
use Cacing69\Cquery\Support\DOMManipulator;
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
        $this->source = $selector->getXpath();

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
                $dom->getCrawler()->filter($dom->getSource()->getValue())->each(function (Crawler $node, $index) use (&$_affect, $key, $adapter, $dom) {
                    if($adapter->getNode() !== null){
                        if ($adapter instanceof ClosureCallbackAdapter) {
                            $node->filter($adapter->getNode())->each(function (Crawler $childNode, $indexChild) use (&$_affect, $key, $adapter, $index, $dom) {
                                $callback = $adapter->getRaw();
                                if ($callback($childNode)) {
                                    $_index = $index;
                                    if ($dom->getCrawler()->filter($dom->getSource()->getValue())->count() === 1) {
                                        $_index = $indexChild;
                                    }

                                    $_affect[$adapter->getOperator()][$key][] = $_index;
                                }
                            });
                        } else {
                            $node->filter($adapter->getNode())->each(function (Crawler $childNode, $indexChild) use (&$_affect, $key, $adapter, $index, $dom) {
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

        // dump($_filtered);

        // $domSource = $dom->getCrawler()->filter($dom->getSource()->getValue());
        foreach ($this->getActiveDom()->getDefiner() as $definer) {
            $dom->getCrawler()->filter($dom->getSource()->getValue())->each(function (Crawler $node, $index) use ($dom, $definer, $limit, $_filtered) {
                if ($definer->getAdapter()->getNode() !== null) {
                    if (count($node->filter($definer->getAdapter()->getNode())) === 0) {
                        if ($_filtered !== null) {
                            if (in_array($index, $_filtered)) {
                                $this->results[$this->source][$index][$definer->getAlias()] = null;
                            }
                        } else {
                            $this->results[$this->source][$index][$definer->getAlias()] = null;
                        }
                    }

                    $node->filter($definer->getAdapter()->getNode())->each(function (Crawler $childNode, $indexChild) use ($dom, $definer, $index, $_filtered, $limit) {
                        $callback = $definer->getAdapter()->getCallback();

                        $_index = $index;
                        if($dom->getCrawler()->filter($dom->getSource()->getValue())->count() === 1) {
                            $_index = $indexChild;
                        }

                        if($_filtered !== null) {
                            if(in_array($_index, $_filtered)) {
                                $this->results[$this->source][$_index][$definer->getAlias()] = $callback($childNode);
                            }
                        } else {
                            $this->results[$this->source][$_index][$definer->getAlias()] = $callback($childNode);
                        }

                        if($limit !== null) {
                            if($limit === count($this->results[$this->source])) {
                                return false;
                            }
                        }
                    });
                } else {
                    if ($_filtered !== null) {
                        if (in_array($index, $_filtered)) {
                            $this->results[$this->source][$index][$definer->getAlias()] = null;
                        }
                    } else {
                        $this->results[$this->source][$index][$definer->getAlias()] = null;
                    }
                }

                // if ($limit !== null && $limit >= count($this->results[$this->source])) {
                //     return false;
                // }
            });
        }

        return collect($this->results[$this->source]);
    }

    public function getActiveDom(): DOMManipulator
    {
        return $this->dom[$this->source];
    }
}

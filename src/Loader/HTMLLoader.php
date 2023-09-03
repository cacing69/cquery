<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Loader;

use Cacing69\Cquery\CqueryException;
use Cacing69\Cquery\Loader;
use Cacing69\Cquery\Source;
use Doctrine\Common\Collections\ArrayCollection;
use React\EventLoop\Loop;
use React\Http\Browser;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class HTMLLoader extends Loader
{
    // private $crawler;

    public function __construct(string $content = null, $isRemote = false)
    {
        $this->isRemote = $isRemote;
        if ($content !== null && !$isRemote) {
            $this->crawler = new Crawler($content);
        } else {
            $this->uri = $content;
        }
    }

    // protected function fetchCrawler()
    // {

    //     if($this->isRemote) {
    //         $this->browser = new HttpBrowser(HttpClient::create());
    //         if($this->callbackReady) {
    //             $_callbackReady = $this->callbackReady;

    //             $_browser = $this->browser;

    //             $this->browser = $_callbackReady($_browser);
    //         }

    //         $this->browser->request('GET', $this->uri);

    //         $this->crawler = new Crawler($this->browser->getResponse()->getContent());
    //     }
    // }

    // public function from(string $value)
    // {
    //     $this->filter = [];
    //     $this->fetchCrawler();
    //     $this->source = new Source($value);
    //     return $this;
    // }

    // public function filter($filter)
    // {
    //     $this->validateSource();
    //     $this->addFilter($filter, "and");

    //     return $this;
    // }

    // public function orFilter($filter)
    // {
    //     $this->validateSource();
    //     $this->addFilter($filter, "or");

    //     return $this;
    // }

    public function get(): ArrayCollection
    {
        $this->validateDefiners();

        // WHERE CHECKING
        // $dom = $this->dom;
        $_filtered = null;

        $bound = null;

        if (count($this->filter) > 0) {
            $_affect = [
                "and" => [],
                "or" => [],
            ];

            foreach ($this->filter as $key => $filterAdapter) {
                $_data = $this->crawler
                    // ->filterXPath($this->getSource()->getXpath())
                    ->filterXPath($this->getSource()->getXpath())
                    ->filterXPath($filterAdapter->getNodeXpath());

                if ($filterAdapter->getCallMethod() === "extract") {
                    $_data = $_data->extract($filterAdapter->getCallMethodParameter());
                } elseif ($filterAdapter->getCallMethod() === "filter") {
                    dd($filterAdapter);
                }

                if ($filterAdapter->getCallback() !== null) {
                    $_callback = $filterAdapter->getCallback();

                    $_data = array_map(function ($_mapValue) use ($_callback) {
                        return $_callback($_mapValue);
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

        foreach ($this->definer as $key => $definer) {
            $_data = null;
            if($definer->getAdapter()->getCallMethod() === "extract") {
                $_data = $this
                        ->crawler
                        // ->filterXPath($this->getSource()->getXpath())
                        ->filterXPath($this->getSource()->getXpath())
                        ->filterXPath($definer->getAdapter()->getNodeXpath());


                if($_filtered !== null) {
                    $_data = $_data->reduce(function (Crawler $node, $i) use ($_filtered) {
                        return in_array($i, $_filtered);
                    });
                }

                $_data = $_data->extract($definer->getAdapter()->getCallMethodParameter());
            } elseif($definer->getAdapter()->getCallMethod() === "filter.each") {
                $_data = [];
                $this
                    ->crawler
                    // ->filterXPath($this->getSource()->getXpath())
                    ->filterXPath($this->getSource()->getXpath())
                    ->filterXPath($definer->getAdapter()->getNodeXpath())
                    ->each(function (Crawler $node, $i) use (&$_data, $definer) {
                        $node->filter("a")->each(function (Crawler $_node, $_i) use ($i, &$_data, $definer) {
                            if(is_array($definer->getAdapter()->getCallMethodParameter()) && count($definer->getAdapter()->getCallMethodParameter()) === 1) {
                                $__callParameter = $definer->getAdapter()->getCallMethodParameter()[0];
                                if($__callParameter === "_text") {
                                    $_data[$i][] = $_node->text();
                                } else {
                                    $_data[$i][] = $_node->attr($__callParameter);
                                }
                            } else {
                                dd('not_supported_yet');
                            }
                        });
                    });
            }

            if($key === 0) {
                $bound = count($_data);
            } else {
                // TODO tambahkan metode ambil data dengan filter->each, walaupun itu akan sedikit lambat, buts its ok, karena hanya untuk kasus tertentu
                // TODO index kolom yang menjadi acuan utama adalah index pertama di definer

                if(count($_data) !== $bound) {
                    throw new CqueryException("error query definer, it looks like an error occurred while attempting to define the column, it's because there are no matching rows in each column.");
                }
            }

            if($definer->getAdapter()->getCallback() !== null) {
                $_callback = $definer->getAdapter()->getCallback();
                $_data = array_map(function ($_mapValue) use ($_callback) {
                    return $_callback((string) $_mapValue);
                }, $_data);
            }

            foreach ($_data as $_key => $_value) {
                if(is_string($_value)) {
                    $_value = trim(preg_replace('/\s+/', ' ', (string) $_value));
                }

                if(is_string($_value) || is_numeric($_value)) {
                    $_value = strlen((string) $_value) > 0 ? $_value : null;
                }

                if ($limit !== null) {
                    if ($_key === $limit) {
                        break;
                    } else {
                        $_hold_data[$_key][$definer->getAlias()] = $_value;
                    }
                } else {
                    // IF ALIAS tags[*][text]
                    if(preg_match('/^\s*([A-Za-z0-9\-\_]+?)\[\*\]\[([A-Za-z0-9\-\_]*?)\]\s*?/', $definer->getAlias())) {
                        preg_match('/^\s*([A-Za-z0-9\-\_]+?)\[\*\]\[([A-Za-z0-9\-\_]*?)\]\s*?/', $definer->getAlias(), $_extractAlias);

                        // TODO perlu di check, panjang setiap element dari setiap key harus sama, jika tidak sama, ambil ulang data dengan dengan filter->each
                        if(array_key_exists($_extractAlias[1], $_hold_data[$_key])) {
                            $_hold_child = $_hold_data[$_key][$_extractAlias[1]];
                        }

                        foreach ($_value as $__key => $__value) {
                            $__value = strlen((string) $__value) > 0 ? $__value : null;

                            $_hold_child[$__key][$_extractAlias[2]] = $__value;
                        }

                        $_hold_data[$_key][$_extractAlias[1]] = $_hold_child;

                        // IF ALIAS tags[text]
                    } elseif(preg_match('/^\s*([A-Za-z0-9\-\_]+?)\[([A-Za-z0-9\-\_]*?)\]\s*?/', $definer->getAlias())) {
                        preg_match('/^\s*([A-Za-z0-9\-\_]+?)\[([A-Za-z0-9\-\_]*?)\]\s*?/', $definer->getAlias(), $_extractAlias);

                        $_hold_data[$_key][$_extractAlias[1]][$_extractAlias[2]] = $_value;
                    } else {
                        $_hold_data[$_key][$definer->getAlias()] = $_value;
                    }
                }
            }
        }

        $this->isFetched = true;

        $this->results = $_hold_data;

        if($this->callbackItem) {
            $_callbackItem = $this->callbackItem;

            foreach ($this->results as $_key => $_value) {
                    $this->results[$_key] = $_callbackItem($_value);
                }

            // if($this->callbackFinishType == "array") {
            //     $this->results = $_callbackFinish($this->results);
            // } elseif($this->callbackFinishType == "element"){
            //     foreach ($this->results as $_key => $_value) {
            //         $this->results[$_key] = $_callbackFinish($_value);
            //     }
            // }
        }

        if ($this->callbackArray) {
            $_callbackArray = $this->callbackArray;
            $this->results = $_callbackArray($this->results);
        }

        return new ArrayCollection($this->results);
    }
}

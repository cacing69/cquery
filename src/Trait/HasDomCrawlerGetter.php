<?php

namespace Cacing69\Cquery\Trait;

use Cacing69\Cquery\Adapter\AppendNodeCallbackAdapter;
use Cacing69\Cquery\CqueryException;
use Cacing69\Cquery\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;

trait HasDomCrawlerGetter
{
    public function get(): Collection
    {
        $this->validateDefiners();

        // WHERE CHECKING
        $_filtered = null;

        $_bound = null;
        $_boundKey = [];

        // $_boundFirstLevel = [];

        if (count($this->filters) > 0) {
            $_affect = [
                'and' => [],
                'or'  => [],
            ];

            foreach ($this->filters as $key => $filterAdapter) {
                $_data = $this->crawler
                    ->filterXPath($this->getSource()->getXpath())
                    ->filterXPath($filterAdapter->getNodeXpath());

                if ($filterAdapter->getCallMethod() === 'extract') {
                    $_data = $_data->extract($filterAdapter->getCallMethodParameter());
                } elseif ($filterAdapter->getCallMethod() === 'filter') {
                    dd($filterAdapter);
                } elseif ($filterAdapter->getCallMethod() === 'static') {
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

                    if ($filterAdapter->filterExecutor($_value)) {
                        $_affect[$filterAdapter->getOperator()][$key][] = $_key;
                    }
                }
            }

            $_filtered = $this->getResultFilter($_affect);

            if (count($_filtered) === 0) {
                return new Collection([]);
            }
        }

        // PROCESS DOM HERE
        $_hold_data = [];

        foreach ($this->definers as $key => $definer) {
            $_data = null;

            if ($definer->getAdapter()->getCallMethod() === 'extract') {
                $_data = $this
                        ->crawler
                        ->filterXPath($this->getSource()->getXpath())
                        ->filterXPath($definer->getAdapter()->getNodeXpath());

                if ($_filtered !== null) {
                    $_data = $_data->reduce(function (Crawler $node, $i) use ($_filtered) {
                        return in_array($i, $_filtered);
                    });
                }

                $_data = $_data->extract($definer->getAdapter()->getCallMethodParameter());
            } elseif ($definer->getAdapter()->getCallMethod() === 'filter.each') {
                $_data = [];

                $this
                    ->crawler
                    ->filterXPath($this->getSource()->getXpath())
                    ->filterXPath($definer->getAdapter()->getNodeXpath())
                    ->each(function (Crawler $node, $i) use (&$_data, $definer) {
                        $node->filter($definer->getAdapter()->getRef())->each(function (Crawler $_node, $_i) use ($i, &$_data, $definer) {
                            if (is_array($definer->getAdapter()->getCallMethodParameter()) && count($definer->getAdapter()->getCallMethodParameter()) === 1) {
                                $__callParameter = $definer->getAdapter()->getCallMethodParameter()[0];
                                if ($__callParameter === '_text') {
                                    $_data[$i][] = $_node->text();
                                } else {
                                    $_data[$i][] = $_node->attr($__callParameter);
                                }
                            } else {
                                dd('not_supported_yet');
                            }
                        });
                    });

                // if($definer->getAdapter() instanceof AppendNodeCallbackAdapter){
                //     if(is_array($_data[0])) {

                //         $_tmpData = $_data;
                //         // $_data = [];

                //         foreach ($_tmpData as $_keyTmp => $_valueTmp) {
                //             // $_data[] = 3;
                //         }

                //     }  else {

                //     }
                //         // dump($_data);
                //     // if($key == 1) {
                //     //     dd($_data);

                //     // }
                // }
            } elseif ($definer->getAdapter()->getCallMethod() === 'static') {
                if ($key === 0) {
                    throw new CqueryException('you cannot define static on the first definer');
                }

                foreach (range(0, $_bound - 1) as $key => $value) {
                    $_data[] = 'static';
                }
            }

            if ($key === 0) {
                $_bound = count($_data ?? []);
            } else {
                // TODO tambahkan metode ambil data dengan filter->each, walaupun itu akan sedikit lambat, buts its ok, karena hanya untuk kasus tertentu
                // TODO index kolom yang menjadi acuan utama adalah index pertama di definer

                if (count($_data ?? []) < $_bound) {
                    $_data = [];
                    $this
                        ->crawler
                        ->filterXPath($this->getSource()->getXpath())
                        ->each(function (Crawler $node, $i) use (&$_data, $definer) {
                            $_filterNode = $node->filter($definer->getAdapter()->getNode());

                            if ($_filterNode->count() === 0) {
                                $_data[$i] = null;
                            } else {
                                $_filterNode->each(function (Crawler $_node, $_i) use ($i, &$_data) {
                                    $_data[$i] = $_node->text();
                                });
                            }
                        });
                    //     dump(count($_data), $_bound);
                    // throw new CqueryException("error query definer, there are no matching rows each column.");
                    // dump($_data);
                } elseif (count($_data ?? []) > $_bound) {
                    // if(!($definer->getAdapter() instanceof AppendNodeCallbackAdapter)) {
                    throw new CqueryException('error query definer, there are no matching rows each column.');
                    // }
                }
            }

            if ($definer->getAdapter()->getCallback() !== null) {
                $_callback = $definer->getAdapter()->getCallback();
                $_data = array_map(function ($_mapValue) use ($_callback) {
                    return $_callback((string) $_mapValue);
                }, $_data ?? []);
            }

            foreach ($_data as $_key => $_value) {
                if (is_string($_value)) {
                    $_value = trim(preg_replace('/\s+/', ' ', (string) $_value));
                }

                if (is_string($_value) || is_numeric($_value)) {
                    $_value = strlen((string) $_value) > 0 ? $_value : null;
                }

                if ($this->limit !== null) {
                    if ($_key === $this->limit) {
                        break;
                    } else {
                        $_hold_data[$_key][$definer->getAlias()] = $_value;
                    }
                } else {
                    // if alias == tags.*.text
                    if (preg_match('/^\s*([A-Za-z0-9\-\_]+?)\.\*\.([A-Za-z0-9\-\_]+)\s*?/', $definer->getAlias())) {
                        preg_match('/^\s*([A-Za-z0-9\-\_]+?)\.\*\.([A-Za-z0-9\-\_]+)\s*?/', $definer->getAlias(), $_extractAlias);

                        if (empty($_boundKey[$_key])) {
                            $_boundKey[$_key] = count($_value);
                        }
                        // dd($_extractAlias);
                        // dd($_key);
                        // TODO perlu di check, panjang setiap element dari setiap key harus sama, jika tidak sama, ambil ulang data dengan dengan filter->each
                        if (count($_value) < $_boundKey[$_key]) {
                            throw new CqueryException('the number of rows in query result for this object is not the same as the previous query.');
                        }

                        // dd($_extractAlias);

                        if (array_key_exists($_extractAlias[1], $_hold_data[$_key] ?? [])) {
                            $_hold_child = $_hold_data[$_key][$_extractAlias[1]];
                        }

                        // dd($_hold_child, $_key, $_extractAlias[1], $_value);

                        foreach ($_value as $__key => $__value) {
                            $__value = strlen((string) $__value) > 0 ? $__value : null;

                            $_hold_child[$__key][$_extractAlias[2]] = $__value;
                        }

                        $_hold_data[$_key][$_extractAlias[1]] = $_hold_child;
                    } elseif (preg_match('/^\s*([A-Za-z0-9\-\_]+?)\.([A-Za-z0-9\-\_]+)\s*?/', $definer->getAlias())) {
                        preg_match('/^\s*([A-Za-z0-9\-\_]+?)\.([A-Za-z0-9\-\_]+)\s*?/', $definer->getAlias(), $_extractAlias);

                        $_hold_data[$_key][$_extractAlias[1]][$_extractAlias[2]] = $_value;
                    } else {
                        $_hold_data[$_key][$definer->getAlias()] = $_value;
                    }
                }
            }
        }

        $this->isFetched = true;

        $this->results = $_hold_data;

        if ($this->callbackEachItem) {
            $_callbackEachItem = $this->callbackEachItem;

            foreach ($this->results as $_key => $_value) {
                $this->results[$_key] = $_callbackEachItem($_value, $_key);
            }
        }

        if ($this->callbackOnObtainedResults) {
            $_callbackOnObtainedResults = $this->callbackOnObtainedResults;
            $this->results = $_callbackOnObtainedResults($this->results);
        }

        $this->filters = [];
        $this->definers = [];
        $this->source = null;

        if ($this->callbackClientOnEnd) {
            $_callbackClientOnEnd = $this->callbackClientOnEnd;
            $this->client = $_callbackClientOnEnd($this->client);
        }

        return new Collection($this->results);
    }
}

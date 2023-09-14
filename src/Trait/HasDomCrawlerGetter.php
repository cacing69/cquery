<?php

/**
 * This file is part of Cquery.
 *
 * (c) 2023 Ibnul Mutaki <ibnuul@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Cacing69\Cquery\Trait;

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

        if (count($this->filters) > 0) {
            $_affect = [
                'and' => [],
                'or'  => [],
            ];

            foreach ($this->filters as $key => $filterExepression) {
                $_data = $this->crawler
                    ->filterXPath($this->getSource()->getXpath())
                    ->filterXPath($filterExepression->getNodeXpath());

                if ($filterExepression->getCallMethod() === 'extract') {
                    $_data = $_data->extract($filterExepression->getCallMethodParameter());
                } elseif ($filterExepression->getCallMethod() === 'filter') {
                    throw new CqueryException("filter on `{$filterExepression->getCallMethod()}` not yet available");

                } elseif ($filterExepression->getCallMethod() === 'static') {
                    throw new CqueryException("filter on  `{$filterExepression->getCallMethod()}` not yet available");
                } elseif ($filterExepression->getCallMethod() === 'static.extract') {
                    throw new CqueryException(" filter on `{$filterExepression->getCallMethod()}` not yet available");
                }

                if ($filterExepression->getCallback() !== null) {
                    $_callback = $filterExepression->getCallback();

                    $_data = array_map(function ($_mapValue) use ($_callback) {
                        return $_callback($_mapValue);
                    }, $_data);
                }

                foreach ($_data as $_key => $_value) {
                    if (!is_numeric($_value)) {
                        $_value = trim(preg_replace('/\s+/', ' ', (string) $_value));
                    }

                    if ($filterExepression->filterExecutor($_value)) {
                        $_affect[$filterExepression->getOperator()][$key][] = $_key;
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

            if ($definer->getExpression()->getCallMethod() === 'extract') {
                $_data = $this
                        ->crawler
                        ->filterXPath($this->getSource()->getXpath())
                        ->filterXPath($definer->getExpression()->getNodeXpath());

                if ($_filtered !== null) {
                    $_data = $_data->reduce(function (Crawler $node, $i) use ($_filtered) {
                        return in_array($i, $_filtered);
                    });
                }

                $_data = $_data->extract($definer->getExpression()->getCallMethodParameter());
            } elseif ($definer->getExpression()->getCallMethod() === 'filter.each') {
                $_data = [];

                $this
                    ->crawler
                    ->filterXPath($this->getSource()->getXpath())
                    ->filterXPath($definer->getExpression()->getNodeXpath())
                    ->each(function (Crawler $node, $i) use (&$_data, $definer) {
                        $node->filter($definer->getExpression()->getRef())->each(function (Crawler $_node, $_i) use ($i, &$_data, $definer) {
                            if (is_array($definer->getExpression()->getCallMethodParameter()) && count($definer->getExpression()->getCallMethodParameter()) === 1) {

                                $__callParameter = $definer->getExpression()->getCallMethodParameter()[0];

                                $__text = null;

                                if ($__callParameter === '_text') {
                                    $__text = $_node->text();
                                } else {
                                    $__text = $_node->attr($__callParameter);
                                }

                                if($definer->getExpression()->getCallback()) {
                                    $__callbackFilterEach = $definer->getExpression()->getCallback();
                                    $__text = $__callbackFilterEach($__text);
                                }

                                $_data[$i][] = $__text;
                            } else {
                                dd('not_supported_yet');
                            }
                        });
                    });
            } elseif ($definer->getExpression()->getCallMethod() === 'static') {
                if ($key === 0) {
                    throw new CqueryException('you cannot define static on the first definer');
                }

                foreach (range(0, $_bound - 1) as $key => $value) {
                    $_data[] = 'static';
                }
            } elseif ($definer->getExpression()->getCallMethod() === 'static.extract') {
                if ($key === 0) {
                    throw new CqueryException('you cannot used append on the first definer');
                }

                $_static = $this
                    ->crawler
                    ->filterXPath($definer->getExpression()->getNodeXpath())
                    ->extract($definer->getExpression()->getCallMethodParameter());

                if(count($_static) > 1) {
                    throw new CqueryException('you cannot append if there was multiple element exist on your document');
                }

                foreach (range(0, $_bound - 1) as $key => $value) {
                    $_data[] = $_static[0];
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
                            $_filterNode = $node->filter($definer->getExpression()->getNode());

                            if ($_filterNode->count() === 0) {
                                $_data[$i] = null;
                            } else {
                                $_filterNode->each(function (Crawler $_node, $_i) use ($i, &$_data) {
                                    $_data[$i] = $_node->text();
                                });
                            }
                        });

                    // throw new CqueryException("error query definer, there are no matching rows each column.");

                } elseif (count($_data ?? []) > $_bound) {
                    // if(!($definer->getExpression() instanceof AppendNodeCallbackExpression)) {
                    throw new CqueryException('error query definer, there are no matching rows each column.');
                    // }
                }
            }

            if ($definer->getExpression()->getCallback() !== null && !$definer->getExpression()->getIgnoreCallbackOnLoop()) {
                $_callback = $definer->getExpression()->getCallback();
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

                        // TODO perlu di check, panjang setiap element dari setiap key harus sama, jika tidak sama, ambil ulang data dengan dengan filter->each
                        if (count($_value) < $_boundKey[$_key]) {
                            throw new CqueryException('the number of rows in query result for this object is not the same as the previous query.');
                        }

                        if (array_key_exists($_extractAlias[1], $_hold_data[$_key] ?? [])) {
                            $_hold_child = $_hold_data[$_key][$_extractAlias[1]];
                        }

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

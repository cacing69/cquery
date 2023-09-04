<?php

declare(strict_types=1);

namespace Cacing69\Cquery;

use Cacing69\Cquery\Adapter\ClosureCallbackAdapter;
use Cacing69\Cquery\DefinerExtractor;
use Cacing69\Cquery\Trait\HasSourceProperty;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Closure;

abstract class Loader
{
    use HasSourceProperty;
    protected $limit = null;
    protected $clientType = "browser-kit";
    protected $client;

    protected $uri = null;
    protected $isRemote = false;
    protected $isFetched = false;

    protected $definer = [];
    protected $filter = [];
    protected $results = [];

    protected $crawler;

    protected $callbackReady;
    protected $callbackEach;
    protected $callbackArray;
    protected $callbackCompose;

    public function limit(int $limit)
    {
        $this->limit = $limit;
        return $this;
    }
    // abstract protected function fetchCrawler();
    // abstract public function from(string $value);
    public function from(string $value)
    {
        $this->filter = [];
        $this->fetchCrawler();
        $this->source = new Source($value);
        return $this;
    }

    protected function fetchCrawler()
    {
        if($this->isRemote) {
            if($this->clientType === "browser-kit") {
                $this->client = new HttpBrowser(HttpClient::create());

                if($this->callbackReady) {
                    $_callbackReady = $this->callbackReady;

                    $_browser = $this->client;

                    $this->client = $_callbackReady($_browser);
                }

                $this->client->request('GET', $this->uri);

                $this->crawler = new Crawler($this->client->getResponse()->getContent());
            } elseif ($this->clientType === "curl") {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->uri);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $output = curl_exec($ch);

                $this->crawler = new Crawler($output);
                curl_close($ch);
            } else {
                throw new CqueryException("client {$this->clientType} doesnt support");
            }
        }
    }

    public function first()
    {
        return $this
            ->limit(1)
            ->get()
            ->first();
    }

    // abstract public function filter(Filter $filter);
    // abstract public function orFilter(Filter $filter);
    abstract public function get();

    public static function getResultFilter(array $filtered): array
    {
        $result = [
            "and" => [],
            "or" => [],
        ];

        if (array_key_exists("and", $filtered) && count($filtered["and"]) > 0) {
            $result["and"] = array_intersect(...$filtered["and"]);
        }

        if (array_key_exists("or", $filtered) && count($filtered["or"]) > 0) {
            $result["or"] = array_unique(array_merge(...$filtered["or"]));
        }

        $filterResult = array_unique(array_merge($result["and"], $result["or"]));

        sort($filterResult, SORT_NUMERIC);

        return $filterResult;
    }

    public function addDefiner($definer)
    {
        array_push($this->definer, new DefinerExtractor($definer, $this->source));

        $this->checkDefineNotDuplicate();
        return $this;
    }

    // TODO Add test for checking alias definer not duplicate
    protected function checkDefineNotDuplicate() :void
    {
        $_key = [];
        foreach ($this->definer as $definer) {
            if(in_array($definer->getAlias(), $_key)){
                throw new CqueryException("the alias column must not be duplicated, only one unique name is allowed for the definer");
            }
            $_key[] = $definer->getAlias();
        }
    }

    public function define(...$defines)
    {
        $this->validateSource();

        if($this->isFetched) {
            $this->definer = [];
            $this->isFetched = false;
        }

        foreach ($defines as $define) {
            $this->addDefiner($define);
        }

        return $this;
    }

    protected function validateSource()
    {
        if ($this->source === null) {
            throw new CqueryException("no source defined");
        }
    }

    protected function validateDefiners()
    {
        if (count($this->definer) === 0) {
            throw new CqueryException("no definer found");
        }
    }

    // TODO From DOM Manipulator
    public function addFilter($filter, $operator = "and")
    {
        $this->validateSource();

        $adapter = null;

        if($filter->operatorIsCallback()) {
            $adapter = new ClosureCallbackAdapter(null);

            $extractor = new DefinerExtractor($filter->getNode());

            $adapter = $adapter
                ->setNode($extractor->getAdapter()->getNode())
                ->setCallMethod($extractor->getAdapter()->getCallMethod())
                ->setCallMethodParameter($extractor->getAdapter()->getCallMethodParameter());
        } else {
            foreach (RegisterAdapter::load() as $adapter) {
                $_checkSignature = $adapter::getSignature();
                if(isset($_checkSignature)) {
                    if(is_array($_checkSignature)) {
                        $_founded = false;
                        foreach ($_checkSignature as $signature) {
                            if(preg_match($signature, $filter->getNode())) {
                                $adapter = new $adapter($filter->getNode(), $this->source);

                                $_founded = true;
                                break;
                            }
                        }

                        if($_founded) {
                            break;
                        }
                    } else {
                        if(preg_match($_checkSignature, $filter->getNode())) {
                            $adapter = new $adapter($filter->getNode(), $this->source);
                            break;
                        }
                    }
                } else {
                    $adapter = new $adapter($filter->getNode(), $this->source);
                }
            }
        }

        $adapter->setOperator($operator);
        $adapter->setFilter($filter);

        $this->filter[] = $adapter;
    }

    public function setCallbackOnReady(Closure $closure)
    {
        $this->callbackReady = $closure;
        return $this;
    }

    public function setCallbackEach(Closure $closure)
    {
        $this->callbackEach = $closure;
        return $this;
    }

    public function setCallbackArray(Closure $closure)
    {
        $this->callbackArray = $closure;
        return $this;
    }
    public function setCallbackCompose(Closure $closure)
    {
        $this->callbackCompose = $closure;
        return $this;
    }

    // public function setCallbackOnFinishType($callbackType)
    // {
    //     $this->callbackFinishType = $callbackType;
    //     return $this;
    // }
}

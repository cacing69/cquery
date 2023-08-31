<?php

declare(strict_types=1);

namespace Cacing69\Cquery;

use Cacing69\Cquery\Adapter\ClosureCallbackAdapter;
use Cacing69\Cquery\DefinerExtractor;
use Cacing69\Cquery\Trait\HasSourceProperty;
use Doctrine\Common\Collections\ArrayCollection;

abstract class Loader
{
    use HasSourceProperty;
    protected $limit = null;

    protected $uri = null;
    protected $isRemote = false;
    protected $isFetched = false;

    protected $definer = [];
    protected $filter = [];

    public function limit(int $limit)
    {
        $this->limit = $limit;
        return $this;
    }
    abstract protected function fetchCrawler();
    abstract public function from(string $value);

    public function first()
    {
        return $this
            ->limit(1)
            ->get()
            ->first();
    }

    abstract public function filter(Filter $filter);
    abstract public function orFilter(Filter $filter);
    abstract public function get(): ArrayCollection;

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

    protected function addDefiner($definer)
    {
        array_push($this->definer, new DefinerExtractor($definer, $this->source));
        return $this;
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
                $checkSignature = $adapter::getSignature();
                if(isset($checkSignature)) {
                    if(preg_match($checkSignature, $filter->getNode())) {
                        $adapter = new $adapter($filter->getNode(), $this->source);
                        break;
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
}

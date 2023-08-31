<?php

declare(strict_types=1);

namespace Cacing69\Cquery;

use Cacing69\Cquery\Adapter\ClosureCallbackAdapter;
use Cacing69\Cquery\Extractor\DefinerExtractor;
use Doctrine\Common\Collections\ArrayCollection;

abstract class Loader
{
    protected $limit = null;
    protected $selector = null;

    protected $results = null;

    protected $uri = null;
    protected $remote = false;
    protected $source;

    protected $content;

    protected $definer = [];
    protected $filter = [];

    public function limit(int $limit)
    {
        $this->limit = $limit;
        return $this;
    }

    abstract protected function validateSource();
    abstract protected function validateDefiners();
    abstract protected function fetchContent();
    abstract public function define(...$defines);
    abstract public function from(string $value);
    abstract public function setContent(string $value);

    public function first()
    {
        return $this
            ->limit(1)
            ->get()
            ->first();
    }

    abstract public function filter(Filter $filter);
    abstract public function OrFilter(Filter $filter);
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

    // TODO From DOMManipulator
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

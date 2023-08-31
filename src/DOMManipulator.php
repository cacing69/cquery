<?php

declare(strict_types=1);

namespace Cacing69\Cquery;

use Cacing69\Cquery\Trait\HasSourceProperty;
use Cacing69\Cquery\Adapter\ClosureCallbackAdapter;
use Cacing69\Cquery\Extractor\DefinerExtractor;
use Symfony\Component\DomCrawler\Crawler;

class DOMManipulator
{
    use HasSourceProperty;
    private $crawler;
    private $definer = [];
    private $filter = [];

    public function __construct($content, $source)
    {
        $this->crawler = new Crawler($content);
        $this->source = $source;
    }

    public function addFilter($filter, $operator = "and")
    {

        $adapter = null;

        if($filter->operatorIsCallback()) {
            $adapter = new ClosureCallbackAdapter(null, $this->source);

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

    public function getFilter()
    {
        return $this->filter;
    }

    public function getCrawler()
    {
        return $this->crawler;
    }
    public function addDefiner($definer)
    {
        array_push($this->definer, new DefinerExtractor($definer, $this->source));
        return $this;
    }

    public function resetFilter()
    {
        $this->filter = [];
        return $this;
    }

    public function getDefiner()
    {
        return $this->definer;
    }
}

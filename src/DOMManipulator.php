<?php

declare(strict_types=1);

namespace Cacing69\Cquery;

use Cacing69\Cquery\Trait\HasSourceProperty;
use Cacing69\Cquery\Adapter\ClosureCallbackAdapter;
use Cacing69\Cquery\Exception\CqueryException;
use Cacing69\Cquery\Extractor\DefinerExtractor;
use Closure;
use Symfony\Component\DomCrawler\Crawler;

class DOMManipulator
{
    use HasSourceProperty;
    private $crawler;
    private $definer = [];
    private $filter = [];
    private $limit = null;

    public function __construct($content, $source)
    {
        $this->crawler = new Crawler($content);
        $this->source = $source;
    }

    public function addFilter($filter, $operator = "and")
    {

        if($filter[0] instanceof Closure) {
            throw new CqueryException("when used closure, u need to place it on second parameter");
        }

        $adapter = null;

        if($filter[1] instanceof Closure) {
            $adapter = new ClosureCallbackAdapter(null, $this->source);

            if(!array_key_exists(1, $filter)) {
                throw new CqueryException("error processing filter, when used callback filter, please set selector and \Closure");
            }

            $extractor = new DefinerExtractor($filter[0]);

            $adapter = $adapter
                ->setNode($extractor->getAdapter()->getNode())
                ->setCall($extractor->getAdapter()->getCall())
                ->setCallParameter($extractor->getAdapter()->getCallParameter());
        } else {
            foreach (RegisterAdapter::load() as $adapter) {
                $checkSignature = $adapter::getSignature();
                if(isset($checkSignature)) {
                    if(preg_match($checkSignature, $filter[0])) {
                        $adapter = new $adapter($filter[0], $this->source);
                        break;
                    }
                } else {
                    $adapter = new $adapter($filter[0], $this->source);
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

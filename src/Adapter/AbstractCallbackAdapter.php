<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Adapter;

abstract class AbstractCallbackAdapter
{
    protected $node;
    protected $filter;
    protected $callbackAdapter;

    public function getCallbackAdapter(){
        return $this->callbackAdapter;
    }

    public function getNode(): string
    {
        return $this->node;
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;

        return $this;
    }

    public function getFilter()
    {
        return $this->filter;
    }
}

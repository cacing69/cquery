<?php
declare(strict_types=1);

namespace Cacing69\Cquery\Trait;
use Closure;

trait HasFilterProperty {
    protected $filter;
    public function setFilter($filter)
    {
        if(!($filter[0] instanceof Closure)) {
            $this->filter = $filter;
            $this->clause = strtolower(trim($filter[1]));
            $this->criteria = $filter[2];
        }

        return $this;
    }

    public function getFilter()
    {
        return $this->filter;
    }
}

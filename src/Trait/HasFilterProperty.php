<?php
declare(strict_types=1);

namespace Cacing69\Cquery\Trait;

trait HasFilterProperty {
    private $filter;

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->clause = strtolower(trim($filter[1]));
        $this->criteria = $filter[2];

        return $this;
    }

    public function getFilter()
    {
        return $this->filter;
    }
}

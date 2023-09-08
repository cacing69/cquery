<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Trait;

trait HasFiltersProperty
{
    protected $filters = [];

    public function setFilters($filters)
    {
        $this->filters = $filters;

        return $this;
    }

    public function getFilters()
    {
        return $this->filters;
    }
}

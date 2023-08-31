<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Trait;

trait HasCriteriaProperty
{
    private $criteria;

    public function getCriteria()
    {
        return $this->criteria;
    }
}

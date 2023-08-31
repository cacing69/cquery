<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Trait;

trait HasAliasProperty
{
    private $alias;

    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    public function getAlias()
    {
        return $this->alias;
    }
}

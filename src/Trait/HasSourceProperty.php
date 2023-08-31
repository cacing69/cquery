<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Trait;

trait HasSourceProperty
{
    protected $source;

    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    public function getSource()
    {
        return $this->source;
    }
}

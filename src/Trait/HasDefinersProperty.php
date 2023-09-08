<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Trait;

trait HasDefinersProperty
{
    protected $definers = [];

    public function setDefiners($definers)
    {
        $this->definers = $definers;

        return $this;
    }

    public function getDefiners()
    {
        return $this->definers;
    }
}

<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Trait;

trait HasCallbackProperty
{
    protected $callback;

    public function setCallback($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    public function getCallback()
    {
        return $this->callback;
    }
}

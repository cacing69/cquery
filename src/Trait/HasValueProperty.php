<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Trait;

trait HasValueProperty
{
    private $value;

    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }
}

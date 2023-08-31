<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Trait;

trait HasOptionsProperty
{
    protected $options;

    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }
}

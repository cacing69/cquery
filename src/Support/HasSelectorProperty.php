<?php

namespace Cacing69\Cquery\Support;

trait HasSelectorProperty {
    private $selector;

    public function setSelector($selector)
    {
        $this->selector = $selector;

        return $this;
    }

    public function getSelector()
    {
        return $this->selector;
    }
}

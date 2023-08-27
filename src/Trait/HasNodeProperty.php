<?php
declare(strict_types=1);

namespace Cacing69\Cquery\Trait;

trait HasNodeProperty {
    protected $node;

    public function setNode($node)
    {
        $this->node = $node;

        return $this;
    }

    public function getNode()
    {
        return $this->node;
    }
}

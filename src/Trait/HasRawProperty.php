<?php
declare(strict_types=1);

namespace Cacing69\Cquery\Trait;

trait HasRawProperty {
    protected $raw;

    public function setRaw($raw)
    {
        $this->raw = $raw;

        return $this;
    }

    public function getRaw()
    {
        return $this->raw;
    }
}

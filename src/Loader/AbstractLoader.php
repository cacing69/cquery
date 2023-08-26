<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Loader;

abstract class AbstractLoader
{
    private $limit;
    public function limit(int $limit)
    {
        $this->limit = $limit;
        return $this;
    }
}

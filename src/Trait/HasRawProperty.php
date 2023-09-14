<?php

/**
 * This file is part of Cquery.
 *
 * (c) 2023 Ibnul Mutaki <ibnuul@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cacing69\Cquery\Trait;

trait HasRawProperty
{
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

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

trait HasSourceProperty
{
    protected $source;

    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Used to get source cquery.
     *
     * @return \Cacing69\Cquery\Source
     */
    public function getSource()
    {
        return $this->source;
    }
}

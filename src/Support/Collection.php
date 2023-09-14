<?php

/**
 * This file is part of Cquery.
 *
 * (c) 2023 Ibnul Mutaki <ibnuul@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Cacing69\Cquery\Support;

use Doctrine\Common\Collections\ArrayCollection;

class Collection extends ArrayCollection
{
    /**
     * Get the values of a given key.
     *
     * @param string|int|array<array-key, string> $value
     * @param string|null                         $key
     *
     * @return static<array-key, mixed>
     */
    public function pluck($value, $key = null)
    {
        $_pluck = [];
        foreach ($this->getValues() as $_value) {
            $_pluck[] = $_value[$value];
        }

        return new ArrayCollection($_pluck);
    }
}

<?php

/**
 * This file is part of Cquery.
 *
 * (c) 2023 Ibnul Mutaki <ibnuul@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Cacing69\Cquery;

abstract class AbstractCqueryWriter
{
    protected $data;
    protected $only;
    protected $exclude;

    abstract public function save();

    public function only(string ...$only)
    {
        $this->only = $only;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function exclude(string ...$exclude)
    {
        $this->exclude = $exclude;
    }
}

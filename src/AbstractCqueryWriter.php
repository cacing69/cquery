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
use Cacing69\Cquery\Support\Collection;

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

    public function getData()
    {
        if($this->only != null){
            $_data = [];
            foreach ($this->data as $key => $value) {
                // foreach ($this->only as $only) {
                foreach ($value as $_keyValue => $value) {
                    if(in_array($_keyValue, $this->only)) {
                        $data[$key][$_keyValue] = $value;
                    }
                }
                // }
            }

            return new Collection($data);
        } else {
            return $this->data;
        }

    }
}

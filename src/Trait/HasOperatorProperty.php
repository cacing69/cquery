<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Trait;

trait HasOperatorProperty
{
    private $operator;

    public function setOperator($operator)
    {
        $this->operator = $operator;

        return $this;
    }

    public function getOperator()
    {
        return $this->operator;
    }
}

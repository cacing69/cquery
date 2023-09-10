<?php

declare(strict_types=1);

namespace Cacing69\Cquery;

use Cacing69\Cquery\Trait\HasNodeProperty;
use Cacing69\Cquery\Trait\HasOperatorProperty;
use Closure;

class Filter
{
    use HasNodeProperty;
    use HasOperatorProperty;

    public function __construct($node, $operator, $value = null)
    {
        $this->node = $node;

        $this->operator = $operator;

        if (!($operator instanceof Closure)) {
            $this->value = $value;
        }
    }

    public function operatorIsCallback()
    {
        return $this->operator instanceof Closure;
    }

    private $value;

    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public static function raw($raw)
    {
        return new Filter('', '');
    }
}

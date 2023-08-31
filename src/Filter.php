<?php

declare(strict_types=1);

namespace Cacing69\Cquery;

use Closure;
use Cacing69\Cquery\Trait\HasNodeProperty;
use Cacing69\Cquery\Trait\HasValueProperty;
use Cacing69\Cquery\Trait\HasOperatorProperty;

class Filter
{
    use HasNodeProperty;
    use HasOperatorProperty;
    use HasValueProperty;

    public function __construct($node, $operator, $value = null)
    {
        $this->node = $node;

        $this->operator = $operator;

        if(!($operator instanceof Closure)) {
            $this->value = $value;
        }
    }

    public function operatorIsCallback() {
        return $this->operator instanceof Closure;
    }
}
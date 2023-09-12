<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Expression;

use Cacing69\Cquery\CallbackExpression;
use Closure;

class ClosureCallbackExpression extends CallbackExpression
{
    public function __construct(Closure $rawDefiner = null)
    {
        $this->raw = $rawDefiner;

        $this->callMethod = 'extract';
        $this->callMethodParameter = ['_text'];

        if (!empty($rawDefiner)) {
            $this->callback = function ($value) use ($rawDefiner) {
                return $rawDefiner($value);
            };
        }
    }
}

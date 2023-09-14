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

namespace Cacing69\Cquery\Expression;

use Cacing69\Cquery\AbstractCallbackExpression;
use Closure;

class ClosureCallbackExpression extends AbstractCallbackExpression
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

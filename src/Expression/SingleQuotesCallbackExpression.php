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

class SingleQuotesCallbackExpression extends AbstractCallbackExpression
{
    protected static $signature = '/^\'(.+)\'$/is';

    public static function getSignature()
    {
        return self::$signature;
    }

    public function __construct(string $raw)
    {
        $this->raw = $raw;

        $this->node = null;

        $this->callback = function ($value) use ($raw) {
            preg_match(self::$signature, $raw, $_extract);

            return $_extract[1];
        };

        $this->callMethod = 'static';
    }
}

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

use Cacing69\Cquery\CallbackExpression;

class StringCallbackExpression extends CallbackExpression
{
    protected static $signature = '/^\s*str\(\s*(.*?)\s*\)\s*$/is';

    public static function getSignature()
    {
        return self::$signature;
    }

    public function __construct(string $raw)
    {
        $this->raw = $raw;

        $this->callback = function (string $value) {
            return strval($value);
        };

        // check if function is nested
        if (preg_match('/^\s?str\(\s?([a-z0-9_]*\(.+?\))\s?\)$/', $raw)) {
            preg_match('/^\s?str\(\s?([a-z0-9_]*\(.+?\))\s?\)$/', $raw, $extract);

            $extractChild = $this->extractChild($extract[1]);
            $_childCallback = $extractChild->getExpression()->getCallback();

            if ($_childCallback) {
                $this->callback = function (string $value) use ($_childCallback) {
                    return strval((string) $_childCallback($value));
                };
            }
        } else {
            preg_match(self::$signature, $raw, $node);

            $this->node = $node[1];

            $this->callMethod = 'extract';
            $this->callMethodParameter = ['_text'];
        }
    }
}

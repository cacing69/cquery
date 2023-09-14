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
use Cacing69\Cquery\DefinerExtractor;

class AppendCallbackExpression extends CallbackExpression
{
    protected static $signature = '/^\s*append\(\s*(.+)\s*\)\s*(as)?\s*\w*((\.\*)?\.\w+)?\s*,?$/i';

    public static function getSignature()
    {
        return self::$signature;
    }

    public function __construct(string $raw)
    {
        $this->raw = $raw;

        preg_match(self::$signature, $raw, $extract);

        $extractRefNode = new DefinerExtractor($extract[1]);

        if (preg_match('/^\s*append\(\s*([a-z0-9_]*\(.+\))\s*\)$/', $raw, $extract)) {

            $extractChild = $this->extractChild($extract[1]);
            $_childCallback = $extractChild->getExpression()->getCallback();

            if ($_childCallback) {
                $this->callback = function ($value) use ($_childCallback) {
                    return $_childCallback($value);
                };
            }
        }

        $this->ref = $extractRefNode->getExpression()->getNode();

        $this->node = $extractRefNode->getExpression()->getNode();

        $this->callMethod = 'static.extract';
        $this->callMethodParameter = $extractRefNode->getExpression()->getCallMethodParameter();
    }
}

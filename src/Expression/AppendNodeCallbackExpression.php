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
use Cacing69\Cquery\ParserExpressionInterface;

class AppendNodeCallbackExpression extends CallbackExpression implements ParserExpressionInterface
{
    protected static $parserIdentifier = 'append_node';
    protected static $parserArguments = ['querySelector', 'children'];
    protected static $signature = '/^\s*?append_node\(\s*(.+?),\s*(.+)\s*\)\s*(as)?\s*\w*((\.\*)?\.\w+)?\s*,?$/i';

    public static function getSignature()
    {
        return self::$signature;
    }

    public static function getParserIdentifier()
    {
        return self::$parserIdentifier;
    }

    public static function getCountParserArguments()
    {
        return count(self::$parserArguments);
    }

    public function __construct(string $raw)
    {
        // TODO Check if append node doesnt not support for nested
        $this->raw = $raw;

        preg_match(self::$signature, $raw, $extract);

        $extractRefNode = new DefinerExtractor($extract[2]);

        $this->ref = $extractRefNode->getExpression()->getNode();

        $this->node = $extract[1];

        $this->callMethod = 'filter.each';
        $this->callMethodParameter = $extractRefNode->getExpression()->getCallMethodParameter();
    }
}

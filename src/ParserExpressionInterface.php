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

interface ParserExpressionInterface
{
    public static function getParserIdentifier();

    public static function getCountParserArguments();
}

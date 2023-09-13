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

use Cacing69\Cquery\Expression\AppendNodeCallbackExpression;
use Cacing69\Cquery\Expression\AttributeCallbackExpression;
use Cacing69\Cquery\Expression\DefaultCallbackExpression;
use Cacing69\Cquery\Expression\FloatCallbackExpression;
use Cacing69\Cquery\Expression\IntegerCallbackExpression;
use Cacing69\Cquery\Expression\LengthCallbackExpression;
use Cacing69\Cquery\Expression\LowerCallbackExpression;
use Cacing69\Cquery\Expression\ReplaceCallbackExpression;
use Cacing69\Cquery\Expression\ReverseCallbackExpression;
use Cacing69\Cquery\Expression\SingleQuotesCallbackExpression;
use Cacing69\Cquery\Expression\StringCallbackExpression;
use Cacing69\Cquery\Expression\UpperCallbackExpression;

/**
 * RegisterExpression used to register available Expressions, this Expression is utilized during create definer and filter.
 *
 * @author Ibnul Mutaki <ibnuu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class RegisterExpression
{
    public static function load()
    {
        return [
            AttributeCallbackExpression::class,
            LengthCallbackExpression::class,
            ReverseCallbackExpression::class,
            UpperCallbackExpression::class,
            AppendNodeCallbackExpression::class,
            LowerCallbackExpression::class,
            LowerCallbackExpression::class,
            ReplaceCallbackExpression::class,
            SingleQuotesCallbackExpression::class,
            IntegerCallbackExpression::class,
            StringCallbackExpression::class,
            FloatCallbackExpression::class,

            /**
             *  Make sure that DefaultCallbackExpression is always at the bottom.
             */
            DefaultCallbackExpression::class,
        ];
    }
}

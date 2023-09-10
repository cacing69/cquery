<?php

namespace Cacing69\Cquery;

use Cacing69\Cquery\Adapter\AppendNodeCallbackAdapter;
use Cacing69\Cquery\Adapter\AttributeCallbackAdapter;
use Cacing69\Cquery\Adapter\DefaultCallbackAdapter;
use Cacing69\Cquery\Adapter\FloatCallbackAdapter;
use Cacing69\Cquery\Adapter\IntegerCallbackAdapter;
use Cacing69\Cquery\Adapter\LengthCallbackAdapter;
use Cacing69\Cquery\Adapter\LowerCallbackAdapter;
use Cacing69\Cquery\Adapter\ReplaceCallbackAdapter;
use Cacing69\Cquery\Adapter\ReverseCallbackAdapter;
use Cacing69\Cquery\Adapter\SingleQuotesCallbackAdapter;
use Cacing69\Cquery\Adapter\StringCallbackAdapter;
use Cacing69\Cquery\Adapter\UpperCallbackAdapter;

/**
 * RegisterAdapter used to register available adapters, this adapter is utilized during create definer and filter.
 *
 * @author Ibnul Mutaki <ibnuu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class RegisterAdapter
{
    public static function load()
    {
        return [
            AttributeCallbackAdapter::class,
            LengthCallbackAdapter::class,
            ReverseCallbackAdapter::class,
            UpperCallbackAdapter::class,
            AppendNodeCallbackAdapter::class,
            LowerCallbackAdapter::class,
            LowerCallbackAdapter::class,
            ReplaceCallbackAdapter::class,
            SingleQuotesCallbackAdapter::class,
            IntegerCallbackAdapter::class,
            StringCallbackAdapter::class,
            FloatCallbackAdapter::class,

            /**
             *  Make sure that DefaultCallbackAdapter is always at the bottom.
             */
            DefaultCallbackAdapter::class,
        ];
    }
}

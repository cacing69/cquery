<?php

namespace Cacing69\Cquery;

use Cacing69\Cquery\Adapter\AttributeCallbackAdapter;
use Cacing69\Cquery\Adapter\DefaultCallbackAdapter;
use Cacing69\Cquery\Adapter\AppendNodeCallbackAdapter;
use Cacing69\Cquery\Adapter\LengthCallbackAdapter;
use Cacing69\Cquery\Adapter\LowerCallbackAdapter;
use Cacing69\Cquery\Adapter\ReplaceCallbackAdapter;
use Cacing69\Cquery\Adapter\ReverseCallbackAdapter;
use Cacing69\Cquery\Adapter\UpperCallbackAdapter;

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

            /**
             *  Make sure that DefaultCallbackAdapter is always at the bottom
             */
            DefaultCallbackAdapter::class
        ];
    }
}

<?php

namespace Cacing69\Cquery;

use Cacing69\Cquery\Adapter\AttributeCallbackAdapter;
use Cacing69\Cquery\Adapter\DefaultCallbackAdapter;
use Cacing69\Cquery\Adapter\GetNodeCallbackAdapter;
use Cacing69\Cquery\Adapter\LengthCallbackAdapter;
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
            GetNodeCallbackAdapter::class,
            DefaultCallbackAdapter::class
        ];
    }
}

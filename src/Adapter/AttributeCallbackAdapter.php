<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Adapter;

use Cacing69\Cquery\CallbackAdapter;
use Cacing69\Cquery\Support\RegExp;

class AttributeCallbackAdapter extends CallbackAdapter
{
    protected static $signature = RegExp::IS_ATTRIBUTE;

    public static function getSignature()
    {
        return self::$signature;
    }

    public function __construct(string $raw)
    {
        $this->raw = $raw;

        preg_match(RegExp::IS_ATTRIBUTE, $raw, $extractParams);

        $this->ref = $extractParams[1];
        $this->node = $extractParams[2];

        $this->callMethod = "extract";
        $this->callMethodParameter = [$this->ref];
    }
}

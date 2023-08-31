<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Adapter;

use Cacing69\Cquery\CallbackAdapter;

class ReplaceCallbackAdapter extends CallbackAdapter
{
    protected static $signature = '/^\s*replace\(\s*(.*?)\s*,\s*(.*?),\s*(.*?)\)\s*/';

    public static function getSignature()
    {
        return self::$signature;
    }

    public function __construct(string $raw)
    {
        $this->raw = $raw;

        $this->callMethod = "extract";
        $this->callMethodParameter = [$this->ref];
    }
}

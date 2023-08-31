<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Adapter;

use Cacing69\Cquery\CallbackAdapter;
use Cacing69\Cquery\Extractor\SourceExtractor;

class DefaultCallbackAdapter extends CallbackAdapter
{
    protected static $signature = null;

    public static function getSignature()
    {
        return self::$signature;
    }
    public function __construct(string $raw, SourceExtractor $source = null)
    {
        $this->raw = $raw;

        $this->node = $raw;
        $this->callMethod = "extract";
        $this->callMethodParameter = ["_text"];
    }
}

<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Adapter;

use Cacing69\Cquery\Extractor\SourceExtractor;
use Cacing69\Cquery\Support\RegExp;

class ReplaceCallbackAdapter extends CallbackAdapter
{
    protected static $signature = '/^\s*replace\(\s*(.*?)\s*,\s*(.*?),\s*(.*?)\)\s*/';

    public static function getSignature()
    {
        return self::$signature;
    }

    public function __construct(string $raw, SourceExtractor $source = null)
    {
        $this->raw = $raw;

        preg_match(RegExp::EXTRACT_FIRST_PARAM_ATTRIBUTE, $raw, $attr);
        preg_match(RegExp::EXTRACT_SECOND_PARAM_ATTRIBUTE, $raw, $node);

        $this->ref = $attr[1];
        $this->node = $node[1];

        $this->callMethod = "extract";
        $this->callMethodParameter = [$this->ref];
    }
}

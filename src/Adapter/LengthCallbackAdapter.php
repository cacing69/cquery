<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Adapter;

use Cacing69\Cquery\Extractor\DefinerExtractor;
use Cacing69\Cquery\Extractor\SourceExtractor;
use Cacing69\Cquery\Support\RegExp;

class LengthCallbackAdapter extends CallbackAdapter
{
    protected static $signature = RegExp::IS_LENGTH;

    public static function getSignature()
    {
        return self::$signature;
    }
    public function __construct(string $raw, SourceExtractor $source = null)
    {
        $this->raw = $raw;

        // check if function is nested
        if(preg_match('/^\s?length\(\s?([a-z0-9_]*\(.+?\))\s?\)$/', $raw)) {
            preg_match('/^\s?length\(\s?([a-z0-9_]*\(.+?\))\s?\)$/', $raw, $extract);

            $extractChild = $this->extractChild($extract[1]);
            $_childCallback = $extractChild->getAdapter()->getCallback();

        } else {
            preg_match(RegExp::EXTRACT_FIRST_PARAM_LENGTH, $raw, $node);
            $this->node = $node[1];

            $this->callMethod = "extract";
            $this->callMethodParameter = ["_text"];
        }

        $this->callback = function (string $value) use ($_childCallback) {
            if(empty($_childCallback)) {
                return strlen((string) $value);
            } else {
                return strlen((string) $_childCallback($value));
            }
        };
    }
}

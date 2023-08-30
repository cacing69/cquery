<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Adapter;

use Cacing69\Cquery\Extractor\DefinerExtractor;
use Cacing69\Cquery\Extractor\SourceExtractor;
use Cacing69\Cquery\Support\RegExp;

class UpperCallbackAdapter extends CallbackAdapter
{
    protected static $signature = RegExp::IS_UPPER;

    public static function getSignature()
    {
        return self::$signature;
    }
    public function __construct(string $raw, SourceExtractor $source = null)
    {
        $this->raw = $raw;
        // check if function is nested
        if (preg_match('/^\s?upper\(\s?([a-z0-9_]*\(.+?\))\s?\)$/', $raw)) {
            preg_match('/^\s?upper\(\s?([a-z0-9_]*\(.+?\))\s?\)$/', $raw, $extract);

            $extractor = new DefinerExtractor($extract[1]);
            $this->node = $extractor->getAdapter()->getNode();

        } else {
            preg_match(RegExp::EXTRACT_FIRST_PARAM_UPPER, $raw, $node);
            $this->node = $node[1];

            $this->call = "extract";
            $this->callParameter = ["_text"];

            $this->afterCall = function (string $value) {
                return strtoupper($value);
            };
        }
    }
}

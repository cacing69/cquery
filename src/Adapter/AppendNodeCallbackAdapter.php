<?php
declare(strict_types = 1);
namespace Cacing69\Cquery\Adapter;

use Cacing69\Cquery\Extractor\SourceExtractor;

class AppendNodeCallbackAdapter extends CallbackAdapter
{
    protected static $signature = '/^\s*?append_node\(\s?(.+?),\s?(.+?)\s?\)\s*?$/';
    public static function getSignature() {
        return self::$signature;
    }

    public function __construct(string $raw, SourceExtractor $source = null)
    {
        $this->raw = $raw;

        preg_match('/^\s*?append_node\(\s?(.+?),\s?(.+?)\s?\)\s*?$/', $raw, $extract);

        $this->ref = $extract[2];
        $this->node = $extract[1];

        $this->call = "filter.extract";
        $this->callParameter = ["_text"];
    }
}

<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Adapter;

use Cacing69\Cquery\CallbackAdapter;
use Cacing69\Cquery\DefinerExtractor;

class AppendNodeCallbackAdapter extends CallbackAdapter
{
    protected static $signature = '/^\s*?append_node\(\s*(.+?),\s*(.+?)\s*\)\s*$/';
    public static function getSignature()
    {
        return self::$signature;
    }

    public function __construct(string $raw)
    {
        $this->raw = $raw;

        preg_match('/^\s*?append_node\(\s*(.+?),\s*(.+?)\s*\)\s*$/', $raw, $extract);

        $extractRefNode = new DefinerExtractor($extract[2]);

        $this->ref = $extractRefNode->getDefiner();
        $this->node = $extract[1];

        $this->callMethod = "filter.each";
        $this->callMethodParameter = $extractRefNode->getAdapter()->getCallMethodParameter();
    }
}

<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Adapter;

use Cacing69\Cquery\CallbackAdapter;
use Cacing69\Cquery\DefinerExtractor;
use Cacing69\Cquery\ParserAdapterInterface;

class AppendNodeCallbackAdapter extends CallbackAdapter implements ParserAdapterInterface
{
    protected static $parserIdentifier = 'append_node';
    protected static $parserArguments = ['querySelector', 'children'];
    protected static $signature = '/^\s*?append_node\(\s*(.+?),\s*(.+?)\s*\)\s*(as)?\s*\w*\s*,?$/i';

    public static function getSignature()
    {
        return self::$signature;
    }

    public static function getParserIdentifier()
    {
        return self::$parserIdentifier;
    }

    public static function getCountParserArguments()
    {
        return count(self::$parserArguments);
    }

    public function __construct(string $raw)
    {
        // TODO Check if append node doesnt not support for nested
        $this->raw = $raw;

        preg_match(self::$signature, $raw, $extract);

        $extractRefNode = new DefinerExtractor($extract[2]);

        $this->ref = $extractRefNode->getDefiner();
        $this->node = $extract[1];

        $this->callMethod = 'filter.each';
        $this->callMethodParameter = $extractRefNode->getAdapter()->getCallMethodParameter();
    }
}

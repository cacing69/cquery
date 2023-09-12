<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Expression;

use Cacing69\Cquery\CallbackExpression;
use Cacing69\Cquery\ParserExpressionInterface;
use Cacing69\Cquery\Support\RegExp;

class AttributeCallbackExpression extends CallbackExpression implements ParserExpressionInterface
{
    protected static $parserIdentifier = 'attr';
    protected static $parserArguments = ['attr', 'querySelector'];
    protected static $signature = RegExp::IS_ATTRIBUTE;

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
        return count(self::$parserArguments ?? []);
    }

    public function __construct(string $raw)
    {
        // TODO Check if attribute expression doesnt support for nested
        $this->raw = $raw;

        preg_match(self::$signature, $raw, $extractParams);

        $this->ref = $extractParams[1];
        $this->node = $extractParams[2];

        $this->callMethod = 'extract';
        $this->callMethodParameter = [$this->ref];
    }
}

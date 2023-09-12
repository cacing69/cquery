<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Expression;

use Cacing69\Cquery\CallbackExpression;

class SingleQuotesCallbackExpression extends CallbackExpression
{
    protected static $signature = '/^\'(.*?)\'$/is';

    public static function getSignature()
    {
        return self::$signature;
    }

    public function __construct(string $raw)
    {
        $this->raw = $raw;

        $this->node = null;

        $this->callback = function (string $value) use ($raw) {
            preg_match(self::$signature, $raw, $_extract);

            return $_extract[1];
        };

        $this->callMethod = 'static';
    }
}

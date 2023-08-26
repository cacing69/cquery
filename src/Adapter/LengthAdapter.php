<?php
declare(strict_types = 1);
namespace Cacing69\Cquery\Adapter;

class LengthAdapter extends AbstractCallbackAdapter
{
    public function __construct(string $raw)
    {
        preg_match('/^length\(\s?(.*?)\s?\)$/is', $raw, $value);
        $this->node = $value[1];

        $this->callbackAdapter = function ($e) {
            return strlen($e);
        };
    }
}

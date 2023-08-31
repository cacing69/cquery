<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Adapter;

use Cacing69\Cquery\Extractor\SourceExtractor;
use Closure;

class ClosureCallbackAdapter extends CallbackAdapter
{
    public function __construct(Closure $rawDefiner = null, SourceExtractor $source = null)
    {
        $this->raw = $rawDefiner;

        $this->call = "extract";
        $this->callParameter = ["_text"];

        if(!empty($rawDefiner)) {
            $this->afterCall = function ($value) use ($rawDefiner) {
                return $rawDefiner($value);
            };
        }
    }
}

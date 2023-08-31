<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Adapter;

use Cacing69\Cquery\CallbackAdapter;
use Cacing69\Cquery\Extractor\SourceExtractor;
use Closure;

class ClosureCallbackAdapter extends CallbackAdapter
{
    public function __construct(Closure $rawDefiner = null, SourceExtractor $source = null)
    {
        $this->raw = $rawDefiner;

        $this->callMethod = "extract";
        $this->callMethodParameter = ["_text"];

        if(!empty($rawDefiner)) {
            $this->callback = function ($value) use ($rawDefiner) {
                return $rawDefiner($value);
            };
        }
    }
}

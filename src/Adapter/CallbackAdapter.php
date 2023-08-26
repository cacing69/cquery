<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Adapter;
use Cacing69\Cquery\Trait\HasOperatorProperty;
use Cacing69\Cquery\Trait\HasExtractMethod;
use Cacing69\Cquery\Trait\HasClauseProperty;
use Cacing69\Cquery\Extractor\ClauseExtractorV2;
use Cacing69\Cquery\Trait\HasSelectorProperty;
use Cacing69\Cquery\Trait\HasFilterProperty;

abstract class CallbackAdapter
{
    use HasOperatorProperty;
    use HasFilterProperty;
    use HasSelectorProperty;
    use HasClauseProperty;
    use HasExtractMethod;

    protected $raw;
    protected $node;
    protected $ref;
    protected $callback;

    public function getNode(): string
    {
        return $this->node;
    }

    public function getCallback()
    {
        return $this->callback;
    }
}

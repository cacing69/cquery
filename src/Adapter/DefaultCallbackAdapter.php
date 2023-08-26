<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Adapter;

use Cacing69\Cquery\Extractor\SourceExtractor;
use Symfony\Component\DomCrawler\Crawler;

class DefaultCallbackAdapter extends CallbackAdapter
{
    public function __construct(string $raw, SourceExtractor $source = null)
    {
        $this->raw = $raw;

        $this->node = $raw;

        $this->callback = function (Crawler $node) {
            return $node->text();
        };
    }
}

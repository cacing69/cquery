<?php
declare(strict_types = 1);
namespace Cacing69\Cquery\Adapter;

use Cacing69\Cquery\Extractor\SourceExtractor;
use Closure;
use Symfony\Component\DomCrawler\Crawler;

class ClosureCallbackAdapter extends CallbackAdapter
{
    public function __construct(Closure $raw, SourceExtractor $source = null)
    {
        $this->raw = $raw;

        $this->callback = function (Crawler $crawlNode) use ($raw) {
            return $raw($crawlNode);
        };
    }
}

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

        $this->call = "extract";
        $this->callParameter = ["_text"];

        $this->afterCall = function (string $value) use ($raw) {
            return $raw($value);
        };

        $this->callback = function (Crawler $crawlNode) use ($raw) {
            if($crawlNode instanceof Crawler) {
                return $raw($crawlNode);
            }
        };
    }
}

<?php
declare(strict_types = 1);
namespace Cacing69\Cquery\Adapter;

use Cacing69\Cquery\Extractor\SourceExtractor;
use Cacing69\Cquery\Trait\HasFilterProperty;
use Symfony\Component\DomCrawler\Crawler;

class LengthCallbackAdapter extends CallbackAdapter
{
    // use HasFilterProperty;
    public function __construct(string $raw, SourceExtractor $source = null)
    {
        $this->raw = $raw;
        preg_match('/^length\(\s?(.*?)\s?\)$/is', $raw, $node);
        $this->node = $node[1];

        $this->callback = function (Crawler $node) {
            return strlen($node->text());
        };
    }
}

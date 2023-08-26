<?php
declare(strict_types = 1);
namespace Cacing69\Cquery\Adapter;

use Cacing69\Cquery\Extractor\SourceExtractor;
use Cacing69\Cquery\Trait\HasFilterProperty;
use Symfony\Component\DomCrawler\Crawler;

class AttributeCallbackAdapter extends CallbackAdapter
{
    // use HasFilterProperty;
    public function __construct(string $raw, SourceExtractor $source = null)
    {
        $this->raw = $raw;

        preg_match('/^attr\(\s*?(.*?),\s*?.*\)$/is', $raw, $attr);
        preg_match('/^attr\(\s*?.*\s?,\s*?(.*?)\)$/is', $raw, $node);

        $this->ref = $attr[1];
        $this->node = $node[1];

        $ref = $this->ref;
        // dd($ref, $node);
        $this->callback = function (Crawler $crawlNode) use ($ref) {
            return $crawlNode->attr($ref);
        };
    }
}

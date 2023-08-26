<?php
declare(strict_types = 1);
namespace Cacing69\Cquery\Adapter;

use Cacing69\Cquery\Extractor\SourceExtractor;
use Cacing69\Cquery\Support\CqueryRegex;
use Cacing69\Cquery\Trait\HasFilterProperty;
use Symfony\Component\DomCrawler\Crawler;

class LengthCallbackAdapter extends CallbackAdapter
{
    public function __construct(string $raw, SourceExtractor $source = null)
    {
        $this->raw = $raw;
        preg_match(CqueryRegex::EXTRACT_FIRST_PARAM_LENGTH, $raw, $node);
        $this->node = $node[1];

        $this->callback = function (Crawler $node) {
            return strlen($node->text());
        };
    }
}

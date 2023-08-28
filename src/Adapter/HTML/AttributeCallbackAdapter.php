<?php
declare(strict_types = 1);
namespace Cacing69\Cquery\Adapter\HTML;

use Cacing69\Cquery\Extractor\SourceExtractor;
use Cacing69\Cquery\Support\CqueryRegex;
use Symfony\Component\DomCrawler\Crawler;

class AttributeCallbackAdapter extends CallbackAdapter
{
    public function __construct(string $raw, SourceExtractor $source = null)
    {
        $this->raw = $raw;

        preg_match(CqueryRegex::EXTRACT_FIRST_PARAM_ATTRIBUTE, $raw, $attr);
        preg_match(CqueryRegex::EXTRACT_SECOND_PARAM_ATTRIBUTE, $raw, $node);

        $this->ref = $attr[1];
        $this->node = $node[1];

        $this->call = "extract";
        $this->callParameter = [$this->ref];

        $ref = $this->ref;
        $this->callback = function (Crawler $crawlNode) use ($ref) {
            return $crawlNode->attr($ref);
        };
    }
}

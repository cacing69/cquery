<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Adapter\HTML;

use Cacing69\Cquery\Extractor\DefinerExtractor;
use Cacing69\Cquery\Extractor\SourceExtractor;
use Cacing69\Cquery\Support\CqueryRegex;
use Symfony\Component\DomCrawler\Crawler;

class UpperCallbackAdapter extends CallbackAdapter
{
    public function __construct(string $raw, SourceExtractor $source = null)
    {
        $this->raw = $raw;
        // check if function is nested
        if (preg_match('/^\s?upper\(\s?([a-z0-9_]*\(.+?\))\s?\)$/', $raw)) {
            preg_match('/^\s?upper\(\s?([a-z0-9_]*\(.+?\))\s?\)$/', $raw, $extract);

            $extractor = new DefinerExtractor($extract[1]);
            $this->node = $extractor->getAdapter()->getNode();
            $callbackFromExtractor = $extractor->getAdapter()->getCallback();

            $this->callback = function (Crawler $node) use ($callbackFromExtractor) {
                return strtoupper($callbackFromExtractor($node));
            };
        } else {
            preg_match(CqueryRegex::EXTRACT_FIRST_PARAM_UPPER, $raw, $node);
            $this->node = $node[1];
            $this->callback = function (Crawler $node) {
                return strtoupper($node->text());
            };
        }
    }
}

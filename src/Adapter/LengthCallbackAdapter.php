<?php
declare(strict_types = 1);
namespace Cacing69\Cquery\Adapter;

use Cacing69\Cquery\Extractor\DefinerExtractor;
use Cacing69\Cquery\Extractor\SourceExtractor;
use Cacing69\Cquery\Support\CqueryRegex;
use Symfony\Component\DomCrawler\Crawler;

class LengthCallbackAdapter extends CallbackAdapter
{
    protected static $signature = CqueryRegex::IS_LENGTH;

    public static function getSignature()
    {
        return self::$signature;
    }
    public function __construct(string $raw, SourceExtractor $source = null)
    {
        $this->raw = $raw;

        // check if function is nested
        if(preg_match('/^\s?length\(\s?([a-z0-9_]*\(.+?\))\s?\)$/', $raw)) {
            preg_match('/^\s?length\(\s?([a-z0-9_]*\(.+?\))\s?\)$/', $raw, $extract);

            $extractor = new DefinerExtractor($extract[1]);

            $this->node = $extractor->getAdapter()->getNode();
            $callbackFromExtractor = $extractor->getAdapter()->getCallback();

            $this->call = $extractor->getAdapter()->getCall();
            $this->callParameter = $extractor->getAdapter()->getCallParameter();

            $afterCallFromExtractor = $extractor->getAdapter()->getAfterCall();

            $this->afterCall = function (string $value) use ($afterCallFromExtractor) {
                return $afterCallFromExtractor ? strlen($afterCallFromExtractor($value)) : strlen($value);
            };

            $this->callback = function (Crawler $node) use ($callbackFromExtractor) {
                return strlen($callbackFromExtractor($node));
            };
        } else {
            preg_match(CqueryRegex::EXTRACT_FIRST_PARAM_LENGTH, $raw, $node);
            $this->node = $node[1];

            $this->call = "extract";
            $this->callParameter = ["_text"];

            $this->afterCall = function (string $value) {
                return strlen($value);
            };

            $this->callback = function (Crawler $node) {
                return strlen($node->text());
            };
        }



    }
}
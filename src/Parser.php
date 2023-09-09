<?php

declare(strict_types=1);

namespace Cacing69\Cquery;

use Cacing69\Cquery\Source;
use Cacing69\Cquery\Support\Str;
use Cacing69\Cquery\Trait\HasSourceProperty;
use Cacing69\Cquery\Trait\HasFiltersProperty;
use Cacing69\Cquery\Trait\HasDefinersProperty;
use Cacing69\Cquery\Trait\HasRawProperty;

class Parser
{
    use HasSourceProperty;
    use HasRawProperty;
    use HasDefinersProperty;
    use HasFiltersProperty;
    public $limit;
    public $onDocumentLoaded;
    // 1). \s*from\s*\(\s*(.*?)\s*\)\s*define\s*(.*?)\s*filter\s*(.*)\s*limit\s*(.*)\s*

    // https://regex101.com/r/mUvz2R/1
    // 2). \s*from\s*\(\s*(.+?)\s*\).*define\s*(.*?)\s*(\s*filter\s*(.*?)\s*)?(\s*limit\s*(.*?)\s*)?\s*$

    /**
    @fn document_loaded use browser, client

    @end_fn

    from ( .item )
    define
        span > a.title as title,
        attr(href, div > h1 > span > a) as url
    filter
        span > a.title has 'narcos',
        span > a.rating > 6
    limit 1
    */
    public function __construct($raw)
    {
        if(empty($raw)) {
            throw new CqueryException("empty query provided");
        }

        $this->raw = $raw;

        $regex = "/\s*from\s*\(\s*(.*?)\s*\).*define/is";

        preg_match($regex, $raw, $_from);

        $this->source = new Source($_from[1]);

        if(preg_match("/define\s*(.*)/is", $raw, $_definer)) {
            if(preg_match("/filter\s*(.*)\s*limit/i", $_definer[1], $_filter)) {
                // get limit
                if(preg_match("/limit\s(\d)/is", $_filter[1], $_limit)) {
                    $this->limit = intval($_limit[1]);
                }
            } elseif(preg_match("/filter\s*(.*)/is", $_definer[1], $_filter)) {
                preg_match("/(.*?)\s*filter/is", $_definer[1], $_extractDefinerFromFilter);
                // extract definer
                $this->makeDefiners($_extractDefinerFromFilter[1]);

                // extract filter
                $this->makeFilters($_filter[1]);
            } else {
                // extract definer
                $this->makeDefiners($_definer[1]);
            }
        }
    }

    private function makeFilters($filters)
    {
        $_strFilter = $filters;
        $_strFilterBefore = "";
        $_loopFilter = 0;

        while (!empty($_strFilter)) {
            if (preg_match("/.+(and|or)/i", $_strFilter, $_strFilterMatch)) {
                $_strFilterBefore = $_strFilterMatch[1];
                $_cleanFilterMatch = trim(preg_replace('/\s+/', ' ', str_replace($_strFilterBefore, "", $_strFilterMatch[0])));

                preg_match('/(.*?)\s*(=|==|===|!=|<>|>|>=|<|<=|has|regex|like)\s*(\'.*\'|\d)/i', $_cleanFilterMatch, $_extractCleanFilterMatch);

                $this->filters[$_strFilterBefore][] = Cquery::makeFilter($_extractCleanFilterMatch[1], $_extractCleanFilterMatch[2], str_replace("'", "", $_extractCleanFilterMatch[3]));
                $_strFilter = str_replace($_strFilterMatch[0], "", $_strFilter);
            } else {
                $_cleanFilterMatch = trim(preg_replace('/\s+/', ' ', str_replace($_strFilterBefore, "", $_strFilter)));

                preg_match('/(.*?)\s*(=|==|===|!=|<>|>|>=|<|<=|has|regex|like)\s*(\'.*\'|\d)/i', $_cleanFilterMatch, $_extractCleanFilterMatch);
                $this->filters[!empty($_strFilterBefore) ? $_strFilterBefore : "and"][] = Cquery::makeFilter($_extractCleanFilterMatch[1], $_extractCleanFilterMatch[2], str_replace("'", "", $_extractCleanFilterMatch[3] ?? ""));

                $_strFilter = trim(preg_replace('/\s+/', ' ', str_replace($_cleanFilterMatch, "", $_strFilter)));
            }

            $_loopFilter++;
        }

    }

    private function makeDefiners($definer)
    {
        $_strDefiner = $definer;
        $_loopDefiner = 0;
        while(!empty($_strDefiner)) {
            if(preg_match("/(.*?)\s*,\s*/i", $_strDefiner, $_strDefinerMatch)) {

                $_adapter = null;

                foreach (RegisterAdapter::load() as $adapter) {
                    if(method_exists($adapter, "getParserIdentifier") && method_exists($adapter, "getCountParserArguments")) {
                        if($adapter::getCountParserArguments() > 1) {

                            $_parserRegexCheck = "/^\s*" . $adapter::getParserIdentifier() . "\(\s*(.*)\s*,/i";

                            if(preg_match($_parserRegexCheck, $_strDefinerMatch[0])) {
                                if(is_array($adapter::getSignature())) {
                                    foreach ($adapter::getSignature() as $_key => $signature) {
                                        // dump($_strDefiner);
                                        if (preg_match($signature, $_strDefiner, $_strDefinerMatch)) {
                                            $_cleanDefiner = Str::endWith(trim($_strDefinerMatch[0]), ",") ? substr(trim($_strDefinerMatch[0]), 0, -1) : trim($_strDefinerMatch[0]);

                                            $this->definers[] = $_cleanDefiner;
                                            $_strDefiner = str_replace($_strDefinerMatch[0], "", $_strDefiner);
                                            break;
                                        } else {
                                            $this->definers[] = trim($_strDefiner);
                                            $_strDefiner = substr($_strDefiner, strlen($_strDefiner));
                                        }
                                    }
                                } else {
                                    if (preg_match($adapter::getSignature(), $_strDefiner, $_strDefinerMatch)) {
                                        $_cleanDefiner = Str::endWith(trim($_strDefinerMatch[0]), ",") ? substr(trim($_strDefinerMatch[0]), 0, -1) : trim($_strDefinerMatch[0]);

                                        $this->definers[] = $_cleanDefiner;
                                        $_strDefiner = str_replace($_strDefinerMatch[0], "", $_strDefiner);
                                    } else {
                                        $this->definers[] = trim($_strDefiner);
                                        $_strDefiner = substr($_strDefiner, strlen($_strDefiner));
                                    }
                                }

                                $_adapter = $adapter;
                                break;
                            }
                        }
                    }
                }

                if(empty($_adapter)) {
                    $this->definers[] = trim($_strDefinerMatch[1]);
                    $_strDefiner = substr($_strDefiner, strlen($_strDefinerMatch[0]));
                }
            } else {
                $this->definers[] = trim($_strDefiner);
                $_strDefiner = substr($_strDefiner, strlen($_strDefiner));
            }

            $_loopDefiner++;
        }
    }

    public function getDefiners()
    {
        return $this->definers;
    }
}

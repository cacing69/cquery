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

        if(preg_match("/define\s*(.*)/is", $raw, $_define)) {

            if(preg_match("/filter\s*(.*)\s*limit/is", $_define[1], $_filter)) {
                if(preg_match("/limit\s(\d)/is", $_filter[1], $_limit)) {
                    $this->limit = intval($_limit[1]);
                }
            } elseif(preg_match("/filter\s*(.*)/is", $_define[1], $_filter)) {

            } else {
                $_strDefine = $_define[1];

                $loop = 0;
                while(!empty($_strDefine)) {
                    if(preg_match("/(.*?)\s*,\s*/", $_strDefine, $_strMatch)) {
                        if(preg_match("/attr\(\s*(.*)\s*,/", $_strMatch[0])) {
                            if(preg_match("/attr\(\s*(.*)\s*,\s*(.*)\s*\).*(as)?.*,/", $_strDefine, $_strMatch)) {
                                $_cleanDefiner = Str::endWith(trim($_strMatch[0]), ",") ? substr(trim($_strMatch[0]), 0, -1) : trim($_strMatch[0]);

                                $this->definers[] = $_cleanDefiner;
                                $_strDefine = str_replace($_strMatch[0], "", $_strDefine);
                            } else {
                                $this->definers[] = trim($_strDefine);
                                $_strDefine = substr($_strDefine, strlen($_strDefine));
                            }
                        } else {
                            $this->definers[] = trim($_strMatch[1]);
                            $_strDefine = substr($_strDefine, strlen($_strMatch[0]));
                        }
                    } else {
                        $this->definers[] = trim($_strDefine);
                        $_strDefine = substr($_strDefine, strlen($_strDefine));
                    }

                    $loop++;
                }
            }
        }
    }

    public function getDefiners()
    {
        return $this->definers;
    }
}

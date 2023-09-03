<?php

declare(strict_types=1);

namespace Cacing69\Cquery;

class Parser
{
    // 1). \s*from\s*\(\s*(.*?)\s*\)\s*define\s*(.*?)\s*filter\s*(.*)\s*limit\s*(.*)\s*

    // https://regex101.com/r/mUvz2R/1
    // 2). \s*from\s*\(\s*(.+?)\s*\)\s+define\s*(.*?)\s*(\s*filter\s*(.*?)\s*)?(\s*limit\s*(.*?)\s*)?\s*$

    //  from ( .item )
    //  define
    //      span > a.title as title,
    //      attr(href, div > h1 > span > a) as url
    //  filter
    //      span > a.title has 'narcos',
    //      span > a.rating > 6
    //  limit 1
}

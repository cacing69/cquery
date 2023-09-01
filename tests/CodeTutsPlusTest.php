<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Cquery;
use PHPUnit\Framework\TestCase;

define("SAMPLE_CODE_TUTS_PLUS", "src/Samples/code-tuts-plus.html");

/**
 * This test following an article from
 * https://github.com/emsifa/tutorial/blob/master/php/scraping-website/scrap.php
 */
final class CodeTutsPlusTest extends TestCase
{
    public function testCqueryCodeTutsPlus()
    {
        // change with this when u want to fetch data from remote
        // $content = "https://code.tutsplus.com/";
        $content = file_get_contents(SAMPLE_CODE_TUTS_PLUS);

        $data = new Cquery($content);

        $result = $data
            ->from("ol.posts")
            ->define(
                "li > article > header > a.posts__post-title > h1 as title",
                "li > article > div as desc",
            )
            ->get();

        $this->assertSame(6, $result->count());
    }
}

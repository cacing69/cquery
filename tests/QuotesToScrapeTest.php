<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Cquery;
use PHPUnit\Framework\TestCase;

define("SAMPLE_QUOTES_TO_SCRAPE", "src/Samples/quotes-toscrape.html");
final class QuotesToScrapeTest extends TestCase
{
    public function testScrapeQuotesToScrape()
    {
        // enable this when u want to fetch data from remote
        // $url = "http://quotes.toscrape.com/";
        $content = file_get_contents(SAMPLE_QUOTES_TO_SCRAPE);

        $data = new Cquery($content);

        $result = $data
            ->from(".col-md-8 > .quote")
            ->pick(
                "span.text as text",
                "span:nth-child(2) > small as author",
                "div > .tags  as tags",
                )
            ->get();

        $resultTopTen = $data
            ->from(".tags-box")
            ->pick(
                ".tag-item > a as text"
            )
            ->get();

        $this->assertCount(10, $result);
        $this->assertCount(10, $resultTopTen);
    }
}

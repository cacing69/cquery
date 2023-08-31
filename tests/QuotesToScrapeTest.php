<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Cquery;
use Cacing69\Cquery\Exception\CqueryException;
use Exception;
use PHPUnit\Framework\TestCase;

define("SAMPLE_QUOTES_TO_SCRAPE", "src/Samples/quotes-toscrape.html");
final class QuotesToScrapeTest extends TestCase
{
    public function testScrapeQuotesToScrape()
    {
        // change with this when u want to fetch data from remote
        // $content = "http://quotes.toscrape.com/";
        $content = file_get_contents(SAMPLE_QUOTES_TO_SCRAPE);

        $data = new Cquery($content);

        $result = $data
            ->from(".col-md-8 > .quote")
            ->define(
                "span.text as text",
                "span:nth-child(2) > small as author",
                "(div > .tags) as tags",
                )
            ->get();

        $resultTopTen = $data
            ->from(".tags-box")
            ->define(
                ".tag-item > a as text"
            )
            ->get();

        $this->assertCount(10, $result);
        $this->assertCount(10, $resultTopTen);
    }

    public function testScrapeQuotesToScrapeWithWrongDefiner()
    {
        // change with this when u want to fetch data from remote
        // $content = "http://quotes.toscrape.com/";
        $content = file_get_contents(SAMPLE_QUOTES_TO_SCRAPE);

        $data = new Cquery($content);

        try {
            $result = $data
                ->from(".col-md-8 > .quote")
                ->define(
                    "span.text as text",
                    "span:nth-child(2) > small as author",
                    "(div > .tags > a)  as tags",
                    )
                ->get();
        } catch (Exception $e) {
            $this->assertSame(CqueryException::class, get_class($e));
            $this->assertStringContainsString("error query definer", $e->getMessage());
            $this->assertStringContainsString("error occurred while attempting to pick the column", $e->getMessage());
        }
    }

    public function testScrapeQuotesToScrapeWithAppendNodeDefiner()
    {
        $content = file_get_contents(SAMPLE_QUOTES_TO_SCRAPE);

        $data = new Cquery($content);

        $result = $data
            ->from(".col-md-8 > .quote")
            ->define(
                "span.text as text",
                "span:nth-child(2) > small as author",
                "append_node(div > .tags, a)  as tags",
            )
            ->get()
            ->toArray();

        $this->assertCount(10, $result);
        $this->assertCount(4, $result[0]['tags']);
        $this->assertCount(2, $result[1]['tags']);
    }
}

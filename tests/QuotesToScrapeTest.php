<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Cquery;
use Cacing69\Cquery\Exception\CqueryException;
use Exception;
use PHPUnit\Framework\TestCase;

define("SAMPLE_QUOTES_TO_SCRAPE", "src/Samples/quotes-toscrape.html");
final class QuotesToScrapeTest extends TestCase
{
    public function testScrapeQuotesWithUrlToScrape()
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

    public function testScrapeQuotesToScrapeWithAppendNodeHrefAttributeDefiner()
    {
        $content = file_get_contents(SAMPLE_QUOTES_TO_SCRAPE);

        $data = new Cquery($content);

        $result = $data
            ->from(".col-md-8 > .quote")
            ->define(
                "span.text as text",
                "span:nth-child(2) > small as author",
                "append_node(div > .tags, a)  as tags",
                "append_node(div > .tags, attr(href, a))  as tags_url",
            )
            ->get();

        $this->assertCount(10, $result);
        $this->assertCount(4, $result[0]['tags']);
        $this->assertCount(2, $result[1]['tags']);
        $this->assertCount(4, $result[0]['tags_url']);
        $this->assertCount(2, $result[1]['tags_url']);
    }

    public function testScrapeQuotesToScrapeWithAppendNodeAndAppendOnKeyDefiner()
    {
        $content = file_get_contents(SAMPLE_QUOTES_TO_SCRAPE);

        $data = new Cquery($content);

        $result = $data
            ->from(".col-md-8 > .quote")
            ->define(
                "span.text as text",
                "append_node(div > .tags, a) as _tags",
                "append_node(div > .tags, a) as tags[*][text]",
                "append_node(div > .tags, attr(href, a)) as tags[*][url]", // * means each index, for now ots limitd only one level
            )
            ->get();

        dump($result);
        $this->assertCount(10, $result);
        $this->assertCount(4, $result[0]['tags']);

        $this->assertSame('change', $result[0]['tags'][0]['text']);
        $this->assertSame('/tag/change/page/1/', $result[0]['tags'][0]['url']);

        $this->assertSame('deep-thoughts', $result[0]['tags'][1]['text']);
        $this->assertSame('/tag/deep-thoughts/page/1/', $result[0]['tags'][1]['url']);

        $this->assertSame('thinking', $result[0]['tags'][2]['text']);
        $this->assertSame('/tag/thinking/page/1/', $result[0]['tags'][2]['url']);

        $this->assertSame('world', $result[0]['tags'][3]['text']);
        $this->assertSame('/tag/world/page/1/', $result[0]['tags'][3]['url']);
    }
}

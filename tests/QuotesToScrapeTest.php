<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Cquery;
use Cacing69\Cquery\CqueryException;
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
            ->get();

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
        $this->assertCount(5, $result[2]['tags']);
        $this->assertCount(4, $result[3]['tags']);
        $this->assertCount(2, $result[4]['tags']);
        $this->assertCount(3, $result[5]['tags']);
        $this->assertCount(2, $result[6]['tags']);
        $this->assertCount(4, $result[7]['tags']);
        $this->assertCount(1, $result[8]['tags']);
        $this->assertCount(3, $result[9]['tags']);

        $this->assertCount(4, $result[0]['tags_url']);
        $this->assertCount(2, $result[1]['tags_url']);
        $this->assertCount(5, $result[2]['tags_url']);
        $this->assertCount(4, $result[3]['tags_url']);
        $this->assertCount(2, $result[4]['tags_url']);
        $this->assertCount(3, $result[5]['tags_url']);
        $this->assertCount(2, $result[6]['tags_url']);
        $this->assertCount(4, $result[7]['tags_url']);
        $this->assertCount(1, $result[8]['tags_url']);
        $this->assertCount(3, $result[9]['tags_url']);
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

    public function testScrapeQuotesToScrapeWithAppendNodeArrayDefiner()
    {
        $content = file_get_contents(SAMPLE_QUOTES_TO_SCRAPE);

        $data = new Cquery($content);

        $result = $data
            ->from(".col-md-8 > .quote")
            ->define(
                "span.text as text",
                "append_node(div > .tags, a) as tags[key]",
            )
            ->get();

        $this->assertCount(10, $result);

        $this->assertCount(4, $result[0]['tags']['key']);
        $this->assertCount(2, $result[1]['tags']['key']);
        $this->assertCount(5, $result[2]['tags']['key']);
        $this->assertCount(4, $result[3]['tags']['key']);
        $this->assertCount(2, $result[4]['tags']['key']);
        $this->assertCount(3, $result[5]['tags']['key']);
        $this->assertCount(2, $result[6]['tags']['key']);
        $this->assertCount(4, $result[7]['tags']['key']);
        $this->assertCount(1, $result[8]['tags']['key']);
        $this->assertCount(3, $result[9]['tags']['key']);
    }

    public function testScrapeQuotesToScrapeWithReplaceDefiner()
    {
        $content = file_get_contents(SAMPLE_QUOTES_TO_SCRAPE);

        $data = new Cquery($content);

        $result = $data
            ->from(".col-md-8 > .quote")
            ->define(
                "replace('The ', 'Lorem ', span.text) as text",
            )
            ->get();

        $this->assertCount(10, $result);
        $this->assertSame("“Lorem world as we have created it is a process of our thinking. It cannot be changed without changing our thinking.”", $result[0]["text"]);
        $this->assertSame("“It is our choices, Harry, that show what we truly are, far more than our abilities.”", $result[1]["text"]);
        $this->assertSame("“There are only two ways to live your life. One is as though nothing is a miracle. Lorem other is as though everything is a miracle.”", $result[2]["text"]);
        $this->assertSame("“Lorem person, be it gentleman or lady, who has not pleasure in a good novel, must be intolerably stupid.”", $result[3]["text"]);
        $this->assertSame("“Imperfection is beauty, madness is genius and it's better to be absolutely ridiculous than absolutely boring.”", $result[4]["text"]);
        $this->assertSame("“Try not to become a man of success. Rather become a man of value.”", $result[5]["text"]);
        $this->assertSame("“It is better to be hated for what you are than to be loved for what you are not.”", $result[6]["text"]);
        $this->assertSame("“I have not failed. I've just found 10,000 ways that won't work.”", $result[7]["text"]);
        $this->assertSame("“A woman is like a tea bag; you never know how strong it is until it's in hot water.”", $result[8]["text"]);
        $this->assertSame("“A day without sunshine is like, you know, night.”", $result[9]["text"]);
    }

    public function testScrapeQuotesToScrapeWithReplaceArrayDefiner()
    {
        $content = file_get_contents(SAMPLE_QUOTES_TO_SCRAPE);

        $data = new Cquery($content);

        $result = $data
            ->from(".col-md-8 > .quote")
            ->define(
                "replace(['The ', 'are'], ['Please ', 'son'], span.text) as text",
            )
            ->get();

        $this->assertCount(10, $result);
        $this->assertSame("“Please world as we have created it is a process of our thinking. It cannot be changed without changing our thinking.”", $result[0]["text"]);
        $this->assertSame("“It is our choices, Harry, that show what we truly son, far more than our abilities.”", $result[1]["text"]);
        $this->assertSame("“There son only two ways to live your life. One is as though nothing is a miracle. Please other is as though everything is a miracle.”", $result[2]["text"]);
        $this->assertSame("“Please person, be it gentleman or lady, who has not pleasure in a good novel, must be intolerably stupid.”", $result[3]["text"]);
        $this->assertSame("“Imperfection is beauty, madness is genius and it's better to be absolutely ridiculous than absolutely boring.”", $result[4]["text"]);
        $this->assertSame("“Try not to become a man of success. Rather become a man of value.”", $result[5]["text"]);
        $this->assertSame("“It is better to be hated for what you son than to be loved for what you son not.”", $result[6]["text"]);
        $this->assertSame("“I have not failed. I've just found 10,000 ways that won't work.”", $result[7]["text"]);
        $this->assertSame("“A woman is like a tea bag; you never know how strong it is until it's in hot water.”", $result[8]["text"]);
        $this->assertSame("“A day without sunshine is like, you know, night.”", $result[9]["text"]);
    }
}

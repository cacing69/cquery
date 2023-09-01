<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Cquery;
use PHPUnit\Framework\TestCase;

define("SAMPLE_SCRAPE_ME_LIVE", "src/Samples/scrape-me-live.html");

/**
 * This test following an article from
 * https://www.zenrows.com/blog/web-scraping-php#basic-PHP-web-scraping
 */
final class ScrapeMeLiveTest extends TestCase
{
    public function testCqueryScrapeMeLivetWithUrl()
    {
        // change with this when u want to fetch data from remote
        // $content = "https://scrapeme.live/shop/";
        $content = file_get_contents(SAMPLE_SCRAPE_ME_LIVE);

        $data = new Cquery($content);

        $result = $data
            ->from("#main > div:nth-child(4) > nav > ul.page-numbers")
            ->define(
                "li > a as text",
                "attr(href, li > a) as href",
            )
            ->get();

        $this->assertSame(7, $result->count());
    }

    public function testCqueryGetProductsScrapeMeLivetWithUrl()
    {
        // change with this when u want to fetch data from remote
        // $content = "https://scrapeme.live/shop/";
        // TODO https://scrapeme.live/shop/page/1/ -> test looping
        $content = file_get_contents(SAMPLE_SCRAPE_ME_LIVE);

        $data = new Cquery($content);

        $result = $data
            ->from("ul.products.columns-4")
            ->define(
                "li > a > h2 as text",
                "li > a > span.price > span.amount as price",
                "attr(src, li > a > img) as image",
                "attr(href, li > a.woocommerce-loop-product__link) as url",
            )
            ->get();

        $this->assertSame(16, $result->count());
    }
}

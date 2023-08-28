<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Cquery;
use PHPUnit\Framework\TestCase;

define("SAMPLE_FREE_PROXY_LIST", "src/Samples/free-proxy-list.html");
final class FreeProxyListScrapeTest extends TestCase
{
    public function testCqueryFreeProxyListWithUrl()
    {
        // enable this when u want to fetch data from remote
        // $content = "https://free-proxy-list.net/";
        $content = file_get_contents(SAMPLE_FREE_PROXY_LIST);;

        $data = new Cquery($content);

        $resultYes = $data
            ->from("#list")
            ->pick(
                "td:nth-child(1) as ip_address",
                "td:nth-child(2) as port",
                "td:nth-child(3) as code",
                "td:nth-child(4) as country",
                "td:nth-child(5) as anonymity",
                "td:nth-child(6) as google",
                "td:nth-child(7) as https",
                "td:nth-child(8) as last_checked",
            )->filter('td:nth-child(7)', "=", "no")
            ->get();

        $resultNo = $data
            ->from("#list")
            ->pick(
                "td:nth-child(1) as ip_address",
                "td:nth-child(2) as port",
                "td:nth-child(3) as code",
                "td:nth-child(4) as country",
                "td:nth-child(5) as anonymity",
                "td:nth-child(6) as google",
                "td:nth-child(7) as https",
                "td:nth-child(8) as last_checked",
            )->filter('td:nth-child(7)', "=", "yes")
            ->get();

        $this->assertNotSame(300, $resultYes->count());
        $this->assertNotSame(300, $resultNo->count());
    }

    public function testCqueryFreeProxyListWithLimit()
    {
        // enable this when u want to fetch data from remote
        // $content = "https://free-proxy-list.net/";
        $content = file_get_contents(SAMPLE_FREE_PROXY_LIST);;

        $data = new Cquery($content);

        // dd($data);

        $result = $data
            ->from("#list")
            ->pick(
                "td:nth-child(1) as ip_address",
                "td:nth-child(2) as port",
                "td:nth-child(3) as code",
                "td:nth-child(4) as country",
                "td:nth-child(5) as anonymity",
                "td:nth-child(6) as google",
                "td:nth-child(7) as https",
                "td:nth-child(8) as last_checked",
            )->filter('td:nth-child(7)', "=", "no")
            ->get()
            ->take(5);

        $this->assertCount(5, $result);
    }
}

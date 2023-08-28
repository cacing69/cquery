<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Cquery;
use PHPUnit\Framework\TestCase;

final class FreeProxyListScrapeTest extends TestCase
{
    public function testCqueryFreeProxyListWithUrl()
    {
        $url = "https://free-proxy-list.net/";

        $data = new Cquery($url);

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
            )
            ->filter('td:nth-child(7)', "=", "yes")
            ->get();

        // $this->assertNotEquals(300, $result->count());
    }
}

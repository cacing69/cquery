<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Cquery;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;

define("GOOGLE", "https://google.com");
define("HTTP_BIN_TEST_K6_FORM_POST", "https://httpbin.test.k6.io/forms/post");
define("LAMBDA_TEST_SELENIUM_PLAYGROUND_SIMPLE_FORM_DEMO", "https://www.lambdatest.com/selenium-playground/simple-form-demo");
define("USER_AGENTS_RANDOM", "https://user-agents.net/random");
define("WIKIPEDIA", "https://id.wikipedia.org/wiki/Halaman_Utama");
define("SEMVER_ORG", "https://semver.org/");

final class SampleOnlineTest extends TestCase
{
    public function testGetGoogleTitle()
    {
        $data = new Cquery(GOOGLE);

        $result = $data
            ->from("html")
            ->define(
                "title",
            )
            ->first();

        $this->assertSame('Google', $result['title']);
    }

    public function testHttpBinTestWithActions()
    {
        $data = new Cquery(HTTP_BIN_TEST_K6_FORM_POST);

        $result = $data
            ->onReady(function (HttpBrowser $browser) {
                $browser->submitForm('Submit order', ["comments" => "Lorem", "custemail" => "iniemail@cust.com"]);
                return $browser;
            })
            ->from("html")
            ->define(
                "title",
            )
            ->get();

        $this->assertSame(true, true);
    }

    public function testGetAndLimitUserAgents()
    {
        $data = new Cquery(USER_AGENTS_RANDOM);

        $result = $data
            ->onReady(function (HttpBrowser $browser) {
                $browser->submitForm("Generate random list", [
                    "limit" => 5,
                ]);

                return $browser;
            })
            ->from("section > article")
            ->define(
                "ol > li > a as user_agent",
            )
            ->get();

        $this->assertCount(5, $result);
    }

    public function testClickLinkOnSemver()
    {
        $data = new Cquery(SEMVER_ORG);

        $result = $data
            ->onReady(function (HttpBrowser $browser, Crawler $crawler) {
                $browser->clickLink("Bahasa Indonesia (id)");
                return $browser;
            })
            ->from("#spec")
            ->define(
                "h2 as text",
            )
            ->get();

        $this->assertSame("Ringkasan", $result[0]["text"]);
        $this->assertSame("Pendahuluan", $result[1]["text"]);
        $this->assertSame("Spesifikasi Pemversian Semantik (SemVer)", $result[2]["text"]);
        $this->assertSame("Grammar Bentuk Backus–Naur untuk Versi SemVer Valid", $result[3]["text"]);
        $this->assertSame("Kenapa Menggunakan Pemversian Semantik?", $result[4]["text"]);
        $this->assertSame("Pertanyaan Yang Sering Diajukan", $result[5]["text"]);
        $this->assertSame("Tentang", $result[6]["text"]);
        $this->assertSame("Lisensi", $result[7]["text"]);
    }

    public function testFormSearchOnWikipedia()
    {
        $data = new Cquery(WIKIPEDIA);

        $result = $data
            ->onReady(function (HttpBrowser $browser, Crawler $crawler) {
                $form = new Form($crawler->filter("#searchform")->getNode(0), WIKIPEDIA);

                $browser->submit($form, [
                    "search" => "sambas",
                ]);
                return $browser;
            })
            ->from("html")
            ->define(
                "title as title",
            )
            ->get();

        $this->assertSame("Kabupaten Sambas - Wikipedia bahasa Indonesia, ensiklopedia bebas", $result[0]["title"]);
    }
}

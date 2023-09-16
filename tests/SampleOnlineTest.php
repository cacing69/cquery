<?php

use Cacing69\Cquery\Cquery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;

define('GOOGLE', 'https://google.com');
define('HTTP_BIN_TEST_K6_FORM_POST', 'https://httpbin.test.k6.io/forms/post');
define('LAMBDA_TEST_SELENIUM_PLAYGROUND_SIMPLE_FORM_DEMO', 'https://www.lambdatest.com/selenium-playground/simple-form-demo');
define('USER_AGENTS_RANDOM', 'https://user-agents.net/random');
define('WIKIPEDIA', 'https://id.wikipedia.org/wiki/Halaman_Utama');
define('SEMVER_ORG', 'https://semver.org/');
define('Y_QQ_COM', 'https://y.qq.com/n/ryqq/singer/00220AYa4Ohak5');

final class SampleOnlineTest extends TestCase
{
    /**
     * @group ignore
     */
    public function testGetGoogleTitle()
    {
        $this->markTestSkipped('will be skipped.');
        $data = new Cquery(GOOGLE);

        $result = $data
            ->from('html')
            ->define(
                'title',
            )
            ->first();

        $this->assertSame('Google', $result['title']);
    }

    /**
     * @group ignore
     */
    public function testHttpBinTestWithActions()
    {
        $this->markTestSkipped('will be skipped.');

        $data = new Cquery(HTTP_BIN_TEST_K6_FORM_POST);

        $result = $data
            ->onContentLoaded(function (HttpBrowser $browser) {
                $browser->submitForm('Submit order', ['comments' => 'Lorem', 'custemail' => 'iniemail@cust.com']);

                return $browser;
            })
            ->from('html')
            ->define(
                'title',
            )
            ->get();

        $this->assertSame(true, true);
    }

    /**
     * @group ignore
     */
    public function testGetAndLimitUserAgents()
    {
        $this->markTestSkipped('will be skipped.');

        $data = new Cquery(USER_AGENTS_RANDOM);

        $result = $data
            ->onContentLoaded(function (HttpBrowser $browser) {
                $browser->submitForm('Generate random list', [
                    'limit' => 5,
                ]);

                return $browser;
            })
            ->from('section > article')
            ->define(
                'ol > li > a as user_agent',
            )
            ->get();

        $this->assertCount(5, $result);
    }

    /**
     * @group ignore
     */
    public function testClickLinkOnSemver()
    {
        $this->markTestSkipped('will be skipped.');

        $data = new Cquery(SEMVER_ORG);

        $result = $data
            ->onContentLoaded(function (HttpBrowser $client, Crawler $crawler) {
                $client->clickLink('Bahasa Indonesia (id)');

                return $client;
            })
            ->from('#spec')
            ->define(
                'h2 as text',
            )
            ->get();

        $this->assertSame('Ringkasan', $result[0]['text']);
        $this->assertSame('Pendahuluan', $result[1]['text']);
        $this->assertSame('Spesifikasi Pemversian Semantik (SemVer)', $result[2]['text']);
        $this->assertSame('Grammar Bentuk Backus–Naur untuk Versi SemVer Valid', $result[3]['text']);
        $this->assertSame('Kenapa Menggunakan Pemversian Semantik?', $result[4]['text']);
        $this->assertSame('Pertanyaan Yang Sering Diajukan', $result[5]['text']);
        $this->assertSame('Tentang', $result[6]['text']);
        $this->assertSame('Lisensi', $result[7]['text']);
    }

    /**
     * @group ignore
     */
    public function testFormSearchOnWikipedia()
    {
        $this->markTestSkipped('will be skipped.');

        $data = new Cquery(WIKIPEDIA);

        $result = $data
            ->onContentLoaded(function (HttpBrowser $client, Crawler $crawler) {
                $form = new Form($crawler->filter('#searchform')->getNode(0), WIKIPEDIA);

                $client->submit($form, [
                    'search' => 'sambas',
                ]);

                return $client;
            })
            ->from('html')
            ->define(
                'title as title',
            )
            ->get();

        $this->assertSame('Kabupaten Sambas - Wikipedia bahasa Indonesia, ensiklopedia bebas', $result[0]['title']);
    }

    /**
     * @group ignore
     */
    public function testFormSearchOnWikipediaButWithClickFirst()
    {
        $this->markTestSkipped('will be skipped.');

        $data = new Cquery(WIKIPEDIA);

        $result = $data
            ->onContentLoaded(function (HttpBrowser $client, Crawler $crawler) {
                $form = new Form($crawler->filter('#searchform')->getNode(0), WIKIPEDIA);

                $client->submit($form, [
                    'search' => 'parit setia',
                ]);

                $_crawler = new Crawler($client->getResponse()->getContent());

                // CHECK IF ON INDEX SEARCH RESULT
                $_result = $_crawler->filter('ul.mw-search-results')->filter('li.mw-search-result');

                if ($_result->count() > 0) {
                    // LETS SIMULATE TO CLICK FIRST RESULT
                    $_check = $_result->filter('table.searchResultImage td.searchResultImage-text > div > a')->first();
                    $_link = $_check->attr('href');

                    $client->request('GET', "https://id.wikipedia.org{$_link}");
                }

                return $client;
            })
            ->from('html')
            ->define(
                'title as title',
            )
            ->get();

        $this->assertSame('Parit Setia, Jawai, Sambas - Wikipedia bahasa Indonesia, ensiklopedia bebas', $result[0]['title']);
    }
}

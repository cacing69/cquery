<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Cquery;
use PHPUnit\Framework\TestCase;

use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Loop;
use React\Http\Browser;
use Symfony\Component\BrowserKit\HttpBrowser;

define("SAMPLE_CADILLAC_CAR", "http://www.classiccardatabase.com/postwar-models/Cadillac.php");
final class CadillacCarDatabaseTest extends TestCase
{

    // without async = 467s
    public function testCollectCadillacCar()
    {
        $data = new Cquery(SAMPLE_CADILLAC_CAR);

        $loop = Loop::get();
        $client = new Browser($loop);

        $result = $data
            ->client("browser-kit")
            ->from(".content")
            ->define(
                ".car-model-link > a as name",
                "replace('../', 'http://www.classiccardatabase.com/', attr(href, .car-model-link > a)) as url",
            )
            ->filter("attr(href, .car-model-link > a)", "!=", "#")
            ->init(function (HttpBrowser $e) {

            })
            // ->each(function ($el){
            //     $detail = new Cquery($el["url"]);

            //     $resultDetail = $detail
            //     ->from(".spec")
            //     ->define(
            //         ".specleft tr:nth-child(1) > td.data as price"
            //     )
            //     ->first();

            //     $el["price"] = $resultDetail["price"];

            //     return $el;
            // })
            ->compose(function ($results) use ($loop, $client){
                // TODO batas maksimal yang kutemukan adalah 25, ketika aku input 30, ada beberapa data yang null
                $results = array_chunk($results, 25);

                foreach ($results as $key => $_chunks) {
                    foreach ($_chunks as $_key => $_result) {
                        $client
                        // ->withHeader("Key", "value")
                        // ->withHeader("Key", "value")
                        ->get($_result["url"])
                            ->then(function (ResponseInterface $response) use (&$results, $key, $_key) {
                                $detail = new Cquery((string) $response->getBody());

                                $resultDetail = $detail
                                    ->from(".spec")
                                    ->define(
                                        ".specleft tr:nth-child(1) > td.data as price"
                                    )
                                    ->first();

                                $results[$key][$_key]["price"] = $resultDetail["price"];
                            });
                    }
                    $loop->run();
                }
                return array_merge(...$results);
            })
            ->limit(10)
            ->get();

        // dump($result);
            // $loop->run();

        $this->assertSame(true, true);
    }
}

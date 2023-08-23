<?php

require_once 'vendor/autoload.php';

$html = file_get_contents("src/samples/sample-simple-1.html");
// ./vendor/bin/phpunit --verbose tests

$data = new Cacing69\Cquery\Cquery($html);

$result = $data
        ->select("h1 as title", "a > p as description", "attr(href, a) as url", "attr(class, a) as class")
        ->from("#lorem .link")
        ->where("attr(class, a)", "like", "%vip%")
        ->get();
// save output

dump($result);

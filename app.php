<?php

require_once 'vendor/autoload.php';

$c = new Cacing69\Cquery\Builder()  ;

$html = '
<html>
  <head>
    <title>Href Attribute Example</title>
  </head>
  <body>
    <span id="lorem">
      <h1>Href Attribute Example</h1>
    </span>
    <p>
      <a href="https://www.freecodecamp.org/contribute/">The freeCodeCamp Contribution Page
    </p>
  </body>
</html>';

$c->setContent($html);

$c->pick("h1");

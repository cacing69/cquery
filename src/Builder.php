<?php

namespace Cacing69\Cquery;

class Builder {
    private $dom;

    public function __construct()
    {
        $this->dom = new \DOMDocument();
    }

    public function pick($selector)
    {
        echo($selector);
        var_dump($this->dom);
    }

    public function setContent($content)
    {
        $this->dom->loadHTML($content);
        return $this;
    }
}

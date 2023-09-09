<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Loader;

use Cacing69\Cquery\Loader;
use Cacing69\Cquery\Trait\HasDomCrawlerGetter;
use Symfony\Component\DomCrawler\Crawler;

class DOMCrawlerLoader extends Loader
{
    use HasDomCrawlerGetter;
    public function __construct(string $content = null, $isRemote = false)
    {
        $this->isRemote = $isRemote;
        if ($content !== null && !$isRemote) {
            $this->crawler = new Crawler($content);
        } else {
            $this->uri = $content;
        }
    }
}

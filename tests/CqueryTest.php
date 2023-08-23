<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Cquery;
use PHPUnit\Framework\TestCase;

define("SAMPLE_SIMPLE_1", "src/Samples/sample-simple-1.html");

final class CqueryTest extends TestCase
{
    public function testCollectFirst()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->select("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->from("#lorem .link")
            ->first();

        $this->assertSame('Title 1', $result['title']);
        $this->assertSame('Href Attribute Example 1', $result['description']);
        $this->assertSame('http://ini-url-1.com', $result['url']);
        $this->assertSame('ini vip class-1', $result['class']);
    }

    public function testWhereLikeWithAnyCondition()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->select("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->from("#lorem .link")
            ->where("attr(class, a)", "like", "%vip%")
            ->first();

        $this->assertSame(4, count($result));
    }

    public function testSetSelector()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $data->from("#lorem .link");

        $selector = $data->getSelector();

        $this->assertSame('#lorem .link', $selector->getValue());
        $this->assertSame(null, $selector->getAlias());
    }

    public function testSetSelectorWithAlias()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $data->from("(#lorem .link) as _el");

        $selector = $data->getSelector();

        $this->assertSame('(#lorem .link) as _el', $selector->getRaw());
        $this->assertSame('#lorem .link', $selector->getValue());
        $this->assertSame('_el', $selector->getAlias());
    }
}

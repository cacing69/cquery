<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Cquery;
use Cacing69\Cquery\Extractor\SelectorExtractor;
use PHPUnit\Framework\TestCase;

final class SelectorExtractorTest extends TestCase
{
    public function testSetSelector()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $data->from("#lorem .link");

        $selector = $data->getActiveDom()->getSelector();

        $this->assertSame('#lorem .link', $selector->getValue());
        $this->assertSame("", $selector->getAlias());
    }

    public function testSetSelectorWithAlias()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $data->from("(#lorem .link) as _el");

        $selector = $data->getActiveDom()->getSelector();

        $this->assertSame('(#lorem .link) as _el', $selector->getRaw());
        $this->assertSame('#lorem .link', $selector->getValue());
        $this->assertSame('_el', $selector->getAlias());
    }

    public function testSelectorExtractorToString()
    {
        $selector = new SelectorExtractor("(a > ul) as _el");

        $this->assertEquals('a > ul', $selector);
    }
}

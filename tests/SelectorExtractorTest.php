<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Cquery;
use Cacing69\Cquery\Extractor\SelectExtractor;
use PHPUnit\Framework\TestCase;

final class SelectExtractorTest extends TestCase
{
    public function testSetSelector()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $data->source("#lorem .link");

        $selector = $data->getActiveSelector();

        $this->assertSame('#lorem .link', $selector->getValue());
        $this->assertSame(null, $selector->getAlias());
    }

    public function testSetSelectorWithAlias()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $data->source("(#lorem .link) as _el");

        $selector = $data->getActiveSelector();

        $this->assertSame('(#lorem .link) as _el', $selector->getRaw());
        $this->assertSame('#lorem .link', $selector->getValue());
        $this->assertSame('_el', $selector->getAlias());
    }

    public function testSelectExtractorToString()
    {
        $selector = new SelectExtractor("(a > ul) as _el");

        $this->assertEquals('a > ul', $selector);
    }
}

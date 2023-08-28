<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Cquery;
use Cacing69\Cquery\Extractor\SourceExtractor;
use PHPUnit\Framework\TestCase;

define("SAMPLE_SIMPLE_1", "src/Samples/sample-simple-1.html");
final class SourceExtractorTest extends TestCase
{
    public function testSetSelector()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $data->from("#lorem .link");

        $selector = $data->getActiveSource()->getSource();

        $this->assertSame('#lorem .link', $selector->getValue());
        $this->assertSame("", $selector->getAlias());
    }

    public function testSetSelectorWithAlias()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $data->from("(#lorem .link) as _el");

        $selector = $data->getActiveSource()->getSource();

        $this->assertSame('(#lorem .link) as _el', $selector->getRaw());
        $this->assertSame('#lorem .link', $selector->getValue());
        $this->assertSame('_el', $selector->getAlias());
    }

    public function SourceExtractorToString()
    {
        $selector = new SourceExtractor("(a > ul) as _el");

        $this->assertEquals('a > ul', $selector);
    }
}

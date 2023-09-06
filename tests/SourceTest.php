<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Cquery;
use Cacing69\Cquery\Source;
use PHPUnit\Framework\TestCase;

define("SAMPLE_HTML", "src/Samples/sample.html");
final class SourceTest extends TestCase
{
    public function testSetSelector()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $data->from("#lorem .link");

        $source = $data->getSource();

        $this->assertSame('#lorem .link', $source->getValue());
        $this->assertSame("", $source->getAlias());
    }

    public function testSetSelectorWithAlias()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $data->from("(#lorem .link) as _el");

        $selector = $data->getSource();

        $this->assertSame('(#lorem .link) as _el', $selector->getRaw());
        $this->assertSame('#lorem .link', $selector->getValue());
        $this->assertSame('_el', $selector->getAlias());
    }

    public function SourceExtractorToString()
    {
        $selector = new Source("(a > ul) as _el");

        $this->assertEquals('a > ul', $selector);
    }
}

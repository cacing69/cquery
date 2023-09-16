<?php

use Cacing69\Cquery\Cquery;
use Cacing69\Cquery\Writer\CSVWriter;
use PHPUnit\Framework\TestCase;

define('SAMPLE_HTML', 'src/Samples/sample.html');

final class WriterTest extends TestCase
{
    public function testCsvWriter()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define(
                'h1 as title',
                'a as content_title',
                'attr(href, url) as content_url',
            )
            ->get();

        $writer = new CSVWriter();

        $writer->setData($result);

        $save = $writer->save(".cached/output.csv");

        $this->assertSame(".cached/output.csv", $save);
    }
}

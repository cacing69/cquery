<?php

use Cacing69\Cquery\Cquery;
use Cacing69\Cquery\Writer\CSVWriter;
use PHPUnit\Framework\TestCase;

define('SAMPLE_HTML', 'src/Samples/sample.html');

final class WriterTest extends TestCase
{
    protected $data;

    protected function setUp() : void
    {
        parent::setUp();

        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $this->data = $data
            ->from('#lorem .link')
            ->define(
                'h1 as title',
                'a as content_title',
                'attr(href, url) as content_url',
            )
            ->get();
    }
    public function testCsvWriter()
    {
        $this->markTestSkipped();

        $writer = new CSVWriter();

        $writer->setData($this->data);

        $writer->save('.cached/output.csv');

        $this->assertFileExists('.cached/output.csv');
    }

    public function testCsvWriterOnlyMethod()
    {
        $writer = new CSVWriter();

        $writer->setData($this->data);

        $writer->only("title", "content_url");

        $this->assertCount(9, $writer->getData());

        $this->assertTrue(!array_key_exists('content_title', $writer->getData()->first()));
        $this->assertTrue(array_key_exists('title', $writer->getData()->first()));
        $this->assertTrue(array_key_exists('content_url', $writer->getData()->first()));
    }

    protected function tearDown():void
    {
        $this->data = null;
    }
}

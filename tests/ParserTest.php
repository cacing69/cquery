<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\CqueryException;
use Cacing69\Cquery\Parser;
use Exception;
use PHPUnit\Framework\TestCase;

final class ParserTest extends TestCase
{
    public function testParseWithEmptyQuery()
    {
        $query = "";

        try {
            $parser = new Parser($query);
        } catch (Exception $e) {
            $this->assertSame(CqueryException::class, get_class($e));
            $this->assertSame("empty query provided", $e->getMessage());
        }
    }
    public function testParseQuery1Selector()
    {
        $query = "
        from ( .item )
        define
            span > a.title as title,
            attr(href, div > h1 > span > a) as url
        ";

        $parser = new Parser($query);

        $this->assertSame(".item", $parser->getSource()->getRaw());
        $this->assertCount(2, $parser->getDefiners());
        $this->assertSame("span > a.title as title", $parser->getDefiners()[0]);
        $this->assertSame("attr(href, div > h1 > span > a) as url", $parser->getDefiners()[1]);
    }

    public function testParseQuery1SelectorOneLine()
    {
        $query = "
        from ( .item )
        define
            span > a.title as title,
            attr(href, div > h1 > span > a) as url
        ";

        $parser = new Parser($query);

        $this->assertSame(".item", $parser->getSource()->getRaw());
        $this->assertCount(2, $parser->getDefiners());
        $this->assertSame("span > a.title as title", $parser->getDefiners()[0]);
        $this->assertSame("attr(href, div > h1 > span > a) as url", $parser->getDefiners()[1]);
    }

    public function testParseQuery1ButWithReverseDefinerSelector()
    {
        $query = "
        from ( .item )
        define
            attr(href, div > h1 > span > a) as url,
            span > a.title as title
        ";

        $parser = new Parser($query);

        $this->assertSame(".item", $parser->getSource()->getRaw());
        $this->assertCount(2, $parser->getDefiners());
        $this->assertSame("attr(href, div > h1 > span > a) as url", $parser->getDefiners()[0]);
        $this->assertSame("span > a.title as title", $parser->getDefiners()[1]);
    }

    public function testParseQuery1SelectorWithFilterHas()
    {
        $query = "
        from ( .item )
        define
            span > a.title as title,
            attr(href, div > h1 > span > a) as url
        filter
            span > a.title has 'lorem'  and
            span > h1 > 9
        ";

        $parser = new Parser($query);

        $this->assertSame(".item", $parser->getSource()->getRaw());
        $this->assertCount(2, $parser->getDefiners(), "should have 2 definers");
        $this->assertCount(2, $parser->getFilters()["and"], "should have 2 filters");
        $this->assertSame("span > a.title as title", $parser->getDefiners()[0]);
        $this->assertSame("attr(href, div > h1 > span > a) as url", $parser->getDefiners()[1]);
    }

    public function testParseQuery1SelectorWithFilterHasOneLineQuery()
    {
        $query = "
        from ( .item ) define span > a.title as title, attr(href, div > h1 > span > a) as url filter span > a.title has 'lorem'  and span > h1 > 9";

        $parser = new Parser($query);

        $this->assertSame(".item", $parser->getSource()->getRaw());
        $this->assertCount(2, $parser->getDefiners(), "should have 2 definers");
        $this->assertCount(2, $parser->getFilters()["and"], "should have 2 filters");
        $this->assertSame("span > a.title as title", $parser->getDefiners()[0]);
        $this->assertSame("attr(href, div > h1 > span > a) as url", $parser->getDefiners()[1]);
    }

    public function testParseQuery1SelectorWithFilterHasAttr()
    {
        $query = "
        from ( .item )
        define
            span > a.title as title,
            attr(href, div > h1 > span > a) as url
        filter
            span > a.title has 'lorem'  and
            attr(id, span > a) > 9
        ";

        $parser = new Parser($query);

        $this->assertSame(".item", $parser->getSource()->getRaw());
        $this->assertCount(2, $parser->getDefiners(), "should have 2 definers");
        $this->assertCount(2, $parser->getFilters()["and"], "should have 2 filters");
        $this->assertSame("span > a.title as title", $parser->getDefiners()[0]);
        $this->assertSame("attr(href, div > h1 > span > a) as url", $parser->getDefiners()[1]);
    }
}

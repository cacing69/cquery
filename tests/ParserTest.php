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
        $query = '';

        try {
            $parser = new Parser($query);
        } catch (Exception $e) {
            $this->assertSame(CqueryException::class, get_class($e));
            $this->assertSame('empty query provided', $e->getMessage());
        }
    }

    public function testParseQuery1Selector()
    {
        $query = '
        from ( .item )
        define
            span > a.title as title,
            attr(href, div > h1 > span > a) as url
        ';

        $parser = new Parser($query);

        $this->assertSame('.item', $parser->getSource()->getRaw());
        $this->assertCount(2, $parser->getDefiners());
        $this->assertSame('span > a.title as title', $parser->getDefiners()[0]);
        $this->assertSame('attr(href, div > h1 > span > a) as url', $parser->getDefiners()[1]);
    }

    public function testParseQuery1SelectorOneLine()
    {
        $query = '
        from ( .item )
        define
            span > a.title as title,
            attr(href, div > h1 > span > a) as url
        ';

        $parser = new Parser($query);

        $this->assertSame('.item', $parser->getSource()->getRaw());
        $this->assertCount(2, $parser->getDefiners());
        $this->assertSame('span > a.title as title', $parser->getDefiners()[0]);
        $this->assertSame('attr(href, div > h1 > span > a) as url', $parser->getDefiners()[1]);
    }

    public function testParseQuery1ButWithReverseDefinerSelector()
    {
        $query = '
        from ( .item )
        define
            attr(href, div > h1 > span > a) as url,
            span > a.title as title
        ';

        $parser = new Parser($query);

        $this->assertSame('.item', $parser->getSource()->getRaw());
        $this->assertCount(2, $parser->getDefiners());
        $this->assertSame('attr(href, div > h1 > span > a) as url', $parser->getDefiners()[0]);
        $this->assertSame('span > a.title as title', $parser->getDefiners()[1]);
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

        $this->assertSame('.item', $parser->getSource()->getRaw());
        $this->assertCount(2, $parser->getDefiners());
        $this->assertCount(2, $parser->getFilters()['and']);
        $this->assertSame('span > a.title as title', $parser->getDefiners()[0]);
        $this->assertSame('attr(href, div > h1 > span > a) as url', $parser->getDefiners()[1]);
    }

    public function testParseQuery1SelectorWithFilterHasOneLineQuery()
    {
        $query = "
        from ( .item ) define span > a.title as title, attr(href, div > h1 > span > a) as url filter span > a.title has 'lorem'  and span > h1 > 9";

        $parser = new Parser($query);

        $this->assertSame('.item', $parser->getSource()->getRaw());
        $this->assertCount(2, $parser->getDefiners());
        $this->assertCount(2, $parser->getFilters()['and']);
        $this->assertSame('span > a.title as title', $parser->getDefiners()[0]);
        $this->assertSame('attr(href, div > h1 > span > a) as url', $parser->getDefiners()[1]);
    }

    public function testParseQuery1SelectorWithFilterHasAttr()
    {
        $query = "
        from ( .item )
        define
            span > a.title as title,
            int(span > a.qty),
            attr(href, div > h1 > span > a) as url
        filter
            span > a.title has 'lorem'  and
            attr(id, span > a) > 9
        ";

        $parser = new Parser($query);

        $this->assertSame('.item', $parser->getSource()->getRaw());
        $this->assertCount(3, $parser->getDefiners());
        $this->assertCount(2, $parser->getFilters()['and']);
        $this->assertSame('span > a.title as title', $parser->getDefiners()[0]);
        $this->assertSame('int(span > a.qty)', $parser->getDefiners()[1]);
        $this->assertSame('attr(href, div > h1 > span > a) as url', $parser->getDefiners()[2]);
    }

    public function testParseQueryWithReplaceAndAttrDefiner()
    {
        $query = "
        from ( .item )
        define
            attr(href, div > h1 > span > a) as url,
            replace('i am', 'you are', div > h1 > span > a) as _text
        filter
            span > a.title has 'lorem'  and
            attr(id, span > a) > 9
        ";

        $parser = new Parser($query);

        $this->assertSame('.item', $parser->getSource()->getRaw());
        $this->assertCount(2, $parser->getDefiners());
        $this->assertCount(2, $parser->getFilters()['and']);
        $this->assertSame('attr(href, div > h1 > span > a) as url', $parser->getDefiners()[0]);
        $this->assertSame("replace('i am', 'you are', div > h1 > span > a) as _text", $parser->getDefiners()[1]);
    }

    public function testParseQueryWithReplaceAndAttrDefinerButWithoutAlias()
    {
        $query = "
        from ( .item )
        define
            attr(href, div > h1 > span > a),
            replace('i am', 'you are', div > h1 > span > a)
        filter
            span > a.title has 'lorem'  and
            attr(id, span > a) > 9
        ";

        $parser = new Parser($query);

        $this->assertSame('.item', $parser->getSource()->getRaw());
        $this->assertCount(2, $parser->getDefiners());
        $this->assertCount(2, $parser->getFilters()['and']);
        $this->assertSame('attr(href, div > h1 > span > a)', $parser->getDefiners()[0]);
        $this->assertSame("replace('i am', 'you are', div > h1 > span > a)", $parser->getDefiners()[1]);
    }

    public function testParseQueryWithReplaceAndAttrAndAppendNodeDefinerButWithoutAlias()
    {
        $query = "
        from ( .item )
        define
            attr(href, div > h1 > span > a),
            replace('i am', 'you are', div > h1 > span > a),
            append_node(.list > .item, li) as list
        filter
            span > a.title has 'lorem'  and
            attr(id, span > a) > 9
        ";

        $parser = new Parser($query);

        $this->assertSame('.item', $parser->getSource()->getRaw());
        $this->assertCount(3, $parser->getDefiners());
        $this->assertCount(2, $parser->getFilters()['and']);
        $this->assertSame('attr(href, div > h1 > span > a)', $parser->getDefiners()[0]);
        $this->assertSame("replace('i am', 'you are', div > h1 > span > a)", $parser->getDefiners()[1]);
        $this->assertSame('append_node(.list > .item, li) as list', $parser->getDefiners()[2]);
    }

    public function testParseQueryWithReplaceAndAttrAndAppendNodeDefinerReverse1ButWithoutAlias()
    {
        $query = "
        from ( .item )
        define
            attr(href, div > h1 > span > a),
            append_node(.list > .item, li) as list,
            replace('i am', 'you are', div > h1 > span > a)
        filter
            span > a.title has 'lorem'  and
            attr(id, span > a) > 9
        ";

        $parser = new Parser($query);

        $this->assertSame('.item', $parser->getSource()->getRaw());
        $this->assertCount(3, $parser->getDefiners());
        $this->assertCount(2, $parser->getFilters()['and']);
        $this->assertSame('attr(href, div > h1 > span > a)', $parser->getDefiners()[0]);
        $this->assertSame('append_node(.list > .item, li) as list', $parser->getDefiners()[1]);
        $this->assertSame("replace('i am', 'you are', div > h1 > span > a)", $parser->getDefiners()[2]);
    }

    public function testParseQueryWithReplaceAndAttrAndAppendNodeDefinerReverse2ButWithoutAlias()
    {
        $query = "
        from ( .item )
        define
            append_node(.list > .item, li) as list,
            attr(href, div > h1 > span > a),
            replace('i am', 'you are', div > h1 > span > a)
        filter
            span > a.title has 'lorem'  and
            attr(id, span > a) > 9
        ";

        $parser = new Parser($query);

        $this->assertSame('.item', $parser->getSource()->getRaw());
        $this->assertCount(3, $parser->getDefiners());
        $this->assertCount(2, $parser->getFilters()['and']);
        $this->assertSame('append_node(.list > .item, li) as list', $parser->getDefiners()[0]);
        $this->assertSame('attr(href, div > h1 > span > a)', $parser->getDefiners()[1]);
        $this->assertSame("replace('i am', 'you are', div > h1 > span > a)", $parser->getDefiners()[2]);
    }

    public function testParseWithLimit()
    {
        $query = "
        from ( .item )
        define
            append_node(.list > .item, li) as list,
            attr(href, div > h1 > span > a),
            replace('i am', 'you are', div > h1 > span > a)
        filter
            span > a.title has 'lorem'  and
            attr(id, span > a) > 9
        limit 1
        ";

        $parser = new Parser($query);

        $this->assertSame('.item', $parser->getSource()->getRaw());
        $this->assertCount(3, $parser->getDefiners(), 'should have 2 definers');
        $this->assertCount(2, $parser->getFilters()['and']);
        $this->assertSame(1, $parser->getLimit());
        $this->assertSame('append_node(.list > .item, li) as list', $parser->getDefiners()[0]);
        $this->assertSame('attr(href, div > h1 > span > a)', $parser->getDefiners()[1]);
        $this->assertSame("replace('i am', 'you are', div > h1 > span > a)", $parser->getDefiners()[2]);
    }

    public function testParseWithLimitButWithNonNumericValue()
    {
        $query = "
        from ( .item )
        define
            append_node(.list > .item, li) as list,
            attr(href, div > h1 > span > a),
            replace('i am', 'you are', div > h1 > span > a)
        filter
            span > a.title has 'lorem'  and
            attr(id, span > a) > 9
        limit abc
        ";

        try {
            $parser = new Parser($query);
        } catch (Exception $e) {
            $this->assertSame(CqueryException::class, get_class($e));
            $this->assertSame('only integer numeric value allowed when used limit argument.', $e->getMessage());
        }
    }

    public function testParseWithLimitButWithFloatValueDot()
    {
        $query = "
        from ( .item )
        define
            append_node(.list > .item, li) as list,
            attr(href, div > h1 > span > a),
            replace('i am', 'you are', div > h1 > span > a)
        filter
            span > a.title has 'lorem'  and
            attr(id, span > a) > 9
        limit 3.5
        ";

        try {
            $parser = new Parser($query);
        } catch (Exception $e) {
            $this->assertSame(CqueryException::class, get_class($e));
            $this->assertSame('only integer numeric value allowed when used limit argument.', $e->getMessage());
        }
    }

    public function testParseWithLimitButWithFloatValueComma()
    {
        $query = "
        from ( .item )
        define
            append_node(.list > .item, li) as list,
            attr(href, div > h1 > span > a),
            replace('i am', 'you are', div > h1 > span > a)
        filter
            span > a.title has 'lorem'  and
            attr(id, span > a) > 9
        limit 3,5
        ";

        try {
            $parser = new Parser($query);
        } catch (Exception $e) {
            $this->assertSame(CqueryException::class, get_class($e));
            $this->assertSame('only integer numeric value allowed when used limit argument.', $e->getMessage());
        }
    }

    public function testParseUsedFilterWithOrOperator()
    {
        $query = "
        from ( .item )
        define
            append_node(.list > .item, li) as list,
            attr(href, div > h1 > span > a),
            replace('i am', 'you are', div > h1 > span > a)
        filter
            span > a.title has 'lorem'  or
            attr(id, span > a) > 9
        limit 4
        ";

        $parser = new Parser($query);

        $this->assertSame('.item', $parser->getSource()->getRaw());
        $this->assertCount(3, $parser->getDefiners());
        $this->assertCount(2, $parser->getFilters()['or']);
        $this->assertSame(4, $parser->getLimit());
        $this->assertSame('append_node(.list > .item, li) as list', $parser->getDefiners()[0]);
        $this->assertSame('attr(href, div > h1 > span > a)', $parser->getDefiners()[1]);
        $this->assertSame("replace('i am', 'you are', div > h1 > span > a)", $parser->getDefiners()[2]);
    }

    public function testParseWithLimitAndWithoutFilter()
    {
        $query = "
        from ( .item )
        define
            append_node(.list > .item, li) as list,
            attr(href, div > h1 > span > a),
            replace('i am', 'you are', div > h1 > span > a)
        limit 4
        ";

        $parser = new Parser($query);

        $this->assertSame('.item', $parser->getSource()->getRaw());
        $this->assertCount(3, $parser->getDefiners());
        $this->assertSame(4, $parser->getLimit());
        $this->assertSame('append_node(.list > .item, li) as list', $parser->getDefiners()[0]);
        $this->assertSame('attr(href, div > h1 > span > a)', $parser->getDefiners()[1]);
        $this->assertSame("replace('i am', 'you are', div > h1 > span > a)", $parser->getDefiners()[2]);
    }
}

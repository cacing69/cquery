<?php

use Cacing69\Cquery\Cquery;
use Cacing69\Cquery\CqueryException;
use Cacing69\Cquery\Definer;
use Cacing69\Cquery\Filter;
use Exception;
use PHPUnit\Framework\TestCase;

define('SAMPLE_HTML', 'src/Samples/sample.html');

final class SampleTest extends TestCase
{
    public function testCollectFirst()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define(
                'h1 as title',
                'a as description',
                'attr(href, a) as url',
                'attr(class, a) as class'
            )
            ->first();

        $this->assertSame('Title 1', $result['title']);
        $this->assertSame('Href Attribute Example 1', $result['description']);
        $this->assertSame('http://ini-url-1.com', $result['url']);
        $this->assertSame('ini vip class-1', $result['class']);
    }

    public function testWhereHasWithAnyCondition()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define('h1 as title', 'a as description', 'attr(href, a) as url', 'attr(class, a) as class')
            ->filter('attr(class, a)', 'has', 'vip')
            ->first();

        $this->assertSame(4, count($result));
    }

    public function testWhereHasWithAnyConditionButSourceWithAlias()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('(#lorem .link) as _el')
            ->define('h1 as title', 'a as description', 'attr(href, a) as url', 'attr(class, a) as class')
            ->filter('attr(class, a)', 'has', 'vip')
            ->first();

        $this->assertSame(4, count($result));
    }

    public function testGetFooter()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('footer')
            ->define('p')
            ->first();

        $this->assertSame('Copyright 2023', $result['p']);
    }

    public function testReusableElement()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define('h1 as title', 'a as description', 'attr(href, a) as url', 'attr(class, a) as class')
            ->first();

        $result_clone = $data
            ->from('footer')
            ->define('p')
            ->first();

        $this->assertSame('Title 1', $result['title']);
        $this->assertSame('Copyright 2023', $result_clone['p']);
    }

    public function testSelectUsedAlias()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $query = $data
            ->from('(#lorem .link) as _el')
            ->define('a > p as title');

        $first = $query->first();

        $this->assertSame('Lorem pilsum', $first['title']);
    }

    public function testShouldGetAnExceptionNoSourceDefined()
    {
        $this->expectException(CqueryException::class);
        $this->expectExceptionMessage('no source defined');

        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $query = $data
                ->define('_el > a > p as title');
    }

    public function testShouldGetAnExceptionNoDefinerFound()
    {
        $this->expectException(CqueryException::class);
        $this->expectExceptionMessage('no definer found');

        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $query = $data
            ->from('(#lorem .link) as _el')
            ->get();
    }

    public function testMultipleWhereWithUsedOrCondition()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define('h1 as title', 'a as description', 'attr(href, a) as url', 'attr(class, a) as class')
            ->filter('attr(class, a)', 'has', 'vip')
            ->orFilter('attr(class, a)', 'has', 'super')
            ->orFilter('attr(class, a)', 'has', 'blocked')
            ->get();

        $this->assertCount(5, $result);
    }

    public function testMultipleWhereWithUsedAndCondition()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define('h1 as title', 'a as description', 'attr(href, a) as url', 'attr(class, a) as class')
            ->filter('attr(class, a)', 'has', 'vip')
            ->andFilter('attr(class, a)', 'has', 'blocked')
            ->andFilter('attr(class, a)', 'has', 'super')
            ->get();

        $this->assertCount(1, $result);
    }

    public function testFilterWithEqualSign()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define('h1 as title', 'a as description', 'attr(href, a) as url', 'attr(class, a) as class')
            ->filter('attr(class, a)', '=', 'test-1-item')
            ->get();

        $this->assertCount(1, $result);
    }

    public function testWithRefIdAttribute()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define('h1 as title', 'a as description', 'attr(href, a) as url', 'attr(class, a) as class')
            ->filter('attr(ref-id, h1)', '=', '23')
            ->get();

        $this->assertCount(1, $result);
    }

    public function testValueWithRefIdAttribute()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define('h1 as title', 'a as description', 'attr(href, a) as url', 'attr(class, a) as class')
            ->filter('attr(ref-id, h1)', '=', '23')
            ->first();

        $this->assertSame('Title 2', $result['title']);
        $this->assertSame('Href Attribute Example 2 Lorem pilsum', $result['description']);
        $this->assertSame('http://ini-url-2.com', $result['url']);
        $this->assertSame('vip class-2 nih tenied', $result['class']);
    }

    public function testWithLikeContains()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define('h1 as title', 'a as description', 'attr(href, a) as url', 'attr(class, a) as class')
            ->filter('attr(class, a)', 'like', '%ni%')
            ->get();

        $this->assertCount(4, $result);
    }

    public function testWithLikeStartWith()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define('h1 as title', 'a as description', 'attr(href, a) as url', 'attr(class, a) as class')
            ->filter('attr(class, a)', 'like', 'pre%')
            ->get();

        $this->assertCount(4, $result);
    }

    public function testWithLikeEndWith()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define('h1 as title', 'a as description', 'attr(href, a) as url', 'attr(class, a) as class')
            ->filter('attr(class, a)', 'like', '%ed')
            ->get();

        $this->assertCount(6, $result);
    }

    public function testWithLikeEndWithNied()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define('h1 as title', 'a as description', 'attr(href, a) as url', 'attr(class, a) as class')
            ->filter('attr(class, a)', 'like', '%nied')
            ->get();

        $this->assertCount(2, $result);
    }

    public function testWithLessThan()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define('h1 as title', 'a as description', 'attr(href, a) as url', 'attr(class, a) as class', 'attr(customer-id, a)')
            ->filter('attr(customer-id, a)', '<', 18)
            ->get();

        $this->assertCount(2, $result);
    }

    public function testWithLessThanEqual()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define('h1 as title', 'a as description', 'attr(href, a) as url', 'attr(class, a) as class')
            ->filter('attr(customer-id, a)', '<=', '18')
            ->get();

        $this->assertCount(3, $result);
    }

    public function testWithGreaterThan()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define('h1 as title', 'a as description', 'attr(href, a) as url', 'attr(class, a) as class')
            ->filter('attr(customer-id, a)', '>', '16')
            ->get();

        $this->assertCount(2, $result);
    }

    public function testWithGreaterThanEquals()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define('h1 as title', 'a as description', 'attr(href, a) as url', 'attr(class, a) as class')
            ->filter('attr(customer-id, a)', '>=', 16)
            ->get();

        $this->assertCount(3, $result);
    }

    public function testWithRegex()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define('h1 as title', 'a as description', 'attr(href, a) as url', 'attr(class, a) as class')
            ->filter('attr(regex-test, a)', 'regex', "/[a-z]+\-[0-9]+\-[a-z]+/im")
            ->get();

        $this->assertCount(3, $result);
    }

    public function testForNewColumnDefiner()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define('attr(href, a) as url', 'attr(class, a) as class')
            ->filter('attr(regex-test, a)', 'regex', "/[a-z]+\-[0-9]+\-[a-z]+/im")
            ->get();

        $this->assertCount(3, $result);
    }

    public function testNewLengthExpression()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define(
                'length(h1) as length'
            )
            ->get();

        $this->assertCount(9, $result);
        $this->assertSame(7, $result[0]['length']);
        $this->assertSame(7, $result[1]['length']);
        $this->assertSame(7, $result[2]['length']);
        $this->assertSame(8, $result[3]['length']);
        $this->assertSame(8, $result[4]['length']);
        $this->assertSame(9, $result[5]['length']);
        $this->assertSame(9, $result[6]['length']);
        $this->assertSame(9, $result[7]['length']);
        $this->assertSame(5, $result[8]['length']);
    }

    public function testNewExpressionWithWhereEquals()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define('attr(class, a) as class_a_p', 'attr(class, a) as url', 'length(h1) as length')
            ->filter('a', '=', 'Href Attribute Example 90')
            ->get();

        $this->assertCount(1, $result);
    }

    public function testWithClosureDefiner()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $definer = new Definer('h1', 'alias', function ($value) {
            return str_replace(' ', '-', strtoupper($value)).'-FROM-CLOSURE';
        });

        $result = $data
            ->from('#lorem .link')
            ->define('upper(a)', $definer)
            ->first();

        $this->assertSame('HREF ATTRIBUTE EXAMPLE 1', $result['upper_a']);
        $this->assertSame('TITLE-1-FROM-CLOSURE', $result['alias']);
    }

    public function testPickWithDefinerUsedClosure()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $closure = function ($node) {
            return $node.'-XXX';
        };

        $result = $data
            ->from('#lorem .link')
            ->define(
                new Definer('a', 'key_2', $closure)
            )
            ->first();

        $this->assertSame('Href Attribute Example 1 -XXX', $result['key_2']);
    }

    public function testPickCustomerId()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define('attr(customer-id, a) as cust_id', 'attr(class, a) as class')
            ->filter('attr(customer-id, a)', '<=', '18')
            ->get();

        $this->assertCount(3, $result);
    }

    public function testUsedFilterLength()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define('h1 as title')
            ->filter('length(h1)', '=', 5)
            ->get();

        $this->assertCount(1, $result);
    }

    public function testUsedFilterAnonymousFunction()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define('h1 as title', 'a as description', 'attr(href, a) as url', 'attr(class, a) as class')
            ->filter('h1', function ($e) {
                return $e === 'Title 3';
            })
            ->get();

        $this->assertCount(1, $result);
    }

    public function testUsedFilterErrorAnonymousFunctionWithoutSelector()
    {
        $this->expectException(CqueryException::class);
        $this->expectExceptionMessage('when used closure, u need to place it on second parameter');

        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
        ->from('#lorem .link')
        ->define('h1 as title', 'a as description', 'attr(href, a) as url', 'attr(class, a) as class')
        ->filter(function ($e) {
            return $e === 'Title 3';
        }, 'h1')
        ->get();
    }

    public function testCqueryWithNestedDefinerFunctionLengthAndAttr()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define('length(attr(class, a)) as length_attr_class_a')
            ->get();

        $this->assertSame(15, $result[0]['length_attr_class_a']);
        $this->assertSame(22, $result[1]['length_attr_class_a']);
        $this->assertSame(15, $result[2]['length_attr_class_a']);
        $this->assertSame(25, $result[3]['length_attr_class_a']);
        $this->assertSame(31, $result[4]['length_attr_class_a']);
        $this->assertSame(28, $result[5]['length_attr_class_a']);
        $this->assertSame(22, $result[6]['length_attr_class_a']);
        $this->assertSame(11, $result[7]['length_attr_class_a']);
        $this->assertSame(23, $result[8]['length_attr_class_a']);
    }

    public function testCqueryWithUpperFunction()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define('upper(h1)')
            ->get();

        $this->assertSame('TITLE 1', $result[0]['upper_h1']);
        $this->assertSame('TITLE 2', $result[1]['upper_h1']);
        $this->assertSame('TITLE 3', $result[2]['upper_h1']);
        $this->assertSame('TITLE 11', $result[3]['upper_h1']);
        $this->assertSame('TITLE 22', $result[4]['upper_h1']);
        $this->assertSame('TITLE 323', $result[5]['upper_h1']);
        $this->assertSame('TITLE 331', $result[6]['upper_h1']);
        $this->assertSame('TITLE 331', $result[7]['upper_h1']);
        $this->assertSame('12345', $result[8]['upper_h1']);
    }

    public function testCqueryWithReplaceFromMultiToSingleFunction()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define("replace(['Title', '331'], 'LOREM', h1)  as title")
            ->get();

        $this->assertSame('LOREM 1', $result[0]['title']);
        $this->assertSame('LOREM 2', $result[1]['title']);
        $this->assertSame('LOREM 3', $result[2]['title']);
        $this->assertSame('LOREM 11', $result[3]['title']);
        $this->assertSame('LOREM 22', $result[4]['title']);
        $this->assertSame('LOREM 323', $result[5]['title']);
        $this->assertSame('LOREM LOREM', $result[6]['title']);
        $this->assertSame('LOREM LOREM', $result[7]['title']);
        $this->assertSame('12345', $result[8]['title']);
    }

    public function testCqueryWithReplaceFromMultiToSingleButWithNestedAttrFunction()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define("replace('http', 'https', attr(href, a))  as title")
            ->get();

        $this->assertSame('https://ini-url-1.com', $result[0]['title']);
        $this->assertSame('https://ini-url-2.com', $result[1]['title']);
        $this->assertSame('https://ini-url-3.com', $result[2]['title']);
        $this->assertSame('https://ini-url-11.com', $result[3]['title']);
        $this->assertSame('https://ini-url-22.com', $result[4]['title']);
        $this->assertSame('https://ini-url-33-1.com', $result[5]['title']);
        $this->assertSame('https://ini-url-33-2.com', $result[6]['title']);
        $this->assertSame('https://ini-url-33-2.com', $result[7]['title']);
        $this->assertSame('https://ini-url-33-0.com', $result[8]['title']);
    }

    public function testCqueryWithNestedThreeDefiner()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem > .link')
            ->define('reverse(length(attr(class, a))) as reverse_length_attr_class_a')
            ->get();

        $this->assertEquals(51, $result[0]['reverse_length_attr_class_a']);
        $this->assertEquals(22, $result[1]['reverse_length_attr_class_a']);
        $this->assertEquals(51, $result[2]['reverse_length_attr_class_a']);
        $this->assertEquals(52, $result[3]['reverse_length_attr_class_a']);
        $this->assertEquals(13, $result[4]['reverse_length_attr_class_a']);
        $this->assertEquals(82, $result[5]['reverse_length_attr_class_a']);
        $this->assertEquals(22, $result[6]['reverse_length_attr_class_a']);
        $this->assertEquals(11, $result[7]['reverse_length_attr_class_a']);
        $this->assertEquals(32, $result[8]['reverse_length_attr_class_a']);
    }

    public function testCqueryResultSelectorSingleId()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#list-test-child > div')
            ->define(
                'span as title',
                'span > .pluck as title_info'
            )
            ->get();

        $this->assertCount(3, $result);

        $this->assertSame('parent 1 text-pluck-1', $result[0]['title']);
        $this->assertSame('text-pluck-1', $result[0]['title_info']);

        $this->assertSame('parent 2', $result[1]['title']);
        $this->assertSame(null, $result[1]['title_info']);

        $this->assertSame('parent 3 text-pluck-3', $result[2]['title']);
        $this->assertSame('text-pluck-3', $result[2]['title_info']);
    }

    public function testCqueryResultSelectorSingleIdWithFilter()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#list-test-child > div')
            ->define('span > .pluck as title')
            ->filter('span > .pluck', '=', 'text-pluck-1')
            ->get();

        $this->assertCount(1, $result);
    }

    public function testCqueryMoreForDoc()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $date = date('Y-m-d H:i:s');

        $result = $data
            ->from('#lorem .link')
            ->define(
                'upper(h1) as title_upper',
                new Definer('a', 'col_2', function ($value) use ($date) {
                    return "{$value} fetched on: {$date}";
                })
            )
            ->filter('attr(class, a)', 'has', 'vip')
            ->limit(2)
            ->get();

        $this->assertCount(2, $result);
    }

    public function testChangePickToDefiner()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem > .link')
            ->define(
                'a as title',
                new Definer('h1', '_test'),
                new Definer('h1', '_closure', function ($e) {
                    return strtoupper($e).' ['.strrev(strtoupper($e)).']';
                })
            )
            ->get();

        $this->assertCount(9, $result);
        $this->assertArrayHasKey('title', $result[0]);
        $this->assertArrayHasKey('_test', $result[0]);
        $this->assertSame('TITLE 1 [1 ELTIT]', $result[0]['_closure']);
    }

    public function testDefinerErrorAliasOnFirstParameter()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        try {
            $result = $data
                ->from('#lorem > .link')
                ->define(
                    'a as title',
                    new Definer('h1 as _test')
                )
                ->get();
        } catch (Exception $e) {
            $this->assertSame(CqueryException::class, get_class($e));
            $this->assertSame('error define, please set alias on second parameter', $e->getMessage());
        }
    }

    public function testWithFilterClass()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem > .link')
            ->define(
                'a as title',
                new Definer('h1', '_test')
            )
            ->filter(
                new Filter('h1', '=', 'Title 331')
            )
            ->get();

        $this->assertCount(2, $result);
    }

    public function testWithReplaceArrayMultiWithArraySingleClass()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem > .link')
            ->define(
                "replace(['Attribute', 'Example'], ['Replaced'], a) as text",
            )
            ->get();

        $this->assertCount(9, $result);
        $this->assertSame('Href Replaced Replaced 1', $result[0]['text']);
        $this->assertSame('Href Replaced Replaced 2 Lorem pilsum', $result[1]['text']);
        $this->assertSame('Href Replaced Replaced 4', $result[2]['text']);
        $this->assertSame('Href Replaced Replaced 78', $result[3]['text']);
        $this->assertSame('Href Replaced Replaced 90', $result[4]['text']);
        $this->assertSame('Href Replaced Replaced 5', $result[5]['text']);
        $this->assertSame('Href Replaced Replaced 51', $result[6]['text']);
        $this->assertSame('Href Replaced Replaced 51', $result[7]['text']);
        $this->assertSame('Href Replaced Replaced 52', $result[8]['text']);
    }

    public function testWithLowerFunc()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem > .link')
            ->define(
                'lower(h1) as title',
                'lower(attr(data-check, h1)) as data_check',
            )
            ->get();

        $this->assertCount(9, $result);
        $this->assertSame('title 1', $result[0]['title']);
        $this->assertSame('title 2', $result[1]['title']);
        $this->assertSame('title 3', $result[2]['title']);
        $this->assertSame('title 11', $result[3]['title']);
        $this->assertSame('title 22', $result[4]['title']);
        $this->assertSame('title 323', $result[5]['title']);
        $this->assertSame('title 331', $result[6]['title']);
        $this->assertSame('title 331', $result[7]['title']);
        $this->assertSame('12345', $result[8]['title']);

        // CHECK ATTR
        $this->assertSame('ini test lagi', $result[0]['data_check']);
        $this->assertSame('ini juga test 2x', $result[1]['data_check']);
        $this->assertSame('ini juga test 2x3x', $result[2]['data_check']);
        $this->assertSame(null, $result[3]['data_check']);
        $this->assertSame(null, $result[4]['data_check']);
        $this->assertSame(null, $result[5]['data_check']);
        $this->assertSame(null, $result[6]['data_check']);
        $this->assertSame(null, $result[7]['data_check']);
        $this->assertSame(null, $result[8]['data_check']);
    }

    public function testCqueryWithTableWithUrl()
    {
        $content = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($content);

        $resultAll = $data
            ->from('#table-test')
            ->define(
                'td:nth-child(1) as first_name',
                'td:nth-child(2) as last_name',
                'td:nth-child(3) as email',
                'td:nth-child(4) as status',
            )
            ->get();

        $resultActive = $data
            ->from('#table-test')
            ->define(
                'td:nth-child(1) as first_name',
                'td:nth-child(2) as last_name',
                'td:nth-child(3) as email',
                'td:nth-child(4) as status',
            )->filter('td:nth-child(4)', '=', 'active')
            ->get();

        $resultInAactive = $data
            ->from('#table-test')
            ->define(
                'td:nth-child(1) as first_name',
                'td:nth-child(2) as last_name',
                'td:nth-child(3) as email',
                'td:nth-child(4) as status',
            )->filter('td:nth-child(4)', '=', 'inactive')
            ->get();

        $this->assertcount(3, $resultAll);
        $this->assertcount(1, $resultActive);
        $this->assertcount(2, $resultInAactive);
        $this->assertSame(count($resultInAactive), 3 - count($resultActive));
    }

    public function testScrapeQuotesToScrapeWithWrongDefiner()
    {
        $this->expectException(CqueryException::class);
        $this->expectExceptionMessage('error query definer, there are no matching rows each column.');

        $content = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($content);

        $result = $data
            ->from('.col-md-8 > .quote')
            ->define(
                'span.text as text',
                'span:nth-child(2) > small as author',
                '(div > .tags > a)  as tags',
            )
            ->get();
    }

    public function testScrapeQuotesToScrapeWithAppendNodeDefiner()
    {
        $content = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($content);

        $result = $data
            ->from('.col-md-8 > .quote')
            ->define(
                'span.text as text',
                'span:nth-child(2) > small as author',
                'append_node(div > .tags, a)  as tags',
            )
            ->get()
            ->toArray();

        $this->assertCount(10, $result);
        $this->assertCount(4, $result[0]['tags']);
        $this->assertCount(2, $result[1]['tags']);
    }

    public function testScrapeQuotesToScrapeWithAppendNodeNestwedWithIntDefiner()
    {
        $content = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($content);

        $result = $data
            ->from('.col-md-8 > .quote')
            ->define(
                'span.text as text',
                'span:nth-child(2) > small as author',
                'append_node(div > .tags, int(attr(data-id, a)))  as tags',
            )
            ->first();

        $this->assertCount(4, $result['tags']);
        $this->assertSame(0, $result['tags'][0]);
        $this->assertSame(0, $result['tags'][1]);
        $this->assertSame(0, $result['tags'][2]);
        $this->assertSame(0, $result['tags'][3]);
    }

    public function testScrapeQuotesToScrapeWithAppendNodeHrefAttributeDefiner()
    {
        $content = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($content);

        $result = $data
            ->from('.col-md-8 > .quote')
            ->define(
                'span.text as text',
                'span:nth-child(2) > small as author',
                'append_node(div > .tags, a)  as tags',
                'append_node(div > .tags, attr(href, a))  as tags_url',
            )
            ->get();

        $this->assertCount(10, $result);
        $this->assertCount(4, $result[0]['tags']);
        $this->assertCount(2, $result[1]['tags']);
        $this->assertCount(5, $result[2]['tags']);
        $this->assertCount(4, $result[3]['tags']);
        $this->assertCount(2, $result[4]['tags']);
        $this->assertCount(3, $result[5]['tags']);
        $this->assertCount(2, $result[6]['tags']);
        $this->assertCount(4, $result[7]['tags']);
        $this->assertCount(1, $result[8]['tags']);
        $this->assertCount(3, $result[9]['tags']);

        $this->assertCount(4, $result[0]['tags_url']);
        $this->assertCount(2, $result[1]['tags_url']);
        $this->assertCount(5, $result[2]['tags_url']);
        $this->assertCount(4, $result[3]['tags_url']);
        $this->assertCount(2, $result[4]['tags_url']);
        $this->assertCount(3, $result[5]['tags_url']);
        $this->assertCount(2, $result[6]['tags_url']);
        $this->assertCount(4, $result[7]['tags_url']);
        $this->assertCount(1, $result[8]['tags_url']);
        $this->assertCount(3, $result[9]['tags_url']);
    }

    public function testScrapeQuotesToScrapeWithAppendNodeAndAppendOnKeyDefiner()
    {
        $content = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($content);

        $result = $data
            ->from('.col-md-8 > .quote')
            ->define(
                'span.text as text',
                'append_node(div > .tags, a) as _tags',
                'append_node(div > .tags, a) as tags.*.text',
                'append_node(div > .tags, attr(href, a)) as tags.*.url', // * means each index, for now ots limitd only one level
            )
            ->get();

        $this->assertCount(10, $result);
        $this->assertCount(4, $result[0]['tags']);

        $this->assertSame('change', $result[0]['tags'][0]['text']);
        $this->assertSame('/tag/change/page/1/', $result[0]['tags'][0]['url']);

        $this->assertSame('deep-thoughts', $result[0]['tags'][1]['text']);
        $this->assertSame('/tag/deep-thoughts/page/1/', $result[0]['tags'][1]['url']);

        $this->assertSame('thinking', $result[0]['tags'][2]['text']);
        $this->assertSame('/tag/thinking/page/1/', $result[0]['tags'][2]['url']);

        $this->assertSame('world', $result[0]['tags'][3]['text']);
        $this->assertSame('/tag/world/page/1/', $result[0]['tags'][3]['url']);
    }

    public function testScrapeQuotesToScrapeWithAppendNodeArrayDefiner()
    {
        $content = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($content);

        $result = $data
            ->from('.col-md-8 > .quote')
            ->define(
                'span.text as text',
                'append_node(div > .tags, a) as tags.key',
            )
            ->get();

        $this->assertCount(10, $result);

        $this->assertCount(4, $result[0]['tags']['key']);
        $this->assertCount(2, $result[1]['tags']['key']);
        $this->assertCount(5, $result[2]['tags']['key']);
        $this->assertCount(4, $result[3]['tags']['key']);
        $this->assertCount(2, $result[4]['tags']['key']);
        $this->assertCount(3, $result[5]['tags']['key']);
        $this->assertCount(2, $result[6]['tags']['key']);
        $this->assertCount(4, $result[7]['tags']['key']);
        $this->assertCount(1, $result[8]['tags']['key']);
        $this->assertCount(3, $result[9]['tags']['key']);
    }

    public function testScrapeQuotesToScrapeWithReplaceDefiner()
    {
        $content = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($content);

        $result = $data
            ->from('.col-md-8 > .quote')
            ->define(
                "replace('The ', 'Lorem ', span.text) as text",
            )
            ->get();

        $this->assertCount(10, $result);
        $this->assertSame('Lorem world as we have created it is a process of our thinking. It cannot be changed without changing our thinking.', $result[0]['text']);
        $this->assertSame('It is our choices, Harry, that show what we truly are, far more than our abilities.', $result[1]['text']);
        $this->assertSame('There are only two ways to live your life. One is as though nothing is a miracle. Lorem other is as though everything is a miracle.', $result[2]['text']);
        $this->assertSame('Lorem person, be it gentleman or lady, who has not pleasure in a good novel, must be intolerably stupid.', $result[3]['text']);
        $this->assertSame("Imperfection is beauty, madness is genius and it's better to be absolutely ridiculous than absolutely boring.", $result[4]['text']);
        $this->assertSame('Try not to become a man of success. Rather become a man of value.', $result[5]['text']);
        $this->assertSame('It is better to be hated for what you are than to be loved for what you are not.', $result[6]['text']);
        $this->assertSame("I have not failed. I've just found 10,000 ways that won't work.", $result[7]['text']);
        $this->assertSame("A woman is like a tea bag; you never know how strong it is until it's in hot water.", $result[8]['text']);
        $this->assertSame('A day without sunshine is like, you know, night.', $result[9]['text']);
    }

    public function testScrapeQuotesToScrapeWithReplaceArrayDefiner()
    {
        $content = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($content);

        $result = $data
            ->from('.col-md-8 > .quote')
            ->define(
                "replace(['The ', 'are'], ['Please ', 'son'], span.text) as text",
            )
            ->get();

        $this->assertCount(10, $result);
        $this->assertSame('Please world as we have created it is a process of our thinking. It cannot be changed without changing our thinking.', $result[0]['text']);
        $this->assertSame('It is our choices, Harry, that show what we truly son, far more than our abilities.', $result[1]['text']);
        $this->assertSame('There son only two ways to live your life. One is as though nothing is a miracle. Please other is as though everything is a miracle.', $result[2]['text']);
        $this->assertSame('Please person, be it gentleman or lady, who has not pleasure in a good novel, must be intolerably stupid.', $result[3]['text']);
        $this->assertSame("Imperfection is beauty, madness is genius and it's better to be absolutely ridiculous than absolutely boring.", $result[4]['text']);
        $this->assertSame('Try not to become a man of success. Rather become a man of value.', $result[5]['text']);
        $this->assertSame('It is better to be hated for what you son than to be loved for what you son not.', $result[6]['text']);
        $this->assertSame("I have not failed. I've just found 10,000 ways that won't work.", $result[7]['text']);
        $this->assertSame("A woman is like a tea bag; you never know how strong it is until it's in hot water.", $result[8]['text']);
        $this->assertSame('A day without sunshine is like, you know, night.', $result[9]['text']);
    }

    public function testCallDefinerTwiceMustGotAnException()
    {
        $this->expectException(CqueryException::class);
        $this->expectExceptionMessage('cannot call method define twice.');

        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('footer')
            ->define('p')
            ->define('p')
            ->first();
    }

    public function testCallFromTwiceMustGotAnException()
    {
        $this->expectException(CqueryException::class);
        $this->expectExceptionMessage('cannot call method from twice.');

        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('footer')
            ->from('footer > .lorem')
            ->define('p')
            ->first();
    }

    public function testWithStaticValueInDefiner()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem > .link')
            ->define(
                'a as title',
                new Definer('h1', '_test'),
                "'this_is_static' as static"
            )
            ->filter(
                new Filter('h1', '=', 'Title 331')
            )
            ->get();

        $this->assertCount(2, $result);
        $this->assertSame('this_is_static', $result[0]['static']);
        $this->assertSame('this_is_static', $result[1]['static']);
    }

    public function testCqueryWithEachMethod()
    {
        $content = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($content);

        $result = $data
            ->from('#table-test')
            ->define(
                'td:nth-child(1) as first_name',
                'td:nth-child(2) as last_name',
                'td:nth-child(3) as email',
                'td:nth-child(4) as status',
            )
            ->eachItem(function ($item, $i) {
                $item['new_key'] = "key-{$i}";

                return $item;
            })
            ->get();

        $this->assertCount(3, $result);
        $this->assertSame('key-0', $result[0]['new_key']);
        $this->assertSame('key-1', $result[1]['new_key']);
        $this->assertSame('key-2', $result[2]['new_key']);
    }

    public function testCqueryOnObtainedResults()
    {
        $content = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($content);

        $result = $data
            ->from('#table-test')
            ->define(
                'td:nth-child(1) as first_name',
                'td:nth-child(2) as last_name',
                'td:nth-child(3) as email',
                'td:nth-child(4) as status',
            )
            ->onObtainedResults(function ($results) {
                foreach ($results as $key => $value) {
                    $results[$key]['new_key'] = "key-{$key}";
                }

                return $results;
            })
            ->get();

        $this->assertCount(3, $result);
        $this->assertSame('key-0', $result[0]['new_key']);
        $this->assertSame('key-1', $result[1]['new_key']);
        $this->assertSame('key-2', $result[2]['new_key']);
    }

    public function testCqueryWithIntegerExpression()
    {
        $content = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($content);

        $result = $data
            ->from('#table-test')
            ->define(
                'int(td:nth-child(5)) as value',
            )
            ->get();

        $this->assertCount(3, $result);
        $this->assertSame(3, $result[0]['value']);
        $this->assertSame(7, $result[1]['value']);
        $this->assertSame(2, $result[2]['value']);
    }

    public function testCqueryWithStringExpression()
    {
        $content = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($content);

        $result = $data
            ->from('#table-test')
            ->define(
                'str(td:nth-child(5)) as value',
            )
            ->get();

        $this->assertCount(3, $result);
        $this->assertSame('3', $result[0]['value']);
        $this->assertSame('7', $result[1]['value']);
        $this->assertSame('2', $result[2]['value']);
    }

    public function testCqueryWithFloatExpression()
    {
        $content = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($content);

        $result = $data
            ->from('#table-test')
            ->define(
                'float(td:nth-child(6)) as floatval',
            )
            ->get();

        $this->assertCount(3, $result);
        $this->assertSame(3.0, $result[0]['floatval']);
        $this->assertSame(7.9, $result[1]['floatval']);
        $this->assertSame(2.7, $result[2]['floatval']);
    }

    public function testUsedRawMethod()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $query = 'from (#lorem .link)
                    define
                        h1 as title,
                        a as description,
                        attr(href, a) as url,
                        attr(class, a) as class
                    ';

        $result = $data
            ->raw($query);

        $this->assertCount(9, $result);
    }

    public function testUsedRawMethodUsed1Filter()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $query = "from (#lorem .link)
                    define
                        h1 as title,
                        a as description,
                        attr(href, a) as url,
                        attr(class, a) as class
                    filter
                        attr(class, a) has 'vip'
                    ";

        $result = $data
            ->raw($query);

        $this->assertCount(4, $result);
    }

    public function testWithNestedData()
    {
        $this->expectException(CqueryException::class);
        $this->expectExceptionMessage('the number of rows in query result for this object is not the same as the previous query.');

        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $query = '
                from (.nested-content)
                define
                    append_node(ul.nested-list > li > ul > li > ul, attr(href, li > a)) as data.*.url_grand_child,
                    append_node(ul.nested-list > li > ul > li > ul, li > p) as data.*.p_grand_child,
                ';

        $result = $data
            ->raw($query);
    }

    public function testPluck()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $query = "from (#lorem .link)
                    define
                        h1 as title,
                        a as description,
                        attr(href, a) as url,
                        attr(class, a) as class
                    filter
                        attr(class, a) has 'vip'
                    ";

        $result = $data
            ->raw($query);

        $pluck = $result->pluck('title')->toArray();

        $this->assertCount(4, $pluck);
        $this->assertsame('Title 1', $pluck[0]);
        $this->assertsame('Title 2', $pluck[1]);
        $this->assertsame('Title 11', $pluck[2]);
        $this->assertsame('Title 22', $pluck[3]);
    }

    public function testCollectLast()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define(
                'h1 as title',
                'a as description',
                'attr(href, a) as url',
                'attr(class, a) as class'
            )
            ->last();

        $this->assertSame('12345', $result['title']);
        $this->assertSame('Href Attribute Example 52', $result['description']);
        $this->assertSame('http://ini-url-33-0.com', $result['url']);
        $this->assertSame('premium class-32 denied', $result['class']);
    }

    public function testFilterWithDataCustomAttrIdIntValue()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define(
                'h1 as title',
                'a as description',
                'attr(href, a) as url',
                'attr(class, a) as class'
            )
            ->filter('attr(data-custom-attr-id, a)', '=', 12)
            ->get();

        $this->assertCount(1, $result);
    }

    public function testWithAppendUsedSelector()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define(
                'h1 as title',
                'append(#title) as head'
            )
            ->get();

        $this->assertCount(9, $result);

        $this->assertSame('MAIN TITLE', $result[0]['head']);
        $this->assertSame('MAIN TITLE', $result[1]['head']);
        $this->assertSame('MAIN TITLE', $result[2]['head']);
        $this->assertSame('MAIN TITLE', $result[3]['head']);
        $this->assertSame('MAIN TITLE', $result[4]['head']);
        $this->assertSame('MAIN TITLE', $result[5]['head']);
        $this->assertSame('MAIN TITLE', $result[6]['head']);
        $this->assertSame('MAIN TITLE', $result[7]['head']);
        $this->assertSame('MAIN TITLE', $result[8]['head']);
    }

    public function testWithAppendNestedUsedSelector()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define(
                'h1 as title',
                'append(attr(class, #title)) as head_class'
            )
            ->get();

        $this->assertCount(9, $result);

        $this->assertSame('main-txt content-title', $result[0]['head_class']);
        $this->assertSame('main-txt content-title', $result[1]['head_class']);
        $this->assertSame('main-txt content-title', $result[2]['head_class']);
        $this->assertSame('main-txt content-title', $result[3]['head_class']);
        $this->assertSame('main-txt content-title', $result[4]['head_class']);
        $this->assertSame('main-txt content-title', $result[5]['head_class']);
        $this->assertSame('main-txt content-title', $result[6]['head_class']);
        $this->assertSame('main-txt content-title', $result[7]['head_class']);
        $this->assertSame('main-txt content-title', $result[8]['head_class']);
    }

    public function testWithIntWithAppendExpressionUsedSelector()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define(
                'h1 as title',
                'int(append(attr(data-id, #title))) as head_id'
            )
            ->get();

        $this->assertCount(9, $result);

        $this->assertSame(9, $result[0]['head_id']);
        $this->assertSame(9, $result[1]['head_id']);
        $this->assertSame(9, $result[2]['head_id']);
        $this->assertSame(9, $result[3]['head_id']);
        $this->assertSame(9, $result[4]['head_id']);
        $this->assertSame(9, $result[5]['head_id']);
        $this->assertSame(9, $result[6]['head_id']);
        $this->assertSame(9, $result[7]['head_id']);
        $this->assertSame(9, $result[8]['head_id']);
    }

    public function testWithAppendWithIntExpressionUsedSelector()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define(
                'h1 as title',
                'append(int(attr(data-id, #title))) as head_id'
            )
            ->get();

        $this->assertCount(9, $result);

        $this->assertSame(9, $result[0]['head_id']);
        $this->assertSame(9, $result[1]['head_id']);
        $this->assertSame(9, $result[2]['head_id']);
        $this->assertSame(9, $result[3]['head_id']);
        $this->assertSame(9, $result[4]['head_id']);
        $this->assertSame(9, $result[5]['head_id']);
        $this->assertSame(9, $result[6]['head_id']);
        $this->assertSame(9, $result[7]['head_id']);
        $this->assertSame(9, $result[8]['head_id']);
    }

    public function testWithAppendWithDoubleQupteExpression()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define(
                'h1 as title',
                '"staticValue" as static_value'
            )
            ->get();

        $this->assertCount(9, $result);

        $this->assertSame('staticValue', $result[0]['static_value']);
        $this->assertSame('staticValue', $result[1]['static_value']);
        $this->assertSame('staticValue', $result[2]['static_value']);
        $this->assertSame('staticValue', $result[3]['static_value']);
        $this->assertSame('staticValue', $result[4]['static_value']);
        $this->assertSame('staticValue', $result[5]['static_value']);
        $this->assertSame('staticValue', $result[6]['static_value']);
        $this->assertSame('staticValue', $result[7]['static_value']);
        $this->assertSame('staticValue', $result[8]['static_value']);
    }

    public function testWithStaticNumericExpression()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define(
                'h1 as title',
                '19 as static_number'
            )
            ->get();

        $this->assertCount(9, $result);

        $this->assertSame(19, $result[0]['static_number']);
        $this->assertSame(19, $result[1]['static_number']);
        $this->assertSame(19, $result[2]['static_number']);
        $this->assertSame(19, $result[3]['static_number']);
        $this->assertSame(19, $result[4]['static_number']);
        $this->assertSame(19, $result[5]['static_number']);
        $this->assertSame(19, $result[6]['static_number']);
        $this->assertSame(19, $result[7]['static_number']);
        $this->assertSame(19, $result[8]['static_number']);
    }

    public function testWithStaticFloatExpression()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define(
                'h1 as title',
                '1.9 as static_number'
            )
            ->get();

        $this->assertCount(9, $result);

        $this->assertSame(1.9, $result[0]['static_number']);
        $this->assertSame(1.9, $result[1]['static_number']);
        $this->assertSame(1.9, $result[2]['static_number']);
        $this->assertSame(1.9, $result[3]['static_number']);
        $this->assertSame(1.9, $result[4]['static_number']);
        $this->assertSame(1.9, $result[5]['static_number']);
        $this->assertSame(1.9, $result[6]['static_number']);
        $this->assertSame(1.9, $result[7]['static_number']);
        $this->assertSame(1.9, $result[8]['static_number']);
    }

    public function testSaveWithCsvWriter()
    {
        $this->markTestSkipped();

        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define(
                'h1 as title',
                '1.9 as static_number'
            )
            ->save('.cached/test_write.csv');

        $this->assertFileExists('.cached/test_write.csv');
    }

    public function testWithFilterNested()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from('#lorem .link')
            ->define(
                'h1 as title',
                'a as description',
                'attr(href, a) as url',
                'attr(class, a) as class'
            )
            // ->filter(function ($query) {
            //     $query
            //         ->filter("attr(class, a)", "has", "vip")
            //         ->andFilter("attr(data-custom-attr-id, a)", "=", 12);
            // })
            ->last();

        $this->assertSame('12345', $result['title']);
        $this->assertSame('Href Attribute Example 52', $result['description']);
        $this->assertSame('http://ini-url-33-0.com', $result['url']);
        $this->assertSame('premium class-32 denied', $result['class']);
    }
}

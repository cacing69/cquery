<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Cquery;
use Cacing69\Cquery\Definer;
use Cacing69\Cquery\CqueryException;
use Cacing69\Cquery\Filter;
use Exception;
use PHPUnit\Framework\TestCase;

define("SAMPLE_HTML", "src/Samples/sample.html");

final class SampleTest extends TestCase
{
    public function testCollectFirst()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define(
                "h1 as title",
                "a as description",
                "attr(href, a) as url",
                "attr(class, a) as class"
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
            ->from("#lorem .link")
            ->define("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(class, a)", "has", "vip")
            ->first();

        $this->assertSame(4, count($result));
    }

    public function testGetFooter()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("footer")
            ->define("p")
            ->first();

        $this->assertSame('Copyright 2023', $result["p"]);
    }

    public function testReusableElement()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->first();

        $result_clone = $data
            ->from("footer")
            ->define("p")
            ->first();

        $this->assertSame('Title 1', $result["title"]);
        $this->assertSame('Copyright 2023', $result_clone["p"]);
    }

    public function testSelectUsedAlias()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $query = $data
            ->from("(#lorem .link) as _el")
            ->define("a > p as title");

        $first = $query->first();

        $this->assertSame("Lorem pilsum", $first['title']);
    }

    public function testShouldGetAnExceptionNoSourceDefined()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        try {
            $query = $data
                ->define("_el > a > p as title");
        } catch (Exception $e) {
            $this->assertSame(CqueryException::class, get_class($e));
            $this->assertSame("no source defined", $e->getMessage());
        }
    }

    public function testShouldGetAnExceptionNoDefinerFound()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        try {
            $query = $data
                ->from("(#lorem .link) as _el")
                ->get();
        } catch (Exception $e) {
            $this->assertSame(CqueryException::class, get_class($e));
            $this->assertSame("no definer found", $e->getMessage());
        }
    }

    public function testMultipleWhereWithUsedOrCondition()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(class, a)", "has", "vip")
            ->orFilter("attr(class, a)", "has", "super")
            ->orFilter("attr(class, a)", "has", "blocked")
            ->get();

        $this->assertCount(5, $result);
    }

    public function testMultipleWhereWithUsedAndCondition()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(class, a)", "has", "vip")
            ->filter("attr(class, a)", "has", "blocked")
            ->filter("attr(class, a)", "has", "super")
            ->get();

        $this->assertCount(1, $result);
    }

    public function testFilterWithEqualSign()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(class, a)", "=", "test-1-item")
            ->get();

        $this->assertCount(1, $result);
    }

    public function testWithRefIdAttribute()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(ref-id, h1)", "=", "23")
            ->get();

        $this->assertCount(1, $result);
    }

    public function testValueWithRefIdAttribute()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(ref-id, h1)", "=", "23")
            ->first();

        $this->assertSame("Title 2", $result["title"]);
        $this->assertSame("Href Attribute Example 2 Lorem pilsum", $result["description"]);
        $this->assertSame("http://ini-url-2.com", $result["url"]);
        $this->assertSame("vip class-2 nih tenied", $result["class"]);
    }

    public function testWithLikeContains()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(class, a)", "like", "%ni%")
            ->get();

        $this->assertCount(4, $result);
    }

    public function testWithLikeStartWith()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(class, a)", "like", "pre%")
            ->get();

        $this->assertCount(4, $result);
    }

    public function testWithLikeEndWith()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(class, a)", "like", "%ed")
            ->get();

        $this->assertCount(6, $result);
    }

    public function testWithLikeEndWithNied()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(class, a)", "like", "%nied")
            ->get();

        $this->assertCount(2, $result);
    }

    public function testWithLessThan()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class", "attr(customer-id, a)")
            ->filter("attr(customer-id, a)", "<", 18)
            ->get();

        $this->assertCount(2, $result);
    }

    public function testWithLessThanEqual()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(customer-id, a)", "<=", "18")
            ->get();

        $this->assertCount(3, $result);
    }

    public function testWithGreaterThan()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(customer-id, a)", ">", "16")
            ->get();

        $this->assertCount(2, $result);
    }

    public function testWithGreaterThanEquals()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(customer-id, a)", ">=", 16)
            ->get();

        $this->assertCount(3, $result);
    }

    public function testWithRegex()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(regex-test, a)", "regex", "/[a-z]+\-[0-9]+\-[a-z]+/im")
            ->get();

        $this->assertCount(3, $result);
    }

    public function testForNewColumnDefiner()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(regex-test, a)", "regex", "/[a-z]+\-[0-9]+\-[a-z]+/im")
            ->get();

        $this->assertCount(3, $result);
    }

    public function testNewAdapter()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define(
                // "attr(class, a > p) as class_a_p",
                // "attr(class, a) as url",
                "length(h1) as length"
            )
            ->get();

        $this->assertCount(9, $result);
    }

    public function testNewAdapterWithWhereEquals()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("attr(class, a) as class_a_p", "attr(class, a) as url", "length(h1) as length")
            ->filter("a", "=", "Href Attribute Example 90")
            ->get();

        $this->assertCount(1, $result);
    }

    public function testWithClosureDefiner()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $definer = new Definer("h1", "alias", function ($value) {
            return str_replace(" ", "-", strtoupper($value))."-FROM-CLOSURE";
        });

        $result = $data
            ->from("#lorem .link")
            ->define("upper(a)", $definer)
            ->first();

        $this->assertSame("HREF ATTRIBUTE EXAMPLE 1", $result["upper_a"]);
        $this->assertSame("TITLE-1-FROM-CLOSURE", $result["alias"]);
    }

    public function testPickWithDefinerUsedClosure()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $closure = function ($node) {
            return $node . "-XXX";
        };

        $result = $data
            ->from("#lorem .link")
            ->define(
                new Definer("a", "key_2", $closure)
            )
            ->first();

        $this->assertSame("Href Attribute Example 1 -XXX", $result["key_2"]);

    }

    public function testPickCustomerId()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("attr(customer-id, a) as cust_id", "attr(class, a) as class")
            ->filter("attr(customer-id, a)", "<=", "18")
            ->get();

        $this->assertCount(3, $result);
    }

    public function testUsedFilterLength()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("h1 as title")
            ->filter("length(h1)", "=", 5)
            ->get();

        $this->assertCount(1, $result);
    }

    public function testUsedFilterAnonymousFunction()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("h1", function ($e) {
                return $e === "Title 3";
            })
            ->get();

        $this->assertCount(1, $result);
    }

    public function testUsedFilterErrorAnonymousFunctionWithoutSelector()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        try {
            $result = $data
            ->from("#lorem .link")
            ->define("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter(function ($e) {
                return $e === "Title 3";
            }, "h1")
            ->get();
        } catch (Exception $e) {
            $this->assertSame(CqueryException::class, get_class($e));
            $this->assertSame("when used closure, u need to place it on second parameter", $e->getMessage());
        }

    }

    public function testCqueryWithNestedDefinerFunctionLengthAndAttr()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("length(attr(class, a)) as length_attr_class_a")
            ->get();

        $this->assertSame(15, $result[0]["length_attr_class_a"]);
        $this->assertSame(22, $result[1]["length_attr_class_a"]);
        $this->assertSame(15, $result[2]["length_attr_class_a"]);
        $this->assertSame(25, $result[3]["length_attr_class_a"]);
        $this->assertSame(31, $result[4]["length_attr_class_a"]);
        $this->assertSame(28, $result[5]["length_attr_class_a"]);
        $this->assertSame(22, $result[6]["length_attr_class_a"]);
        $this->assertSame(11, $result[7]["length_attr_class_a"]);
        $this->assertSame(23, $result[8]["length_attr_class_a"]);
    }

    public function testCqueryWithUpperFunction()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("upper(h1)")
            ->get();

        $this->assertSame("TITLE 1", $result[0]['upper_h1']);
        $this->assertSame("TITLE 2", $result[1]['upper_h1']);
        $this->assertSame("TITLE 3", $result[2]['upper_h1']);
        $this->assertSame("TITLE 11", $result[3]['upper_h1']);
        $this->assertSame("TITLE 22", $result[4]['upper_h1']);
        $this->assertSame("TITLE 323", $result[5]['upper_h1']);
        $this->assertSame("TITLE 331", $result[6]['upper_h1']);
        $this->assertSame("TITLE 331", $result[7]['upper_h1']);
        $this->assertSame("12345", $result[8]['upper_h1']);
    }

    public function testCqueryWithReplaceFromMultiToSingleFunction()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("replace(['Title', '331'], 'LOREM', h1)  as title")
            ->get();

        $this->assertSame("LOREM 1", $result[0]['title']);
        $this->assertSame("LOREM 2", $result[1]['title']);
        $this->assertSame("LOREM 3", $result[2]['title']);
        $this->assertSame("LOREM 11", $result[3]['title']);
        $this->assertSame("LOREM 22", $result[4]['title']);
        $this->assertSame("LOREM 323", $result[5]['title']);
        $this->assertSame("LOREM LOREM", $result[6]['title']);
        $this->assertSame("LOREM LOREM", $result[7]['title']);
        $this->assertSame("12345", $result[8]['title']);
    }

    public function testCqueryWithReplaceFromMultiToSingleButWithNestedAttrFunction()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("replace('http', 'https', attr(href, a))  as title")
            ->get();

        $this->assertSame("https://ini-url-1.com", $result[0]['title']);
        $this->assertSame("https://ini-url-2.com", $result[1]['title']);
        $this->assertSame("https://ini-url-3.com", $result[2]['title']);
        $this->assertSame("https://ini-url-11.com", $result[3]['title']);
        $this->assertSame("https://ini-url-22.com", $result[4]['title']);
        $this->assertSame("https://ini-url-33-1.com", $result[5]['title']);
        $this->assertSame("https://ini-url-33-2.com", $result[6]['title']);
        $this->assertSame("https://ini-url-33-2.com", $result[7]['title']);
        $this->assertSame("https://ini-url-33-0.com", $result[8]['title']);
    }

    public function testCqueryWithNestedThreeDefiner()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem > .link")
            ->define("reverse(length(attr(class, a))) as reverse_length_attr_class_a")
            ->get();

        $this->assertEquals(51, $result[0]["reverse_length_attr_class_a"]);
        $this->assertEquals(22, $result[1]["reverse_length_attr_class_a"]);
        $this->assertEquals(51, $result[2]["reverse_length_attr_class_a"]);
        $this->assertEquals(52, $result[3]["reverse_length_attr_class_a"]);
        $this->assertEquals(13, $result[4]["reverse_length_attr_class_a"]);
        $this->assertEquals(82, $result[5]["reverse_length_attr_class_a"]);
        $this->assertEquals(22, $result[6]["reverse_length_attr_class_a"]);
        $this->assertEquals(11, $result[7]["reverse_length_attr_class_a"]);
        $this->assertEquals(32, $result[8]["reverse_length_attr_class_a"]);
    }

    public function testCqueryResultSelectorSingleId()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#list-test-child > div")
            ->define(".pluck as title")
            ->get();

        $this->assertCount(2, $result);
    }

    public function testCqueryResultSelectorSingleIdWithFilter()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#list-test-child > div")
            ->define("span > .pluck as title")
            ->filter("span > .pluck", "=", "text-pluck-1")
            ->get();

        $this->assertCount(1, $result);
    }

    public function testCqueryMoreForDoc()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $date = date("Y-m-d H:i:s");

        $result = $data
            ->from("#lorem .link")
            ->define(
                "upper(h1) as title_upper",
                new Definer("a", "col_2", function ($value) use ($date) {
                    return "{$value} fetched on: {$date}";
                })
            )
            ->filter("attr(class, a)", "has", "vip")
            ->limit(2)
            ->get();

        $this->assertCount(2, $result);
    }

    public function testChangePickToDefiner()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem > .link")
            ->define(
                "a as title",
                new Definer("h1", "_test"),
                new Definer("h1", "_closure", function ($e) {
                    return strtoupper($e) . " [" . strrev(strtoupper($e)) . "]";
                })
            )
            ->get();

        $this->assertCount(9, $result);
        $this->assertArrayHasKey("title", $result[0]);
        $this->assertArrayHasKey("_test", $result[0]);
        $this->assertSame("TITLE 1 [1 ELTIT]", $result[0]["_closure"]);
    }

    public function testDefinerErrorAliasOnFirstParameter()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        try {
            $result = $data
                ->from("#lorem > .link")
                ->define(
                    "a as title",
                    new Definer("h1 as _test")
                )
                ->get();
        } catch (Exception $e) {
            $this->assertSame(CqueryException::class, get_class($e));
            $this->assertSame("error define, please set alias on second parameter", $e->getMessage());
        }
    }

    public function testWithFilterClass()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem > .link")
            ->define(
                "a as title",
                new Definer("h1", "_test")
            )
            ->filter(
                new Filter("h1", "=", "Title 331")
            )
            ->get();

        $this->assertCount(2, $result);
    }

    public function testWithReplaceArrayMultiWithArraySingleClass()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem > .link")
            ->define(
                "replace(['Attribute', 'Example'], ['Replaced'], a) as text",
            )
            ->get();

        $this->assertCount(9, $result);
        $this->assertSame("Href Replaced Replaced 1", $result[0]['text']);
        $this->assertSame("Href Replaced Replaced 2 Lorem pilsum", $result[1]['text']);
        $this->assertSame("Href Replaced Replaced 4", $result[2]['text']);
        $this->assertSame("Href Replaced Replaced 78", $result[3]['text']);
        $this->assertSame("Href Replaced Replaced 90", $result[4]['text']);
        $this->assertSame("Href Replaced Replaced 5", $result[5]['text']);
        $this->assertSame("Href Replaced Replaced 51", $result[6]['text']);
        $this->assertSame("Href Replaced Replaced 51", $result[7]['text']);
        $this->assertSame("Href Replaced Replaced 52", $result[8]['text']);
    }

    public function testWithLowerFunc()
    {
        $simpleHtml = file_get_contents(SAMPLE_HTML);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem > .link")
            ->define(
                "lower(h1) as title",
                "lower(attr(data-check, h1)) as data_check",
            )
            ->get();

        $this->assertCount(9, $result);
        $this->assertSame("title 1", $result[0]['title']);
        $this->assertSame("title 2", $result[1]['title']);
        $this->assertSame("title 3", $result[2]['title']);
        $this->assertSame("title 11", $result[3]['title']);
        $this->assertSame("title 22", $result[4]['title']);
        $this->assertSame("title 323", $result[5]['title']);
        $this->assertSame("title 331", $result[6]['title']);
        $this->assertSame("title 331", $result[7]['title']);
        $this->assertSame("12345", $result[8]['title']);

        // CHECK ATTR
        $this->assertSame("ini test lagi", $result[0]['data_check']);
        $this->assertSame("ini juga test 2x", $result[1]['data_check']);
        $this->assertSame("ini juga test 2x3x", $result[2]['data_check']);
        $this->assertSame(null, $result[3]['data_check']);
        $this->assertSame(null, $result[4]['data_check']);
        $this->assertSame(null, $result[5]['data_check']);
        $this->assertSame(null, $result[6]['data_check']);
        $this->assertSame(null, $result[7]['data_check']);
        $this->assertSame(null, $result[8]['data_check']);
    }

    public function testCqueryFreeProxyListWithUrl()
    {
        // change with this when u want to fetch data from remote
        // $content = "https://free-proxy-list.net/";
        $content = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($content);

        $resultYes = $data
            ->from("#list")
            ->define(
                "td:nth-child(1) as ip_address",
                "td:nth-child(2) as port",
                "td:nth-child(3) as code",
                "td:nth-child(4) as country",
                "td:nth-child(5) as anonymity",
                "td:nth-child(6) as google",
                "td:nth-child(7) as https",
                "td:nth-child(8) as last_checked",
            )->filter('td:nth-child(7)', "=", "yes")
            ->get();

        $resultNo = $data
            ->from("#list")
            ->define(
                "td:nth-child(1) as ip_address",
                "td:nth-child(2) as port",
                "td:nth-child(3) as code",
                "td:nth-child(4) as country",
                "td:nth-child(5) as anonymity",
                "td:nth-child(6) as google",
                "td:nth-child(7) as https",
                "td:nth-child(8) as last_checked",
            )->filter('td:nth-child(7)', "=", "no")
            ->get();

        $this->assertNotSame(300, $resultYes->count());
        $this->assertNotSame(300, $resultNo->count());
        $this->assertSame($resultNo->count(), 300 - $resultYes->count());
    }

    public function testCqueryFreeProxyListWithLimit()
    {
        // enable this when u want to fetch data from remote
        // $content = "https://free-proxy-list.net/";
        $content = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($content);

        $result = $data
            ->from(".fpl-list")
            ->define(
                // "td:nth-child(1):contains('209'),td:nth-child(1):contains('240') as ip_address",
                // "td:nth-child(1):contains('114'):contains('209') as ip_address",
                // "td:nth-child(1):contains('114'):td:nth-child(7):contains('no') as ip_address",
                "td:nth-child(1) as ip_address",
                // "td:nth-child(2) as port",
                // "td:nth-child(3) as code",
                "td:nth-child(4) as country",
                // "td:nth-child(5) as anonymity",
                // "td:nth-child(6) as google",
                "td:nth-child(7) as https",
                // "td:nth-child(8) as last_checked",
            )->filter('td:nth-child(7)', "=", "no")
            ->limit(1)
            ->get();

        $this->assertCount(1, $result);
    }

    public function testScrapeQuotesWithUrlToScrape()
    {
        // change with this when u want to fetch data from remote
        // $content = "http://quotes.toscrape.com/";
        $content = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($content);

        $result = $data
            ->from(".col-md-8 > .quote")
            ->define(
                "span.text as text",
                "span:nth-child(2) > small as author",
                "(div > .tags) as tags",
            )
            ->get();

        $resultTopTen = $data
            ->from(".tags-box")
            ->define(
                ".tag-item > a as text"
            )
            ->get();

        $this->assertCount(10, $result);
        $this->assertCount(10, $resultTopTen);
    }

    public function testScrapeQuotesToScrapeWithWrongDefiner()
    {
        // change with this when u want to fetch data from remote
        // $content = "http://quotes.toscrape.com/";
        $content = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($content);

        try {
            $result = $data
                ->from(".col-md-8 > .quote")
                ->define(
                    "span.text as text",
                    "span:nth-child(2) > small as author",
                    "(div > .tags > a)  as tags",
                )
                ->get();
        } catch (Exception $e) {
            $this->assertSame(CqueryException::class, get_class($e));
            $this->assertStringContainsString("error query definer", $e->getMessage());
            $this->assertStringContainsString("error occurred while attempting to pick the column", $e->getMessage());
        }
    }

    public function testScrapeQuotesToScrapeWithAppendNodeDefiner()
    {
        $content = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($content);

        $result = $data
            ->from(".col-md-8 > .quote")
            ->define(
                "span.text as text",
                "span:nth-child(2) > small as author",
                "append_node(div > .tags, a)  as tags",
            )
            ->get();

        $this->assertCount(10, $result);
        $this->assertCount(4, $result[0]['tags']);
        $this->assertCount(2, $result[1]['tags']);
    }

    public function testScrapeQuotesToScrapeWithAppendNodeHrefAttributeDefiner()
    {
        $content = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($content);

        $result = $data
            ->from(".col-md-8 > .quote")
            ->define(
                "span.text as text",
                "span:nth-child(2) > small as author",
                "append_node(div > .tags, a)  as tags",
                "append_node(div > .tags, attr(href, a))  as tags_url",
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
            ->from(".col-md-8 > .quote")
            ->define(
                "span.text as text",
                "append_node(div > .tags, a) as _tags",
                "append_node(div > .tags, a) as tags[*][text]",
                "append_node(div > .tags, attr(href, a)) as tags[*][url]", // * means each index, for now ots limitd only one level
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
            ->from(".col-md-8 > .quote")
            ->define(
                "span.text as text",
                "append_node(div > .tags, a) as tags[key]",
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
            ->from(".col-md-8 > .quote")
            ->define(
                "replace('The ', 'Lorem ', span.text) as text",
            )
            ->get();

        $this->assertCount(10, $result);
        $this->assertSame("“Lorem world as we have created it is a process of our thinking. It cannot be changed without changing our thinking.”", $result[0]["text"]);
        $this->assertSame("“It is our choices, Harry, that show what we truly are, far more than our abilities.”", $result[1]["text"]);
        $this->assertSame("“There are only two ways to live your life. One is as though nothing is a miracle. Lorem other is as though everything is a miracle.”", $result[2]["text"]);
        $this->assertSame("“Lorem person, be it gentleman or lady, who has not pleasure in a good novel, must be intolerably stupid.”", $result[3]["text"]);
        $this->assertSame("“Imperfection is beauty, madness is genius and it's better to be absolutely ridiculous than absolutely boring.”", $result[4]["text"]);
        $this->assertSame("“Try not to become a man of success. Rather become a man of value.”", $result[5]["text"]);
        $this->assertSame("“It is better to be hated for what you are than to be loved for what you are not.”", $result[6]["text"]);
        $this->assertSame("“I have not failed. I've just found 10,000 ways that won't work.”", $result[7]["text"]);
        $this->assertSame("“A woman is like a tea bag; you never know how strong it is until it's in hot water.”", $result[8]["text"]);
        $this->assertSame("“A day without sunshine is like, you know, night.”", $result[9]["text"]);
    }

    public function testScrapeQuotesToScrapeWithReplaceArrayDefiner()
    {
        $content = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($content);

        $result = $data
            ->from(".col-md-8 > .quote")
            ->define(
                "replace(['The ', 'are'], ['Please ', 'son'], span.text) as text",
            )
            ->get();

        $this->assertCount(10, $result);
        $this->assertSame("“Please world as we have created it is a process of our thinking. It cannot be changed without changing our thinking.”", $result[0]["text"]);
        $this->assertSame("“It is our choices, Harry, that show what we truly son, far more than our abilities.”", $result[1]["text"]);
        $this->assertSame("“There son only two ways to live your life. One is as though nothing is a miracle. Please other is as though everything is a miracle.”", $result[2]["text"]);
        $this->assertSame("“Please person, be it gentleman or lady, who has not pleasure in a good novel, must be intolerably stupid.”", $result[3]["text"]);
        $this->assertSame("“Imperfection is beauty, madness is genius and it's better to be absolutely ridiculous than absolutely boring.”", $result[4]["text"]);
        $this->assertSame("“Try not to become a man of success. Rather become a man of value.”", $result[5]["text"]);
        $this->assertSame("“It is better to be hated for what you son than to be loved for what you son not.”", $result[6]["text"]);
        $this->assertSame("“I have not failed. I've just found 10,000 ways that won't work.”", $result[7]["text"]);
        $this->assertSame("“A woman is like a tea bag; you never know how strong it is until it's in hot water.”", $result[8]["text"]);
        $this->assertSame("“A day without sunshine is like, you know, night.”", $result[9]["text"]);
    }

    public function testCqueryCodeTutsPlus()
    {
        // change with this when u want to fetch data from remote
        // $content = "https://code.tutsplus.com/";
        $content = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($content);

        $result = $data
            ->from("ol.posts")
            ->define(
                "li > article > header > a.posts__post-title > h1 as title",
                "li > article > div as desc",
            )
            ->get();

        $this->assertSame(6, $result->count());
    }

    public function testCqueryScrapeMeLivetWithUrl()
    {
        // change with this when u want to fetch data from remote
        // $content = "https://scrapeme.live/shop/";
        $content = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($content);

        $result = $data
            ->from("#main > div:nth-child(4) > nav > ul.page-numbers")
            ->define(
                "li > a as text",
                "attr(href, li > a) as href",
            )
            ->get();

        $this->assertSame(7, $result->count());
    }

    public function testCqueryGetProductsScrapeMeLivetWithUrl()
    {
        // change with this when u want to fetch data from remote
        // $content = "https://scrapeme.live/shop/";
        // TODO https://scrapeme.live/shop/page/1/ -> test looping
        $content = file_get_contents(SAMPLE_HTML);

        $data = new Cquery($content);

        $result = $data
            ->from("ul.products.columns-4")
            ->define(
                "li > a > h2 as text",
                "li > a > span.price > span.amount as price",
                "attr(src, li > a > img) as image",
                "attr(href, li > a.woocommerce-loop-product__link) as url",
            )
            ->get();

        $this->assertSame(16, $result->count());
    }
}

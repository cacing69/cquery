<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Cquery;
use Cacing69\Cquery\Definer;
use Cacing69\Cquery\Exception\CqueryException;
use Exception;
use PHPUnit\Framework\TestCase;

define("SAMPLE_SIMPLE_1", "src/Samples/sample-simple-1.html");

final class CquerySimpleHtml1Test extends TestCase
{
    public function testCollectFirst()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("footer")
            ->define("p")
            ->first();

        $this->assertSame('Copyright 2023', $result["p"]);
    }

    public function testReusableElement()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $query = $data
            ->from("(#lorem .link) as _el")
            ->define("a > p as title");

        $first = $query->first();

        $this->assertSame("Lorem pilsum", $first['title']);
    }

    public function testShouldGetAnExceptionNoSourceDefined()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        try {
            $query = $data
                ->define("_el > a > p as title");
        } catch (Exception $e) {
            $this->assertSame(CqueryException::class, get_class($e));
            $this->assertSame("no source defined", $e->getMessage());
        }
    }

    public function testMultipleWhereWithUsedOrCondition()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(class, a)", "has", "vip")
            ->OrFilter("attr(class, a)", "has", "super")
            ->OrFilter("attr(class, a)", "has", "blocked")
            ->get();

        $this->assertCount(5, $result);
    }

    public function testMultipleWhereWithUsedAndCondition()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->define(
                // "attr(class, a > p) as class_a_p",
                "attr(class, a) as url",
                "length(h1) as length"
            )
            ->get();

        $this->assertCount(9, $result);
    }

    public function testNewAdapterWithWhereEquals()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        try {
            $result = $data
            ->from("#lorem .link")
            ->define("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter(function ($e) {
                return $e === "Title 3";
            })
            ->get();
        } catch (Exception $e) {
            $this->assertSame(CqueryException::class, get_class($e));
            $this->assertSame("when used closure, u need to place it on second parameter", $e->getMessage());
        }

    }

    public function testCqueryWithNestedDefinerFunctionLengthAndAttr()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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

    public function testCqueryWithNestedThreeDefiner()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#list-test-child > div")
            ->define(".pluck as title")
            ->get();

        $this->assertCount(2, $result);
    }

    public function testCqueryResultSelectorSingleIdWithFilter()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
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
}

<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Cquery;
use Cacing69\Cquery\Definer;
use Cacing69\Cquery\Exception\CqueryException;
use Cacing69\Cquery\Picker;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

define("SAMPLE_SIMPLE_1", "src/Samples/sample-simple-1.html");

final class CquerySimpleHtml1Test extends TestCase
{
    public function testCollectFirst()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
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
            ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
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
            ->pick("p")
            ->first();

        $this->assertSame('Copyright 2023', $result["p"]);
    }

    public function testReusableElement()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->first();

        $result_clone = $data
            ->from("footer")
            ->pick("p")
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
            ->pick("_el > a > p as title");

        $first = $query->first();

        $this->assertSame(null, $first["title"]);
    }

    public function testShouldGetAnExceptionNoSourceDefined()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        try {
            $query = $data
                ->pick("_el > a > p as title");
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
            ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(class, a)", "has", "vip")
            ->OrFilter("attr(class, a)", "has", "super")
            ->OrFilter("attr(class, a)", "has", "blocked")
            ->get();

        $this->assertSame(5, count($result));
    }

    public function testMultipleWhereWithUsedAndCondition()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(class, a)", "has", "vip")
            ->filter("attr(class, a)", "has", "blocked")
            ->filter("attr(class, a)", "has", "super")
            ->get();

        $this->assertSame(1, count($result));
    }

    public function testFilterWithEqualSign()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(class, a)", "=", "test-1-item")
            ->get();

        $this->assertSame(1, count($result));
    }

    public function testWithRefIdAttribute()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(ref-id, h1)", "=", "23")
            ->get();

        $this->assertSame(1, count($result));
    }

    public function testValueWithRefIdAttribute()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
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
            ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(class, a)", "like", "%ni%")
            ->get();

        $this->assertSame(4, $result->count());
    }

    public function testWithLikeStartWith()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(class, a)", "like", "pre%")
            ->get();

        $this->assertSame(4, $result->count());
    }

    public function testWithLikeEndWith()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(class, a)", "like", "%ed")
            ->get();

        $this->assertSame(6, $result->count());
    }

    public function testWithLikeEndWithNied()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(class, a)", "like", "%nied")
            ->get();

        $this->assertSame(2, $result->count());
    }

    public function testWithLessThan()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class", "attr(customer-id, a)")
            ->filter("attr(customer-id, a)", "<", 18)
            ->get();

        // dd($result);

        $this->assertSame(2, $result->count());
    }

    public function testWithLessThanEqual()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(customer-id, a)", "<=", "18")
            ->get();

        $this->assertSame(3, $result->count());
    }

    public function testWithGreaterThan()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(customer-id, a)", ">", "16")
            ->get();

        $this->assertSame(2, $result->count());
    }

    public function testWithGreaterThanEquals()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(customer-id, a)", ">=", 16)
            ->get();

        $this->assertSame(3, $result->count());
    }

    public function testWithRegex()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(regex-test, a)", "regex", "/[a-z]+\-[0-9]+\-[a-z]+/im")
            ->get();

        $this->assertSame(3, $result->count());
    }

    public function testForNewColumnDefiner()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->pick("attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(regex-test, a)", "regex", "/[a-z]+\-[0-9]+\-[a-z]+/im")
            ->get();

        $this->assertSame(3, $result->count());
    }

    public function testNewAdapter()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->pick("attr(class, a > p) as class_a_p", "attr(class, a) as url", "length(h1) as length")
            ->get();

        $this->assertSame(9, $result->count());
    }

    public function testNewAdapterWithWhereEquals()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->pick("attr(class, a) as class_a_p", "attr(class, a) as url", "length(h1) as length")
            // ->filter("attr(class, a)", "=", "test-1-item")
            ->filter("a", "=", "Href Attribute Example 90")
            ->get();

        $this->assertSame(1, $result->count());
    }

    public function testWithClosurePicker()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->pick(new Picker(function ($node) {
                    return strtoupper($node->text());
                }, "a", "title"))
            ->first();

        $this->assertSame("HREF ATTRIBUTE EXAMPLE 1", $result["title"]);
    }

    public function testPickWithPickerUsedClosure()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $closure = function ($node) {
            return $node->text() . "-XXX";
        };

        $result = $data
            ->from("#lorem .link")
            ->pick(
                new Picker("a", "key_2")
            )
            ->first();

        $this->assertSame("Href Attribute Example 1", $result["key_2"]);

    }

    public function testPickCustomerId()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->pick("attr(customer-id, a) as cust_id", "attr(class, a) as class")
            ->filter("attr(customer-id, a)", "<=", "18")
            ->get();

        $this->assertSame(3, $result->count());
    }

    public function testUsedFilterLength()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->pick("h1 as title")
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
            ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter(function ($e) {
                return $e->text() === "Title 3";
            }, "h1")
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
            ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter(function ($e) {
                return $e->text() === "Title 3";
            })
            ->get();
        } catch (Exception $e) {
            $this->assertSame(CqueryException::class, get_class($e));
            $this->assertSame("error processing filter, when used callback filter, please set selector on second parameter", $e->getMessage());
        }

    }
}

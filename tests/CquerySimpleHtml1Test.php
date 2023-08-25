<?php

namespace Cacing69\Cquery\Test;

use Cacing69\Cquery\Cquery;
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
            ->pick("h1 > p", "a as description", "attr(href, a) as url", "attr(class, a) as class")
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

        $this->assertSame('Lorem pilsum', $first["title"]);
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

    public function testWithWrongFilterAttribute()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(ref-id, a)", "=", "23")
            ->get();

        $this->assertSame(0, count($result));
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
            ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(customer-id, a)", "<", 18)
            ->get();

        $this->assertSame(2, $result->count());
    }

    public function testWithLessThanEqual()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(customer-id, a)", "<", 18)
            ->get();

        $this->assertSame(2, $result->count());
    }

    public function testWithGreaterThan()
    {
        $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
        $data = new Cquery($simpleHtml);

        $result = $data
            ->from("#lorem .link")
            ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
            ->filter("attr(customer-id, a)", ">", 16)
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

    // public function testUsedFilterLength()
    // {
    //     $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
    //     $data = new Cquery($simpleHtml);

    //     $result = $data
    //         ->from("#lorem .link")
    //         ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
    //         ->filter("length(h1)", "=", 5)
    //         ->first();
    // }

    // public function testUsedFilterAnonymousFunction()
    // {
    //     $simpleHtml = file_get_contents(SAMPLE_SIMPLE_1);
    //     $data = new Cquery($simpleHtml);

    //     $result = $data
    //         ->from("#lorem .link")
    //         ->pick("h1 as title", "a as description", "attr(href, a) as url", "attr(class, a) as class")
    //         ->filter(function ($e) {
    //             return $e->text();
    //         })
    //         ->first();
    // }
}

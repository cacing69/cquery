# Cquery (Crawl Query)

## Currently experimenting to attempt scraping a webpage using different methods

Cquery is an acronym for crawl query, used to extract text from an HTML element using PHP, simply its tool for crawling/scraping web page. It called a query, as it adopts the structure present in an SQL query, so you can analogize that your DOM/HTML Document is a table you will query.

Let's play for a moment and figure out how to make website scraping easier, much like crafting a query for a database.

Please keep in mind that I haven't yet reached a beta/stable release for this library, so the available features are still very limited.

I would greatly accept any support/contribution from everyone. See [CONTRIBUTING.md](CONTRIBUTING.md) for help getting started.
## Quick Installation

```bash
composer require cacing69/cquery
```

For example, you have a simple HTML element as shown below.


<details>
  <summary>Click to show HTML : <code>src/Samples/sample-simple-1.html</code></summary>

  ```html
<!DOCTYPE HTML>
<html lang="en-US">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Href Attribute Example</title>
  </head>
  <body>
    <span id="lorem">
      <div class="link valid">
        <h1 data-link-from-source="/url-1" id="title-id-1" class="class-title-1">Title 1</h1>
        <a class="ini vip class-1" data-custom-attr-id="12" href="http://ini-url-1.com">Href Attribute Example 1
        </a>
      </div>
      <div class="link">
        <h1 ref-id="23" id="title-id-2" class="class-title-1">Title 2</h1>
        <a class="vip class-2 nih tenied" data-custom-attr-id="212" href="http://ini-url-2.com">
          Href Attribute Example 2
          <p>Lorem pilsum</p>
        </a>
      </div>
      <div class="link">
        <h1 id="title-id-3" class="class-title-1">Title 3</h1>
        <a class="premium class-3" data-custom-attr-id="122" regex-test="a-abc-ab" href="http://ini-url-3.com">Href Attribute Example 4</a>
      </div>
      <div class="link">
        <h1>Title 11</h1>
        <a class="vip class-1 super blocked" data-custom-attr-id="132" regex-test="a-192-ab" href="http://ini-url-11.com">Href Attribute Example 78</a>
        </div>
      <div class="link">
        <h1>Title 22</h1>
        <a class="preview itu class-2 vip blocked" data-custom-attr-id="712" regex-test="b-12-ac" href="http://ini-url-22.com">Href Attribute Example 90</a>
      </div>
      <div class="link">
        <h1>Title 323</h1>
        <a class="nied premium class-3 blocked" data-custom-attr-id="132" href="http://ini-url-33-1.com">Href Attribute Example 5</a>
      </div>
      <div class="link pending">
        <h1>Title 331</h1>
        <a class="premium class-31 ended" data-custom-attr-id="121" regex-test="zx-1223-ac" customer-id="18" href="http://ini-url-33-2.com">Href Attribute Example 51</a>
      </div>
      <div class="link pending">
        <h1>Title 331</h1>
        <a class="test-1-item" data-custom-attr-id="121" customer-id="16" href="http://ini-url-33-2.com">Href Attribute Example 51</a>
      </div>
      <div class="link pending">
        <h1>12345</h1>
        <a class="premium class-32 denied" data-custom-attr-id="1652" customer-id="17" href="http://ini-url-33-0.com">Href Attribute Example 52</a>
      </div>
    </span>
    <p>
      <a href="https://www.freecodecamp.org/contribute/">The freeCodeCamp Contribution Page
    </p>
    <footer>
      <p>Copyright 2023</p>
    </footer>
  </body>
</html>
```

</details>

### List function available

Below are the functions you are can use, they may change over time. <br>**Note:** nested function has been supported.
| function | example | description |
| --------- | ------------- | ------------------ |
| `attr(attrName, selector)` | `attr(class, .link)` |  will retrieve all class value present on the element/container according to the selector. (.link) |
| `length(selector)` | `length(h1)` | will retrieve all length string on the element/container according to the selector. (h1) |
| `upper(selector)` | `upper(h1)` | will change text to uppercase element/container according to the selector. (h1) |
| `reverse(selector)` | `reverse(h1)` | will reverse text according to the selector. (h1) |

#### How to use filter
x
| operator | example | description |
| --------- | ------------- | ------------------ |
| `(= or ==)` | `filter("h1", "=", "99")` | retrieve data according to elements that only have the same value = 99 |
| `===` | `filter("h1", "===", "99")` | retrieve data according to elements that only have the same and identic with value = 99 |
| `<` | `filter("attr(id, a)", "<", 99)` | retrieve data according to elements that only have values smaller than 99 |
| `<=` | `filter("attr(id, a)", "<=", 99)` | get data from elements with values that are lesser than or equal to 99 |
| `>` | `filter("attr(id, a)", ">", 99)` |  get data from elements with values that are greater than 99 |
| `>=` | `filter("attr(id, a)", ">=", 99)` |  Get data from elements with values that are greater than or equal 99 |
| `(<> or !=)` | `filter("attr(id, a)", "!=", 99)` |  get data from elements that are not equal to 99 |
| `!==` | `filter("attr(id, a)", "!==", 99)` |  get data from elements that are not equal or they are not the same type to 99 |
| `has` | `filter("attr(class, a)", "has", "foo")` | get data from elements that only have class "foo" |
| `regex` | `filter("attr(class, a)", "regex", "/[a-z]+\-[0-9]+\-[a-z]+/im")` | get data from elements that match the given regex pattern only, with the provided pattern being (a-192-ab, b-12-ac, zx-1223-ac) |
| `like` | `filter("attr(class, a)", "like", "%foo%")` <br><br> `filter("attr(class, a)", "like", "%foo")` <br><br> `filter("attr(class, a)", "like", "foo%")` | retrieve data according to elements and value criteria. <br><br> %foo% = anything containing the phrase "foo" <br><br> %foo = all sentences ending with "foo" <br><br> foo% = all sentences starting with "foo"|
---

So, let's start scraping this website.

```php
require_once 'vendor/autoload.php';

$html = file_get_contents("src/Samples/sample-simple-1.html");
$data = new Cacing69\Cquery\Cquery($html);

$result = $query
        ->from("#lorem .link") // next will be from("(#lorem .link) as el")
        ->pick(
            "h1 as title",
            "a as description",
            "attr(href, a) as url", // get href attribute from all element at #lorem .link a
            "attr(class, a) as class"
        )
        // just imagine this is your table, and every element as your column
        ->filter("attr(class, a)", "has", "vip") // add some filter here
        // ->OrFilter("attr(class, a)", "has", "super") // add another condition its has OR condition SQL
        // ->filter("attr(class, a)", "has", "blocked") // add another condition its has AND condition SQL
        ->get();
```

And here are the results

![Alt text](https://gcdnb.pbrd.co/images/Q6XHKRydSigl.png?o=1 "a title")

### Another example with anonymous function

```php
require_once 'vendor/autoload.php';

$html = file_get_contents("src/Samples/sample-simple-1.html");
$data = new Cacing69\Cquery\Cquery($html);

$result_1 = $data
          ->from("#lorem .link")
          ->pick(
              "upper(h1) as title_upper",
              new Picker(function($value) use ($date) {
                  return "{$value} fetched on: {$date}";
              }, "a", "col_2")
          )
          ->filter("attr(class, a)", "has", "vip")
          ->limit(2)
          ->get();

```

Here are the result for `$result_1`

![Alt text](https://gcdnb.pbrd.co/images/qtItVezcEUq7.png?o=1 "a title")

```php
// another example, filter with closure
$result_2 = $data
            ->from("#lorem .link")
            ->pick("reverse(h1) as title", "attr(href, a) as url")
            ->filter(function ($e) {
                return $e->text() === "Title 3";
            }, "h1")
            ->get();

```

Here are the result for `$result_2`

![Alt text](https://gcdnb.pbrd.co/images/qtItVezcEUq7.png?o=1 "a title")

```php
// another example, to load data from url
// for now it used curl without any config, but i have plan to change it withh browserKit, i hope have much time to realize that

$url = "https://free-proxy-list.net/";
$data = new Cquery($url);

$result_3 = $data
    ->from(".fpl-list")
    ->pick(
        "td:nth-child(1) as ip_address",
        "td:nth-child(4) as country",
        "td:nth-child(7) as https",
    )->filter('td:nth-child(7)', "=", "no")
    ->limit(1)
    ->get();

```

Here are the result for `$result_3`

![Alt text](https://gcdnb.pbrd.co/images/We0ea7frlZw1.png?o=1 "a title")

### Note

I've recently started building this, and if anyone is interested,I would certainly appreciate a lot of feedback from everyone who has read/seen my little project, in any way (issue, pull request or whatever).However, right now I'm considering making it better to be more flexible and user-friendly for website scraping.

This is just the beginning, and I will continue to develop it as long as I can

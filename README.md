# Cquery (Crawl Query)

## Please Read

### Currently experimenting

to attempt extract data from webpage which, in my opinion, becomes more enjoyable. All methods and usage instructions provided here are designed according to the i needs. If you have any suggestions or feedback to improve them, it would be highly appreciated and

## What kind of thing is this

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
| `lower(selector)` | `lower(h1)` | will change text to lowercase element/container according to the selector. (h1) |
| `upper(selector)` | `upper(h1)` | will change text to uppercase element/container according to the selector. (h1) |
| `reverse(selector)` | `reverse(h1)` | will reverse text according to the selector. (h1) |
| `replace(from, to, selector)` | `replace('lorem', 'ipsum', h1)` | will change text from `lorem` to `ipsum` according to the selector (h1). <br> have 3 option to use that <br><br> `replace('lorem', 'ipsum', h1)` <br><br> `replace(['lorem', 'dolor'], ['ipsum', 'sit'], h1)` <br><br> `replace(['lorem', 'ipsum'], 'ipsum', h1)` <br><br> it used single tick on argument/param |
| `append_node(selectorParent, selectorChildAfterParent)` | `append_node(div > .tags, a)  as tags` | will append array element as a child each item, for its usage, you can refer to the sample code below in $result_4. |

### List rules for alias

Below are the functions you are can use, they may change over time. <br>**Note:** nested function has been supported.
| # | example | alias | description |
| ------------- | --------- | --------- | ------------- |
| 1| `h1` |  `h1` | - |
| 2 | `h1 > 1` |  `h1_a` | - |
| 3 | `h1 > 1 as title` |  `title` | - |
| 4 | `append_node(div > .tags, a) as _tags` |  `_tags[key]` | it will be append element as array each element |
| 5| `append_node(div > .tags, a) as tags[*][text]` | `tags[0]['text']` | `*` the star symbol signifies all elements at the index. it will be append new key (in this case `text`) each array element
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
        ->define(
            "h1 as title",
            "a as description",
            "attr(href, a) as url", // get href attribute from all element at #lorem .link a
            "attr(class, a) as class"
        )
        // just imagine this is your table, and every element as your column
        ->filter("attr(class, a)", "has", "vip") // add some filter here
        // ->orFilter("attr(class, a)", "has", "super") // add another condition its has OR condition SQL
        // ->filter("attr(class, a)", "has", "blocked") // add another condition its has AND condition SQL
        ->get(); // -> return type is \Doctrine\Common\Collections\ArrayCollection
```

And here are the results

![Alt text](https://gcdnb.pbrd.co/images/Q6XHKRydSigl.png?o=1 "a title")

### Another example with anonymous function

```php
require_once 'vendor/autoload.php';

use Cacing69\Cquery\Definer;
$html = file_get_contents("src/Samples/sample-simple-1.html");
$data = new Cacing69\Cquery\Cquery($html);

$result_1 = $data
          ->from("#lorem .link")
          ->define(
              "upper(h1) as title_upper",
              new Definer( "a", "col_2", function($value) use ($date) {
                  return "{$value} fetched on: {$date}";
              })
          )
          ->filter("attr(class, a)", "has", "vip")
          ->limit(2)
          ->get() // -> return type is \Doctrine\Common\Collections\ArrayCollection
          ->toArray();
```
<details>
  <summary>Click to show output : <code>$result_2</code></summary>

![Alt text](https://gcdnb.pbrd.co/images/qtItVezcEUq7.png?o=1 "a title")
</details>

```php
// another example, filter with closure
$result_2 = $data
            ->from("#lorem .link")
            ->define("reverse(h1) as title", "attr(href, a) as url")
            ->filter("h1", function ($e) {
                return $e->text() === "Title 3";
            })
            ->get() // -> return type is \Doctrine\Common\Collections\ArrayCollection
            ->toArray();

```

<details>
  <summary>Click to show output : <code>$result_2</code></summary>

  ![Alt text](https://gcdnb.pbrd.co/images/qtItVezcEUq7.png?o=1 "a title")
</details>

```php
// another example, to load data from url used browserkit

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
    ->get() // -> return type is \Doctrine\Common\Collections\ArrayCollection
    ->toArray();

```

<details>
  <summary>Click to show output : <code>$result_3</code></summary>

  ![Alt text](https://gcdnb.pbrd.co/images/We0ea7frlZw1.png?o=1 "a title")
</details>


#### how to use append_node(a, b)

```php
// another example, to load data from url used browserkit

$url = "http://quotes.toscrape.com/";
$data = new Cquery($url);

$result_4 = $data
              ->from(".col-md-8 > .quote")
              ->define(
                  "span.text as text",
                  "span:nth-child(2) > small as author",
                  "append_node(div > .tags, a)  as tags",
              )
              ->get() // -> return type is \Doctrine\Common\Collections\ArrayCollection
              ->toArray();

```

<details>
  <summary>Click to show output : <code>$result_4</code></summary>

  ![Alt text](https://gcdnb.pbrd.co/images/46mETzAatjur.png?o=1 "a title")
</details>

#### Another example how to use append_child() with custom key each item

```php
// another example, to load data from url used browserkit

$url = "http://quotes.toscrape.com/";
$data = new Cquery($url);

$result_5 = $data
              ->from(".col-md-8 > .quote")
              ->define(
                  "span.text as text",
                  "append_node(div > .tags, a) as tags[key]", // grab child `a` on element `div > .tags` and place it into tags['key']
              )
              ->get() // -> return type is \Doctrine\Common\Collections\ArrayCollection
              ->toArray();

```

<details>
  <summary>Click to show output : <code>$result_5</code></summary>

  ![Alt text](https://gcdnb.pbrd.co/images/NYUsStjIshsf.png?o=1 "a title")
</details>

```php
// another example, to load data from url used browserkit

$url = "http://quotes.toscrape.com/";
$data = new Cquery($url);

$result_6 = $data
              ->from(".col-md-8 > .quote")
              ->define(
                  "span.text as text",
                  "append_node(div > .tags, a) as _tags",
                  "append_node(div > .tags, a) as tags[*][text]",
                  "append_node(div > .tags, attr(href, a)) as tags[*][url]", // [*] means each index, for now ots limitd only one level
              )
              ->get() // -> return type is \Doctrine\Common\Collections\ArrayCollection
              ->toArray();

```

<details>
  <summary>Click to show output : <code>$result_6</code></summary>

  ![Alt text](https://gcdnb.pbrd.co/images/lXhhw7hA8LYf.png?o=1 "a title")
</details>

### Note

I've recently started building this, and if anyone is interested,I would certainly appreciate a lot of feedback from everyone who has read/seen my little project, in any way (issue, pull request or whatever).However, right now I'm considering making it better to be more flexible and user-friendly for website scraping.

This is just the beginning, and I will continue to develop it as long as I can

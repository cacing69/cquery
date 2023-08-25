# Cquery (Crawl Query)

## Currently experimenting to attempt scraping a webpage using different methods

Cquery is an acronym for crawl query, used to extract text from an HTML element using PHP, simply its tool for crawlin/scraping web page. It called a query, as it adopts the structure present in an SQL query, so you can analogize that your DOM/HTML Document is a table you will query.

Let's play for a moment and figure out how to make website scraping easier, much like crafting a query for a database.

Please keep in mind that I haven't yet reached a beta/stable release for this library, so the available features are still very limited.

## Quick Installation

```bash
composer require cacing69/cquery
```

For example, you have a simple HTML element as shown below.

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
          <p>Lorem pilsum</p>
        </a>
      </div>
      <div class="link">
        <h1 id="title-id-2" class="class-title-1">Title 2</h1>
        <a class="vip class-2 nih" data-custom-attr-id="212" href="http://ini-url-2.com">Href Attribute Example 2</a>
      </div>
      <div class="link">
        <h1 id="title-id-3" class="class-title-1">Title 3</h1>
        <a class="premium class-3" data-custom-attr-id="122" href="http://ini-url-3.com">Href Attribute Example 4</a>
      </div>
      <div class="link">
        <h1>Title 11</h1>
        <a class="vip class-1 super blocked" data-custom-attr-id="132" href="http://ini-url-11.com">Href Attribute Example 78</a>
        </div>
      <div class="link">
        <h1>Title 22</h1>
        <a class="itu class-2 vip blocked" data-custom-attr-id="712" href="http://ini-url-22.com">Href Attribute Example 90</a>
      </div>
      <div class="link">
        <h1>Title 323</h1>
        <a class="premium class-3 blocked" data-custom-attr-id="132" href="http://ini-url-33-1.com">Href Attribute Example 5</a>
      </div>
      <div class="link pending">
        <h1>Title 331</h1>
        <a class="premium class-31" data-custom-attr-id="121" href="http://ini-url-33-2.com">Href Attribute Example 51</a>
      </div>
      <div class="link pending">
        <h1>Title 339</h1>
        <a class="premium class-32" data-custom-attr-id="1652" href="http://ini-url-33-0.com">Href Attribute Example 52</a>
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
        ->filter("attr(class, a)", "like", "%vip%") // add some filter here
        // ->OrFilter("attr(class, a)", "like", "%super%") // add another condition its like OR condition SQL
        // ->filter("attr(class, a)", "like", "%blocked%") // add another condition its like AND condition SQL
        ->get();
```

And here are the results

![Alt text](https://gcdnb.pbrd.co/images/Q6XHKRydSigl.png?o=1 "a title")

### Note

I've recently started building this, and if anyone is interested,I would certainly appreciate a lot of feedback from everyone who has read/seen my little project, in any way (issue, pull request or whatever).However, right now I'm considering making it better to be more flexible and user-friendly for website scraping.

This is just the beginning, and I will continue to develop it as long as I can

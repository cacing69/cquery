# Quickstart

This page provides a quick introduction to Cquery and introductory examples. If you have not already installed, Cquery, head over to the [Installation](overview?id=installation) page.

## Sample HTML

For example, you have a simple HTML element as shown below.

<details>
  <summary>Click to show HTML : <code>src/Samples/sample.html</code></summary>

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

## Methods

Lists of method available you can used for query html

| method | example | description |
| --------- | ------------- | ------------------ |
| `from($selector)` | `from('.content')` |  Used to set the source of the main element that will be used for querying |
| `define(...$string)` | `define("h1 as title", "attr(href, a) as url)"` | Used to define element to scrape, and saved them to object depend on alias directly |
| `first()` | `first()` | To retrieve first item query results. |
| `get()` | `get()` | To retrieve collection from query results. |
| `save($path, $writer)` | `save('public/output.csv')` | To save query as external file, default writer is CSVWriter. |
| `raw(string)` | `raw('from... define... filter...')` | To get data based on raw cquery. |
| `filter()` | `filter()` | - |
| `getSource()` | `getSource()` | - |
| `orFilter()` | `orFilter()` | - |
| `onContentLoaded(function($browser))` | `onContentLoaded()` | - |
| `eachItem(function($item, $index))` | `eachItem()` | - |
| `onObtainedResult(function($results))` | `onObtainedResult()` | - |

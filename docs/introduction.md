# Introduction

**Cquery** it's an acronym for **Crawl Query** used to extract text from an HTML element using PHP, simply its tool for crawling/scraping web page. It called a query, as it adopts the structure present in an **SQL query**, so you can analogize that your **DOM/HTML Document** is a document that you will query.

Its a scraper library used [`symfony/dom-crawler`](https://github.com/symfony/dom-crawler) and [`symfony/browser-kit`](https://github.com/symfony/browser-kit) under the hoods, that used expression to extract data from a webpage which support javascript or ajax webpage

To perform web scraping on pages loaded with javascript or ajax, you need an adapter outside of this package. It's not a default package in the core of cquery. Refer to it as [`cacing69/cquery-panther-loader`](https://github.com/cacing69/cquery-panther-loader). Read more information about [`symfony/panther`](https://github.com/symfony/panther), you'll discover installation and more information

>Please keep in mind that all methods and usage instructions provided here are designed according to that i needs, so the available features and expression are still very limited. If you have any suggestions or feedback to improve them, it would be highly appreciated.

# README.md

[![Latest Version on Packagist](https://img.shields.io/packagist/v/cacing69/cquery.svg)](https://packagist.org/packages/cacing69/cquery)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg?style=flat-square)](http://makeapullrequest.com)

> This is a query for web scraping, example query :

```sql
from (.item)
define
    span > a.title as title
    attr(href, div > h1 > span > a) as url
filter
    span > a.title has 'history'
limit 1
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

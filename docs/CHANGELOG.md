# Release Notes

## [v1.5.0](https://github.com/cacing69/cquery/compare/v1.5.0...v1.4.0) (2023-09-??)

- Prepare for nested filter, only for 2 level
- Add writer for save as file (ex: csv, json)

## [v1.4.0](https://github.com/cacing69/cquery/compare/v1.4.0...v1.3.1) (2023-09-14)

- Can used limit on Parse class, based on this PR [#9](https://github.com/cacing69/cquery/pull/9)
- Fixing filter on `raw()` method
- create new expression `append(selector)` for append single element to query
- create new expression for static value with double quote `"rawValue"`
- create new expression for static int  example `99`
- create new expression for static float  example `9.9`

## [v1.3.1](https://github.com/cacing69/cquery/compare/v1.3.1...v1.3.0) (2023-09-11)

- Add method pluck on collection for get single array from collection given
- Add method last on cquery for get single array from collection given
- Improvement on `append_node` function definer

## [v1.3.0](https://github.com/cacing69/cquery/compare/v1.2.0...v1.3.0) (2023-09-09)

- Add property callbackClientOnEnd on Loader class for handling when scraping finish on loader base class
- Add raw method based on this pull request [#3](https://github.com/cacing69/cquery/pull/3), it's still very limited, but at least it's working,

## [v1.2.0](https://github.com/cacing69/cquery/compare/v1.1.0...v1.2.0) (2023-09-06)

- Support for multi Loader, it will be enable to scrape with another engine
- Now u can scrape webpage loaded by js/ajax, but u need to use [Panther Loader](https://github.com/cacing69/cquery-panther-loader) to make it work

## [v1.1.0](https://github.com/cacing69/cquery/compare/v1.0.0...v1.1.0) (2023-09-06)

- Add method onContentLoaded(Closure $closure) for submit or go to another page when content loaded
- Add method eachItem(Closure $closure) to manipulate each item
- Add method onObtainedResults(Closure $closure) to manipulate result array with closure callback
- Add single quotes expression
- Add String expression
- Add Integer expression
- Add Float expression

## [v1.0.0](https://github.com/cacing69/cquery/releases/tag/v1.0.0) (2023-09-05)

- First release

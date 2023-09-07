# Release Notes

## [v1.3.0](https://github.com/cacing69/cquery/compare/v1.2.0...v1.3.0) (2023-09-0?)

- Add property callbackOnEnd on Loader class

## [v1.2.0](https://github.com/cacing69/cquery/compare/v1.1.0...v1.2.0) (2023-09-06)

- Support for multi Loader, it will be enable to scrape with another engine
- Now u can scrape webpage loaded by js/ajax, but u need to use [Panther Adapter](https://github.com/cacing69/cquery-panther-loader) to make it work

## [v1.1.0](https://github.com/cacing69/cquery/compare/v1.0.0...v1.1.0) (2023-09-06)

- Add method onContentLoaded(Closure $closure) for submit or go to another page when content loaded
- Add method eachItem(Closure $closure) to manipulate each item
- Add method onObtainedResults(Closure $closure) to manipulate result array with closure callback
- Add single quotes adapter
- Add String adapter
- Add Integer adapter
- Add Float adapter

## [v1.0.0](https://github.com/cacing69/cquery/releases/tag/v1.0.0) (2023-09-05)

- First release

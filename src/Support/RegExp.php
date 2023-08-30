<?php
namespace Cacing69\Cquery\Support;

class RegExp {
    const EXTRACT_FIRST_PARAM_ATTRIBUTE = '/^attr\(\s*?(.*?),\s*?.*\)$/is';
    const EXTRACT_SECOND_PARAM_ATTRIBUTE = '/^attr\(\s*?.*\s?,\s*?(.*?)\)$/is';
    const EXTRACT_FIRST_PARAM_LENGTH = '/^length\(\s?(.*?)\s?\)$/is';
    const EXTRACT_FIRST_PARAM_UPPER = '/^upper\(\s?(.*?)\s?\)$/is';
    const EXTRACT_FIRST_PARAM_REVERSE = '/^reverse\(\s?(.*?)\s?\)$/is';
    const IS_DEFINER_HAVE_ALIAS = '/.+\s+?as\s+?.+/is';
    const IS_ATTRIBUTE = '/^attr(\s?.*\s?,\s?.*\s?)$/is';
    const IS_LENGTH = '/^length(\s?.*\s?)$/is';
    const IS_UPPER = '/^upper(\s?.*\s?)$/is';
    const IS_REVERSE = '/^reverse(\s?.*\s?)$/is';
    const IS_SOURCE_HAVE_ALIAS = '/\s*?\(\s*?.*\s*?\)\s+as\s+.*$/is';
    const EXTRACT_FIRST_PARAM_SOURCE_HAVE_ALIAS = '/^\s*?\(\s*(.*)\)\s+as\s+.*$/is';
    const EXTRACT_SECOND_PARAM_SOURCE_HAVE_ALIAS = '/^\s*?\(\s*.*\s+as\s+(.*)$/is';
    const IS_FILTER_LIKE_CONTAINS_VALUE = '/^%.+%$/is';
    const EXTRACT_FIRST_PARAM_FILTER_LIKE_CONTAINS_VALUE = '/^%(.*?)%$/is';
    const IS_FILTER_LIKE_END_WITH = '/^%.+$/im';
    const EXTRACT_FIRST_PARAM_FILTER_LIKE_END_WITH = '/^%(.*?)$/is';
    const IS_FILTER_LIKE_START_WITH = '/^.+%$/im';
    const EXTRACT_FIRST_PARAM_FILTER_LIKE_START_WITH = '/^(.*?)%$/is';
    const CHECK_AND_EXTRACT_PICKER_WITH_WRAP = '/^\s?\(\s?(.+)\s?\)\s?$/is';
}

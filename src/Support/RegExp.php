<?php

namespace Cacing69\Cquery\Support;

class RegExp
{
    public const EXTRACT_FIRST_PARAM_ATTRIBUTE = '/^attr\(\s*?(.*?),\s*?.*\)$/is';
    public const EXTRACT_SECOND_PARAM_ATTRIBUTE = '/^attr\(\s*?.*\s?,\s*?(.*?)\)$/is';
    public const EXTRACT_FIRST_PARAM_LENGTH = '/^length\(\s?(.*?)\s?\)$/is';
    public const EXTRACT_FIRST_PARAM_UPPER = '/^upper\(\s?(.*?)\s?\)$/is';
    public const EXTRACT_FIRST_PARAM_REVERSE = '/^reverse\(\s?(.*?)\s?\)$/is';
    public const IS_DEFINER_HAVE_ALIAS = '/.+\s+?as\s+?.+/is';
    public const IS_ATTRIBUTE = '/^attr(\s?.*\s?,\s?.*\s?)$/is';
    public const IS_LENGTH = '/^length(\s?.*\s?)$/is';
    public const IS_UPPER = '/^upper(\s?.*\s?)$/is';
    public const IS_REVERSE = '/^reverse(\s?.*\s?)$/is';
    public const IS_SOURCE_HAVE_ALIAS = '/\s*?\(\s*?.*\s*?\)\s+as\s+.*$/is';
    public const EXTRACT_FIRST_PARAM_SOURCE_HAVE_ALIAS = '/^\s*?\(\s*(.*)\)\s+as\s+.*$/is';
    public const EXTRACT_SECOND_PARAM_SOURCE_HAVE_ALIAS = '/^\s*?\(\s*.*\s+as\s+(.*)$/is';
    public const IS_FILTER_LIKE_CONTAINS_VALUE = '/^%.+%$/is';
    public const EXTRACT_FIRST_PARAM_FILTER_LIKE_CONTAINS_VALUE = '/^%(.*?)%$/is';
    public const IS_FILTER_LIKE_END_WITH = '/^%.+$/im';
    public const EXTRACT_FIRST_PARAM_FILTER_LIKE_END_WITH = '/^%(.*?)$/is';
    public const IS_FILTER_LIKE_START_WITH = '/^.+%$/im';
    public const EXTRACT_FIRST_PARAM_FILTER_LIKE_START_WITH = '/^(.*?)%$/is';
    public const CHECK_AND_EXTRACT_DEFINER_HAVE_WRAP = '/^\s?\(\s?(.+)\s?\)\s?$/is';
}

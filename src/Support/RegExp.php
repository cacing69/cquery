<?php

/**
 * This file is part of Cquery.
 *
 * (c) 2023 Ibnul Mutaki <ibnuul@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */


namespace Cacing69\Cquery\Support;

class RegExp
{
    public const IS_ATTRIBUTE = '/^\s*attr\(\s*([\w-]*?)\s*,\s*(.*?)\s*\)\s*(as)?\s*\w*\s*,?/i';
    public const IS_LENGTH = '/^\s*length\(\s*(.*?)\s*\)\s*$/is';
    public const IS_UPPER = '/^\s*upper\(\s*(.*?)\s*\)\s*$/is';
    public const IS_LOWER = '/^\s*lower\(\s*(.*?)\s*\)\s*$/is';
    public const IS_REVERSE = '/^\s*reverse\(\s*(.*?)\s*\)\s*$/is';
    public const IS_REPLACE = '/^\s*replace\(\s*(.*?)\s*,\s*(.*?)\s*,\s*(.*?)\s*\)\s*$/';
    public const IS_DEFINER_HAVE_ALIAS = '/.+\s+?as\s+?.+/is';
    public const IS_SOURCE_HAVE_ALIAS = '/\s*?\(\s*?(.*)\s*?\)\s+as\s+(.*)$/is';
    public const IS_FILTER_LIKE_CONTAINS = '/^%(.*?)%$/is';
    public const IS_FILTER_LIKE_END_WITH = '/^%(.*?)$/im';
    public const IS_FILTER_LIKE_START_WITH = '/^(.*?)%$/im';
    public const IS_DEFINER_HAVE_PARENTHESES = '/^\s?\(\s?(.+)\s?\)\s?$/is';
    public const EXTRACT_FILTER_FROM_PARSER = '/(.*?)\s*(=|==|===|!=|<>|>|>=|<|<=|has|regex|like)\s*(\'.*\'|\d)/i';
}

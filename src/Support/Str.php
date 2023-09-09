<?php

namespace Cacing69\Cquery\Support;

use Cocur\Slugify\Slugify;

class Str
{
    public static function slug($text): string
    {
        $slugify = new Slugify(["separator" => "_"]);

        return $slugify->slugify($text);
    }

    // https://stackoverflow.com/a/33546903
    public static function isNonEmptyString($val): bool
    {
        return is_string($val) && $val !== '';
    }

    // https://stackoverflow.com/a/7168986
    public static function startsWith($haystack, $needle)
    {
        return preg_match('~' . preg_quote($needle, '~') . '~A', $haystack) > 0;
    }

    // https://stackoverflow.com/a/834355
    public static function endWith($haystack, $needle)
    {
        $length = strlen($needle);
        if(!$length) {
            return true;
        }
        return substr($haystack, -$length) === $needle;
    }
}

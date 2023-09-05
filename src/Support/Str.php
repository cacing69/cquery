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
}

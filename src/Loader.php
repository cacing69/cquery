<?php

declare(strict_types=1);

namespace Cacing69\Cquery;

use Doctrine\Common\Collections\ArrayCollection;

abstract class Loader
{
    protected $limit = null;

    protected $results = [];

    protected $source;

    protected $content;

    public function limit(int $limit)
    {
        $this->limit = $limit;
        return $this;
    }

    abstract protected function validateSource();
    abstract public function define(...$defines);
    abstract public function from(string $value);
    abstract public function setContent(string $value);

    public function first()
    {
        return $this
            ->limit(1)
            ->get()
            ->first();
    }

    abstract public function filter(...$filter);
    abstract public function OrFilter(...$filter);
    abstract public function get() : ArrayCollection;

    public static function getResultFilter(array $filtered): array
    {
        $result = [
            "and" => [],
            "or" => [],
        ];

        if (array_key_exists("and", $filtered) && count($filtered["and"]) > 0) {
            $result["and"] = array_intersect(...$filtered["and"]);
        }

        if (array_key_exists("or", $filtered) && count($filtered["or"]) > 0) {
            $result["or"] = array_unique(array_merge(...$filtered["or"]));
        }

        $filterResult = array_unique(array_merge($result["and"], $result["or"]));

        sort($filterResult, SORT_NUMERIC);

        return $filterResult;
    }
}

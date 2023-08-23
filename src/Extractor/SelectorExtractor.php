<?php

namespace Cacing69\Cquery\Extractor;

use Cacing69\Cquery\Adapter\WhereAttributeAdapter;

class SelectorExtractor
{
    private $raw;
    private $value;
    private $alias;

    public function __construct($init)
    {
        $this->raw = $init;
        if (preg_match('/\s*?\(\s*?.*\s*?\)\s+as\s+.*$/is', $init)) {
            preg_match('/^\s*?\(\s*(.*)\)\s+as\s+.*$/is', $init, $value);
            preg_match('/^\s*?\(\s*.*\s+as\s+(.*)$/is', $init, $alias);

            $this->value = $value[1];
            $this->alias = $alias[1];
        } else {
            $this->value = $init;
        }
    }

    public function __toString()
    {
        return $this->value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getRaw()
    {
        return $this->raw;
    }

    public function getAlias()
    {
        return $this->alias;
    }
}

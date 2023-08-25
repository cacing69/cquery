<?php

declare(strict_types=1);
namespace Cacing69\Cquery\Extractor;

use Symfony\Component\CssSelector\CssSelectorConverter;

class SourceExtractor
{
    private $raw;
    private $value;
    private $alias;
    private $xpath;

    public function __construct($init)
    {

        $this->raw = $init;
        $css = new CssSelectorConverter();
        if (preg_match('/\s*?\(\s*?.*\s*?\)\s+as\s+.*$/is', $init)) {
            preg_match('/^\s*?\(\s*(.*)\)\s+as\s+.*$/is', $init, $value);
            preg_match('/^\s*?\(\s*.*\s+as\s+(.*)$/is', $init, $alias);

            $this->value = $value[1];
            $this->alias = $alias[1];
        } else {
            $this->value = $init;
            $this->alias = "";
        }
        $this->xpath = $css->toXPath($this->value);
    }

    public function __toString()
    {
        return "{$this->value}";
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getRaw()
    {
        return $this->raw;
    }

    public function getXpath()
    {
        return $this->xpath;
    }

    public function isHasAlias()
    {
        return $this->alias !== "";
    }

    public function getAlias()
    {
        return $this->alias;
    }
}
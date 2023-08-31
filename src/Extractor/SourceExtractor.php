<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Extractor;

use Cacing69\Cquery\Support\RegExp;
use Cacing69\Cquery\Trait\HasAliasProperty;
use Symfony\Component\CssSelector\CssSelectorConverter;

class SourceExtractor
{
    use HasAliasProperty;
    private $raw;
    private $value;
    private $xpath;

    public function __construct($init)
    {

        $this->raw = $init;
        $css = new CssSelectorConverter();
        if (preg_match(RegExp::IS_SOURCE_HAVE_ALIAS, $init)) {
            preg_match(RegExp::IS_SOURCE_HAVE_ALIAS, $init, $extractAlias);

            $this->value = $extractAlias[1];
            $this->alias = $extractAlias[2];
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
}

<?php

declare(strict_types=1);
namespace Cacing69\Cquery\Extractor;

use Cacing69\Cquery\Support\RegExp;
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
        if (preg_match(RegExp::IS_SOURCE_HAVE_ALIAS, $init)) {
            preg_match(RegExp::EXTRACT_FIRST_PARAM_SOURCE_HAVE_ALIAS, $init, $value);
            preg_match(RegExp::EXTRACT_SECOND_PARAM_SOURCE_HAVE_ALIAS, $init, $alias);

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

<?php

declare(strict_types=1);

namespace Cacing69\Cquery;

use Cacing69\Cquery\Support\RegExp;
use Cacing69\Cquery\Trait\HasAliasProperty;
use Cacing69\Cquery\Trait\HasRawProperty;
use Symfony\Component\CssSelector\CssSelectorConverter;

/**
 * Source class used to define the source element to be scraped
 *
 * @author Ibnul Mutaki <ibnuu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Source
{
    use HasAliasProperty;
    use HasRawProperty;
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

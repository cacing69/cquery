<?php

/**
 * This file is part of Cquery.
 *
 * (c) 2023 Ibnul Mutaki <ibnuul@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cacing69\Cquery;

use Cacing69\Cquery\Support\RegExp;
use Cacing69\Cquery\Support\Str;
use Cacing69\Cquery\Trait\HasAliasProperty;
use Cacing69\Cquery\Trait\HasNodeProperty;
use Cacing69\Cquery\Trait\HasRawProperty;

/**
 * AN implementation Definer used to define each element that wil be scraped from predefined source.
 *
 * @author Ibnul Mutaki <ibnuu@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Definer
{
    use HasAliasProperty;
    use HasNodeProperty;
    use HasRawProperty;

    /**
     * @param string  $node     Its to define expression for scrape,
     *                          you can used query selector/function expression available
     * @param string  $alias    Its to set alias for key result
     * @param Closure $callback To create callback action to manipulate value
     */
    public function __construct($node, $alias = null, $callback = null)
    {
        // First parameter should be clean query selector without an alias
        // example 'h1 > a' or 'h1' and not 'h1 > a as title'
        // query selector contains alias, it will be throw an CqueryException
        if (preg_match('/^\s?.+\s+(as)\s+.+/', $node)) {
            throw new CqueryException('error define, please set alias on second parameter');
        }

        // Check if definer $node have parentheses, it should be exctract with these regex pattern
        if (preg_match(RegExp::IS_DEFINER_HAVE_PARENTHESES, $node)) {
            preg_match(RegExp::IS_DEFINER_HAVE_PARENTHESES, $node, $extract);
            $this->node = $extract[1];
        } else {
            $this->node = $node;
        }

        // If there is no alias, then an alias will be created as a slug using the separator _
        if ($alias) {
            $this->alias = $alias;
        } else {
            $this->alias = Str::slug($node);
        }

        // If there is no callback, raw will be an string expression with alias like
        // 'h1 > a as text' or if alias no provided it will be 'h1 > a as h1_a'
        if (!empty($callback)) {
            $this->raw = $callback;
        } else {
            $this->raw = $this->getNodeWithAlias();
        }
    }

    /**
     * Returns query selector string with alias provided.
     *
     * @return string
     */
    public function getNodeWithAlias()
    {
        return $this->node.' as '.$this->alias;
    }
}

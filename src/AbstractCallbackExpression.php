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
use Cacing69\Cquery\Trait\HasCallbackProperty;
use Cacing69\Cquery\Trait\HasNodeProperty;
use Cacing69\Cquery\Trait\HasOperatorProperty;
use Cacing69\Cquery\Trait\HasRawProperty;
use Closure;
use Symfony\Component\CssSelector\CssSelectorConverter;

abstract class AbstractCallbackExpression
{
    use HasOperatorProperty;
    use HasRawProperty;
    use HasNodeProperty;
    use HasCallbackProperty;
    protected $ref;
    protected $ignoreCallbackOnLoop = false;
    protected $callMethod;
    protected $callMethodParameter;
    protected $filter;

    public function getIgnoreCallbackOnLoop()
    {
        return $this->ignoreCallbackOnLoop;
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;

        return $this;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function setCallMethod($callMethod)
    {
        $this->callMethod = $callMethod;

        return $this;
    }

    public function getCallMethod()
    {
        return $this->callMethod;
    }

    protected function extractChild($raw)
    {
        $extractChild = new DefinerExtractor($raw);

        $this->node = $extractChild->getExpression()->getNode();

        $this->callMethod = $extractChild->getExpression()->getCallMethod();
        $this->callMethodParameter = $extractChild->getExpression()->getCallMethodParameter();

        return $extractChild;
    }

    public function getRef()
    {
        return $this->ref;
    }

    public function getCallMethodParameter()
    {
        return $this->callMethodParameter;
    }

    public function setCallMethodParameter($callMethodParameter)
    {
        $this->callMethodParameter = $callMethodParameter;

        return $this;
    }

    final public function getNodeWithoutAlias()
    {
        return $this;
    }

    public function getNodeXpath()
    {
        $css = new CssSelectorConverter();

        return $css->toXPath($this->node);
    }

    final public function extract($value)
    {
        return $this->filterExecutor($value);
    }

    final public function filterExecutor($value)
    {
        if (empty($value)) {
            return false;
        }

        if (in_array($this->filter->getOperator(), ['=', '==', '==='])) {
            if (in_array($this->filter->getOperator(), ['=', '=='])) {
                return $this->filter->getValue() == $value;
            } else {
                return $this->filter->getValue() === $value;
            }
        } elseif ($this->filter->getOperator() === 'regex') {
            return (empty($value)) ? false : preg_match($this->filter->getValue(), $value);
        } elseif ($this->filter->getOperator() === 'has') {
            return preg_match("/^\s?{$this->filter->getValue()}|\s{$this->filter->getValue()}\s|\s{$this->filter->getValue()}$/im", $value);
        } elseif ($this->filter->getOperator() === '>') {
            return (int) $value > (int) $this->filter->getValue();
        } elseif ($this->filter->getOperator() === '>=') {
            return (int) $value >= (int) $this->filter->getValue();
        } elseif ($this->filter->getOperator() === '<') {
            return  (int) $value < (int) $this->filter->getValue();
        } elseif ($this->filter->getOperator() === '<=') {
            return (int) $value <= (int) $this->filter->getValue();
        } elseif ($this->filter->getOperator() === 'like') {
            if (preg_match(RegExp::IS_FILTER_LIKE_CONTAINS, $this->filter->getValue())) {
                preg_match(RegExp::IS_FILTER_LIKE_CONTAINS, $this->filter->getValue(), $extract);

                return preg_match("/{$extract[1]}/im", $value);
            } elseif (preg_match(RegExp::IS_FILTER_LIKE_END_WITH, $this->filter->getValue())) {
                preg_match(RegExp::IS_FILTER_LIKE_END_WITH, $this->filter->getValue(), $extract);

                return preg_match("/.*{$extract[1]}$/im", $value);
            } elseif (preg_match(RegExp::IS_FILTER_LIKE_START_WITH, $this->filter->getValue())) {
                preg_match(RegExp::IS_FILTER_LIKE_START_WITH, $this->filter->getValue(), $extract);

                return preg_match("/^{$extract[1]}.*/im", $value);
            }
        } elseif (in_array($this->filter->getOperator(), ['<>', '!=', '!=='])) {
            if (in_array($this->filter->getOperator(), ['<>', '!='])) {
                return $value != $this->filter->getValue();
            } else {
                return $value != $this->filter->getValue();
            }
        } elseif ($this->filter->getOperator() instanceof Closure) {
            $_closure = $this->filter->getOperator();

            return $_closure($value);
        } else {
            throw new CqueryException("operator {$this->filter->getOperator()} doesn't support");
        }
    }
}

<?php

declare(strict_types=1);

namespace Cacing69\Cquery;

use Cacing69\Cquery\Exception\CqueryException;
use Cacing69\Cquery\Support\RegExp;
use Cacing69\Cquery\Trait\HasOperatorProperty;
use Cacing69\Cquery\Extractor\DefinerExtractor;
use Closure;
use Cacing69\Cquery\Trait\HasNodeProperty;
use Cacing69\Cquery\Trait\HasSelectorProperty;
use Cacing69\Cquery\Trait\HasRawProperty;
use Symfony\Component\CssSelector\CssSelectorConverter;

abstract class CallbackAdapter
{
    use HasOperatorProperty;
    use HasSelectorProperty;
    use HasRawProperty;
    use HasNodeProperty;
    protected $ref;
    protected $callMethod;
    protected $callMethodParameter;
    protected $callback;

    protected $filter;
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

        $this->node = $extractChild->getAdapter()->getNode();

        $this->callMethod = $extractChild->getAdapter()->getCallMethod();
        $this->callMethodParameter = $extractChild->getAdapter()->getCallMethodParameter();

        return $extractChild;
    }

    public function getRef()
    {
        return $this->ref;
    }

    public function getCallback()
    {
        return $this->callback;
    }

    public function setCallback($callback)
    {
        $this->callback = $callback;
        return $this;
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
        if(empty($value)) {
            return false;
        }

        if (in_array($this->filter->getOperator(), ["=", "==", "==="])) {
            if (in_array($this->filter->getOperator(), ["=", "=="])) {
                return $this->filter->getValue() == $value;
            } else {
                return $this->filter->getValue() === $value;
            }
        } elseif ($this->filter->getOperator() === "regex") {
            return (empty($value)) ? false : preg_match($this->filter->getValue(), $value);
        } elseif ($this->filter->getOperator() === "has") {
            return preg_match("/^\s?{$this->filter->getValue()}|\s{$this->filter->getValue()}\s|\s{$this->filter->getValue()}$/im", $value);
        } elseif ($this->filter->getOperator() === ">") {
            return (int) $value > (int) $this->filter->getValue();
        } elseif ($this->filter->getOperator() === ">=") {
            return (int) $value >= (int) $this->filter->getValue();
        } elseif ($this->filter->getOperator() === "<") {
            $criteria = $this->filter->getValue();
            return  (int) $value < (int) $criteria;
        } elseif ($this->filter->getOperator() === "<=") {

            return (int) $value <= (int) $this->filter->getValue();
        } elseif ($this->filter->getOperator() === "like") {
            if (preg_match(RegExp::IS_FILTER_LIKE_CONTAINS_VALUE, $this->filter->getValue())) {

                preg_match(RegExp::IS_FILTER_LIKE_CONTAINS_VALUE, $this->filter->getValue(), $extract);

                return preg_match("/{$extract[1]}/im", $value);
            } elseif (preg_match(RegExp::IS_FILTER_LIKE_END_WITH, $this->filter->getValue())) {

                preg_match(RegExp::IS_FILTER_LIKE_END_WITH, $this->filter->getValue(), $extract);

                return preg_match("/.*{$extract[1]}$/im", $value);
            } elseif (preg_match(RegExp::IS_FILTER_LIKE_START_WITH, $this->filter->getValue())) {

                preg_match(RegExp::IS_FILTER_LIKE_START_WITH, $this->filter->getValue(), $extract);

                return preg_match("/^{$extract[1]}.*/im", $value);
            }
        } elseif (in_array($this->filter->getOperator(), ["<>", "!=", "!=="])) {
            if (in_array($this->filter->getOperator(), ["<>", "!="])) {
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

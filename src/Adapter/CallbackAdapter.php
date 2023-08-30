<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Adapter;

use Cacing69\Cquery\Exception\CqueryException;
use Cacing69\Cquery\Support\RegExp;
use Cacing69\Cquery\Trait\HasOperatorProperty;
use Closure;
use Cacing69\Cquery\Trait\HasNodeProperty;
use Cacing69\Cquery\Trait\HasCriteriaProperty;
use Cacing69\Cquery\Trait\HasClauseProperty;
use Cacing69\Cquery\Trait\HasSelectorProperty;
use Cacing69\Cquery\Trait\HasFilterProperty;
use Cacing69\Cquery\Trait\HasRawProperty;
use Symfony\Component\CssSelector\CssSelectorConverter;

abstract class CallbackAdapter
{
    use HasOperatorProperty;
    use HasFilterProperty;
    use HasSelectorProperty;
    use HasClauseProperty;
    use HasCriteriaProperty;
    use HasRawProperty;
    use HasNodeProperty;
    protected $ref;
    protected $call;
    protected $callParameter;
    protected $afterCall;

    protected $callback;

    public function getCallback()
    {
        return $this->callback;
    }

    public function setCall($call)
    {
        $this->call = $call;
        return $this;
    }

    public function getCall()
    {
        return $this->call;
    }

    public function getRef()
    {
        return $this->ref;
    }

    public function getAfterCall()
    {
        return $this->afterCall;
    }

    public function setAfterCall($afterCall)
    {
        $this->afterCall = $afterCall;
        return $this;
    }

    public function getCallParameter()
    {
        return $this->callParameter;
    }

    public function setCallParameter($callParameter)
    {
        $this->callParameter = $callParameter;
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
        if(empty($value)) return false;

        if (in_array($this->clause, ["=", "==", "==="])) {
            if (in_array($this->clause, ["=", "=="])) {
                $this->clauseType = "equals";
                return $this->criteria == $value;
            } else {
                $this->clauseType = "identical";
                return $this->criteria === $value;
            }
        } else if ($this->clause === "regex") {
            $this->clauseType = "regular expressions";

            return (empty($value)) ? false : preg_match($this->criteria, $value);
        } else if ($this->clause === "has") {
            $this->clauseType = 'has value';
            return preg_match("/^\s?{$this->criteria}|\s{$this->criteria}\s|\s{$this->criteria}$/im", $value);
        } else if ($this->clause === ">") {
            $this->clauseType = "greater than";

            return (int) $value > (int) $this->criteria;
        } else if ($this->clause === ">=") {
            $this->clauseType = "greater than equals";

            return (int) $value >= (int) $this->criteria;
        } else if ($this->clause === "<") {
            $this->clauseType = "less than";
            $criteria = $this->criteria;

            return  (int) $value < (int) $criteria;
        } else if ($this->clause === "<=") {
            $this->clauseType = "less than equals";

            return (int) $value <= (int) $this->criteria;
        } else if ($this->clause === "like") {
            if (preg_match(RegExp::IS_FILTER_LIKE_CONTAINS_VALUE, $this->criteria)) {
                $this->clauseType = "contains value '{$this->criteria}'";

                preg_match(RegExp::EXTRACT_FIRST_PARAM_FILTER_LIKE_CONTAINS_VALUE, $this->criteria, $extract);

                return preg_match("/{$extract[1]}/im", $value);
            } else if (preg_match(RegExp::IS_FILTER_LIKE_END_WITH, $this->criteria)) {
                $this->clauseType = "end with '{$this->criteria}'";

                preg_match(RegExp::EXTRACT_FIRST_PARAM_FILTER_LIKE_END_WITH, $this->criteria, $extract);

                return preg_match("/.*{$extract[1]}$/im", $value);
            } else if (preg_match(RegExp::IS_FILTER_LIKE_START_WITH, $this->criteria)) {
                $this->clauseType = "start with '{$this->criteria}'";

                preg_match(RegExp::EXTRACT_FIRST_PARAM_FILTER_LIKE_START_WITH, $this->criteria, $extract);

                return preg_match("/^{$extract[1]}.*/im", $value);
            }

            return  $value > $this->criteria;
        } else if (in_array($this->clause, ["<>", "!=", "!=="])) {
            if (in_array($this->clause, ["<>", "!="])) {
                $this->clauseType = 'not equal';
                return $value != $this->criteria;
            } else {
                $this->clauseType = 'not identical';
                return $value != $this->criteria;
            }
        } else if ($this->clause instanceof Closure) {
            $clause = $this->clause;
            return $clause($value);
        }else {
            throw new CqueryException("operator {$this->clause} doesn't support");
        }
    }
}

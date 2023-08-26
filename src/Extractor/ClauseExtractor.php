<?php

declare(strict_types=1);
namespace Cacing69\Cquery\Extractor;
use Cacing69\Cquery\Exception\CqueryException;

trait ClauseExtractor
{
    protected $clause = "and";
    protected $clauseType;
    protected $pattern;
    protected $callback;
    protected $value;
    final protected function extract($clause, $clauseValue){
        $this->clause = strtolower(trim($clause));
        if (strtolower(trim($clause)) === "has") {
            $this->clauseType = 'has';

            $this->value = $clauseValue;
            $this->pattern = "/^\s?{$clauseValue}|\s{$clauseValue}\s|\s{$clauseValue}$/im";
        } else if(trim($clause) === "="){
            $this->clauseType = 'equals';

            $this->value = $clauseValue;
            // dd($this->getCallbackAdapter()("lorem"));
            if ($this->getCallbackAdapter !== null) {

            } else {
                if ($this->pattern !== null) {
                    $this->pattern = "/^{$clauseValue}$/im";
                }
                // else {
                //     $this->callback = function ($e) use ($clauseValue) {
                //         return $this->getCallbackAdapter()($clauseValue);
                //     };
                // }
            }

        } else if(trim($clause) === "like") {
            if (preg_match('/^%.+%$/im', $clauseValue)) {
                $this->clauseType = 'contains';

                preg_match('/^%(.*?)%$/is', $clauseValue, $value);
                $this->value = $value[1];
                $this->pattern = "/{$value[1]}/im";
            } else if (preg_match('/^%.+$/im', $clauseValue)) {
                $this->clauseType = 'end with';

                preg_match('/^%(.*?)$/is', $clauseValue, $value);
                $this->value = $value[1];
                $this->pattern = "/.*{$value[1]}$/im";
            } else if (preg_match('/^.+%$/im', $clauseValue)) {
                $this->clauseType = 'start with';

                preg_match('/^(.*?)%$/is', $clauseValue, $value);
                $this->value = $value[1];
                $this->pattern = "/^{$value[1]}.*/im";
            }
        } else if (in_array(trim($clause), ["<", ">", "<=", ">=", "<>", "!="])) {
            if(!is_numeric($clauseValue)){
                throw new CqueryException("comparison operator need a numeric value");
            }
            if (trim($clause) === "<") {
                $this->clauseType = 'less than';
                $this->value = $clauseValue;

                $this->callback = function ($e) use ($clauseValue) {
                    return (int) $e < $clauseValue;
                };
            } else if (trim($clause) === ">") {
                $this->clauseType = 'less than';
                $this->value = $clauseValue;

                $this->callback = function ($e) use ($clauseValue) {
                    return (int) $e > $clauseValue;
                };
            } else if (trim($clause) === "<=") {
                $this->clauseType = 'less than equal';
                $this->value = $clauseValue;

                $this->callback = function ($e) use ($clauseValue) {
                    return (int) $e <= $clauseValue;
                };
            } else if (trim($clause) === ">=") {
                $this->clauseType = 'greater than equal';
                $this->value = $clauseValue;

                $this->callback = function ($e) use ($clauseValue) {
                    return (int) $e >= $clauseValue;
                };
            } else if (in_array(trim($clause), ["<>", "!="])) {
                $this->clauseType = 'not equal';
                $this->value = $clauseValue;

                $this->callback = function ($e) use ($clauseValue) {
                    return (int) $e !== $clauseValue;
                };
            }
        } if(trim($clause) === "regex"){
            $this->clauseType = 'regex';

            $this->value = $clauseValue;
            $this->pattern = $clauseValue;
        }

        return $this;
    }

    public function getClause(): string
    {
        return $this->clause;
    }

    public function getClauseType(): string
    {
        return $this->clauseType;
    }

    public function getPattern()
    {
        return $this->pattern;
    }

    public function getCallback()
    {
        return $this->callback;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}

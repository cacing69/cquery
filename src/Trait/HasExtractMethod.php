<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Trait;

use Cacing69\Cquery\Exception\CqueryException;

trait HasExtractMethod
{
    final public function extract($value)
    {
        if (in_array($this->clause, ["=", "==", "==="])) {
            if(in_array($this->clause, ["=", "=="])) {
                $this->clauseType = "equals";
                return $this->criteria == $value;
            } else {
                $this->clauseType = "identical";
                return $this->criteria === $value;
            }
        } else if($this->clause === "regex"){
            $this->clauseType = "regex pattern";

            // dd($this, $value);

            if($value === null) {
                return false;
            } else {
                return preg_match($this->criteria, $value);
            }
        } else if ($this->clause === "has") {
            $this->clauseType = 'has value';
            return preg_match("/^\s?{$this->criteria}|\s{$this->criteria}\s|\s{$this->criteria}$/im", $value);
        } else if ($this->clause === ">") {
            $this->clauseType = "greater than";

            if ($value === null) {
                return false;
            } else {
                return  $value > $this->criteria;
            }
        } else if ($this->clause === ">=") {
            $this->clauseType = "greater than equals";

            if ($value === null) {
                return false;
            } else {
                return (int) $value >= (int) $this->criteria;
            }
        } else if ($this->clause === "<") {
            $this->clauseType = "less than";
            $criteria = $this->criteria;
            // dd($this);
            // dump(gettype($this->criteria), gettype($value));
            // return (int) $this->criteria < (int) $value;
            // $fn = fu/nction ($criteria, $value) {
                // echo "{$value} < {$criteria} = " . ($value < $criteria) .strlen($value)."\n";
                if($value === null) {
                    return false;
                } else {
                    return (int) $value < (int) $criteria;
                }
            // };

            // return $fn($this->criteria, $value);
        } else if ($this->clause === "<=") {
            $this->clauseType = "less than equals";

            if ($value === null) {
                return false;
            } else {
                return (int) $value <= (int) $this->criteria;
            }

        } else if ($this->clause === "like") {
            if (preg_match('/^%.+%$/im', $this->criteria)) {
                $this->clauseType = "contains value '{$this->criteria}'";

                preg_match('/^%(.*?)%$/is', $this->criteria, $extract);

                // dd($value, $extract, $this->criteria);
                // $this->value = $value[1];
                return preg_match("/{$extract[1]}/im", $value);

            } else if (preg_match('/^%.+$/im', $this->criteria)) {
                $this->clauseType = "end with '{$this->criteria}'";

                preg_match('/^%(.*?)$/is', $this->criteria, $extract);
                // $this->value = $value[1];
                return preg_match("/.*{$extract[1]}$/im", $value);
            } else if (preg_match('/^.+%$/im', $this->criteria)) {
                $this->clauseType = "start with '{$this->criteria}'";

                preg_match('/^(.*?)%$/is', $this->criteria, $extract);
                // $this->value = $value[1];
                return preg_match("/^{$extract[1]}.*/im", $value);
            }
            // $this->clauseType < "less than equals";

            return  $value > $this->criteria;
        } else if (in_array($this->clause, ["<>", "!=", "!=="])) {
            if(in_array($this->clause, ["<>", "!="])) {
                $this->clauseType = 'not equal';
                return $value != $this->criteria;
            } else {
                $this->clauseType = 'not identical';
                return $value != $this->criteria;
            }
        }  else {
            throw new CqueryException("operator {$this->clause} doesn't support");

        }
    }
}

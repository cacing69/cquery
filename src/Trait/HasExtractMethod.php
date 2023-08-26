<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Trait;

use Cacing69\Cquery\Exception\CqueryException;
use Cacing69\Cquery\Support\CqueryRegex;

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
            $this->clauseType = "regular expressions";

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

            if($value === null) {
                return false;
            } else {
                return (int) $value < (int) $criteria;
            }
        } else if ($this->clause === "<=") {
            $this->clauseType = "less than equals";

            if ($value === null) {
                return false;
            } else {
                return (int) $value <= (int) $this->criteria;
            }

        } else if ($this->clause === "like") {
            if (preg_match(CqueryRegex::IS_FILTER_LIKE_CONTAINS_VALUE, $this->criteria)) {
                $this->clauseType = "contains value '{$this->criteria}'";

                preg_match(CqueryRegex::EXTRACT_FIRST_PARAM_FILTER_LIKE_CONTAINS_VALUE, $this->criteria, $extract);

                return preg_match("/{$extract[1]}/im", $value);
            } else if (preg_match(CqueryRegex::IS_FILTER_LIKE_END_WITH, $this->criteria)) {
                $this->clauseType = "end with '{$this->criteria}'";

                preg_match(CqueryRegex::EXTRACT_FIRST_PARAM_FILTER_LIKE_END_WITH, $this->criteria, $extract);

                return preg_match("/.*{$extract[1]}$/im", $value);
            } else if (preg_match(CqueryRegex::IS_FILTER_LIKE_START_WITH, $this->criteria)) {
                $this->clauseType = "start with '{$this->criteria}'";

                preg_match(CqueryRegex::EXTRACT_FIRST_PARAM_FILTER_LIKE_START_WITH, $this->criteria, $extract);

                return preg_match("/^{$extract[1]}.*/im", $value);
            }

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

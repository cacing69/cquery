<?php

declare(strict_types=1);
namespace Cacing69\Cquery\Extractor;

trait ClauseExtractor
{
    protected $clause = "and";
    protected $clauseType;
    protected $pattern;
    protected $value;
    final protected function extract($clause, $clauseValue){
        if (strtolower(trim($clause)) === "like") {
            $this->clause = strtolower(trim($clause));

            // search parameter is match with %val%
            if (preg_match('/^%.+%$/im', $clauseValue)) {
                $this->clauseType = 'contains';

                preg_match('/^%(.*?)%$/is', $clauseValue, $value);
                $this->value = $value[1];
                $this->pattern = "/^\s?{$value[1]}|\s{$value[1]}\s|\s{$value[1]}$/im";
            }
        } else if(trim($clause) === "="){
            $this->clause = "=";
            $this->clauseType = 'equals';
            $this->value = $clauseValue;
            $this->pattern = "/^\s?{$clauseValue}\s?$/im";
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

    public function getValue(): string
    {
        return $this->value;
    }
}

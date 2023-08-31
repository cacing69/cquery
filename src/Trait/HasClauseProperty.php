<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Trait;

trait HasClauseProperty
{
    private $clause;
    private $clauseType;

    public function setClause($clause)
    {
        $this->clause = $clause;

        return $this;
    }

    public function getClause()
    {
        return $this->clause;
    }

    protected function setClauseType($clauseType)
    {
        $this->clauseType = $clauseType;

        return $this;
    }

    public function getClauseType()
    {
        return $this->clauseType;
    }
}

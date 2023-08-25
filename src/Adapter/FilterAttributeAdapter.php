<?php

declare(strict_types=1);
namespace Cacing69\Cquery\Adapter;

use Cacing69\Cquery\Support\HasOperatorProperty;
use Cacing69\Cquery\Support\HasSelectorProperty;

class FilterAttributeAdapter extends AttributeAdapter{
    use HasSelectorProperty;
    use HasOperatorProperty;
    private $filter;
    private $clause;
    private $clauseType;
    private $pattern;
    private $value;

    public function __construct(array $raw)
    {
        parent::__construct($raw[0]);
        $this->filter = $raw;
        $this->clause = "and";
    }

    public function transform(): FilterAttributeAdapter
    {
        preg_match('/^attr\(\s*?(.*?),\s*?.*\)$/is', $this->filter[0], $attr);
        preg_match('/^attr\(\s*?.*\s?,\s*?(.*?)\)$/is', $this->filter[0], $node);
        $this->ref = $attr[1];
        $this->node = $node[1];

        if(in_array(strtolower(trim($this->filter[1])), ["like", "=", "!=", "<>"])) {
            $this->clause = strtolower(trim($this->filter[1]));

            // search parameter is match with %val%
            if(preg_match('/^%.+%$/im', $this->filter[2])) {
                $this->clauseType = 'contains';

                preg_match('/^%(.*?)%$/is', $this->filter[2], $value);
                $this->value = $value[1];
                $this->pattern = "/^\s?{$value[1]}|\s{$value[1]}\s|\s{$value[1]}$/im";
            }
        } else {
            $this->clause = "=";
            $this->value = $this->filter[1];
        }

        if($this->selector && $this->selector->getAlias() != null) {

        }

        return $this;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function getClause(): string
    {
        return $this->clause;
    }

    public function getClauseType(): string
    {
        return $this->clauseType;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function adapt($extractor): FilterAttributeAdapter
    {
        $this->selector = $extractor->getSelector();
        $this->operator = $extractor->getOperator();

        return $this;
    }
}

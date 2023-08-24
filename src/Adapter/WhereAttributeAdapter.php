<?php
namespace Cacing69\Cquery\Adapter;

use Cacing69\Cquery\Support\HasSelectorProperty;

class WhereAttributeAdapter extends AttributeAdapter{
    use HasSelectorProperty;
    private $where;
    private $operator;
    private $operatorType;
    private $pattern;
    private $value;

    public function __construct($raw)
    {
        parent::__construct($raw[0]);
        $this->where = $raw;
    }

    public function transform()
    {
        preg_match('/^attr\(\s*?(.*?),\s*?.*\)$/is', $this->where[0], $attr);
        preg_match('/^attr\(\s*?.*\s?,\s*?(.*?)\)$/is', $this->where[0], $node);

        $this->ref = $attr[1];
        $this->node = $node[1];

        if(in_array(strtolower(trim($this->where[1])), ["like", "=", "!=", "<>"])) {
            $this->operator = strtolower(trim($this->where[1]));
            // search parameter is match with %val%
            if(preg_match('/^%.+%$/im', $this->where[2])) {
                $this->operatorType = 'contains';

                preg_match('/^%(.*?)%$/is', $this->where[2], $value);
                $this->value = $value[1];
                $this->pattern = "/^\s?{$value[1]}|\s{$value[1]}\s|\s{$value[1]}$/im";
            }
        } else {
            $this->operator = "=";
            $this->value = $this->where[1];
        }

        if($this->selector && $this->selector->getAlias() != null) {

        }
        return $this;
    }

    public function getWhere()
    {
        return $this->where;
    }

    public function getOperator()
    {
        return $this->operator;
    }

    public function getOperatorType()
    {
        return $this->operatorType;
    }

    public function getPattern()
    {
        return $this->pattern;
    }

    public function getValue()
    {
        return $this->value;
    }
}

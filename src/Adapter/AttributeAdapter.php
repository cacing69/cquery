<?php
namespace Cacing69\Cquery\Adapter;

use Cacing69\Cquery\Support\HasSelectorProperty;

class AttributeAdapter {
    use HasSelectorProperty;
    private $raw;
    private $ref;
    private $refType;
    private $operator;
    private $operatorType;
    private $pattern;
    private $selectNode;
    private $value;

    public function __construct($where)
    {
        $this->raw = $where;
        $this->refType = "attribute";
    }

    public function transform()
    {
        preg_match('/^attr\(\s*?(.*?),\s*?.*\)$/is', $this->raw[0], $attr);
        preg_match('/^attr\(\s*?.*\s?,\s*?(.*?)\)$/is', $this->raw[0], $node);

        $this->ref = $attr[1];
        $this->selectNode = trim($node[1]);

        if(in_array(strtolower(trim($this->raw[1])), ["like", "=", "!=", "<>"])) {
            $this->operator = strtolower(trim($this->raw[1]));
            // search parameter is match with %val%
            if(preg_match('/^%.+%$/im', $this->raw[2])) {
                $this->operatorType = 'contains';

                preg_match('/^%(.*?)%$/is', $this->raw[2], $value);
                $this->value = $value[1];
                $this->pattern = "/^\s?{$value[1]}|\s{$value[1]}\s|\s{$value[1]}$/im";
            }
        } else {
            $this->operator = "=";
            $this->value = $this->raw[1];
        }

        if($this->selector && $this->selector->getAlias() != null) {

        }
        return $this;
    }

    public function getRaw()
    {
        return $this->raw;
    }

    public function getRef()
    {
        return $this->ref;
    }

    public function getRefType()
    {
        return $this->refType;
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

    public function getSelectNode()
    {
        return $this->selectNode;
    }

    public function getValue()
    {
        return $this->value;
    }
}

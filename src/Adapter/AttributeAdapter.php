<?php

namespace Cacing69\Cquery\Adapter;

abstract class AttributeAdapter {
    protected $raw;
    protected $ref;
    protected $refType = "attribute";
    protected $node;

    public function __construct($raw)
    {
        $this->raw = $raw;

        preg_match('/^attr\(\s*?(.*?),\s*?.*\)$/is', $raw, $attr);
        preg_match('/^attr\(\s*?.*\s?,\s*?(.*?)\)$/is', $raw, $node);

        $this->ref = $attr[1];
        $this->node = $node[1];
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

    public function getNode()
    {
        return $this->node;
    }
}

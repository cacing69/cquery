<?php
declare(strict_types = 1);
namespace Cacing69\Cquery\Adapter;

class AttributeAdapter {
    protected $raw;
    protected $ref;
    protected $refType = "attribute";
    protected $node;

    public function __construct(string $raw)
    {
        $this->raw = $raw;

        preg_match('/^attr\(\s*?(.*?),\s*?.*\)$/is', $raw, $attr);
        preg_match('/^attr\(\s*?.*\s?,\s*?(.*?)\)$/is', $raw, $node);

        $this->ref = $attr[1];
        $this->node = $node[1];
    }

    public function getRaw(): string
    {
        return $this->raw;
    }

    public function getRef(): string
    {
        return $this->ref;
    }

    public function getRefType(): string
    {
        return $this->refType;
    }

    public function getNode(): string
    {
        return $this->node;
    }

    protected $callbackAdapter;

    public function getCallbackAdapter()
    {
        return $this->callbackAdapter;
    }
}

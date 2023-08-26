<?php

declare(strict_types=1);
namespace Cacing69\Cquery\Filter;

use Cacing69\Cquery\Adapter\AttributeAdapter;
use Cacing69\Cquery\Extractor\ClauseExtractor;
use Cacing69\Cquery\Trait\HasOperatorProperty;
use Cacing69\Cquery\Trait\HasSelectorProperty;

class FilterAttributeAdapter extends AttributeAdapter{
    use HasSelectorProperty;
    use HasOperatorProperty;
    use ClauseExtractor;
    private $sign = "attr(attrName, selector)";
    private $filter;

    public function __construct(array $raw)
    {
        parent::__construct($raw[0]);
        $this->filter = $raw;
    }

    public function transform(): FilterAttributeAdapter
    {
        preg_match('/^attr\(\s*?(.*?),\s*?.*\)$/is', $this->filter[0], $attr);
        preg_match('/^attr\(\s*?.*\s?,\s*?(.*?)\)$/is', $this->filter[0], $node);
        $this->ref = $attr[1];
        $this->node = $node[1];

        $clause = $this->filter[1];
        $clauseValue = $this->filter[2];

        return $this->extract($clause, $clauseValue);
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function adapt($extractor)
    {
        $this->selector = $extractor->getSelector();
        $this->operator = $extractor->getOperator();

        return $this;
    }
}

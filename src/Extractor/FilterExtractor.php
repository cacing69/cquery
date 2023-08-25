<?php
namespace Cacing69\Cquery\Extractor;

use Cacing69\Cquery\Adapter\FilterAttributeAdapter;
use Cacing69\Cquery\Support\HasOperatorProperty;
use Cacing69\Cquery\Support\HasSelectorProperty;

class FilterExtractor {
    use HasSelectorProperty;
    use HasOperatorProperty;

    private $raw;
    private $adapter;

    public function __construct($where, $operator = "and")
    {
        $this->raw = $where;
        $this->operator = $operator;
        if(preg_match('/^attr\(.*\s?,\s?.*\s?\)$/', $where[0])) {
            $this->adapter = new FilterAttributeAdapter($where);
        }
    }

    public function extract()
    {
        return $this->adapter->adapt($this)->transform();
    }
}

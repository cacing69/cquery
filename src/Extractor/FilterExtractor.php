<?php
namespace Cacing69\Cquery\Extractor;

use Cacing69\Cquery\Adapter\FilterAttributeAdapter;
use Cacing69\Cquery\Support\HasSelectorProperty;

class FilterExtractor {
    use HasSelectorProperty;

    private $raw;
    private $result;

    public function __construct($where)
    {
        $this->raw = $where;
        if(preg_match('/^attr\(.*\s?,\s?.*\s?\)$/', $where[0])) {
            $this->result = new FilterAttributeAdapter($where);
        }
    }

    public function extract()
    {
        return $this->result->setSelector($this->selector)->transform();
    }
}

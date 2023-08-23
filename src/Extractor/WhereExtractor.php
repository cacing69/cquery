<?php
namespace Cacing69\Cquery\Extractor;

use Cacing69\Cquery\Adapter\AttributeAdapter;
use Cacing69\Cquery\Support\HasSelectorProperty;

class WhereExtractor {
    use HasSelectorProperty;

    private $raw;
    private $result;

    public function __construct($where)
    {
        $this->raw = $where;
        if(preg_match('/^attr\(.*\s?,\s?.*\s?\)$/', $where[0])) {
            $this->result = new AttributeAdapter($where);
        }
    }

    public function extract()
    {
        return $this->result->setSelector($this->selector)->transform();
    }
}

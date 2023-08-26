<?php

declare(strict_types=1);
namespace Cacing69\Cquery\Extractor;

use Cacing69\Cquery\Filter\FilterAttributeAdapter;
use Cacing69\Cquery\Filter\FilterLengthAdapter;
use Cacing69\Cquery\Trait\HasOperatorProperty;
use Cacing69\Cquery\Trait\HasSelectorProperty;

class FilterExtractor {
    use HasSelectorProperty;
    use HasOperatorProperty;

    private $raw;
    private $adapter;

    public function __construct($where, $operator = "and")
    {
        $this->raw = $where;
        $this->operator = $operator;
        if(preg_match('/^attr\(.*\s?,\s.*\s?\)$/', $where[0])) {
            $this->adapter = new FilterAttributeAdapter($where);
        } else if (preg_match('/^length\(\s?.*\s?\)$/', $where[0])) {
            $this->adapter = new FilterLengthAdapter($where);
        } else {
            $this->adapter = $where;
        }
    }

    public function extract()
    {
        return $this->adapter->adapt($this)->transform();
    }
}

<?php

namespace Cacing69\Cquery\Extractor;

use Cacing69\Cquery\Adapter\AttributeAdapter;
use Cacing69\Cquery\Support\StringHelper;

class ColumnExtractor {
    private $raw;
    private $column;
    private $alias;
    public function __construct($column)
    {
        $this->raw = $column;
        $_column = null;
        if (preg_match('/.+\s+?as\s+?.+/im', $column)) {
            $decodeSelect = explode(" as ", $column);
            $_column = trim($decodeSelect[0]);
            $this->alias = StringHelper::slug($decodeSelect[1]);
        } else {
            $_column = $column;
            $this->alias = StringHelper::slug($column, "_");
        }

        if (preg_match("/^attr(.*,\s?.*)$/is", $column)) {
            $this->column = new AttributeAdapter($_column);
        } else {
            $this->column = $_column;
        }
    }

    public function getAlias() {
        return $this->alias;
    }

    public function getColumn() {
        return $this->column;
    }
}

<?php

namespace Cacing69\Cquery\Extractor;

use Cacing69\Cquery\Adapter\AttributeCallbackAdapter;
use Cacing69\Cquery\Adapter\DefaultCallbackAdapter;
use Cacing69\Cquery\Adapter\LengthCallbackAdapter;
use Cacing69\Cquery\Support\StringHelper;
use Cacing69\Cquery\Trait\HasSelectorProperty;

class DefinerExtractor {
    use HasSelectorProperty;
    private $raw;
    private $alias;
    private $definer;
    private $adapter;
    public function __construct($definer, SourceExtractor $selectorParent = null)
    {
        $this->selector = $selectorParent;
        $this->raw = $definer;
        if (preg_match('/.+\s+?as\s+?.+/im', $definer)) {
            $decodeSelect = explode(" as ", $definer);
            $this->definer = trim($decodeSelect[0]);
            $this->alias = StringHelper::slug($decodeSelect[1]);
        } else {
            $this->definer = $definer;
            $this->alias = StringHelper::slug($definer, "_");
        }

        if (preg_match("/^attr(.*,\s?.*)$/is", $definer)) {
            $this->adapter = new AttributeCallbackAdapter($this->definer, $selectorParent);
        } else if (preg_match("/^length(\s?.*\s?)$/is", $definer)) {
            $this->adapter = new LengthCallbackAdapter($this->definer, $selectorParent);
        } else {
            $this->adapter = new DefaultCallbackAdapter($this->definer, $selectorParent);
        }
    }

    public function getAlias() {
        return $this->alias;
    }

    public function getDefiner() {
        return $this->definer;
    }

    public function getAdapter()
    {
        return $this->adapter;
    }
}

<?php
declare(strict_types=1);

namespace Cacing69\Cquery;

use Cacing69\Cquery\Support\RegExp;
use Cacing69\Cquery\Support\Str;
use Cacing69\Cquery\Trait\HasRawProperty;
use Closure;
use Cacing69\Cquery\Trait\HasAliasProperty;
use Cacing69\Cquery\Trait\HasNodeProperty;
use Cacing69\Cquery\Trait\HasOptionsProperty;

class Picker
{
    use HasRawProperty;
    use HasAliasProperty;
    use HasNodeProperty;
    use HasOptionsProperty;
    private $tmpNode;

    public function __construct($raw, ...$options)
    {
        $this->options = $options;

        if($options[0] instanceof Closure) {
            $this->raw = $options[0];
            if(count($options) === 2) {
                $this->node = $raw;
                $this->alias = $options[1];
            } else if (count($options) === 1) {
                $this->node = $raw;
                $this->alias = Str::slug($this->node);
            }
        } else {
            $this->raw = $raw;
            if (preg_match(RegExp::CHECK_AND_EXTRACT_PICKER_WITH_WRAP, $raw)) {
                preg_match(RegExp::CHECK_AND_EXTRACT_PICKER_WITH_WRAP, $raw, $extract);
                $this->node = $extract[1];
            } else {
                $this->node = $raw;
            }

            $this->alias = Str::slug($this->node);

            if (count($options) === 1) {
                $this->alias = $options[0];
            }
        }
    }

    public function getRawWithAlias()
    {
        if($this->alias !== null){
            return $this->node . " as " . $this->alias;
        } else {
            return $this->node;
        }
    }
}

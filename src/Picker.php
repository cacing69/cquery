<?php
declare(strict_types=1);

namespace Cacing69\Cquery;

use Cacing69\Cquery\Support\CqueryRegex;
use Cacing69\Cquery\Support\StringHelper;
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
        $this->raw = $raw;
        $this->options = $options;

        if($raw instanceof Closure) {
            if(count($options) === 2) {
                $this->node = $options[0];
                $this->alias = $options[1];
            } else if (count($options) === 1) {
                $this->node = $options[0];
                $this->alias = StringHelper::slug($this->node);
            }
        } else {

            if (preg_match(CqueryRegex::CHECK_AND_EXTRACT_PICKER_WITH_WRAP, $raw)) {
                preg_match(CqueryRegex::CHECK_AND_EXTRACT_PICKER_WITH_WRAP, $raw, $extract);
                $this->node = $extract[1];
            } else {
                $this->node = $raw;
            }

            $this->alias = StringHelper::slug($this->node);

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

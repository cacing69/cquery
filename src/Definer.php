<?php
declare(strict_types=1);

namespace Cacing69\Cquery;
use Cacing69\Cquery\Trait\HasAliasProperty;
use Cacing69\Cquery\Exception\CqueryException;
use Cacing69\Cquery\Support\Str;
use Cacing69\Cquery\Trait\HasRawProperty;
use Cacing69\Cquery\Trait\HasNodeProperty;

class Definer
{
    use HasAliasProperty;
    use HasNodeProperty;
    use HasRawProperty;

    public function __construct($node, $alias = null, $callback = null)
    {

        if(preg_match('/^\s?.+\s+(as)\s+.+/', $node)){
            throw new CqueryException("error define, please set alias on second parameter");

        }

        $this->node = $node;

        if($alias) {
            $this->alias = $alias;
        } else {
            $this->alias = Str::slug($node);
        }

        if(!empty($callback)) {
            $this->raw = $callback;
        } else {
            $this->raw = $this->getNodeWithAlias();
        }
    }

    public function getNodeWithAlias()
    {
        return $this->node . " as " . $this->alias;
    }
}
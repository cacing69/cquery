<?php

declare(strict_types=1);

namespace Cacing69\Cquery;

use Cacing69\Cquery\Trait\HasNodeProperty;
use Cacing69\Cquery\Trait\HasValueProperty;
use Cacing69\Cquery\Trait\HasOperatorProperty;

class Filter
{
    use HasNodeProperty;
    use HasOperatorProperty;
    use HasValueProperty;
}

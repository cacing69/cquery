<?php

/**
 * This file is part of Cquery.
 *
 * (c) 2023 Ibnul Mutaki <ibnuul@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Cacing69\Cquery;

use Cacing69\Cquery\Expression\ClosureCallbackExpression;
use Cacing69\Cquery\Support\RegExp;
use Cacing69\Cquery\Support\Str;
use Cacing69\Cquery\Trait\HasAliasProperty;
use Cacing69\Cquery\Trait\HasSourceProperty;
use Closure;

class DefinerExtractor
{
    use HasSourceProperty;
    use HasAliasProperty;
    private $raw;
    private $definer;
    private $expression;

    public function __construct($definer, Source $source = null)
    {
        $this->source = $source;
        $this->raw = $definer;

        if ($definer instanceof Definer) {
            $this->alias = $definer->getAlias();

            if ($definer->getRaw() instanceof Closure) {
                $this->definer = $definer;
                $expression = new ClosureCallbackExpression($definer->getRaw());

                $extractor = new DefinerExtractor("{$definer->getNode()} as {$definer->getAlias()}");

                $expression = $expression->setNode($extractor->getExpression()->getNode())
                    ->setCallMethod($extractor->getExpression()->getCallMethod())
                    ->setCallMethodParameter($extractor->getExpression()->getCallMethodParameter())
                    ->setCallback($expression->getCallback());

                $this->expression = $expression;
            } else {
                $this->handlerExtractor($definer->getNodeWithAlias());
            }
        } else {
            $this->handlerExtractor($definer);
        }
    }

    private function handlerExtractor($definerRaw)
    {
        $_alias = null;
        if (preg_match(RegExp::IS_DEFINER_HAVE_ALIAS, $definerRaw)) {
            $decodeSelect = explode(' as ', $definerRaw);

            if (preg_match(RegExp::IS_DEFINER_HAVE_PARENTHESES, $decodeSelect[0])) {
                preg_match(RegExp::IS_DEFINER_HAVE_PARENTHESES, $decodeSelect[0], $extract);

                $this->definer = trim($extract[1]);
            } else {
                $this->definer = trim($decodeSelect[0]);
            }

            $_alias = $decodeSelect[1];
        } else {
            $this->definer = $definerRaw;
            $_alias = Str::slug($definerRaw);
        }

        $this->setAlias($_alias);

        foreach (RegisterExpression::load() as $expression) {
            $_checkSignature = $expression::getSignature();

            if (is_array($_checkSignature)) {
                $_founded = false;
                foreach ($_checkSignature as $signature) {
                    if (preg_match($signature, $this->definer)) {
                        $this->expression = new $expression($this->definer);
                        $_founded = true;
                        break;
                    }
                }

                if ($_founded) {
                    break;
                }
            } else {
                if (isset($_checkSignature)) {
                    if (preg_match($_checkSignature, $this->definer)) {
                        $this->expression = new $expression($this->definer);
                        break;
                    }
                } else {
                    $this->expression = new $expression($this->definer);
                }
            }
        }
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;

        if ($this->source) {
            $this->source->setAlias($alias);
        }

        return $this;
    }

    public function getDefiner()
    {
        return $this->definer;
    }

    public function getExpression()
    {
        return $this->expression;
    }
}

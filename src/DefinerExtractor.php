<?php

namespace Cacing69\Cquery;

use Cacing69\Cquery\Support\RegExp;
use Cacing69\Cquery\Support\Str;
use Cacing69\Cquery\RegisterAdapter;
use Cacing69\Cquery\Adapter\ClosureCallbackAdapter;
use Cacing69\Cquery\Trait\HasAliasProperty;
use Cacing69\Cquery\Source;
use Cacing69\Cquery\Definer;
use Cacing69\Cquery\Trait\HasSourceProperty;
use Closure;

class DefinerExtractor
{
    use HasSourceProperty;
    use HasAliasProperty;
    private $raw;
    private $definer;
    private $adapter;
    public function __construct($definer, Source $source = null)
    {
        $this->source = $source;
        $this->raw = $definer;

        if($definer instanceof Definer) {
            $this->alias = $definer->getAlias();

            if($definer->getRaw() instanceof Closure) {
                $this->definer = $definer;
                $adapter = new ClosureCallbackAdapter($definer->getRaw());

                $extractor = new DefinerExtractor("{$definer->getNode()} as {$definer->getAlias()}");

                $adapter = $adapter->setNode($extractor->getAdapter()->getNode())
                    ->setCallMethod($extractor->getAdapter()->getCallMethod())
                    ->setCallMethodParameter($extractor->getAdapter()->getCallMethodParameter())
                    ->setCallback($adapter->getCallback());

                $this->adapter = $adapter;
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
            $decodeSelect = explode(" as ", $definerRaw);

            if(preg_match(RegExp::IS_DEFINER_HAVE_WRAP, $decodeSelect[0])) {
                preg_match(RegExp::IS_DEFINER_HAVE_WRAP, $decodeSelect[0], $extract);

                $this->definer = trim($extract[1]);
            } else {
                $this->definer = trim($decodeSelect[0]);
            }

            $_alias = $decodeSelect[1];
        } else {
            $this->definer = $definerRaw;
            $_alias = Str::slug($definerRaw, "_");
        }

        $this->setAlias($_alias);

        foreach (RegisterAdapter::load() as $adapter) {
            $_checkSignature = $adapter::getSignature();

            if(is_array($_checkSignature)) {
                $_founded = false;
                foreach ($_checkSignature as $signature) {
                    if(preg_match($signature, $this->definer)) {
                        $this->adapter = new $adapter($this->definer);
                        $_founded = true;
                        break;
                    }
                }

                if($_founded) {
                    break;
                }
            } else {
                if(isset($_checkSignature)) {
                    if(preg_match($_checkSignature, $this->definer)) {
                        $this->adapter = new $adapter($this->definer);
                        break;
                    }
                } else {
                    $this->adapter = new $adapter($this->definer);
                }
            }
        }
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;

        if($this->source) {
            $this->source->setAlias($alias);
        }

        return $this;
    }

    public function getDefiner()
    {
        return $this->definer;
    }

    public function getAdapter()
    {
        return $this->adapter;
    }
}

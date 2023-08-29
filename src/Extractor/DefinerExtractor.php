<?php

namespace Cacing69\Cquery\Extractor;

use Cacing69\Cquery\Picker;
use Cacing69\Cquery\Support\CqueryRegex;
use Cacing69\Cquery\Support\StringHelper;
use Cacing69\Cquery\Support\RegisterAdapter;
use Cacing69\Cquery\Adapter\ClosureCallbackAdapter;
use Cacing69\Cquery\Trait\HasAliasProperty;
use Cacing69\Cquery\Trait\HasSourceProperty;
use Closure;

class DefinerExtractor {
    // use HasSelectorProperty;
    use HasSourceProperty;
    use HasAliasProperty;
    private $raw;
    private $definer;
    private $adapter;
    public function __construct($picker, SourceExtractor $source = null)
    {
        $this->source = $source;
        $this->raw = $picker;

        if($picker instanceof Picker) {
            $this->alias = $picker->getAlias();

            if($picker->getRaw() instanceof Closure) {
                $this->definer = $picker;
                $adapter = new ClosureCallbackAdapter($picker->getRaw(), $this->source);
                $extractor = new DefinerExtractor("{$picker->getNode()} as {$picker->getAlias()}");

                $adapter = $adapter->setNode($extractor->getAdapter()->getNode())
                            ->setCall($extractor->getAdapter()->getCall())
                            ->setCallParameter($extractor->getAdapter()->getCallParameter());

                $this->adapter = $adapter;
            } else {
                $this->handlerDefiner($picker->getRawWithAlias());
            }
        } else {
            $this->handlerDefiner($picker);
        }
    }

    private function handlerDefiner($pickerRaw) {
        if (preg_match(CqueryRegex::IS_DEFINER_HAVE_ALIAS, $pickerRaw)) {
            $decodeSelect = explode(" as ", $pickerRaw);
            $this->definer = trim($decodeSelect[0]);
            $this->alias = StringHelper::slug($decodeSelect[1]);
        } else {
            $this->definer = $pickerRaw;
            $this->alias = StringHelper::slug($pickerRaw, "_");
        }

        foreach (RegisterAdapter::load() as $adapter) {
            $checkSignature = $adapter::getSignature();
            if(isset($checkSignature)) {
                if(preg_match($checkSignature, $pickerRaw)) {
                    $this->adapter = new $adapter($this->definer, $this->source);
                    break;
                }
            } else {
                $this->adapter = new $adapter($this->definer, $this->source);
            }
        }
    }

    public function getDefiner() {
        return $this->definer;
    }

    public function getAdapter()
    {
        return $this->adapter;
    }
}

<?php

declare(strict_types=1);

namespace Cacing69\Cquery\Adapter;

use Cacing69\Cquery\CallbackAdapter;

class ReplaceCallbackAdapter extends CallbackAdapter
{
    protected static $signature = [
        "replace_from_single_to_single" => '/^\s*replace\(\s*\'(.*?)\'\s*,\s*\'(.*?)\'\s*,\s*(.*?)\s*\)\s*$/', // replace('_', '-', .txt > a)
        "replace_from_array_to_array" => '/^\s*replace\(\s*\[\s*(.*?)\s*\]\s*,\s*\[\s*(.*?)\s*\]\s*,\s*(.*?)\s*\)\s*$/', // replace([' _ ', '1'], [' - ', '1'], .txt > a)
        "replace_from_array_to_single" => '/^\s*replace\(\s*\[\s*(.*?)\s*\]\s*,\s*\'(.*?)\'\s*,\s*(.*?)\s*\)\s*$/' // replace(['_', '-'], '*', .txt > a)
    ];

    public static function getSignature()
    {
        return self::$signature;
    }

    public function __construct(string $raw)
    {
        $this->raw = $raw;

        $_callbackTmp = null;

        foreach (self::$signature as $key => $sign) {
            if(preg_match($sign, $raw)) {
                preg_match($sign, $raw, $extractParams);
                $this->node = $extractParams[3];
                $_callbackTmp = function (string $value) use ($extractParams, $key) {
                    if($key === "replace_from_single_to_single") {
                        return str_replace($extractParams[1], $extractParams[2], $value);
                    }elseif($key === "replace_from_array_to_array") {
                        $explodeParamsFrom = explode(",", $extractParams[1]);
                        $explodeParamsTo = explode(",", $extractParams[2]);

                        if(count($explodeParamsFrom) === count($explodeParamsTo)){
                            foreach ($explodeParamsFrom as $_key => $_value) {
                                preg_match('/\'(.*?)\'/', $_value, $_extractFrom);
                                preg_match('/\'(.*?)\'/', $explodeParamsTo[$_key], $_extractTo);
                                $value = str_replace($_extractFrom[1], $_extractTo[1], $value);
                            }
                            return $value;
                        } elseif(count($explodeParamsTo) === 1) {
                            foreach ($explodeParamsFrom as $_value) {
                                preg_match('/\'(.*?)\'/', $_value, $_extractFrom);
                                preg_match('/\'(.*?)\'/', $explodeParamsTo[0], $_extractTo);
                                $value = str_replace($_extractFrom[1], $_extractTo[1], $value);
                            }
                            return $value;
                        }
                    }elseif($key === "replace_from_array_to_single") {
                        $explodeParamsFrom = explode(",", $extractParams[1]);
                        $explodeParamsTo = $extractParams[2];

                        foreach ($explodeParamsFrom as $_value) {
                            preg_match('/\'(.*?)\'/', $_value, $_extractFrom);
                            $value = str_replace($_extractFrom[1],  $explodeParamsTo, $value);
                        }
                        return $value;
                    }
                };

                break;
            }
        }

        if (preg_match('/^\s*replace\(.*,.*,\s?([a-z0-9_]*\(.+?\))\s?\)\s*$/', $raw)) {
            preg_match('/^\s*replace\(.*,.*,\s?([a-z0-9_]*\(.+?\))\s?\)\s*$/', $raw, $extract);

            $extractChild = $this->extractChild($extract[1]);
            $_childCallback = $extractChild->getAdapter()->getCallback();

            if($_childCallback) {
                $this->callback = function (string $value) use ($_childCallback, $_callbackTmp) {
                    return $_callbackTmp((string) $_childCallback($value));
                };
            } else {
                $this->callback = $_callbackTmp;
            }
        } else {
            $this->ref = "_text";


            $this->callMethod = "extract";
            $this->callMethodParameter = [$this->ref];
            $this->callback = $_callbackTmp;
        }
    }
}

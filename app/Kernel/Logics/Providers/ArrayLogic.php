<?php

namespace App\Kernel\Logics\Providers;

class ArrayLogic
{
    public function mergeDefault(&$arregloBase, $arrayDefault)
    {
        foreach ($arrayDefault as $key => $val) {
            if (isset($arregloBase[$key])) {
                $arrayDefault[$key] = $arregloBase[$key];
                unset($arregloBase[$key]);
            } else {
                $arrayDefault = array_merge($arregloBase, $arrayDefault);
            }
        }
        return array_merge($arregloBase, $arrayDefault);
    }

    public function interpolate($template, array $context = [])
    {
        /* build a replacement array with braces around the context keys */
        $keysReplace = [];
        foreach ($context as $key => $val) {
            // check that the value can be casted to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $keysReplace['{' . $key . '}'] = $val;
            }
        }
        /* interpolate replacement values into the message and return */
        return strtr($template, $keysReplace);
    }
}

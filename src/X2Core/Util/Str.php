<?php

namespace X2Core\Util;


class Str
{
    /**
     * @param string $str
     *
     *
     * @desc Parse a string to chunk to Camel Case Format
     * @return array
     */
    public static function camelCaseParse($str){
        $length = strlen($str);
        $result = [""];
        for($i = 0, $j = 0;$i < $length;$i++){
            $current = ord($str[$i]);
            if($current < 90 && $current > 64)
                $result[++$j] = strtolower($str[$i]);
            else
                $result[$j] .= $str[$i];

        }
        return $result;
    }

    /**
     * @param array|string $str
     * @return string
     */
    public static function toDashCase($str)
    {
        return join('_',(is_array($str)) ? $str : self::camelCaseParse($str));
    }

    /**
     * @param array $str
     * @return string
     */
    public static function toCamelCase(array $str)
    {
        $length = count($str);
        if($length > 0)
            $result = [$str[0]];
        else
            return "";
        for($i = 1;$i < $length;$i++){
            $result .= ucfirst($str[$i]);
        }
        return $result;
    }


}
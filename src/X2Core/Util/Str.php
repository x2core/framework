<?php

namespace X2Core\Util;
use X2Core\Types\IterableString;

/**
 * Class Str
 * @package X2Core\Util
 */
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

    /**
     * @param $str
     * @param $chunk
     * @return bool
     */
    public static function start($str, $chunk){
        $result = true;
        foreach (new IterableString($chunk) as $index => $char){
            if($str[$index] === $char){
                continue;
            }else{
                $result = false;
                break;
            }
        }
        return $result;
    }

    /**
     * @param $str
     * @param $chunk
     * @return bool
     */
    public static function end($str, $chunk){
        return self::start(strrev($str), strrev($chunk));
    }

    /**
     * @param $str
     * @param $offset
     * @param null $length
     * @return string
     */
    public static function slice($str, $offset, $length = NULL)
    {
        $result = "";
        $lenStr = ($length !== NULL) ? $length+$offset : strlen($str);
        for($i = $offset; $i < $lenStr; $i++){
            $result .= $str[$i];
        }
        return $result;
    }

    /**
     * @param $str
     * @param $first
     * @param $end
     * @param int $status
     * @return string[]
     * @desc Take chunk string that found in wrap sub string
     */
    public static function capture($str, $first, $end, $status = 1){
        $result = [];
        $i = 0;
        $pointer = 0;
        $chunk = "";
        $fMaxPointer = strlen($first);
        $eMaxPointer = strlen($end);
        foreach (new IterableString($str) as $index => $char){
            if($status === 1 && $char === $first[$pointer] ){
                $pointer++;
                if($pointer === $fMaxPointer){
                    $status = 2;
                    $pointer = 0;
                }
            }elseif($status === 2 && $char === $end[$pointer]){
                $pointer++;
                if($pointer === $eMaxPointer){
                    $result[$i] = $chunk;
                    $chunk = "";
                    $status = 1;
                    $i++;
                }
            }elseif ($status === 2){
                $chunk .= $char;
            }
        }

        return $result;
    }

    /**
     * @param $source
     * @param $matches
     * @param bool $sensitives
     * @return bool|string
     */
    public static function contains($source, $matches, $sensitives = true){
        if(is_array($matches)){
            foreach($matches as $match){
                if(self::contains($source, $match, $sensitives)){
                    return true;
                }
            }
            return false;
        }else{
            return $sensitives ? strstr($source, $matches) : stristr($source, $matches);
        }
    }

    /**
     * @param $len
     * @param int $limit
     * @return string
     */
    public static function random($len, $limit = 127){
        $str = '';
        for ($i = 0; $i < $len; $i++){
            $str .= chr(mt_rand(32, $limit));
        }
        return $str;
    }
}
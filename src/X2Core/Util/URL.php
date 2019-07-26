<?php

namespace X2Core\Util;

/**
 * Class URL
 * @package X2Core\Util
 */
class URL
{
    /**
     * constants of class
     */
    const MATCH_STATIC = 1;
    const MATCH_ARRAY_PARAM = 2;
    const MATCH_REGEXP = 3;

    /**
     * @var string raw
     */
    private $raw;

    /**
     * @var bool
     */
    private $error;


    /**
     * @var string[]
     */
    private $data;

    /**
     * URL constructor.
     * @param $str
     */
    public function __construct($str)
    {
        $this->error = (bool) $this->raw = filter_var($str, FILTER_VALIDATE_URL);
        if(!$this->error)
            $this->parse();
    }

    /**
     * @return void
     */
    private function parse()
    {
        $this->data = parse_url($this->raw);
    }

    /**
     * @param array $rule
     * @param $src
     * @return bool|array
     */
    private static function pathMatch(array $rule, $src)
    {

        $length = count($rule);
        if($src[0] === '/'){
            $src = Str::slice($src, 1);
        }
        $src = explode('/', $src);
        $srcLength = count($src);
        if($length !== $srcLength){
            return false;
        }
        $result = [];
        for($i = 0; $i < $length; $i++){
            $current = $rule[$i];
            if($current[0] === '$' ){
                $result[Str::slice($current, 1)] = $src[$i];
            }else{
                if($current !== $src[$i])
                    return false;
            }
        }
        return $result;
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getRaw()
    {
        return $this->raw;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->data['host'];
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->data['user'];
    }

    /**
     * @return string
     */
    public function getPass()
    {
        return $this->data['pass'];
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->data['path'];
    }
    /**
     * @return string
     */
    public function getPort()
    {
        return $this->data['port'];
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->data['scheme'];
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->data['query'];
    }

    /**
     * @param $type
     * @param $rule
     * @param $src
     * @return bool|array
     */
    public static function match($type, $rule, $src){
        switch($type){
            case 1:
                return $rule === $src;
                break;
            case 2:
                if(is_string($rule)){
                  $rule = $rule[0] === '/' ? array_slice(explode('/', $rule), 1) : explode('/', $rule);
                }
                return self::pathMatch($rule, $src);
                break;
            case 3:
                $matches = NULL;
                preg_match($rule, $src, $matches);
                return is_array($matches) ? $matches : false;
                break;
        }
        return false;
    }

}
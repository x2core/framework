<?php

namespace X2Core\View\Cowboy;


use X2Core\Util\Str;

class Parser
{
    const OUTPUT = 1;
    const STRUCTURE = 2;
    const SEMANTIC = 3;
    const RAW = 4;
    const END = 4;

    /**
     * Contains the source of template
     *
     * @var \Generator
     */
    private $generator;

    /**
     * Contains the view structure to render
     *
     * @var array
     */
    private $ast = [];

    /**
     * Parser constructor.
     * @param \Generator $generator
     */
    public function __construct(\Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Parse the view
     *
     * @return $this
     */
    public function parse(){
        $current = $this->generator->current();
        $flag = false;
        $this->generator->next();
        while($this->generator->valid()){
            $current .= $this->generator->current();
            $flag = $this->validate($current, $flag);
        }
        return $this;
    }

    /**
     * @param $current
     * @param $flag
     * @return bool
     */
    private function validate(&$current, $flag)
    {
        static $buffer = "";
        if($flag && $current = ">>"){
            $this->emmit(self::OUTPUT, $buffer);
            return false;
        }elseif($flag === false && $current = "<<"){
            if($buffer!==""){
                $this->emmit(self::RAW,$buffer);
            }
            $this->emmit(self::END);
            return true;
        }else{
            $buffer .= $current[0];
            $current = Str::slice($current,1);
            return $flag;
        }
    }

    /**
     * @param $structure
     * @param null $payload
     */
    private function emmit($structure, $payload = null)
    {
        $this->ast[] = [
            'type' => $structure,
            'data' => $payload
        ];
    }

}
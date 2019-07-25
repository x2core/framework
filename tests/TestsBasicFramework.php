<?php

/**
 * Class TestsBasicFramework
 * @abstract
 * @desc Base class to create simple unit test
 */
abstract class TestsBasicFramework
{
    /**
     * @var int[]
     */
    private $status;

    /**
     * TestsBasicFramework constructor.
     */
    public function __construct()
    {
        $this->status = [0,0];
    }

    /**
     * @param $value1
     * @param $value2
     * @param null $msgTag
     * @internal param mixed $value
     */
    public function assert($value1, $value2, $msgTag = NULL){
        $this->addGoal();
        if ($value1 === $value2)
            $this->addScore();
        else
            $this->printMsg($msgTag);
    }

    /**
     * @param mixed $value
     * @param null $msg
     */
    public function assertToTrue($value, $msg = NULL){
        $this->addGoal();
        if($value === true)
            $this->addScore();
        else
            $this->printMsg($msg);
    }

    /**
     * @param mixed $value
     * @param null $msg
     */
    public function assertToFalse($value, $msg = NULL){
        $this->addGoal();
        if ($value === false)
            $this->addScore();
        else
            $this->printMsg($msg);
    }

    /**
     * @param mixed $value
     * @param null $msg
     */
    public function assertPositive($value, $msg = NULL){
        $this->addGoal();
        if( ((bool) $value) === true)
            $this->addScore();
        else
            $this->printMsg($msg);
    }

    /**
     * @param mixed $value
     * @param null $msg
     */
    public function assertNegative($value, $msg = NULL){
        $this->addGoal();
         if(((bool) $value) === false)
             $this->addScore();
         else
             $this->printMsg($msg);
    }

    /**
     * @param array $arr1
     * @param array $arr2
     * @return bool
     */
    public function arrayDeepEqual($arr1, $arr2){
        $this->addGoal();
        if($result = (($length = count($arr1)) === count($arr2))){
            for($i = 0; $i < $length; $i++){
                if($arr2[$i] !== $arr1[$i]){
                    $result =  false;
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * @param $msg
     */
    public function info($msg){
        echo $msg . "\n";
    }

    /**
     * @param $name
     */
    public function depends($name){
        $this->{$name}();
    }

    /**
     * @return mixed
     */
    public abstract function run();

    /**
     * @return int[]
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * return void
     */
    public function addGoal()
    {
        $this->status[1]++;
    }

    /**
     * @return void
     */
    public function addScore()
    {
        $this->status[0]++;
    }

    /**
     * @desc to initialize test and run to show result
     */
    public static function toTest(){
        $test = new static();
        $test->run();
        $test->finished();
    }

    /**
     * @return void
     */
    private function finished()
    {
        echo "Result to " . static::class . ': ' . $this->status[0] . '/' .  $this->status[1] . "\n";
    }

    /**
     * @param $msg
     */
    private function printMsg($msg)
    {
        echo ("Assert with tag: '{$msg}' is failed\n");
    }
}
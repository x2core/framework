<?php

namespace Test;


class Event
{
    /**
     * @var mixed
     */
    public $data;

    /**
     * @var array
     */
    public $arrRecord;

    /**
     * @var mixed
     */
    public $data2;

    /**
     * @var mixed
     */
    public $testEvent;

    /**
     * @var mixed
     */
    public $data3;

    public $record = [];

    /**
     * Event constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData2()
    {
        return $this->data2;
    }

    /**
     * @param mixed $data2
     */
    public function setData2($data2)
    {
        $this->data2 = $data2;
    }

    /**
     * @return mixed
     */
    public function getArrRecord()
    {
        return $this->arrRecord;
    }

    /**
     * @param mixed $arrRecord
     */
    public function setArrRecord($arrRecord)
    {
        $this->arrRecord = $arrRecord;
    }

}
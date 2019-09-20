<?php

namespace X2Core\Preset\Contracts;


interface ActiveRecordInterface
{
    /**
     * @param $data
     * @return boolean
     */
    public function add($data);

    /**
     * @param $id
     * @return mixed
     */
    public function get($id);

    /**
     * @param $id
     * @param $data
     * @return boolean
     */
    public function save($id, $data);

    /**
     * @param $id
     * @return boolean
     */
    public function remove($id);

}
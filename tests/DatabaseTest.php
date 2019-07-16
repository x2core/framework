<?php

use X2Core\Foundation\Database\Connector\DBAL;
use X2Core\Foundation\Database\DatabaseAccessor;

class DatabaseTest extends TestsBasicFramework
{

    /**
     * @return mixed
     */
    public function run()
    {
      $conn = DBAL::makeConnection(
           'pdo_mysql', 'localhost', 'root', null, 'ale');
      $access = new DatabaseAccessor($conn);
//      $access->insert('data', [ 'system' => 34, 'support' => 456]);
//        $access->update('data', ['support' => 10000], 1);
//        $access->delete('data',2);
        $data = $access->find('data', 3);
        var_dump($data);
        $data = $access->findByRules('data', ['support:greater-than' => 5000, '|support:equal' => 46]);
        var_dump($data);
    }
}
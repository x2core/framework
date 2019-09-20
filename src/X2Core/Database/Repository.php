<?php

namespace X2Core\Database;
use X2Core\Preset\Contracts\DatabaseManager;

/**
 * Class Repository
 * @package x2core\database\X2Core\Database
 *
 * @author Oliver Valiente <oliver021val@gmail.com>
 *
 * This class is base to create a repository of records
 * Basically a subclass repository has one or many method that describe a set of records
 */
abstract class Repository
{
    /**
     * @var string[]
     */
    private $links;

    /**
     * mixed[]
     */
    private $cache;

    /**
     * @var DatabaseManager
     */
    private $db;

    /**
     * @var
     */
    private $table;

    /**
     * Repository constructor.
     *
     * @param DatabaseManager $db
     * @param $table
     */
    public function __construct(DatabaseManager $db, $table)
    {
        $this->db = $db;
        $this->table = $table;
    }

    /**
     * Map to all descriptions available in a repository class
     *
     * @return void
     */
    abstract public function descriptions();

    /**
     * Bind a type name of description with a method that return a description
     *
     * @param $name
     * @param $method
     */
    protected function setLink($name, $method){
        $this->links[$name] = $method;
    }

    /**
     * Return a data of a query base on the description
     *
     * @param $name
     * @param null $extend
     * @return mixed[]
     */
    public function fetch($name, $extend = NULL){
       return $this->query($this->db, $this->table, $this->resolve($name, $extend));
    }

    /**
     * Return a data of a query base on the description
     *
     * @param DatabaseManager $db
     * @param $name
     * @return mixed[]
     */
    public function external(DatabaseManager $db, $name){
        return $this->query($db, $this->table, $this->resolve($name));
    }

    /**
     * Return array with description
     *
     * @param $name
     * @param array|null $extend
     * @return mixed[]
     */
    public function resolve($name, array $extend = NULL)
    {
        if(isset($this->cache[$name])){
            $this->cache[$name] = $this->{$name}(new DataDescriptor);
        }
        return $extend !== NULL ? array_merge($this->cache[$name], $extend) : $this->cache[$name];
    }

    /**
     * Return a query base on description and a connection
     *
     * @param DatabaseManager $db
     * @param $table
     * @param $description
     * @return array
     */
    private function query(DatabaseManager $db, $table, $description)
    {
        return $db->findByRules($table, $description);
    }
}
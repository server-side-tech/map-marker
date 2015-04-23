<?php
/**
 * Database class: is the parent class for any database type that will be used
 * within the code. It can't be instantiated. Therefore, it defined as abstract
 * class. Note that only one connection is allowed for each type. Note that
 * there must be only one connection for each type to keep high performance.
 *
 * @author wael showair
 */
abstract class Database {

    const MYSQL = 1;
    const SQLITE = 2;

    // Database types.
    static public $validTypes = array(
      Database::MYSQL => 'MySQL',
      Database:: SQLITE=> 'SQLite'
    );

    protected static $_instance;

    /* This field is used to hold a reference to the connection that will be
        created once for every database type. Note that there must be only one
        connection for each type to keep high performance.
     */
    protected $_connection;

    protected $_tableName;


    /** todo: May be i don't need, will see.
    * Determine if a database has a valid type.
    * @param int $typeId
    * @return boolean
    */
    static public function isValidType($typeId) {
        return array_key_exists($typeId, self::$validTypes);
    }

    /* Force child classes to implement the following methods by adding abstract
       keyword before each function. */
    abstract protected function _connect();
    abstract protected function disconnect();
    abstract protected function selectAll($tableName);
    abstract protected function flush();
    abstract protected function _fetchAll($result);
    abstract protected function freeResult($result);
    abstract protected function resetCursor();
    abstract protected function createTable($tableName);
    abstract protected function insertOnce($data);

    /**
     * Empty clone magic method to prevent duplication.
     */
     protected function __clone() {}
}

<?php
require_once 'class.database.php';

/**
 * Description of MySql
 *
 * @author waelshowair
 */
class MySql extends Database{

    const DB_USERNAME = "root";
    const DB_SERVER   = "localhost";
    const DB_PASSWORD = "root";
    const DB_NAME     = "final";

    /**
     * Class constuctor will call corresponding connect function in each class
     * type.
     */
    private function __construct() {
        $this->_connect();
    }

    /**
    * Get an instance of the Database.
    * @return Database
    */
    public  static function getInstance() {

        /* if the single class instance has not been created yet, instantiate it.*/
        if (!self::$_instance) {
            /* This will call the class constructor. */
            self::$_instance = new self();

        }

        /* return the single object */
        return self::$_instance;
    }

    protected function _connect(){
        try{
                $this->_connection = new PDO("mysql:host=". self::DB_SERVER.";dbname=".self::DB_NAME,
                                             self::DB_USERNAME,
                                             self::DB_PASSWORD);
        }catch (PDOException $exception){
            die("Connection error!!!" . $exception->getMessage());
        }
    }

    public function disconnect(){
        $this->_connection = NULL;
        self::$_instance = NULL;
    }

    public function selectAll($tableName){
        $querySelectAll = "SELECT * FROM `".$tableName."`";

        try{

            $result = $this->_connection->query($querySelectAll);

            if($result){
               /* make sure to set table name.*/
               $this->_tableName = $tableName;

               return $this->_fetchAll($result);
            }else{

            }

        } catch (Exception $exception) {
            die("Can't select all rows from database!!!");
        }
    }

    public function flush(){
        $queryTruncate = "TRUNCATE ". $this->_tableName;
        try{
            $this->_connection->query($queryTruncate);
        } catch (Exception $exception) {
            die ("Can't truncate table!!!" . $exception->getMessage());
        }
    }

    protected function _fetchAll($result){
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function freeResult($result){
        $result->closeCursor();
    }

    public function resetCursor(){

    }

    public function createTable($tableName){
        $queryCreateTable =
                'CREATE TABLE IF NOT EXISTS `'. $tableName.'` (
                `position_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `position_row` TINYINT(1) UNSIGNED NOT NULL,
                `position_col` TINYINT(1) UNSIGNED NOT NULL,
                `tile_id` TINYINT(1) UNSIGNED NOT NULL)';
        try{
            $this->_connection->query($queryCreateTable);
            $this->_tableName = $tableName;
        }  catch (PDOException $exception){
            die ("Can't create table!!!" . $exception->getMessage());
        }
    }

    public function insertOnce($data){
        $queryInsert  = "INSERT INTO `".$this->_tableName.'` (';
        $queryInsert .= "`position_row`, `position_col`, `tile_id`";
        $queryInsert .= ") VALUES ";

        for($row=0;$row<count($data);$row++)
        {
            for($column=0;$column<count($data[$row]);$column++)
            {
                    $queryInsert .= "(".$row.",".$column.",".$data[$row][$column]."),";
            }
        }

        /* Remove last character from query insert string. This character is comma*/
        $queryInsert = substr($queryInsert, 0, -1);

        try{
            $this->_connection->query($queryInsert);
        } catch (Exception $exception) {
            die ("Can't inert multiple rows in table!!!" . $exception->getMessage());
        }
    }
}

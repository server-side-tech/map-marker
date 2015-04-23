<?php
require_once 'class.database.php';

/**
 * Description of MySql
 *
 * @author waelshowair
 */
class SqLite extends Database{

    const DB_NAME = "final.sqlite3";

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
            $this->_connection = new PDO('sqlite:'.self::DB_NAME);
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

        $result = $this->_connection->query($querySelectAll);

        if($result){
           /* make sure to set table name.*/
           $this->_tableName = $tableName;

           return $this->_fetchAll($result);
        }else{
            return FALSE;
        }


    }

    public function flush(){
        $queryDelete = "DELETE FROM ".$this->_tableName;
        $this->_connection->query($queryDelete);

        $queryVaccum = "VACUUM";
        $this->_connection->query($queryVaccum);

        $this->resetPrimaryKey();
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
                `position_id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                `position_row` INTEGER NOT NULL,
                `position_col` INTEGER NOT NULL,
                `tile_id` INTEGER NOT NULL)';

            echo $queryCreateTable."<br>";
            $result = $this->_connection->query($queryCreateTable);

            /* save table name for future use.*/
            $this->_tableName = $tableName;

            if(!$result){
                echo "Can't create table!!!<br>";
            }else{
                /*free result memoery */
                $this->freeResult($result);
            }
    }

    public function insertOnce($data){

        /* Example of sqlite command to insert two rows at once:
           insert into map1 (position_row,position_col,tile_id)
           ...> select 22,23,24
           ...> union select 33,34,35;
         */
        $queryInsert  = "INSERT INTO `".$this->_tableName.'` (';
        $queryInsert .= "`position_row`, `position_col`, `tile_id`";
        $queryInsert .= ")";

        for($row=0;$row<count($data);$row++)
        {
            for($column=0;$column<count($data[$row]);$column++)
            {
                if(0==$row && 0==$column){
                   $queryInsert .="SELECT {$row},{$column},".$data[$row][$column];
                }else{
                    $queryInsert .=" UNION SELECT {$row},{$column},".$data[$row][$column];
                }
            }
        }

        $result = $this->_connection->query($queryInsert);
        if(!$result){

            echo "Can't insert multiple rows in table!!!<br>";
        }
    }

    public function isTableExist($tableName){

        $queryCount = "SELECT COUNT(name) AS table_count FROM `sqlite_master` WHERE name='".$tableName."'";

        $result = $this->_connection->query($queryCount);
        if(!$result){
            echo "Can't check table existence !!!<br>";
            return FALSE;
        }else{
            $row = $result->fetch(PDO::FETCH_ASSOC);

            /* save table name for future use. */
            $this->_tableName = $tableName;

            /* free result memory. */
            $this->freeResult($result);

            return (1 == $row['table_count']);
        }
    }

    protected function resetPrimaryKey(){
        /* SQLite keeps track of the largest ROWID that a table has ever held
         * using the special SQLITE_SEQUENCE table, reset the seq column for
         * the related table. Actually, this special table has only two columns:
         * first column is seq , second column is name.
         */
        $queryReset = "update sqlite_sequence set seq=0 where name='".$this->_tableName."'";
        $result = $this->_connection->query($queryReset);

        if(!$result){
            echo "Can't reset primary key for sqlite database.<br>";
        }else{
            /*free result memoery */
            $this->freeResult($result);
        }


    }
}

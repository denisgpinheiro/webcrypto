<?php
namespace WBCrypto\Config;

use WBCrypto\Interfaces\DatabaseInterface;

class ConnectDatabase extends \PDO implements DatabaseInterface{
       private $host = "localhost";
       private $dbName = "academiaWebjump";
       private $username = "root";
       private $password = "db123";
       private $conection;

    public function __construct()
    {
        $dsn = "mysql:dbname=" . $this->dbName . ";host=" . $this->host;

        parent::__construct($dsn, $this->username, $this->password);
    }
    public function connect(){
            if($this->conection != null){
                return $this->conection;
            }
            try{
                $this->conection = $this;
                return $this->conection;
            } catch (\PDOException $e){
                echo "Error!".$e->getMessage();
                die();
            }
    }
}

//$connect = new ConnectDatabase();

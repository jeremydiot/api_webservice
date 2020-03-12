<?php

class Database{

    private $host = "db";
    private $db_name = "php_api";
    private $username = "root";
    private $password = "root";
    
    public $connection;

    function getConnection(){
        $this->connection = null;

        try{
            $this->connection = new PDO("mysql:host=".$this->host.";dbname=".$this->db_name,$this->username,$this->password);
            $this->connection->exec("set names utf8");
        }catch(PDOException $e){
            echo "Erreur de connexion : ".$e->getMessage();
        }

        return $this->connection;
    }
}
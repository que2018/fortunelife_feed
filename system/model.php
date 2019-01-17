<?php

class Model {
    private $db;

    public function __construct(){
    }

    public function setDBConnection($db_connection) {
        $this->db = $db_connection;
    }

    // Magic function
    public function __get($name) {
        return $this->db;
    }

}
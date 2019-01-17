<?php

class DB {
    private $db;
    private $api;
    private static $instance;

    private function __construct($api){
        //Load DB Files
        require_once(DIR_SYSTEM . "db/dbmysqli.php");

        $db_class = 'DB' . DB_DRIVER;

        $this->api = $api;

        try {
            $this->db = new $db_class();
        }catch (Exception $exception) {
            $log = Log::getInstance();
            $log->writeErrorLog("[". $exception->getCode() ."] ==> " . $exception->getMessage());

            $this->api->sendResponse(500, "DB Error!");
            exit(0);
        }

    }

    public function __destruct(){
        if ( $this->db != null ) {
            $this->db->close();
        }
    }

    public static function getInstance($api) {
        if (! (self::$instance instanceof self)) {
            self::$instance = new self($api);
        }

        return self::$instance;
    }

    public function query($sql) {
        try {
            return $this->db->query($sql);
        } catch (Exception $exception) {
            $log = Log::getInstance();
            $log->writeErrorLog("[". $exception->getCode() ."] ==> " . $exception->getMessage());
            $log->writeErrorLog("===> SQL is: " . $sql);

            // Roll Back
            $this->transactionRollback();

            $this->api->sendResponse(500, "DB Error!");
            exit(0);
        }

    }

    public function getLastId() {
        return $this->db->getLastId();
    }

    public function transactionStart() {
        return $this->db->transactionStart();
    }

    public function transactionComplete() {
        return $this->db->transactionComplete();
    }

    public function transactionRollback() {
        return $this->db->transactionRollback();
    }

    public function escape($sql) {
        return $this->db->escape($sql);
    }

}
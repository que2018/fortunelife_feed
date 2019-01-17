<?php
/**
 * Created by PhpStorm.
 * User: cody
 * Date: 8/1/2017
 * Time: 2:10 PM
 */

class DBMySQLi {

    private $db_connection;

    public function __construct() {
        $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
        if ( $this->db_connection->connect_errno ) {
            throw new Exception("Failed to connect to Mysql: " . $this->db_connection->connect_error, $this->db_connection->connect_errno);
        }

        $this->db_connection->set_charset("utf8");
        $this->db_connection->query("SET SQL_MODE='' ");
    }

    public function close() {
        if ( $this->db_connection != null ) {
            $this->db_connection->close();
        }
    }

    public function query($sql) {

        $query = $this->db_connection->query($sql);

//        print_r($this->db_connection->errno);
//        print_r($this->db_connection->server_info);
//        print_r($this->db_connection->affected_rows);
//        var_dump($this->db_connection->error_list);
//        print_r($this->db_connection);
//        print_r($this->db_connection->errno);

        if ( $this->db_connection->errno ) {
            throw new Exception("Mysql SQL Error: " . $this->db_connection->error, $this->db_connection->errno);

        } else {
            if ( $query instanceof mysqli_result) {
                $data = array();

                while ( $row = $query->fetch_assoc() ) {
                    $data[] = $row;
                }

                $result = new stdClass();
                $result->num_rows = $query->num_rows;
                $result->row = isset($data[0]) ? $data[0] : array();
                $result->rows = $data;

                $query->close();

                return $result;
            } else {
                return true;
            }
        }

    }

    public function getLastId() {
        return $this->db_connection->insert_id;
    }

    public function transactionStart() {
        $this->db_connection->query("SET AUTOCOMMIT=0");
        $this->db_connection->query("START TRANSACTION");
        return true;
    }

    public function transactionComplete() {
        $this->db_connection->query("COMMIT");
        $this->db_connection->query("SET AUTOCOMMIT=1");
        return true;
    }

    public function transactionRollback() {
        $this->db_connection->query("ROLLBACK");
        $this->db_connection->query("SET AUTOCOMMIT=1");
        return true;
    }

    public function escape($sql) {
        return $this->db_connection->real_escape_string($sql);
    }

}
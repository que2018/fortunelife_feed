<?php

class Log {
    private $log_handle;
    private static $instance;

    private function __construct(){
        $this->log_handle = fopen(DIR_LOGS, 'a');

    }

    public function __destruct(){
        if ( $this->log_handle != null ) {
            fclose($this->log_handle);
        }
    }

    public static function getInstance() {

        if ( ! (self::$instance instanceof self) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function writeErrorLog($content) {
        $temp_content = "[ ". date('Y-m-d G:i:s') ." ] --- " . $content . "\n";

        fwrite($this->log_handle, $temp_content);
    }

}
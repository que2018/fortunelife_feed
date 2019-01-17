<?php

class Route{
    private $folder;
    private $controller;
    private $method;
    private $id;

    public function __construct() {
    }

    public function resolveRoute($route) {
        if ( strpos($route, '/') === false ) {
            // Bad Request
            return false;
        }

        $array_route = explode('/', $route);
        if ( count($array_route) <= 1 ) {
            // Bad Request
            return false;
        }

        // For Debug
//        print_r($array_route);

        // Set Parameters
        $this->folder = strtolower($array_route[0]);
        $this->controller = strtolower($array_route[1]);
        $this->method = strtolower($array_route[2]);

        if ( array_key_exists(3, $array_route) ) {
            $this->id = $array_route[3];
        } else {
            $this->id = null;
        }

        return true;
    }

    public function getFolder() {
        return $this->folder;
    }

    public function getController() {
        return $this->controller;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getId() {
        return $this->id;
    }
}
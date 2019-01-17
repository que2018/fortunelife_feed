<?php

class Controller {

    private $registry;
    protected $error = array();
    protected $params = array();

    public function __construct(){
    }

    public function setRegistry($registry) {
        $this->registry = $registry;
    }

    // Magic function
    public function __get($name) {
        return $this->registry->get($name);
    }

    public function __set($name, $value){
        $this->registry->set($name, $value);
    }

    public function model($model_path) {

        $loader = $this->registry->get("loader");

        $model = $loader->model($model_path);

        if ( $model instanceof Model) {
            // Set DB Connection
            require_once(DIR_SYSTEM . "db.php");
            $model->setDBConnection(DB::getInstance($this->registry->get("api")));

            $array_name = explode('/', $model_path);
            $model_name = "model_" . strtolower($array_name[0]). "_" . strtolower($array_name[1]);

            $this->registry->set($model_name, $model);
        } else {
            $this->registry->get("api")->sendResponse(404, $model);
            exit(0);
        }
    }
	
	public function library($library_path) {

        $loader = $this->registry->get("loader");

        $library = $loader->library($library_path);

		$array_name = explode('/', $library_path);

		if (isset($array_name[1])) {
			$library_name = strtolower($array_name[1]);
		} else {
			$library_name = $array_name[0];
		}
		
        $this->registry->set($library_name, $library);
    }
}
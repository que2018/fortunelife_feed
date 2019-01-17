<?php

class Loader {

    public function controller($route) {
        require_once(DIR_SYSTEM . "controller.php");

        $file_controller = DIR_API . $route->getFolder() . "/". $route->getController() . ".php";

        if ( file_exists($file_controller) ) {
            require_once($file_controller);
        } else {
            return "Controller file does not exists.";
        }

        // Check the controller name. Transfer name from 'user_group' to 'UserGroup'.
        if ( strpos($route->getController(), '_') ) {
            $controller_name = str_replace('_', '', $route->getController());
        } else {
            $controller_name = $route->getController();
        }

        // Get the class
        $class_name = "Controller" . ucfirst($route->getFolder()) . ucfirst($controller_name);
        if ( !class_exists($class_name) ) {
            return "Class does not exists.";
        }

        // Define class
        $controller = new $class_name();

        // Get the method
        if ( !method_exists($controller, $route->getMethod()) ) {
            return "Method does not exists.";
        }

        return $controller;
    }

    public function model($model_path) {
        require_once(DIR_SYSTEM . "model.php");

        try {
            $file_model = DIR_MODEL . $model_path . ".php";

            require_once($file_model);

            $array_model = explode('/', $model_path);

            // Check the controller name. Transfer name from 'user_group' to 'UserGroup'.
            if ( strpos($array_model[1], '_') ) {
                $model_name = str_replace('_', '', $array_model[1]);
            } else {
                $model_name = $array_model[1];
            }

            $class_name = "Model" . ucfirst($array_model[0]) . ucfirst($model_name);

            $model = new $class_name();

            return $model;
        } catch (Exception $e) {
            return "Can not load model by " . $model_path . "!";
        }
    }
	
	public function library($library_path) {
        try {
            $file_library = DIR_LIBRARY . $library_path . ".php";

            require_once($file_library);

            $array_library = explode('/', $library_path);

			if (isset($array_library[1])) 
			{
				if ( strpos($array_library[1], '_') ) {
					$second_library = explode('_', $array_library[1]);
					
					$library_name = '';
					
					foreach($second_library as $value) {
						$library_name .= ucfirst($value);
					}
				} else {
					$library_name = ucfirst($array_library[1]);
				}
				
				$class_name = "Lib" . ucfirst($array_library[0]) . $library_name;
			} 
			else 
			{
				$library_name = $array_library[0];
				
				$class_name = "Lib" . ucfirst($array_library[0]);
			}
			
            $library = new $class_name();

            return $library;
        } catch (Exception $e) {
            return "Can not load library by " . $library_path . "!";
        }
    }

}
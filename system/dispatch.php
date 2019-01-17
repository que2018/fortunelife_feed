<?php

class Dispatch {

    public function dispatcher($registry) {
        // Check the path
        $route = $registry->get('route');
        $loader = $registry->get('loader');

//        $controller_name = $route->getController();

        $controller = $loader->controller($route);

        if ( $controller instanceof Controller) {

            $controller->setRegistry($registry);

            $method_name = $route->getMethod();
            $controller->$method_name();
        } else {
            $registry->get('api')->sendResponse(404, $controller);
        }

    }

}
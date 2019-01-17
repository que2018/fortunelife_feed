<?php

require_once('config.php');

// Set Log
require_once(DIR_SYSTEM . "log.php");

if ( FOR_DEBUG ) {
    $log = Log::getInstance();
    $log->writeErrorLog("This is POST: " . json_encode($_POST));
    $log->writeErrorLog("This is GET: " . json_encode($_GET));
    $log->writeErrorLog("This is REQUEST: " . json_encode($_REQUEST));
//    $log->writeErrorLog("This is SERVER: " . json_encode($_SERVER));

}

require_once(DIR_SYSTEM . "restfulapi.php");
$rest_api = new RestfulAPI();

// Set RESTful API Header
$rest_api->setRestfulAPIHeader();
$rest_api->checkRestfulAPIOperation();

require_once(DIR_SYSTEM . "route.php");
$route = new Route();
if ( $route->resolveRoute($_GET['_route_']) == false ) {
    $rest_api->sendResponse(400);
    exit(0);
}

// Set Email
require_once(DIR_SYSTEM . "mail.php");

require_once(DIR_SYSTEM . "registry.php");
$registry = new Registry();

require_once(DIR_SYSTEM . "loader.php");
$loader = new Loader();

$registry->set('api', $rest_api);
$registry->set('route', $route);
$registry->set('loader', $loader);

// Load system message
require_once(DIR_SYSTEM . "systemmessage.php");
$registry->set('system_message', new SystemMessage());

//print_r($registry);
require_once(DIR_SYSTEM . "dispatch.php");
$dispatch = new Dispatch();

$dispatch->dispatcher($registry);


<?php

class RestfulAPI {

    private $httpVersion = "HTTP/1.1";

    public function sendResponse($statusCode, $responseContent='', $messageType='json') {
        $this->setHttpHeader($statusCode);

        if ( strpos(strtolower($messageType), 'json') !== false ) {

            $tempContent = $responseContent;
            if ( $tempContent == '' ) {
                $tempContent = $this->getHttpStatusMessage($statusCode);
            }

            echo json_encode($tempContent);

            // set log
            if ( FOR_DEBUG ) {
                $log = Log::getInstance();
                $log->writeErrorLog("[Response Code]: " . $statusCode);
                $log->writeErrorLog("[Response Message] :" . json_encode($tempContent));
            }
        }

    }

    public function setHttpHeader($statusCode) {
        $statusMessage = $this->getHttpStatusMessage($statusCode);

        header($this->httpVersion . " " . $statusCode . " " . $statusMessage);
        header("Content-Type: application/json; charset=UTF-8");

    }

    public function setRestfulAPIHeader() {
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Origin: ' . ALLOW_ACCESS_URL);
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Max-Age: 3600');
        header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    }

    public function checkRestfulAPIOperation() {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'OPTIONS':
                $this->sendResponse(200, "Acceptable Operation: GET, POST, PUT, DELETE, OPTIONS");
                exit(0);
            case 'POST':
                // Get parameters from client through json style.
                $_POST = json_decode(file_get_contents('php://input'), true);
                break;
            case 'PUT':
                // Get parameters from client through json style.
                $_POST = json_decode(file_get_contents('php://input'), true);
                break;
            case 'DELETE':
                break;
            case 'GET':
                break;
        }

        if ( FOR_DEBUG ) {
            $log = Log::getInstance();
            $log->writeErrorLog("[POST Parameters] :" . json_encode($_POST));
        }
    }

    private function getHttpStatusMessage($statusCode) {
        $httpStatus = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        );

        return ( $httpStatus[$statusCode] ? $httpStatus[$statusCode] : $httpStatus[500] );
    }

}
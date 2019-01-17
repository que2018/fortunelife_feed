<?php
class ControllerAuthLogin extends Controller {

    public function verify() {
        $result = array(
            'result' => OPERATION_SUCCESSFULLY,
            'message' => $this->system_message->getSystemMessage(OPERATION_SUCCESSFULLY),
            'data' => array(),
        );

        // Check information
        if ( $this->validator() ) {

            $this->model("user/user");
            $login_result = $this->model_user_user->verifyLogin($_POST['user_name'], $_POST['user_password']);

            if ( !$login_result ) {
                $result['result'] = ERROR_USERNAME_PASSWORD;
                $result['message'] = $this->system_message->getSystemMessage(ERROR_USERNAME_PASSWORD);
            } else {
                // Check the status
                if ( $login_result['user_status'] == '0' ) {
                    $result['result'] = ERROR_ACCOUNT_LOCKED;
                    $result['message'] = $this->system_message->getSystemMessage(ERROR_ACCOUNT_LOCKED);
                } else {
                    // Login successfully.
                    $result['data'] = $login_result;
                }
            }

        } else {
            $tempErrorMessageList = array();
            foreach ( $this->error as $message ) {
                array_push($tempErrorMessageList, $message);
            }
            $result['result'] = ERROR_REQUEST_INFORMATION;
            $result['message'] = $this->system_message->getSystemMessage(ERROR_REQUEST_INFORMATION);
            $result['data'] = $tempErrorMessageList;
        }

        $this->api->sendResponse(200, $result);
    }

    private function validator() {
        if ( isset($_POST['user_name']) ) {
            if ( (strlen($_POST['user_name']) < 1) || (strlen($_POST['user_name']) > 32) ) {
                $this->error['error_user_name'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_NAME), 'User name');
            } else {
                if ( preg_match('/[\<\>\=\?\*\%]+/', $_POST['user_name']) ) {
                    $this->error['error_user_name'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_SPECIAL_CHARACTER), 'User name');
                }
            }
        } else {
            $this->error['error_user_name'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_EMPTY), 'User name');
        }

        if ( isset($_POST['user_password']) ) {
            if ( (strlen($_POST['user_password']) < 1) || (strlen($_POST['user_password']) > 20) ) {
                $this->error['error_user_password'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_NAME), 'Password');
            } else {
                if ( preg_match('/[\<\>\=\?\*\%]+/', $_POST['user_password']) ) {
                    $this->error['error_user_password'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_SPECIAL_CHARACTER), 'Password');
                }
            }
        } else {
            $this->error['error_user_password'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_EMPTY), 'Password');
        }

        if ( count($this->error) > 0 ) {
            return false;
        } else {
            return true;
        }
    }

}
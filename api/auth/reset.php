<?php

class ControllerAuthReset extends Controller {

    public function check_token() {
        $result = array(
            'result' => '1000',
            'message' => 'Check reset token successfully!',
            'data' => array(),
        );

        $data = array(
            'user_name' => $_POST['user_name'],
            'forgot_token' =>  $_POST['forgot_token']
        );

        $this->model("user/user");
        $check_flag = $this->model_user_user->checkResetToken($data);

        if ( $check_flag == false ) {
            $result['result'] = 1006;
            $result['message'] = 'We can not find your reset password application.';
        }

        $this->api->sendResponse(200, $result);
    }

    public function reset() {
        $result = array(
            'result' => '1000',
            'message' => 'Reset password successfully!',
            'data' => array(),
        );

        if ( $this->validator() ) {
            $data = array(
                'user_name' => $_POST['user_name'],
                'new_password' => $_POST['new_password'],
                'forgot_token' =>  $_POST['forgot_token']
            );

            $this->model("user/user");
            $reset_flag = $this->model_user_user->resetPassword($data);

            if ( !$reset_flag ) {
                $result['result'] = 1005;
                $result['message'] = 'Can not find your application, please contact the administrator!';
            }

        } else {
            $result['result'] = 1001;
            $result['message'] = 'Request Error!';
            $result['data'] = $this->error;
        }

        $this->api->sendResponse(200, $result);
    }

    public function validator() {
        if ( isset($_POST['user_name']) ) {
            if ( (strlen($_POST['user_name']) < 1) || (strlen($_POST['user_name']) > 50) ) {
                $this->error['error_user_name'] = "Username has errors!";
            } else {
                if ( preg_match('/[\<\>\=\?\*\%]+/', $_POST['user_name']) ) {
                    $this->error['error_user_name'] = "Username could not contain special characters!";
                }
            }
        } else {
            $this->error['error_user_name'] = "Username could not be empty.";
        }

        if ( isset($_POST['new_password']) ) {
            if ( (strlen($_POST['new_password']) < 1) || (strlen($_POST['new_password']) > 20) ) {
                $this->error['error_user_password'] = "New password has errors!";
            } else {
                if ( preg_match('/[\<\>\=\?\*\%]+/', $_POST['new_password']) ) {
                    $this->error['error_user_password'] = "User password could not contain special characters!";
                }
            }
        } else {
            $this->error['error_user_password'] = "User password could not be empty.";
        }

        if ( isset($_POST['forgot_token']) ) {
             if ( preg_match('/[\<\>\=\?\*\%]+/', $_POST['forgot_token']) ) {
                    $this->error['error_token'] = "Token Error.";
             }
        } else {
            $this->error['error_token'] = "Token Error.";
        }

        if ( count($this->error) > 0 ) {
            return false;
        } else {
            return true;
        }
    }

}
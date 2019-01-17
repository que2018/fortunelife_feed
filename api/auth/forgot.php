<?php

class ControllerAuthForgot extends Controller {

    public function apply() {
        $result = array(
            'result' => '1000',
            'message' => 'Forgot password application has already applied successfully.',
            'data' => array(),
        );

        if ( $this->validator() ) {
            $token = sha1(uniqid(mt_rand(), true));

            $this->model('user/user');
            $forgot_flag = $this->model_user_user->forgotApplication($_POST['user_name'], $token);

            if ( $forgot_flag != false ) {
                try {
                    // Need to send a email <-- coming soon
                    $mail = new Mail();

                    // Set parameters
                    $mail->protocol = EMAIL_PROTOCOL;
                    $mail->smtp_hostname = EMAIL_HOSTNAME;
                    $mail->smtp_port = EMAIL_PORT;
                    $mail->smtp_username = EMAIL_USERNAME;
                    $mail->smtp_password = EMAIL_PASSEORD;
                    $mail->smtp_timeout = EMAIL_TIMEOUT;

                    // Set email content
                    $msg = 'Dear ' . $_POST['user_name'] . ': ' . "\r\n\r\n";
                    $msg .= 'Please click the link as below to reset your password: ' . "\r\n\r\n";
                    $msg .= HTTP_FRONTEND_URL . 'auth/reset?username=' . $_POST['user_name'] . '&token=' . $token . "\r\n\r\n";
                    $msg .= 'Best Regards, ' . "\r\n\r\n";
                    $msg .= EMAIL_SENDER . "\r\n";

                    // Set email parameters
                    $mail->setFrom(EMAIL_USERNAME);
                    $mail->setTo($forgot_flag);
                    $mail->setSender(EMAIL_SENDER);
                    $mail->setSubject('Reset Your Password');
                    $mail->setText($msg);
                    $mail->send();

                } catch (Exception $exception) {
                    $result['result'] = 1005;
                    $result['message'] = 'Can not send reset password email! <br>';
                    $result['message'] .= $exception->getMessage();
                }

            } else {
                $result['result'] = 1004;
                $result['message'] = 'Can not find this username!';
            }
        } else {
            $result['result'] = 1001;
            $result['message'] = 'Request Error!';
            $result['data'] = $this->error;
        }

        $this->api->sendResponse(200, $result);
    }

    private function validator() {
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

        if ( count($this->error) > 0 ) {
            return false;
        } else {
            return true;
        }
    }

}
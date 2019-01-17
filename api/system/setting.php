<?php

class ControllerSystemSetting extends Controller {

    public function get_system_setting() {
        $result = array(
            'result' => OPERATION_SUCCESSFULLY,
            'message' => $this->system_message->getSystemMessage(OPERATION_SUCCESSFULLY),
            'data' => array(),
        );

        $this->model("system/setting");

        $settings = $this->model_system_setting->getSettingByCode($_POST['setting_code']);

        if ( count($settings) === 0 ) {
            $result['result']  = ERROR_SETTING_NOT_SET;
            $result['message']  = $this->system_message->getSystemMessage(ERROR_SETTING_NOT_SET);
        } else {
            $result['data'] = $settings;
        }

        $this->api->sendResponse(200, $result);
    }

    public function edit_sytem_setting() {
        $result = array(
            'result' => OPERATION_SUCCESSFULLY,
            'message' => $this->system_message->getSystemMessage(OPERATION_SUCCESSFULLY),
            'data' => array(),
        );

        if ( $this->validator() ) {
            $data = $_POST;

            $this->model("system/setting");

            $edit_flag = $this->model_system_setting->editSetting($_POST['setting_code'], $data);

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
        if ( !isset($_POST['setting_code']) ) {
            $this->error['error_setting_code'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_EMPTY), 'Setting Code');
        }

//        if ( isset($_POST['smtp_hostname']) ) {
//            if ( (strlen($_POST['smtp_hostname']) < 1) || (strlen($_POST['smtp_hostname']) > 60) ) {
//                $this->error['error_smtp_hostname'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_NAME), 'SMTP Hostname');
//            } else {
//                if ( preg_match('/[\<\>\=\?\*\%]+/', $_POST['smtp_hostname']) ) {
//                    $this->error['error_smtp_hostname'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_SPECIAL_CHARACTER), 'SMTP Hostname');
//                }
//            }
//        }
//
//        if ( isset($_POST['smtp_username']) ) {
//            if ( (strlen($_POST['smtp_username']) < 1) || (strlen($_POST['smtp_username']) > 64) ) {
//                $this->error['error_smtp_username'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_NAME), 'SMTP Username');
//            } else {
//                if ( preg_match('/[\<\>\=\?\*\%]+/', $_POST['smtp_username']) ) {
//                    $this->error['error_smtp_username'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_SPECIAL_CHARACTER), 'SMTP Username');
//                }
//            }
//        }
//
//        if ( isset($_POST['smtp_password']) ) {
//            if ( (strlen($_POST['smtp_password']) < 1) || (strlen($_POST['smtp_password']) > 64) ) {
//                $this->error['error_smtp_password'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_NAME), 'SMTP Password');
//            } else {
//                if ( preg_match('/[\<\>\=\?\*\%]+/', $_POST['smtp_password']) ) {
//                    $this->error['error_smtp_password'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_SPECIAL_CHARACTER), 'SMTP Password');
//                }
//            }
//        }

        if ( count($this->error) > 0 ) {
            return false;
        } else {
            return true;
        }

    }

}
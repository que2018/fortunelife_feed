<?php

class ControllerUserUser extends Controller {

    public function get_user_list() {
        $result = array(
            'result' => OPERATION_SUCCESSFULLY,
            'message' => $this->system_message->getSystemMessage(OPERATION_SUCCESSFULLY),
            'data' => array(),
        );

        if ( $this->validator_search() ) {
            $data_filter = array(
                'user_name'      => $_POST['user_name'],
                'user_email'     => $_POST['user_email'],
                'user_status'    => $_POST['user_status'],
                'user_full_name' => $_POST['user_full_name'],
                'user_group_id'  => $_POST['user_group_id'],
                'start'          => ($_POST['current_page'] - 1) * $_POST['page_size'],
                'limit'          => $_POST['page_size'],
                'sort'           => $_POST['sort_name'],
                'order'          => $_POST['order_name']
            );

            $this->model("user/user");

            // Get total number of user list
            $total_num = $this->model_user_user->getTotalUsers($data_filter);

            $total_page = ceil(($total_num / $_POST['page_size']));

            $result['data']['total_page'] = $total_page;
            $result['data']['total_num'] = $total_num;

            // Get user list
            $result['data']['user_list'] = $this->model_user_user->getUsers($data_filter);

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

    public function add_user() {
        $result = array(
            'result' => OPERATION_SUCCESSFULLY,
            'message' => $this->system_message->getSystemMessage(OPERATION_SUCCESSFULLY),
            'data' => array(),
        );

        if ( $this->validator() ) {
            $data = array(
                'user_fname'        => $_POST['first_name'],
                'user_lname'        => $_POST['last_name'],
                'user_email'        => $_POST['email'],
                'user_name'         => $_POST['user_name'],
                'user_status'       => $_POST['user_status'],
            );

            if ( isset($_POST['user_group_id']) ) {
                $data['user_group_id'] = $_POST['user_group_id'];
            }

            $this->model("user/user");

            // Add new user group
            $add_flag = $this->model_user_user->addUser($data);

            if ( !$add_flag ) {
                $result['result'] = ERROR_USERNAME_OR_EMAIL_EXISTED;
                $result['message'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_EXISTED), 'User name or Email');

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

    public function edit_user() {
        $result = array(
            'result' => OPERATION_SUCCESSFULLY,
            'message' => $this->system_message->getSystemMessage(OPERATION_SUCCESSFULLY),
            'data' => array(),
        );

        if ( $this->validator() ) {
            $data = array(
                'user_id'        => $_POST['user_id'],
                'user_fname'     => $_POST['first_name'],
                'user_lname'     => $_POST['last_name'],
                'user_email'     => $_POST['email'],
                'user_name'      => $_POST['user_name'],
                'user_status'    => $_POST['user_status'],
                'user_group_id'  => $_POST['user_group_id'],
            );

            $this->model("user/user");

            // Edit user
            $edit_flag = $this->model_user_user->editUser($data);

            if ( !$edit_flag ) {
                $result['result'] = ERROR_USERNAME_OR_EMAIL_EXISTED;
                $result['message'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_EXISTED), 'User name or Email');
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

    public function delete_user() {
        $result = array(
            'result' => OPERATION_SUCCESSFULLY,
            'message' => $this->system_message->getSystemMessage(OPERATION_SUCCESSFULLY),
            'data' => array(),
        );

        $this->model("user/user");

        $this->model_user_user->deleteUser($this->route->getId());

        $this->api->sendResponse(200, $result);
    }

    // Not use any more for this project.
//    public function get_user_permission() {
//        $result = array(
//            'result' => '1000',
//            'message' => 'Get user current permission successfully!',
//            'data' => array(),
//        );
//
//        $this->model("user/user");
//
//        // Get user group current permission
//        $permission_info = $this->model_user_user->getUserPermission($this->route->getId());
//
//        foreach($permission_info as $permission) {
//            $result['data'][] = array(
//                'menu_id'   => $permission['menu_id'],
//                'all'       => ( $permission['user_all_flag'] == 1 ? true : false ),
//                'search'    => ( $permission['user_search_flag'] == 1 ? true : false ),
//                'add'       => ( $permission['user_add_flag'] == 1 ? true : false ),
//                'edit'      => ( $permission['user_edit_flag'] == 1 ? true : false ),
//                'delete'    => ( $permission['user_delete_flag'] == 1 ? true : false )
//            );
//        }
//
//        $this->api->sendResponse(200, $result);
//    }

    private function validator() {
        if ( isset($_POST['user_name']) ) {
            if ( (strlen($_POST['user_name']) < 1) || (strlen($_POST['user_name']) > 32) ) {
                $this->error['error_user_name'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_NAME), 'Username');
            } else {
                if ( preg_match('/[\<\>\=\?\*\%]+/', $_POST['user_name']) ) {
                    $this->error['error_user_name'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_SPECIAL_CHARACTER), 'Username');
                }
            }
        } else {
            $this->error['error_user_name'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_EMPTY), 'Username');
        }

        if ( isset($_POST['first_name']) ) {
            if ( (strlen($_POST['first_name']) < 1) || (strlen($_POST['first_name']) > 32) ) {
                $this->error['error_user_fname'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_NAME), 'User First Name');
            } else {
                if ( preg_match('/[\<\>\=\?\*\%]+/', $_POST['first_name']) ) {
                    $this->error['error_user_fname'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_SPECIAL_CHARACTER), 'User First Name');
                }
            }
        } else {
            $this->error['error_user_fname'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_EMPTY), 'User First Name');
        }

        if ( isset($_POST['last_name']) ) {
            if ( (strlen($_POST['last_name']) < 1) || (strlen($_POST['last_name']) > 32) ) {
                $this->error['error_user_lname'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_NAME), 'User Last Name');
            } else {
                if ( preg_match('/[\<\>\=\?\*\%]+/', $_POST['last_name']) ) {
                    $this->error['error_user_lname'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_SPECIAL_CHARACTER), 'User Last Name');
                }
            }
        } else {
            $this->error['error_user_lname'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_EMPTY), 'User Last Name');
        }

        if ( isset($_POST['email']) ) {
            if ( (strlen($_POST['email']) < 1) || (strlen($_POST['email']) > 64) ) {
                $this->error['error_email'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_NAME), 'User Email');
            } else {
                if ( !preg_match('/^[a-zA-Z0-9][\w-.]*@[a-zA-Z0-9]+[a-zA-Z0-9-.]+[a-zA-Z0-9]+[\.]{1}[a-zA-Z0-9]+([\.]?)$/', $_POST['email']) ) {
                    $this->error['error_email'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_INVALID), 'User Email');
                }
            }
        } else {
            $this->error['error_email'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_EMPTY), 'User Email');
        }

//        if ( isset($_POST['user_password']) ) {
//            if ( (strlen($_POST['user_password']) < 1) || (strlen($_POST['user_password']) > 20) ) {
//                $this->error['error_user_password'] = $this->system_message->getSystemMessage(ERROR_PASSWORD);
//            } else {
//                if ( preg_match('/[\<\>\=\?\*\%]+/', $_POST['user_password']) ) {
//                    $this->error['error_user_password'] = $this->system_message->getSystemMessage(ERROR_PASSWORD_SPECIAL_CHARACTER);
//                }
//            }
//        } else {
//            $this->error['error_user_password'] = $this->system_message->getSystemMessage(ERROR_PASSWORD_EMPTY);
//        }

        if ( count($this->error) > 0 ) {
            return false;
        } else {
            return true;
        }

    }

    private function validator_search() {
        if ( isset($_POST['user_name']) ) {
            if ( preg_match('/[\<\>\=\?\*\%]+/', $_POST['user_name']) ) {
                $this->error['error_user_name'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_SPECIAL_CHARACTER), 'User Name');
            }
        }

        if ( isset($_POST['user_email']) ) {
            if ( preg_match('/[\<\>\=\?\*\%]+/', $_POST['user_email']) ) {
                $this->error['error_user_email'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_SPECIAL_CHARACTER), 'User Email');
            }
        }

        if ( isset($_POST['user_full_name']) ) {
            if ( preg_match('/[\<\>\=\?\*\%]+/', $_POST['user_full_name']) ) {
                $this->error['error_user_full_name'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_SPECIAL_CHARACTER), 'User Full Name');
            }
        }

        if ( count($this->error) > 0 ) {
            return false;
        } else {
            return true;
        }
    }

}
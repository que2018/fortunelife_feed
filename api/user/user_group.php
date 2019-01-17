<?php

class ControllerUserUserGroup extends Controller {

    public function get_user_group_list() {
        $result = array(
            'result' => OPERATION_SUCCESSFULLY,
            'message' => $this->system_message->getSystemMessage(OPERATION_SUCCESSFULLY),
            'data' => array(),
        );

        if ( $this->validator_search() ) {
            $data_filter = array(
                'user_group_name'    => $_POST['user_group_name'],
                'user_group_status'  => $_POST['user_group_status'],
                'start'              => ($_POST['current_page'] - 1) * $_POST['page_size'],
                'limit'              => $_POST['page_size'],
            );

            $this->model("user/user_group");

            // Get total number of user list
            $total_num = $this->model_user_user_group->getTotalUserGroups($data_filter);

            $total_page = ceil(($total_num / $_POST['page_size']));

            $result['data']['total_page'] = $total_page;
            $result['data']['total_num'] = $total_num;

            // Get user list
            $result['data']['user_group_list'] = $this->model_user_user_group->getUserGroups($data_filter);

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

    public function add_user_group() {
        $result = array(
            'result' => OPERATION_SUCCESSFULLY,
            'message' => $this->system_message->getSystemMessage(OPERATION_SUCCESSFULLY),
            'data' => array(),
        );

        if ( $this->validator() ) {
            $data = array(
                'user_group_name'    => $_POST['user_group_name'],
                'user_group_status'  => $_POST['user_group_status'],
                'user_group_permission' => $_POST['user_group_permission']
            );

            $this->model("user/user_group");

            // Add new user group
            $add_flag = $this->model_user_user_group->addUserGroup($data);

            if ( !$add_flag ) {
                $result['result'] = ERROR_USER_GROUP_NAME_EXISTED;
                $result['message'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_EXISTED), 'User Group Name');

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

    public function edit_user_group() {
        $result = array(
            'result' => OPERATION_SUCCESSFULLY,
            'message' => $this->system_message->getSystemMessage(OPERATION_SUCCESSFULLY),
            'data' => array(),
        );

        if ( $this->validator() ) {
            $data = array(
                'user_group_id'      => $_POST['user_group_id'],
                'user_group_name'    => $_POST['user_group_name'],
                'user_group_status'  => $_POST['user_group_status'],
                'user_group_permission' => $_POST['user_group_permission'],
            );

            $this->model("user/user_group");

            // Edit new user group
            $edit_flag = $this->model_user_user_group->editUserGroup($data);

            if ( !$edit_flag ) {
                $result['result'] = ERROR_USER_GROUP_NAME_EXISTED;
                $result['message'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_EXISTED), 'User Group Name');

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

    public function delete_user_group() {
        $result = array(
            'result' => OPERATION_SUCCESSFULLY,
            'message' => $this->system_message->getSystemMessage(OPERATION_SUCCESSFULLY),
            'data' => array(),
        );

        $this->model("user/user_group");

        // Add new user group
        $delete_flag = $this->model_user_user_group->deleteUserGroup($this->route->getId());

        if ( !$delete_flag ) {
            $result['result'] = ERROR_USER_GROUP_NOT_EMPTY;
            $result['message'] = $this->system_message->getSystemMessage(ERROR_USER_GROUP_NOT_EMPTY);
        }

        $this->api->sendResponse(200, $result);
    }

    public function get_enable_user_group_list() {
        $result = array(
            'result' => OPERATION_SUCCESSFULLY,
            'message' => $this->system_message->getSystemMessage(OPERATION_SUCCESSFULLY),
            'data' => array(),
        );

        $this->model("user/user_group");

        // Get user group
        $result['data'] = $this->model_user_user_group->getEnableUserGroups();

        $this->api->sendResponse(200, $result);
    }

    public function get_user_group_permission() {
        $result = array(
            'result' => OPERATION_SUCCESSFULLY,
            'message' => $this->system_message->getSystemMessage(OPERATION_SUCCESSFULLY),
            'data' => array(),
        );

        $this->model("user/user_group");

        // Get user group current permission
        $permission_info = $this->model_user_user_group->getUserGroupPermission($this->route->getId());

        foreach($permission_info as $permission) {
            $result['data'][] = array(
                'menu_id'   => $permission['menu_id'],
                'all'       => ( $permission['group_all_flag'] == 1 ? true : false ),
                'search'    => ( $permission['group_search_flag'] == 1 ? true : false ),
                'add'       => ( $permission['group_add_flag'] == 1 ? true : false ),
                'edit'      => ( $permission['group_edit_flag'] == 1 ? true : false ),
                'delete'    => ( $permission['group_delete_flag'] == 1 ? true : false )
            );
        }

        $this->api->sendResponse(200, $result);
    }

    private function validator() {
        if ( isset($_POST['user_group_name']) ) {
            if ( (strlen($_POST['user_group_name']) < 1) || (strlen($_POST['user_group_name']) > 32) ) {
                $this->error['error_user_group_name'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_NAME), 'User Group Name');
            } else {
                if ( preg_match('/[\<\>\=\?\*\%]+/', $_POST['user_group_name']) ) {
                    $this->error['error_user_group_name'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_SPECIAL_CHARACTER), 'User Group Name');
                }
            }
        } else {
            $this->error['error_user_group_name'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_EMPTY), 'User Group Name');
        }

        if ( count($this->error) > 0 ) {
            return false;
        } else {
            return true;
        }

    }

    private function validator_search() {
        if ( isset($_POST['user_group_name']) ) {
            if ( preg_match('/[\<\>\=\?\*\%]+/', $_POST['user_group_name']) ) {
                $this->error['error_user_group_name'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_NAME), 'User Group Name');
            }
        }

        if ( count($this->error) > 0 ) {
            return false;
        } else {
            return true;
        }
    }

}
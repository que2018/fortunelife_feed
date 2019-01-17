<?php

class ControllerSystemMenu extends Controller {
    public function get_menu_list() {
        $result = array(
            'result' => OPERATION_SUCCESSFULLY,
            'message' => $this->system_message->getSystemMessage(OPERATION_SUCCESSFULLY),
            'data' => array(),
        );

        if ( $this->validator_search() ) {
            $data_filter = array(
                'menu_name'     => $_POST['menu_name'],
                'menu_status'   => $_POST['menu_status'],
                'start'         => ($_POST['current_page'] - 1) * $_POST['page_size'],
                'limit'         => $_POST['page_size'],
            );

            $this->model("system/menu");

            // Get total number of user list
            $total_num = $this->model_system_menu->getTotalMenus($data_filter);

            $total_page = ceil(($total_num / $_POST['page_size']));

            $result['data']['total_page'] = $total_page;
            $result['data']['total_num'] = $total_num;

            // Get user list
            $result['data']['menu_list'] = $this->model_system_menu->getMenus($data_filter);
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

    public function add_menu() {
        $result = array(
            'result' => OPERATION_SUCCESSFULLY,
            'message' => $this->system_message->getSystemMessage(OPERATION_SUCCESSFULLY),
            'data' => array(),
        );

        if ( $this->validator() ) {
            $data = array(
                'menu_name'         => $_POST['menu_name'],
                'menu_parent_id'    => $_POST['menu_parent_id'],
                'menu_url'          => $_POST['menu_url'],
                'menu_status'       => $_POST['menu_status'],
            );

            $this->model("system/menu");

            // Add new user group
            $add_flag = $this->model_system_menu->addMenu($data);

            if ( !$add_flag ) {
                $result['result'] = ERROR_MENU_NAME_EXISTED;
                $result['message'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_EXISTED), 'Menu Name');

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

    public function edit_menu() {
        $result = array(
            'result' => OPERATION_SUCCESSFULLY,
            'message' => $this->system_message->getSystemMessage(OPERATION_SUCCESSFULLY),
            'data' => array(),
        );

        if ( $this->validator() ) {
            $data = array(
                'menu_id'      => $_POST['menu_id'],
                'menu_name'    => $_POST['menu_name'],
                'menu_url'     => $_POST['menu_url'],
                'menu_parent_id'     => $_POST['menu_parent_id'],
                'menu_status'  => $_POST['menu_status']
            );

            $this->model("system/menu");

            // Edit new user group
            $edit_flag = $this->model_system_menu->editMenu($data);

            if ( !$edit_flag ) {
                $result['result'] = ERROR_MENU_NAME_EXISTED;
                $result['message'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_EXISTED), 'Menu Name');
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

    public function delete_menu() {
        $result = array(
            'result' => OPERATION_SUCCESSFULLY,
            'message' => $this->system_message->getSystemMessage(OPERATION_SUCCESSFULLY),
            'data' => array(),
        );

        $this->model("system/menu");

        // Delete user group
        $this->model_system_menu->deleteMenu($this->route->getId());

        $this->api->sendResponse(200, $result);
    }

    public function get_enable_menu_lists() {
        $result = array(
            'result' => OPERATION_SUCCESSFULLY,
            'message' => $this->system_message->getSystemMessage(OPERATION_SUCCESSFULLY),
            'data' => array(),
        );

        $this->model("system/menu");

        // Get user list
        $menu_list = $this->model_system_menu->getEnableMenuLists();

        // Add permission parameters
        foreach($menu_list as $item) {
            $item['all'] = false;
            $item['search'] = false;
            $item['add'] = false;
            $item['delete'] = false;
            $item['edit'] = false;

            $result['data']['menu_list'][] = $item;
        }

//        $result['data']['menu_list'] = $menu_list;

        $this->api->sendResponse(200, $result);
    }

    public function get_user_menu() {
        $result = array(
            'result' => OPERATION_SUCCESSFULLY,
            'message' => $this->system_message->getSystemMessage(OPERATION_SUCCESSFULLY),
            'data' => array(),
        );

        $this->model("system/menu");

        $user_menu_list = $this->model_system_menu->getUserMenuList($this->route->getId());
        $result['data']['menu_list'] = $user_menu_list;

        $this->api->sendResponse(200, $result);
    }

    public function get_user_group_menu() {
        $result = array(
            'result' => OPERATION_SUCCESSFULLY,
            'message' => $this->system_message->getSystemMessage(OPERATION_SUCCESSFULLY),
            'data' => array(),
        );

        $this->model("system/menu");

        $user_menu_list = $this->model_system_menu->getUserGroupMenuList($this->route->getId());
        $result['data']['menu_list'] = $user_menu_list;

        $this->api->sendResponse(200, $result);
    }

    private function validator() {
        if ( isset($_POST['menu_name']) ) {
            if ( (strlen($_POST['menu_name']) < 1) || (strlen($_POST['menu_name']) > 32) ) {
                $this->error['error_menu_name'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_NAME), 'Menu Name');
            } else {
                if ( preg_match('/[\<\>\=\?\*\%]+/', $_POST['menu_name']) ) {
                    $this->error['error_menu_name'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_SPECIAL_CHARACTER), 'Menu Name');
                }
            }
        } else {
            $this->error['error_menu_name'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_EMPTY), 'Menu Name');
        }

        if ( isset($_POST['menu_url']) ) {
            if ( (strlen($_POST['menu_url']) < 1) || (strlen($_POST['menu_url']) > 64) ) {
                $this->error['error_menu_url'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_NAME), 'Menu URL');
            } else {
                if ( preg_match('/[\<\>\=\?\*\%]+/', $_POST['menu_url']) ) {
                    $this->error['error_menu_url'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_SPECIAL_CHARACTER), 'Menu URL');
                }
            }
        }

        if ( count($this->error) > 0 ) {
            return false;
        } else {
            return true;
        }

    }

    private function validator_search() {
        if ( isset($_POST['menu_name']) ) {
            if ( preg_match('/[\<\>\=\?\*\%]+/', $_POST['menu_name']) ) {
                $this->error['error_menu_name'] = sprintf($this->system_message->getSystemMessage(ERROR_COMMON_NAME), 'Menu Name');
            }
        }

        if ( count($this->error) > 0 ) {
            return false;
        } else {
            return true;
        }
    }


}


<?php

class ControllerAuthPermission extends Controller {

    public function get_user_permission_list() {
        $result = array(
            'result' => OPERATION_SUCCESSFULLY,
            'message' => $this->system_message->getSystemMessage(OPERATION_SUCCESSFULLY),
            'data' => array(),
        );

        $this->model("user/user");
        $permission_list = $this->model_user_user->getUserPermissionList($this->route->getId());
        $result['data']['permission_list'] = $permission_list;

        $this->api->sendResponse(200, $result);
    }

    public function get_user_group_permission_list() {
        $result = array(
            'result' => OPERATION_SUCCESSFULLY,
            'message' => $this->system_message->getSystemMessage(OPERATION_SUCCESSFULLY),
            'data' => array(),
        );

        $this->model("user/user_group");
        $permission_list = $this->model_user_user_group->getUserGroupPermissionList($this->route->getId());
        $result['data']['permission_list'] = $permission_list;

        $this->api->sendResponse(200, $result);
    }

}

<?php

class ControllerSystemZone extends Controller {

    public function get_zone_list() {
        $result = array(
            'result' => '1000',
            'message' => 'Get zone list successfully!',
            'data' => array(),
        );

        $data_filter = array(
            'zone_name'       => $_POST['zone_name'],
            'zone_status'     => $_POST['zone_status'],
            'country_id'      => $_POST['country_id'],
            'start'           => ($_POST['current_page'] - 1) * $_POST['page_size'],
            'limit'           => $_POST['page_size'],
            'sort'            => $_POST['sort_name'],
            'order'           => $_POST['order_name']
        );

        $this->model('system/zone');

        // Get total number of user list
        $total_num = $this->model_system_zone->getTotalZones($data_filter);

        $total_page = ceil(($total_num / $_POST['page_size']));

        $result['data']['total_page'] = $total_page;
        $result['data']['total_num'] = $total_num;

        // Get user list
        $result['data']['zone_list'] = $this->model_system_zone->getZones($data_filter);

        $this->api->sendResponse(200, $result);

    }

    public function add_zone() {
        $result = array(
            'result' => '1000',
            'message' => 'Add new zone successfully!',
            'data' => array(),
        );

        $data = array(
            'zone_name'  => $_POST['zone_name'],
            'code'    => $_POST['zone_code'],
            'country_id'    => $_POST['country_id'],
            'status'        => $_POST['status'],
        );

        $this->model("system/zone");

        // Add new user group
        $add_flag = $this->model_system_zone->addZone($data);

        if ( !$add_flag ) {
            $result['result'] = 1031;
            $result['message'] = 'The zone name already exists.';
        }

        $this->api->sendResponse(200, $result);
    }

    public function edit_zone() {
        $result = array(
            'result' => '1000',
            'message' => 'Edit zone successfully!',
            'data' => array(),
        );

        $data = array(
            'zone_id'     => $_POST['zone_id'],
            'zone_name'   => $_POST['zone_name'],
            'code'     => $_POST['zone_code'],
            'country_id'     => $_POST['country_id'],
            'status'         => $_POST['status']
        );

        $this->model("system/zone");

        // Edit new user group
        $edit_flag = $this->model_system_zone->editZone($data);

        if ( !$edit_flag ) {
            $result['result'] = 1032;
            $result['message'] = 'The zone name already exists.';

        }

        $this->api->sendResponse(200, $result);
    }

    public function delete_zone() {
        $result = array(
            'result' => '1000',
            'message' => 'Delete zone successfully!',
            'data' => array(),
        );

        $this->model("system/zone");

        // Delete zone
        $this->model_system_zone->deleteZone($this->route->getId());

        $this->api->sendResponse(200, $result);
    }


}


<?php

class ControllerSystemCountry extends Controller {

    public function get_all_country_list() {
        $result = array(
            'result' => '1000',
            'message' => 'Get country all list successfully!',
            'data' => array(),
        );

        $this->model('system/country');

        // Get country all list
        $result['data']['country_list'] = $this->model_system_country->getAllCountryList();

        $this->api->sendResponse(200, $result);
    }

    public function get_country_list() {
        $result = array(
            'result' => '1000',
            'message' => 'Get country list successfully!',
            'data' => array(),
        );

        $data_filter = array(
            'country_name'    => $_POST['country_name'],
            'country_status'  => $_POST['country_status'],
            'start'           => ($_POST['current_page'] - 1) * $_POST['page_size'],
            'limit'           => $_POST['page_size'],
            'sort'            => $_POST['sort_name'],
            'order'           => $_POST['order_name']
        );

        $this->model('system/country');

        // Get total number of user list
        $total_num = $this->model_system_country->getTotalCountries($data_filter);

        $total_page = ceil(($total_num / $_POST['page_size']));

        $result['data']['total_page'] = $total_page;
        $result['data']['total_num'] = $total_num;

        // Get country list
        $result['data']['country_list'] = $this->model_system_country->getCountries($data_filter);

        $this->api->sendResponse(200, $result);
    }

    public function add_country() {
        $result = array(
            'result' => '1000',
            'message' => 'Add new country successfully!',
            'data' => array(),
        );

        $data = array(
            'country_name'  => $_POST['country_name'],
            'iso_code_2'    => $_POST['iso_code_2'],
            'iso_code_3'    => $_POST['iso_code_3'],
            'status'        => $_POST['status'],
        );

        $this->model("system/country");

        // Add new user group
        $add_flag = $this->model_system_country->addCountry($data);

        if ( !$add_flag ) {
            $result['result'] = 1031;
            $result['message'] = 'The country name already exists.';
        }

        $this->api->sendResponse(200, $result);
    }

    public function edit_country() {
        $result = array(
            'result' => '1000',
            'message' => 'Edit country successfully!',
            'data' => array(),
        );

        $data = array(
            'country_id'     => $_POST['country_id'],
            'country_name'   => $_POST['country_name'],
            'iso_code_2'     => $_POST['iso_code_2'],
            'iso_code_3'     => $_POST['iso_code_3'],
            'status'         => $_POST['status']
        );

        $this->model("system/country");

        // Edit new user group
        $edit_flag = $this->model_system_country->editCountry($data);

        if ( !$edit_flag ) {
            $result['result'] = 1032;
            $result['message'] = 'The country name has already existed.';

        }

        $this->api->sendResponse(200, $result);
    }

    public function delete_country() {
        $result = array(
            'result' => '1000',
            'message' => 'Delete country successfully!',
            'data' => array(),
        );

        $this->model("system/country");

        // Delete country
        $this->model_system_country->deleteCountry($this->route->getId());

        $this->api->sendResponse(200, $result);

    }


}
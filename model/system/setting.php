<?php

class ModelSystemSetting extends Model {

    public function getSettingByCode($code) {
        $sql = "SELECT * FROM " . DB_PREFIX . "setting " ;
        $sql .= "WHERE setting_code = '". $this->db->escape($code) ."' ";

        $query = $this->db->query($sql);

        $return_data = array();
        $return_data['setting_code'] = $code;
        foreach($query->rows as $item) {
            $return_data[$item['setting_key']] = $item['setting_value'];
        }
        return $return_data;
    }

    public function editSetting($code, $data = array()) {
        $this->db->transactionStart();

        // Delete old data
        $sql = "DELETE FROM " . DB_PREFIX . "setting " ;
        $sql .= "WHERE setting_code = '". $this->db->escape($code) ."' ";
        $this->db->query($sql);

        // Add new data
        foreach( $data as $key=>$value ) {
            if ( strcmp($key, 'setting_code') === 0 ) continue;

            $sql = "INSERT INTO " . DB_PREFIX . "setting SET " ;
            $sql .= "setting_code = '" . $this->db->escape($code) . "', ";
            $sql .= "setting_key = '" . $this->db->escape($key) . "', ";
            $sql .= "setting_value = '" . $this->db->escape($value) . "' ";
            $this->db->query($sql);
        }

        $this->db->transactionComplete();
        return true;
    }
}
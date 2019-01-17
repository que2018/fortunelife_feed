<?php

class ModelSystemCountry extends Model {

    public function getAllCountryList() {
        $sql = "SELECT * FROM " . DB_PREFIX . "country " ;
        $sql .= " ORDER BY country_name ";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalCountries($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "country " ;
        $sql .= "WHERE 1=1 ";

        if ( isset($data['country_name']) && (strlen($data['country_name']) > 0) ) {
            $sql .= " AND country_name like '%". $this->db->escape($data['country_name']) ."%' ";
        }

        if ( strlen($data['country_status']) > 0 ) {
            $sql .= " AND status = '". $this->db->escape($data['country_status']) ."' ";
        }

        $query = $this->db->query($sql);

        return $query->num_rows;
    }

    public function getCountries($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "country " ;
        $sql .= "WHERE 1=1 ";

        if ( isset($data['country_name']) && (strlen($data['country_name']) > 0) ) {
            $sql .= " AND country_name like '%". $this->db->escape($data['country_name']) ."%' ";
        }

        if ( strlen($data['country_status']) > 0 ) {
            $sql .= " AND status = '". $this->db->escape($data['country_status']) ."' ";
        }

        $sort_data = array(
            'country_name',
        );

        if ( isset($data['sort']) && in_array($data['sort'], $sort_data) ) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY country_name ";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . $data['start'] . ", " . $data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function addCountry($data = array()) {
        // Check the country name
        $sql = "SELECT * FROM " . DB_PREFIX . "country  WHERE country_name = '". $this->db->escape($data['country_name']) ."' ";
        $query = $this->db->query($sql);
        if ( $query->num_rows > 0 ){
            return false;
        }

        // Add the new country
        $sql = "INSERT INTO "  . DB_PREFIX . "country SET ";
        $sql .= "country_name = '". $this->db->escape($data['country_name']) ."', ";
        $sql .= "iso_code_2 = '". $this->db->escape($data['iso_code_2']) ."', ";
        $sql .= "iso_code_3 = '". $this->db->escape($data['iso_code_3']) ."', ";
        $sql .= "status = '". $this->db->escape($data['status']) ."' ";

        $this->db->query($sql);

        return true;
    }

    public function editCountry($data = array()) {
        // Check the username
        $sql = "SELECT country_id FROM " . DB_PREFIX . "country WHERE ";
        $sql .= "country_name = '". $this->db->escape($data['country_name']) ."' ";
        $sql .= "AND country_id <> '". $this->db->escape($data['country_id']) ."' ";

        $query = $this->db->query($sql);
        if( $query->num_rows > 0 ) {
            return false;
        }

        // Edit user
        $sql = "UPDATE " . DB_PREFIX . "country SET ";
        $sql .= "country_name = '". $this->db->escape($data['country_name']) ."', ";
        $sql .= "iso_code_2 = '". $this->db->escape($data['iso_code_2']) ."', ";
        $sql .= "iso_code_3 = '". $this->db->escape($data['iso_code_3']) ."', ";
        $sql .= "status = '". $this->db->escape($data['status']) ."' ";
        $sql .= "WHERE country_id = '". $this->db->escape($data['country_id']) ."' ";

        $this->db->query($sql);
        return true;
    }

    public function deleteCountry($country_id) {
        // Delete this country
        $sql = "DELETE FROM " . DB_PREFIX . "country WHERE ";
        $sql .= "country_id = '". $this->db->escape($country_id) ."' ";

        $this->db->query($sql);
        return true;

    }

}
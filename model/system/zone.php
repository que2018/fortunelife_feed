<?php

class ModelSystemZone extends Model {

    public function getTotalZones($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "zone " ;
        $sql .= "WHERE 1=1 ";

        if ( isset($data['zone_name']) && (strlen($data['zone_name']) > 0) ) {
            $sql .= " AND zone_name like '%". $this->db->escape($data['zone_name']) ."%' ";
        }

        if ( strlen($data['zone_status']) > 0 ) {
            $sql .= " AND status = '". $this->db->escape($data['zone_status']) ."' ";
        }

        if ( isset($data['country_id']) && ((int)$data['country_id'] > 0) ) {
            $sql .= " AND country_id = '". $this->db->escape($data['country_id']) ."' ";
        }

        $query = $this->db->query($sql);

        return $query->num_rows;
    }

    public function getZones($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "zone " ;
        $sql .= "WHERE 1=1 ";

        if ( isset($data['zone_name']) && (strlen($data['zone_name']) > 0) ) {
            $sql .= " AND zone_name like '%". $this->db->escape($data['zone_name']) ."%' ";
        }

        if ( strlen($data['zone_status']) > 0 ) {
            $sql .= " AND status = '". $this->db->escape($data['zone_status']) ."' ";
        }

        if ( isset($data['country_id']) && ((int)$data['country_id'] > 0) ) {
            $sql .= " AND country_id = '". $this->db->escape($data['country_id']) ."' ";
        }

        $sort_data = array(
            'zone_name',
        );

        if ( isset($data['sort']) && in_array($data['sort'], $sort_data) ) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY zone_name ";
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

    public function addZone($data = array()) {
        // Check the zone name
        $sql = "SELECT * FROM " . DB_PREFIX . "zone  WHERE zone_name = '". $this->db->escape($data['zone_name']) ."' ";
        $query = $this->db->query($sql);
        if ( $query->num_rows > 0 ){
            return false;
        }

        // Add the new zone
        $sql = "INSERT INTO "  . DB_PREFIX . "zone SET ";
        $sql .= "zone_name = '". $this->db->escape($data['zone_name']) ."', ";
        $sql .= "code = '". $this->db->escape($data['code']) ."', ";
        $sql .= "country_id = '". $this->db->escape($data['country_id']) ."', ";
        $sql .= "status = '". $this->db->escape($data['status']) ."' ";

        $this->db->query($sql);

        return true;
    }

    public function editZone($data = array()) {
        // Check the username
        $sql = "SELECT zone_id FROM " . DB_PREFIX . "zone WHERE ";
        $sql .= "zone_name = '". $this->db->escape($data['zone_name']) ."' ";
        $sql .= "AND zone_id <> '". $this->db->escape($data['zone_id']) ."' ";

        $query = $this->db->query($sql);
        if( $query->num_rows > 0 ) {
            return false;
        }

        // Edit user
        $sql = "UPDATE " . DB_PREFIX . "zone SET ";
        $sql .= "zone_name = '". $this->db->escape($data['zone_name']) ."', ";
        $sql .= "code = '". $this->db->escape($data['code']) ."', ";
        $sql .= "country_id = '". $this->db->escape($data['country_id']) ."', ";
        $sql .= "status = '". $this->db->escape($data['status']) ."' ";
        $sql .= "WHERE zone_id = '". $this->db->escape($data['zone_id']) ."' ";

        $this->db->query($sql);
        return true;
    }

    public function deleteZone($zone_id) {
        // Delete this zone
        $sql = "DELETE FROM " . DB_PREFIX . "zone WHERE ";
        $sql .= "zone_id = '". $this->db->escape($zone_id) ."' ";

        $this->db->query($sql);
        return true;

    }




}
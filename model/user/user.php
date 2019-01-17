<?php

class ModelUserUser extends Model {

    public function verifyLogin($user_name, $user_password) {

        $sql = "SELECT user_id, user_group_id, user_status, user_name FROM ". DB_PREFIX ."user WHERE user_name='". $this->db->escape($user_name) ."' AND user_password=sha('". $this->db->escape($user_password) ."') ";

        $query = $this->db->query($sql);

        if ( $query->num_rows ) {
            return $query->row;
        } else {
            return false;
        }

    }

    public function forgotApplication($user_name, $token) {
        $sql = "SELECT user_id, employee_id FROM ". DB_PREFIX ."user WHERE user_name='". $this->db->escape($user_name) ."' ";
        $query = $this->db->query($sql);

        if ( $query->num_rows <= 0 ) {
            return false;
        }

        // Get a random string
        $employee_id = $query->row['employee_id'];
        $id = $query->row['user_id'];

        $sql = "UPDATE ". DB_PREFIX ."user SET forgot_token='". $this->db->escape($token) ."' WHERE user_id='". $id ."' ";
        $this->db->query($sql);

        // Get email
        $sql = "SELECT employee_email FROM ". DB_PREFIX ."employee WHERE employee_id = '".  $employee_id."' ";
        $query = $this->db->query($sql);

        return $query->row['employee_email'];

    }

    public function resetPassword($data=array()) {
        $sql = "SELECT user_id FROM ". DB_PREFIX ."user WHERE user_name='". $this->db->escape($data['user_name']) ."' AND forgot_token='". $this->db->escape($data['forgot_token']) ."' ";
        $query = $this->db->query($sql);

        if ( $query->num_rows <= 0 ) {
            return false;
        }

        // Reset password
        $id = $query->row['user_id'];

        $sql = "UPDATE ". DB_PREFIX ."user SET user_password=sha('". $this->db->escape($data['new_password']) ."'), forgot_token='' WHERE user_id='". $id ."' ";
        $this->db->query($sql);

        return true;
    }

    public function checkResetToken($data=array()) {
        $sql = "SELECT user_id FROM ". DB_PREFIX ."user WHERE user_name='". $this->db->escape($data['user_name']) ."' AND forgot_token='". $this->db->escape($data['forgot_token']) ."' ";
        $query = $this->db->query($sql);

        if ( $query->num_rows <= 0 ) {
            return false;
        } else {
            return true;
        }
    }

    public function getUsers($data=array()) {
        $sql = "SELECT u.*, g.* FROM " . DB_PREFIX . "user u ";
        $sql .= "LEFT JOIN " . DB_PREFIX . "user_group g ON (u.user_group_id = g.user_group_id) ";
        $sql .= "WHERE 1=1 ";

        if ( isset($data['user_name']) && (strlen($data['user_name']) > 0) ) {
            $sql .= "AND u.user_name like '%". $this->db->escape($data['user_name']) ."%' ";
        }

        if ( isset($data['user_email']) && (strlen($data['user_email']) > 0) ) {
            $sql .= "AND u.user_email like '%". $this->db->escape($data['user_email']) ."%' ";
        }

        if ( isset($data['user_full_name']) && (strlen($data['user_full_name']) > 0) ) {
            $sql .= "AND ( (u.user_fname like '%". $this->db->escape($data['user_full_name']) ."%') OR ( (u.user_lname like '%". $this->db->escape($data['user_full_name']) ."%') ) ) ";
        }

        if ( strlen($data['user_group_id']) > 0 ) {
            $sql .= "AND u.user_group_id = '". $this->db->escape($data['user_group_id']) ."' ";
        }

        if ( strlen($data['user_status']) > 0 ) {
            $sql .= "AND u.user_status = '". $this->db->escape($data['user_status']) ."' ";
        }

        $sort_data = array(
            'user_name',
        );

        if ( isset($data['sort']) && in_array($data['sort'], $sort_data) ) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY user_name ";
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

    public function getTotalUsers($data=array()) {
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "user u ";
        $sql .= "LEFT JOIN " . DB_PREFIX . "user_group g ON (u.user_group_id = g.user_group_id) ";
        $sql .= "WHERE 1=1 ";

        if ( isset($data['user_name']) && (strlen($data['user_name']) > 0) ) {
            $sql .= "AND u.user_name like '%". $this->db->escape($data['user_name']) ."%' ";
        }

        if ( isset($data['user_email']) && (strlen($data['user_email']) > 0) ) {
            $sql .= "AND u.user_email like '%". $this->db->escape($data['user_email']) ."%' ";
        }

        if ( isset($data['user_full_name']) && (strlen($data['user_full_name']) > 0) ) {
            $sql .= "AND ( (u.user_fname like '%". $this->db->escape($data['user_full_name']) ."%') OR ( (u.user_lname like '%". $this->db->escape($data['user_full_name']) ."%') ) ) ";
        }

        if ( strlen($data['user_group_id']) > 0 ) {
            $sql .= "AND u.user_group_id = '". $this->db->escape($data['user_group_id']) ."' ";
        }

        if ( strlen($data['user_status']) > 0 ) {
            $sql .= "AND u.user_status = '". $this->db->escape($data['user_status']) ."' ";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function editUser($data = array()) {
        // Check the username
        $sql = "SELECT user_id FROM " . DB_PREFIX . "user WHERE ";
        $sql .= "user_name = '". $this->db->escape($data['user_name']) ."' ";
        $sql .= "AND user_id <> '". $this->db->escape($data['user_id']) ."' ";

        $query = $this->db->query($sql);
        if( $query->num_rows > 0 ) {
            return false;
        }

        // Check the user email
        $sql = "SELECT user_id FROM " . DB_PREFIX . "user WHERE ";
        $sql .= "user_email = '". $this->db->escape($data['user_email']) ."' ";
        $sql .= "AND user_id <> '". $this->db->escape($data['user_id']) ."' ";

        $query = $this->db->query($sql);
        if( $query->num_rows > 0 ) {
            return false;
        }

        // Edit user
        $sql = "UPDATE " . DB_PREFIX . "user SET ";
        $sql .= "user_fname = '". $this->db->escape($data['user_fname']) ."', ";
        $sql .= "user_lname = '". $this->db->escape($data['user_lname']) ."', ";
        $sql .= "user_email = lower('". $this->db->escape($data['user_email']) ."'), ";
        $sql .= "user_name = '". $this->db->escape($data['user_name']) ."', ";
        $sql .= "user_group_id = '". $this->db->escape($data['user_group_id']) ."', ";
        $sql .= "user_status = '". $this->db->escape($data['user_status']) ."', ";
        $sql .= "update_at = NOW() ";
        $sql .= "WHERE user_id = '". $this->db->escape($data['user_id']) ."' ";
        $this->db->query($sql);

        return true;
    }

    public function deleteUser($user_id) {
        // Delete this user
        $sql = "DELETE FROM " . DB_PREFIX . "user WHERE ";
        $sql .= "user_id = '". $this->db->escape($user_id) ."' ";
        $this->db->query($sql);

        return true;
    }

    public function addUser($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "user  WHERE user_name = '". $this->db->escape($data['user_name']) ."' ";
        $sql .= "OR user_email = LOWER('" . $this->db->escape($data['user_email']) ."') ";
        $query = $this->db->query($sql);
        if ( $query->num_rows > 0 ){
            return false;
        }

        // Add new user
        $sql = "INSERT INTO "  . DB_PREFIX . "user SET ";
        $sql .= "user_fname = '". $this->db->escape($data['user_fname']) ."', ";
        $sql .= "user_lname = '". $this->db->escape($data['user_lname']) ."', ";
        $sql .= "user_email = lower('". $this->db->escape($data['user_email']) ."'), ";
        $sql .= "user_name = '". $this->db->escape($data['user_name']) ."', ";
        $sql .= "user_status = '". $this->db->escape($data['user_status']) ."', ";
        $sql .= "user_password = sha('". $this->db->escape($data['user_name']) ."'), ";
        if (isset($data['user_group_id'])) {
            $sql .= "user_group_id = '". $this->db->escape($data['user_group_id']) ."', ";
        }
        $sql .= "create_at = NOW(), ";
        $sql .= "update_at = NOW() ";

        $this->db->query($sql);

        return true;

    }

//    public function addUserGroupPermission($user_id, $permissions=array()) {
//        foreach( $permissions as $permission ) {
//            $sql = "INSERT INTO " . DB_PREFIX . "permission_user SET ";
//            $sql .= "user_id = '". $this->db->escape($user_id) ."', ";
//            $sql .= "menu_id = '". $permission['menu_id'] ."', ";
//            $sql .= "user_all_flag = '". $permission['all'] ."', ";
//            $sql .= "user_search_flag = '". $permission['search'] ."', ";
//            $sql .= "user_add_flag = '". $permission['add'] ."', ";
//            $sql .= "user_edit_flag = '". $permission['edit'] ."', ";
//            $sql .= "user_delete_flag = '". $permission['delete'] ."' ";
//            $this->db->query($sql);
//
//        }
//        return true;
//    }
//
//    public function getUserPermission($user_id) {
//        // This function is for user add or user edit.
//        $sql = "SELECT * FROM ". DB_PREFIX . "permission_user WHERE ";
//        $sql .= "user_id = '". $this->db->escape($user_id) ."' ";
//        $query = $this->db->query($sql);
//
//        return $query->rows;
//    }
//
//    public function getUserPermissionList($user_id) {
//        // This function is for check user permission.
//
//        // Get user's permissions
//        $sql = "SELECT m.menu_name, m.menu_url, p.* FROM " . DB_PREFIX . "permission_user p ";
//        $sql .= "LEFT JOIN " . DB_PREFIX . "menu m ";
//        $sql .= "ON (p.menu_id = m.menu_id) WHERE ";
//        $sql .= "p.user_id = '". $this->db->escape($user_id) ."' ";
//        $sql .= "AND p.user_search_flag = '1' ";
//        $sql .= "AND m.menu_status = '1' ";
//        $sql .= "AND m.menu_url != '' ";
//        $sql .= "ORDER BY m.menu_name, m.menu_id ASC ";
//
//        $query = $this->db->query($sql);
//
//        if ( $query->num_rows <= 0 ) {
//            return false;
//        }
//
//        return $query->rows;
//    }

}
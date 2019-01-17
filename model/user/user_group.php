<?php

class ModelUserUserGroup extends Model {
    public function getUserGroups($data=array()) {
        $sql = "SELECT *  FROM " . DB_PREFIX . "user_group WHERE 1=1 ";

        if ( isset($data['user_group_name']) && (strlen($data['user_group_name']) > 0) ) {
            $sql .= " AND user_group_name like '%". $this->db->escape($data['user_group_name']) ."%' ";
        }

        if ( strlen($data['user_group_status']) > 0 ) {
            $sql .= " AND user_group_status = '". $this->db->escape($data['user_group_status']) ."' ";
        }

        $sql .= " ORDER BY user_group_name ASC ";

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

    public function getTotalUserGroups($data=array()) {
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "user_group WHERE 1=1 ";

        if ( isset($data['user_group_name']) && (strlen($data['user_group_name']) > 0) ) {
            $sql .= " AND user_group_name like '%". $this->db->escape($data['user_group_name']) ."%' ";
        }

        if ( strlen($data['user_group_status']) > 0 ) {
            $sql .= " AND user_group_status = '". $this->db->escape($data['user_group_status']) ."' ";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getEnableUserGroups() {
        $sql = "SELECT user_group_id, user_group_name FROM " . DB_PREFIX . "user_group WHERE ";
        $sql .= "user_group_status = '1' ";

        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function addUserGroup($data=array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "user_group  WHERE user_group_name = '". $this->db->escape($data['user_group_name']) ."' ";
        $query = $this->db->query($sql);
        if ( $query->num_rows > 0 ){
            return false;
        }

        $this->db->transactionStart();

        // Add new user group
        $sql = "INSERT INTO "  . DB_PREFIX . "user_group SET ";
        $sql .= "user_group_name = '". $this->db->escape($data['user_group_name']) ."', ";
        $sql .= "user_group_status = '". $this->db->escape($data['user_group_status']) ."', ";
        $sql .= "create_at = NOW(), ";
        $sql .= "update_at = NOW() ";

        $this->db->query($sql);

        $user_group_id = $this->db->getLastId();

        // Add permission for new user group
        $this->addUserGroupPermission($user_group_id, $data['user_group_permission']);

        $this->db->transactionComplete();
        return true;
    }

    public function editUserGroup($data=array()) {
        // Check for the group name
        $sql = "SELECT * FROM " . DB_PREFIX . "user_group WHERE ";
        $sql .= "user_group_name = '". $this->db->escape($data['user_group_name']) ."' ";
        $sql .= "AND user_group_id <> '". $this->db->escape($data['user_group_id']) ."' ";

        $query = $this->db->query($sql);
        if( $query->num_rows > 0 ) {
            return false;
        }

        $this->db->transactionStart();

        // Update user group information.
        $sql = "UPDATE " . DB_PREFIX . "user_group SET ";
        $sql .= "user_group_name = '". $this->db->escape($data['user_group_name']) ."', ";
        $sql .= "user_group_status = '". $this->db->escape($data['user_group_status']) ."', ";
        $sql .= "update_at = NOW() ";
        $sql .= "WHERE user_group_id = '". $this->db->escape($data['user_group_id']) ."' ";

        $this->db->query($sql);

        // Modify user group permission
        $this->deleteUserGroupPermission($data['user_group_id']);
        $this->addUserGroupPermission($data['user_group_id'], $data['user_group_permission']);

        $this->db->transactionComplete();
        return true;
    }

    public function deleteUserGroup($user_group_id) {
        // Check users belong to this user group.
        $sql = "SELECT * FROM ". DB_PREFIX . "user WHERE ";
        $sql .= "user_group_id = '". $this->db->escape($user_group_id) ."' ";

        $query = $this->db->query($sql);
        if( $query->num_rows > 0 ) {
            return false;
        }

        // Delete this user group permission
        $this->deleteUserGroupPermission($user_group_id);

        // Delete this user group
        $sql = "DELETE FROM ". DB_PREFIX . "user_group WHERE ";
        $sql .= "user_group_id = '". $this->db->escape($user_group_id) ."' ";
        $this->db->query($sql);

        return true;
    }

    public function getUserGroupPermission($user_group_id) {
        $sql = "SELECT * FROM ". DB_PREFIX . "user_group_permission WHERE ";
        $sql .= "user_group_id = '". $this->db->escape($user_group_id) ."' ";
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function addUserGroupPermission($user_group_id, $permissions=array()) {
        foreach( $permissions as $permission ) {
            $sql = "INSERT INTO " . DB_PREFIX . "user_group_permission SET ";
            $sql .= "user_group_id = '". $this->db->escape($user_group_id) ."', ";
            $sql .= "menu_id = '". $permission['menu_id'] ."', ";
            $sql .= "group_all_flag = '". $permission['all'] ."', ";
            $sql .= "group_search_flag = '". $permission['search'] ."', ";
            $sql .= "group_add_flag = '". $permission['add'] ."', ";
            $sql .= "group_edit_flag = '". $permission['edit'] ."', ";
            $sql .= "group_delete_flag = '". $permission['delete'] ."' ";
            $this->db->query($sql);

        }
        return true;
    }

    public function deleteUserGroupPermission($user_group_id) {
        $sql = "DELETE FROM ". DB_PREFIX . "user_group_permission WHERE ";
        $sql .= "user_group_id = '". $this->db->escape($user_group_id) ."' ";
        $this->db->query($sql);

        return true;
    }

    public function getUserGroupPermissionList($user_group_id) {
        // This function is for check user permission.

        // Get user's permissions
        $sql = "SELECT m.menu_name, m.menu_url, p.* FROM " . DB_PREFIX . "user_group_permission p ";
        $sql .= "LEFT JOIN " . DB_PREFIX . "menu m ";
        $sql .= "ON (p.menu_id = m.menu_id) WHERE ";
        $sql .= "p.user_group_id = '". $this->db->escape($user_group_id) ."' ";
        $sql .= "AND p.group_search_flag = '1' ";
        $sql .= "AND m.menu_status = '1' ";
        $sql .= "AND m.menu_url != '' ";
        $sql .= "ORDER BY m.menu_name, m.menu_id ASC ";

        $query = $this->db->query($sql);

        if ( $query->num_rows <= 0 ) {
            return false;
        }

        return $query->rows;
    }
}
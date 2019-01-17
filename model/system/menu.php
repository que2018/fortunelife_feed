<?php

class ModelSystemMenu extends Model {

    public function getTotalMenus($data) {
        $sql = "SELECT * FROM " . DB_PREFIX . "view_menu_list " ;
        $sql .= "WHERE 1=1 ";

        if ( isset($data['menu_name']) && (strlen($data['menu_name']) > 0) ) {
            $sql .= " AND path like '%". $this->db->escape($data['menu_name']) ."%' ";
        }

        if ( strlen($data['menu_status']) > 0 ) {
            $sql .= " AND menu_status = '". $this->db->escape($data['menu_status']) ."' ";
        }

        $query = $this->db->query($sql);

        return $query->num_rows;
    }

    public function getMenus($data) {
        $sql = "SELECT * FROM " . DB_PREFIX . "view_menu_list " ;
        $sql .= "WHERE 1=1 ";

        if ( isset($data['menu_name']) && (strlen($data['menu_name']) > 0) ) {
            $sql .= " AND path like '%". $this->db->escape($data['menu_name']) ."%' ";
        }

        if ( strlen($data['menu_status']) > 0 ) {
            $sql .= " AND menu_status = '". $this->db->escape($data['menu_status']) ."' ";
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

    public function getMenuById($menu_id) {
        $sql = "SELECT * FROM " . DB_PREFIX . "menu WHERE menu_id = '". $menu_id ."' ";

        $query = $this->db->query($sql);
        return $query->row;
    }

    public function getEnableMenuLists() {
        $sql = "SELECT * FROM " . DB_PREFIX . "view_menu_list " ;
//        $sql .= "WHERE menu_status = '1' ";
        $sql .= "ORDER BY path ";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function addMenu($data = array()) {
        // Check the menu name
        $sql = "SELECT * FROM " . DB_PREFIX . "menu  WHERE menu_name = '". $this->db->escape($data['menu_name']) ."' ";
        $query = $this->db->query($sql);
        if ( $query->num_rows > 0 ){
            return false;
        }

        $this->db->transactionStart();

        // Add the new menu
        $parent_id = 0;
        $sql = "INSERT INTO "  . DB_PREFIX . "menu SET ";
        $sql .= "menu_name = '". $this->db->escape($data['menu_name']) ."', ";
        if ( isset($data['menu_parent_id']) && ( strlen($data['menu_parent_id']) > 0 ) ) {
            $parent_id = (int)$this->db->escape($data['menu_parent_id']);
            $sql .= "menu_parent_id = '". $parent_id ."', ";
        } else {
            $sql .= "menu_parent_id = '0', ";
        }

        if ( isset($data['menu_url']) && ( strlen($data['menu_url']) > 0 ) ) {
            $sql .= "menu_url = '". $this->db->escape($data['menu_url']) ."', ";
        }

        $sql .= "menu_status = '". $this->db->escape($data['menu_status']) ."', ";
        $sql .= "has_submenu = '0', ";
        $sql .= "create_at = NOW(), ";
        $sql .= "update_at = NOW() ";

        $this->db->query($sql);

        $menu_id = $this->db->getLastId();

        if ( $menu_id != 0 ) {
            // Set menu path
            $this->setMenuPath($menu_id, $parent_id);
        }

        $this->db->transactionComplete();

        return true;
    }

    public function editMenu($data = array()) {
        // Get parameters
        $parent_id = (int)$this->db->escape($data['menu_parent_id']);
        $menu_id = $this->db->escape($data['menu_id']);

        // Check the menu name
        $sql = "SELECT * FROM " . DB_PREFIX . "menu  WHERE menu_name = '". $this->db->escape($data['menu_name']) ."' ";
        $sql .= "AND menu_id <> '". $menu_id ."' ";
        $query = $this->db->query($sql);
        if ( $query->num_rows > 0 ){
            return false;
        }

        // Get original data
        $old_record = $this->getMenuById($menu_id);

        $this->db->transactionStart();

        // Update menu
        $sql = "UPDATE "  . DB_PREFIX . "menu SET ";
        $sql .= "menu_name = '". $this->db->escape($data['menu_name']) ."', ";
        $sql .= "menu_parent_id = '". $parent_id ."', ";
        $sql .= "menu_url = '". $this->db->escape($data['menu_url']) ."', ";
        $sql .= "menu_status = '". $this->db->escape($data['menu_status']) ."', ";
        $sql .= "update_at = NOW() ";
        $sql .= "WHERE menu_id = '". $menu_id ."' ";

        $this->db->query($sql);

        if ( $parent_id == $old_record['menu_parent_id'] ) {
            // Did not need to change the parent menu.
        } else {
            // Need to change the parent menu.

            // Get delete level for use later
            $sql = "SELECT level FROM "  . DB_PREFIX . "menu_path WHERE path_id = '". $menu_id ."' AND menu_id = '". $menu_id ."' ";
            $query = $this->db->query($sql);
            $del_level = $query->row['level'];

            // Delete old records
            $sql = "DELETE FROM " . DB_PREFIX . "menu_path WHERE menu_id = '". $menu_id ."' ";
            $this->db->query($sql);

            // Add new records
            $this->setMenuPath($menu_id, $parent_id);

            // Get new menu path
            $sql = "SELECT * FROM "  . DB_PREFIX . "menu_path WHERE menu_id = '". $menu_id ."' ORDER BY level";
            $query = $this->db->query($sql);
            $new_menu_info = $query->rows;

            // Get all sub menu id list.
            $sql = "SELECT * FROM " . DB_PREFIX . "menu_path WHERE path_id = '". $menu_id ."' AND menu_id <> '". $menu_id ."' ";
            $query = $this->db->query($sql);

            foreach ( $query->rows as $submenu ) {
                // Delete old records for submenu
                $sql = "DELETE FROM " . DB_PREFIX . "menu_path WHERE menu_id = '". $submenu['menu_id'] ."' AND level <= '". $del_level ."' ";
                $this->db->query($sql);

                // Get all other records of submenu
                $sql = "SELECT * FROM " . DB_PREFIX . "menu_path WHERE menu_id = '". $submenu['menu_id'] ."' ORDER BY level ";
                $query = $this->db->query($sql);
                $keep_info = $query->rows;

                // Delete all records of submenu
                $sql = "DELETE FROM " . DB_PREFIX . "menu_path WHERE menu_id = '". $submenu['menu_id'] ."' ";
                $this->db->query($sql);

                // Insert new menu path
                $current_level = 0;
                foreach( $new_menu_info as $new_menu_item ) {
                    // Insert
                    $sql = "INSERT INTO " . DB_PREFIX . "menu_path SET ";
                    $sql .= "menu_id = '". $submenu['menu_id'] ."', ";
                    $sql .= "path_id = '". $new_menu_item['path_id'] ."', ";
                    $sql .= "level = '". $current_level ."' ";
                    $this->db->query($sql);

                    $current_level++;
                }

                foreach($keep_info as $item) {
                    // Insert
                    $sql = "INSERT INTO " . DB_PREFIX . "menu_path SET ";
                    $sql .= "menu_id = '". $submenu['menu_id'] ."', ";
                    $sql .= "path_id = '". $item['path_id'] ."', ";
                    $sql .= "level = '". $current_level ."' ";
                    $this->db->query($sql);

                    $current_level++;
                }
            }
        }

        // Check for old parent menu
        $sql = "SELECT * FROM " . DB_PREFIX . "menu_path WHERE path_id = '". $old_record['menu_parent_id'] ."' AND menu_id <> '". $old_record['menu_parent_id'] ."' ";
        $query = $this->db->query($sql);
        if ( $query->num_rows <= 0 ) {
            // The old parent menu does not have any other submenus.
            // Update old parent menu
            $sql = "UPDATE "  . DB_PREFIX . "menu SET ";
            $sql .= "has_submenu = '0' ";
            $sql .= "WHERE menu_id = '". $old_record['menu_parent_id'] ."' ";

            $this->db->query($sql);
        }

        $this->db->transactionComplete();
        return true;
    }

    public function deleteMenu($menu_id) {
        $this->db->transactionStart();

        // Get all submenu and self.
        $sql = "SELECT * FROM " . DB_PREFIX . "menu_path WHERE path_id = '". $this->db->escape($menu_id) ."' ";
        $query = $this->db->query($sql);

        foreach($query->rows as $del_menu) {
            // Delete menu path by menu_id
            $sql = "DELETE FROM " . DB_PREFIX . "menu_path WHERE menu_id = '". $this->db->escape($del_menu['menu_id']) ."' ";
            $this->db->query($sql);

            // Delete menu by menu_id
            $sql = "DELETE FROM " . DB_PREFIX . "menu WHERE menu_id = '". $this->db->escape($del_menu['menu_id']) ."' ";
            $this->db->query($sql);
        }

        $this->db->transactionComplete();
        return true;
    }

    private function setMenuPath($menu_id, $parent_id) {
        // Update menu path
        $level = 0;

        if ( $parent_id > 0 ) {

            // Get parent menu path
            $sql = "SELECT * FROM "  . DB_PREFIX . "menu_path WHERE menu_id = '". $parent_id ."' ";
            $query = $this->db->query($sql);

            // Add parent menu path.
            foreach ( $query->rows as $item) {
                $sql = "INSERT INTO "  . DB_PREFIX . "menu_path SET ";
                $sql .= "menu_id = '". $menu_id ."', ";
                $sql .= "path_id = '". $item['path_id'] ."', ";
                $sql .= "level = '". $level ."' ";

                $this->db->query($sql);

                // Update has_submenu flag
                $sql = "UPDATE "  . DB_PREFIX . "menu SET ";
                $sql .= "has_submenu = '1' ";
                $sql .= "WHERE menu_id = '". $item['path_id'] ."' ";

                $this->db->query($sql);

                $level++;
            }

        }

        // Add last path for menu
        $sql = "INSERT INTO "  . DB_PREFIX . "menu_path SET ";
        $sql .= "menu_id = '". $menu_id ."', ";
        $sql .= "path_id = '". $menu_id ."', ";
        $sql .= "level = '". $level ."' ";

        $this->db->query($sql);

        return $level;
    }

    public function getUserMenuList($user_id) {
        // Get user's permissions
        $sql = "SELECT m.*, p.* FROM " . DB_PREFIX . "permission_user p LEFT JOIN " . DB_PREFIX . "menu m ";
        $sql .= "ON (p.menu_id = m.menu_id) WHERE ";
        $sql .= "p.user_id = '". $this->db->escape($user_id) ."' ";
        $sql .= "AND p.user_search_flag = '1' ";
        $sql .= "ORDER BY m.menu_name, m.menu_id ASC ";

        $query = $this->db->query($sql);

//        print_r($sql);
//        echo "<br><br>";

        $all_menus = array();

        foreach( $query->rows as $menu ) {
            if ( $menu['menu_parent_id'] != 0 ){
                continue;
            }

            $tempResult = $this->getAllSubMenu($menu, $query->rows, $all_menus);


            array_push($all_menus, $tempResult);
        }

//        print_r($all_menus);

        return $all_menus;
    }

    public function getUserGroupMenuList($user_group_id) {
        // Get user's permissions
        $sql = "SELECT m.*, p.* FROM " . DB_PREFIX . "user_group_permission p LEFT JOIN " . DB_PREFIX . "menu m ";
        $sql .= "ON (p.menu_id = m.menu_id) WHERE ";
        $sql .= "p.user_group_id = '". $this->db->escape($user_group_id) ."' ";
        $sql .= "AND p.group_search_flag = '1' ";
        $sql .= "ORDER BY m.menu_name, m.menu_id ASC ";

        $query = $this->db->query($sql);

//        print_r($sql);
//        echo "<br><br>";

        $all_menus = array();

        foreach( $query->rows as $menu ) {
            if ( $menu['menu_parent_id'] != 0 ){
                continue;
            }

            $tempResult = $this->getAllSubMenu($menu, $query->rows, $all_menus);


            array_push($all_menus, $tempResult);
        }

//        print_r($all_menus);

        return $all_menus;
    }

    public function getAllSubMenu($tempMenu, $whole_list, &$result) {

        $tempMenu['children'] = array();

//        print_r($tempMenu['menu_name']);
//        print_r('<br>----------------------------'. "\n");

        if ( $tempMenu['has_submenu'] == '1' ) {
            foreach ($whole_list as $menu) {
                if ($menu['menu_parent_id'] == $tempMenu['menu_id']) {
                    $result_menu = $this->getAllSubMenu($menu, $whole_list, $result);
                    array_push($tempMenu['children'], $result_menu);
                }
            }
        }

        return $tempMenu;

    }

}
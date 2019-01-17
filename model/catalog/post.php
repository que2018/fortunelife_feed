<?php

class ModelCatalogPost extends Model {

    public function addPost($data) {
		$sql = "INSERT INTO " . DB_PREFIX . "post SET ";
		$sql .= "post_title = '" . $this->db->escape($data['post_title']) . "',";
		$sql .= "post_author = '" . $this->db->escape($data['post_author']) . "',";
		$sql .= "post_cover_img = '" . $this->db->escape($data['post_cover_img']) . "',";
		$sql .= "post_source = '" . $this->db->escape($data['post_source']) . "',";
		$sql .= "post_source_url = '" . $this->db->escape($data['post_source_url']) . "',";
		$sql .= "post_content = '" . $this->db->escape($data['post_content']) . "',";
		$sql .= "create_at = NOW(),";
		$sql .= "update_at = NOW()";
		
        $this->db->query($sql);
		
		if($data['post_category_id']) {
			$post_id = $this->db->getLastId();

			$sql = "INSERT INTO " . DB_PREFIX . "post_category SET ";
			$sql .= "post_id = '" . $post_id . "',";
			$sql .= "category_id = '" . $this->db->escape($data['post_category_id']) . "'";
			
			$this->db->query($sql);
		}
    }
	
	public function getPost($post_id) {
        $sql = "SELECT * FROM " . DB_PREFIX . "post WHERE post_id = '" . $post_id . "'";
		
        $query = $this->db->query($sql);

        if ( $query->num_rows > 0 ) {
			return $query->row;
        } else {
			return false;
		}
    }
	
	public function getPosts() {
        $sql = "SELECT * FROM " . DB_PREFIX . "post ORDER BY post_id DESC";
		
        $query = $this->db->query($sql);

        if ( $query->num_rows > 0 ) {
			return $query->rows;
        } else {
			return false;
		}
    }
	
	public function getPostByTitle($title) {
        $sql = "SELECT * FROM " . DB_PREFIX . "post WHERE post_title = '" . $title . "'";
		
        $query = $this->db->query($sql);

        if ( $query->num_rows > 0 ) {
			return $query->row;
        } else {
			return false;
		}
    }
	
	public function updatePostCoverImg($post_id, $post_cover_img) {
       $sql = "UPDATE " . DB_PREFIX . "post SET post_cover_img = '". $this->db->escape($post_cover_img) ."' WHERE post_id = " . $post_id;
	   	   
       $query = $this->db->query($sql);
    }
	
	public function updatePostContent($post_id, $post_content) {
       $sql = "UPDATE " . DB_PREFIX . "post SET post_content = '". $this->db->escape($post_content) ."' WHERE post_id = " . $post_id;
	   	   
       $query = $this->db->query($sql);
    }
}
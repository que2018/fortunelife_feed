<?php

class ModelRssFeed extends Model {

    public function getFeedPointer() {
        $sql = "SELECT * FROM ". DB_PREFIX ."feed_pointer LIMIT 1";

        $query = $this->db->query($sql);

        return $query->row;
    }
	
	public function getPostPointer() {
        $sql = "SELECT * FROM ". DB_PREFIX ."post_pointer LIMIT 1";

        $query = $this->db->query($sql);

        return $query->row;
    }
	
	public function updateFeedPointer($feed_code, $page) {
        $sql = "UPDATE " . DB_PREFIX . "feed_pointer SET feed_code = '". $feed_code ."', page = '". $page ."'";
        $query = $this->db->query($sql);
    }
	
	public function updatePostPointer() {
		$post_pointer = $this->getPostPointer();
		$post_id = $post_pointer['post_id'];
		
		$sql = "select * FROM " . DB_PREFIX . "post where post_id = (select min(post_id) from " . DB_PREFIX . "post where post_id > " . $post_id . ")";
								
        $query = $this->db->query($sql);
		
        if ( $query->num_rows > 0 ) {
			$post_id_next = $query->row['post_id'];
			
		} else {
			$sql = "select * FROM " . DB_PREFIX . "post where post_id = (select min(post_id) from " . DB_PREFIX . "post)";
			
			$query = $this->db->query($sql);
			
			$post_id_next = $query->row['post_id'];
		}
		
		$sql = "UPDATE " . DB_PREFIX . "post_pointer SET post_id = '". $post_id_next ."'";
        $query = $this->db->query($sql);
	}
	
	public function getNextFeed($feed_code) {
		$sql = "select * FROM " . DB_PREFIX . "feed where code = '" . $feed_code . "'";
        $query = $this->db->query($sql);
		
		$feed_id = $query->row['feed_id'];
		
        $sql = "select * FROM " . DB_PREFIX . "feed where feed_id = (select min(feed_id) from " . DB_PREFIX . "feed where feed_id > " . $feed_id . ") AND status = 1";
								
        $query = $this->db->query($sql);
		
        if ( $query->num_rows > 0 ) {
			return $query->row['code'];
			
		} else {
			$sql = "select * FROM " . DB_PREFIX . "feed where feed_id = (select min(feed_id) from " . DB_PREFIX . "feed where status = 1)";
			
			$query = $this->db->query($sql);
			
			return $query->row['code'];
		}
    }
}
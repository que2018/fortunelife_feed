<?php

class ControllerRssFeed extends Controller {

	private $num = 5;

	public function scan() {
		$this->model("rss/feed");
		
		$feed_pointer = $this->model_rss_feed->getFeedPointer();
		
		$feed_code = $feed_pointer['feed_code'];
		$page = $feed_pointer['page'];
		
		$this->library("feed/".$feed_code);
		
		$count = $this->{$feed_code}->count();
		
		//go to next feed
		if($this->num * $page > $count) {
			$next_feed_code = $this->model_rss_feed->getNextFeed($feed_code);
			$this->model_rss_feed->updateFeedPointer($next_feed_code, 1);	
			
			$log = array(
				'adjustment'  => 'next feed'
			);
			
		//continue with current feed
		} else {
			$start = $this->num * ($page - 1) + 1;
			
			if(($start + $this->num - 1) > $count) {
				$limit = $count - $start + 1;
			} else {
				$limit = $this->num;
			}
			
			$log = array(
				'feed'  => $feed_code,
				'start' => $start,
				'end'   => $start + $limit - 1,
				'total' => $count
			);
			
			$this->log($log);		

			$result = $this->{$feed_code}->fetch($start, $limit);
			
			$logs = $result['logs'];
			$feeds = $result['feeds'];
			
			if($logs) {
				foreach($logs as $log) {
					$this->log($log);		
				}
			}
			
			if($feeds) {
				$this->import($feeds);
			}
						
			$this->model_rss_feed->updateFeedPointer($feed_code, $page + 1);
		}
		
		$this->api->sendResponse(200, $logs); 
	}
	
	public function fix() {
		$this->model("rss/feed");
		$this->model("catalog/post");
		
		$post_pointer = $this->model_rss_feed->getPostPointer();
		
		$post_id = $post_pointer['post_id'];
			
		$post = $this->model_catalog_post->getPost($post_id);
		
		$post_content = $post['post_content'];
		
		$post_content_clean = str_replace('strong', 'span', $post_content);

		$this->model_catalog_post->updatePostContent($post_id, $post_content_clean);

		$this->model_rss_feed->updatePostPointer();
	}
	
	/* public function fix_image() {
		$img_placeholders = array(
			'default/default-cover-image-01.jpg',
			'default/default-cover-image-02.jpg',
			'default/default-cover-image-03.jpg',
			'default/default-cover-image-04.jpg',
			'default/default-cover-image-05.jpg',
			'default/default-cover-image-06.jpg',
			'default/default-cover-image-07.jpg',
			'default/default-cover-image-08.jpg',
			'default/default-cover-image-09.jpg',
			'default/default-cover-image-10.jpg'
		);
		
		$this->model("catalog/post");
		
		$posts = $this->model_catalog_post->getPosts();
		
		if($posts) {
			foreach($posts as $post) {
				if(empty($post['post_cover_img'])) {
					$index = rand(0, 9);
					
					$post_id = $post['post_id'];
					$post_cover_img = $img_placeholders[$index];

					$this->model_catalog_post->updatePostCoverImg($post_id, $post_cover_img);
					
					echo $post_id . "\n";
				}
			}
		}
	} */
		
	private function import($feeds) {
		$img_placeholders = array(
			'default/default-cover-image-01.jpg',
			'default/default-cover-image-02.jpg',
			'default/default-cover-image-03.jpg',
			'default/default-cover-image-04.jpg',
			'default/default-cover-image-05.jpg',
			'default/default-cover-image-06.jpg',
			'default/default-cover-image-07.jpg',
			'default/default-cover-image-08.jpg',
			'default/default-cover-image-09.jpg',
			'default/default-cover-image-10.jpg'
		);
		
		$this->model("catalog/post");

		$logs = array();
		$feeds_valid = array();
		
		foreach($feeds as $feed) {
			$post_title = $feed['post_title'];
						
			$result = $this->model_catalog_post->getPostByTitle($post_title);
			
			if(!$result) {
				$feeds_valid[] = $feed;
				
				if(empty($feed['post_cover_img'])) {
					$index = rand(0, 9);
					$feed['post_cover_img'] = $img_placeholders[$index];
				}
				
				$this->model_catalog_post->addPost($feed);				
			} else {
				$logs[] = array(
					'duplicated title' => $post_title
				);
			}
		}

		$logs[] = array(
			'total_import' => count($feeds),
			'total_valid'  => count($feeds_valid)
		);
		
		if($logs) {
			foreach($logs as $log) {
				$this->log($log);		
			}
		}
    }
	
	private function log($log) {		
		$content = "[" . gmdate('d-M-Y h:i:s') . " UTC] ";
		
		foreach($log as $key => $value) {
			$content .= $key . ": " . $value . " ";
		}
		
		$content .= "\n\n";
		
		file_put_contents("log.txt", $content, FILE_APPEND);
    }
}
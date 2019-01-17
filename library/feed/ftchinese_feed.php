<?php

require_once(DIR_SYSTEM . "html_parser/vendor/autoload.php");
use PHPHtmlParser\Dom;

class LibFeedFtchineseFeed {
	
	public function count() {
		$nodes = array();
		
		$dom = new Dom;
		$doc = new DOMDocument;
		
		$url = 'http://www.ftchinese.com/rss/feed';
	
		$reader = new XMLReader;
		$reader->open($url);

		while($reader->read()) {
			if(($reader->name == 'item') && ($reader->nodeType == XMLReader::ELEMENT)) {
				$nodes[] = simplexml_import_dom($doc->importNode($reader->expand(), true));
			}
		}
				
		$reader->close();
		
		return count($nodes);
	}
	
	public function fetch($start = null, $limit = null) {
		$nodes = array();
		$feeds = array();
		$logs = array();
		
		$dom = new Dom;
		$doc = new DOMDocument;
		
		$url = 'http://www.ftchinese.com/rss/feed';
	
		$reader = new XMLReader;
		$reader->open($url);

		while($reader->read()) {
			if(($reader->name == 'item') && ($reader->nodeType == XMLReader::ELEMENT)) {
				$nodes[] = simplexml_import_dom($doc->importNode($reader->expand(), true));
			}
		}
				
		$reader->close();
		
		if($nodes) {
			if($start && $limit) {
				$nodes = array_slice($nodes, ($start - 1), ($limit + 1));
			}
			
			foreach($nodes as $i => $node) {
				$title = trim((string)$node->title);
				$link = (string)$node->link;
				
				//full article link
				$link_full = $link . '?adchannelID=&full=y';
				
				$html = file_get_contents($link_full);
				$dom->load($html);
																
				//get cover image
				if(isset($dom->find('.story-image figure')[0])) {
					$post_cover_img_data = $dom->find('.story-image figure')[0]->getAttribute('data-url');
					$post_cover_img = ($post_cover_img_data)?$post_cover_img_data:'';
				} else {
					$post_cover_img = '';
					
					$logs[] = array(
						'feed_code' => 'ftchinese_feed',
						'url'       => $link_full,
						'error'     => 'can not fetch image tag'
					);
				}
				
				//get content
				if(isset($dom->find('.story-body')[0])) {
					$content = $dom->find('.story-body')[0];
					$content_html_clean = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $content->innerHtml);
					
					if($title && $link && $content_html_clean) {
						$feeds[] = array(
							'post_title'       => $title,
							'post_author'      => 'FT中文网',
							'post_cover_img'   => $post_cover_img,
							'post_source'      => $url,
							'post_source_url'  => $link_full,
							'post_content'     => $content_html_clean,
							'post_category_id' => 56
						);
					} 
				} else {
					$logs[] = array(
						'feed_code' => 'ftchinese_feed',
						'url'       => $link_full,
						'error'     => 'can not fetch content tag'
					);
				}
			} 
		}
		
		$result = array(
			'feeds' => $feeds,
			'logs'  => $logs
		);
		
		return $result;
	}
	
	public function fetch_test($url) {	
		$dom = new Dom;
	
		$html = file_get_contents($url);
		file_put_contents("fetch_test.html", $html);
		$dom->load($html);
										
		//get cover image
		if(isset($dom->find('.story-image figure')[0])) {
			$post_cover_img_data = $dom->find('.story-image figure')[0]->getAttribute('data-url');
			$result['post_cover_img'] = ($post_cover_img_data)?$post_cover_img_data:'';
		} else {
			$result['post_cover_img'] = null;	
		}
		
		//get content
		if(isset($dom->find('.story-body')[0])) {
			$content = $dom->find('.story-body')[0];
			$result['content_html_clean'] = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $content->innerHtml);			
		} else {
			$result['content_html_clean'] = null;
		}
		
		return $result;
	}
}
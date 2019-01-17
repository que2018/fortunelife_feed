<?php

require_once(DIR_SYSTEM . "html_parser/vendor/autoload.php");
use PHPHtmlParser\Dom;

class LibFeedWallstreetcn {
	
	public function count() {
		$nodes = array();
		
		$dom = new Dom;
		$doc = new DOMDocument;
		
		$url = 'https://dedicated.wallstreetcn.com/rss.xml';
	
		$reader = new XMLReader;
		$reader->open($url);

		while($reader->read()) {
			if(($reader->name == 'item') && ($reader->nodeType == XMLReader::ELEMENT)) {
				if($reader->expand()) 
				{
					$nodes[] = simplexml_import_dom($doc->importNode($reader->expand(), true));
				}
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
		
		$url = 'https://dedicated.wallstreetcn.com/rss.xml';
	
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
				$description = (string)$node->description;

				if($title && $link && $description) {
					$feeds[] = array(
						'post_title'       => $title,
						'post_author'      => '华尔街见闻',
						'post_cover_img'   => '',
						'post_source'      => $url,
						'post_source_url'  => $link,
						'post_content'     => $description,
						'post_category_id' => 56
					);
				} else {
					$logs[] = array(
						'feed_code' => 'wallstreetcn',
						'url'       => $link,
						'error'     => 'can not fetch content'
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
	
	public function fetch_test($url) {}
}
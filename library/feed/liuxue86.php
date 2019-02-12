<?php

require_once(DIR_SYSTEM . "html_parser/vendor/autoload.php");
use PHPHtmlParser\Dom;

class LibFeedLiuxue86 {
	
	public function count() {
		$nodes = array();
		
		$dom = new Dom;
		$doc = new DOMDocument;
		
		$url = 'https://www.liuxue86.com/rss/733.xml';
	
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
		
		$url = 'https://www.liuxue86.com/rss/733.xml';
	
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
				
				$html = file_get_contents($link);
				$dom->load($html);

				if(isset($dom->getElementsByClass('main_zhengw')[0])) {
					$content = $dom->getElementsByClass('main_zhengw')[0];
					$content_html_clean = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $content->innerHtml);
									
					$dom = new Dom;
					$dom->load($content_html_clean);

					$img_src='';
					
					if(isset($dom->find('img')[0])){
						$img_src = $dom->find('img')[0]->getAttribute('src');
					} 
	
					if($title && $link && $content_html_clean) {
						$feeds[] = array(
							'post_title'       => $title,
							'post_author'      => '出国留学网',
							'post_cover_img'   => $img_src,
							'post_source'      => $url,
							'post_source_url'  => $link,
							'post_content'     => $content_html_clean,
							'post_category_id' => 51
						);
					}
				} else {
					$logs[] = array(
						'feed_code' => 'liuxue86',
						'url'       => $link,
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
}
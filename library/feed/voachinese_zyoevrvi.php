<?php

require_once(DIR_SYSTEM . 'html_parser/vendor/autoload.php');
use PHPHtmlParser\Dom;

class LibFeedVoachineseZyoevrvi {
	
	public function count() {
		$nodes = array();
		
		$dom = new Dom;
		$doc = new DOMDocument;
		
		$url = 'https://www.voachinese.com/api/z-_yoevrvi';
	
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
		
		$url = 'https://www.voachinese.com/api/z-_yoevrvi';
	
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
																				
				//get cover image
				if(isset($dom->find('.cover-media img')[0])) {
					$post_cover_img_data = $dom->find('.cover-media img')[0]->getAttribute('src');
					$post_cover_img = ($post_cover_img_data)?$post_cover_img_data:'';
				} else {
					$post_cover_img = '';
					
					$logs[] = array(
						'feed_code' => 'voachinese_zyoevrvi',
						'url'       => $link,
						'error'     => 'can not fetch image tag'
					);
				}
				
				//get content
				if(isset($dom->find('.wsw')[0])) {
					$content = $dom->find('.wsw')[0];
					$content_html_clean = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $content->innerHtml);
					
					if($title && $link && $content_html_clean) {
						$feeds[] = array(
							'post_title'       => $title,
							'post_author'      => '美国之音-经济·金融·贸易',
							'post_cover_img'   => $post_cover_img,
							'post_source'      => $url,
							'post_source_url'  => $link,
							'post_content'     => $content_html_clean,
							'post_category_id' => 43
						);
					} 
				} else {
					$logs[] = array(
						'feed_code' => 'voachinese_zyoevrvi',
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

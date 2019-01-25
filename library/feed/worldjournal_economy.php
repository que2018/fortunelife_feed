<?php

require_once(DIR_SYSTEM . "html_parser/vendor/autoload.php");
use PHPHtmlParser\Dom;

class LibFeedWorldjournalEconomy {
	
	public function count() {
		$nodes = array();
		
		$dom = new Dom;
		$doc = new DOMDocument;
		
		$url = 'https://www.worldjournal.com/caterss/?cat=207853&variant=zh-cn';
	
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
		
		$url = 'https://www.worldjournal.com/caterss/?cat=207853&variant=zh-cn';
	
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

				if(isset($dom->getElementsByClass('full-post')[0])) {
					$content = $dom->getElementsByClass('full-post')[0];
					$content_html_clean = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $content->innerHtml);
					
					//Delete ad and trilMark div
					$dom = new Dom;
					$dom->load($content_html_clean);
					
					if(!empty($dom->getElementsByClass('declare-title')[0])){
						$ad = $dom->getElementsByClass('declare-title')[0]->outerHtml;
						$content_html_clean = str_replace($ad, '', $content_html_clean);
					}
					
					if(!empty($dom->getElementsByClass('pagination')[0])){
						$ad = $dom->getElementsByClass('pagination')[0]->outerHtml;
						$content_html_clean = str_replace($ad, '', $content_html_clean);
					}
					
					if(!empty($dom->getElementsByClass('most-popular'))){
						$tailMark = $dom->getElementsByClass('most-popular')->outerHtml;
						$indexStart = strpos($content_html_clean, $tailMark);
						$tailMarkToEnd = substr($content_html_clean, $indexStart, strlen($content_html_clean)-$indexStart);
						$content_html_clean = str_replace($tailMarkToEnd, '', $content_html_clean);
					}

					if(!empty($dom->getElementsByClass('post-controls'))){
						$ad = $dom->getElementsByClass('post-controls')->outerHtml;
						$content_html_clean = str_replace($ad, '', $content_html_clean);
					}						
					
					if(!empty($dom->getElementsByClass('post-title'))){
						$ad = $dom->getElementsByClass('post-title')->outerHtml;
						$content_html_clean = str_replace($ad, '', $content_html_clean);
					}					
					
					if(isset($dom->find('time')[0])){
						$ad = $dom->find('time')[0]->outerHtml;
						$content_html_clean = str_replace($ad, '', $content_html_clean);
					}
										
					$dom = new Dom;
					$dom->load($content_html_clean);

					$img_src='';
					
					if(isset($dom->find('img')[0])){
						$img_src = $dom->find('img')[0]->getAttribute('src');
					} 
	
					if($title && $link && $content_html_clean) {
						$feeds[] = array(
							'post_title'       => $title,
							'post_author'      => '世界新闻网 - 经济',
							'post_cover_img'   => $img_src,
							'post_source'      => $url,
							'post_source_url'  => $link,
							'post_content'     => $content_html_clean,
							'post_category_id' => 5
						);
					}
				} else {
					$logs[] = array(
						'feed_code' => 'worldjournal',
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
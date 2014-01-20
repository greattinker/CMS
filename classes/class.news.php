<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class news extends default_class 
{
	public function getNews($limit = 50, $limitstart = 0){
		global $db;
		$news_content = new news_content();
		
		$r = $db->_getData($news_content->getDBTable(), array('id'), '', $limit, $limitstart);
		
		$news_contents = array();
		foreach($r as $c){
			$news_contents[]=new news_content($c['id']);
		}
		
		return $news_contents;
	}
	
	public function getAllNewsXML($dom = '', $limit = 50, $limitstart = 0){
		if($dom == '')
			$dom = new DOMDocument('1.0', 'utf-8');
		$xmlNews = $dom->appendChild($dom->createElement('news'));
		
		$news = $this->getNews($limit, $limitstart);
		foreach($news as $news_content){
			$xmlNews->appendChild($news_content->getXML($dom));
		}
		
		return $xmlNews;
	}
	
	

}

?>

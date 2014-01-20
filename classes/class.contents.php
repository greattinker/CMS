<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class contents extends default_class 
{
	public function getAllContents($limit = 50, $limitstart = 0){
		global $db;
		$content = new content();
		
		$r = $db->_getData($content->getDBTable(), array('id'), '', $limit, $limitstart);
		
		$contents = array();
		foreach($r as $c){
			$contents[]=new content($c['id']);
		}
		
		return $contents;
	}
	
	public function getAllContentsXML($dom = '', $limit = 50, $limitstart = 0){
		if($dom == '')
			$dom = new DOMDocument('1.0', 'utf-8');
		$xmlContents = $dom->appendChild($dom->createElement('contents'));
		
		$contents = $this->getAllContents($limit, $limitstart);
		foreach($contents as $content){
			$xmlContents->appendChild($content->getXMLHead($dom));
		}
		
		return $xmlContents;
	}

}

?>

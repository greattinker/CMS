<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class categories extends default_class 
{
	public function getContents($categoryId, $limit = 50, $limitstart = 0){
		global $db;
		$content = new content();
		
		$r = $db->_getData($content->getDBTable(), array('id'), "catid=".$categoryId, $limit, $limitstart);
		
		$contents = array();
		foreach($r as $c){
			$contents[]=new content($c['id']);
		}
		
		return $contents;
	}
	
	

}

?>

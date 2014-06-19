<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class content_parts extends default_class 
{
	public function getContentParts($contentId, $limit = 50, $limitstart = 0, $orderBy = 'ordering', $orderDir = 'ASC', $conditions = array()){
		global $db;
		
#		$content = new content($contentId);
#		if($content->doesExist() == false) return array();
		
		$content_part = new content_part();
		$cond = 'content_id='.$contentId;
		
		foreach($conditions as $field => $value){
			$cond.=" AND ".$field."=".$value."";
		}
#		print_r($cond);
		$r = $db->_getData($content_part->getDBTable(), array('id'), $cond, $limit, $limitstart, $orderBy, $orderDir);
		
		$content_parts = array();
		foreach($r as $c){
			$content_parts[]=new content_part($c['id']);
		}
		
		return $content_parts;
	}

	

}

?>

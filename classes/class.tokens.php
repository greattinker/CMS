<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class tokens extends default_class 
{
	public function getToken($tokenHash){
		global $db;
		
		$t = new token();
		$data = $db->_getData($t->getDBTable(), array('id'), "token='".$tokenHash."'");
		
		if(count($data)==0) return null;
		$tr = new token($data[0]['id']);
		
		return $tr;
	}	
}

?>

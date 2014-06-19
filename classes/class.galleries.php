<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class galleries extends default_class 
{
	public function getGalleries($limit = 50, $limitstart = 0){
		global $db;
		$gallery = new gallery();
		
		$r = $db->_getData($gallery->getDBTable(), array('id'), '', $limit, $limitstart);
		
		$galleries = array();
		foreach($r as $c){
			$galleries[]=new gallery($c['id']);
		}
		
		return $galleries;
	}
	
	public function getAllGalleriesXML($dom = '', $limit = 50, $limitstart = 0){
		if($dom == '')
			$dom = new DOMDocument('1.0', 'utf-8');
		$xmlGalleries = $dom->appendChild($dom->createElement('galleries'));
		
		$galleries = $this->getGalleries($limit, $limitstart);
		foreach($galleries as $gallery){
			$xmlGalleries->appendChild($gallery->get('head', $dom));
		}
		
		return $xmlGalleries;
	}
	
	

}

?>

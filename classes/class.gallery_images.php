<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class gallery_images extends default_class 
{
	public function getImagesForGallery($galleryId, $limit = 50, $limitstart = 0, $orderBy = 'ordering', $orderDir = 'ASC', $conditions = array()){
		global $db;
		
		
		$gallery_image = new gallery_image();
		$cond = "gallery_id=".$galleryId;
		
		if(is_array($conditions)){
			foreach($conditions as $field => $value){
				$cond.=" AND ".$field."=".$value."";
			}
		}
		$r = $db->_getData($gallery_image->getDBTable(), array('id'), $cond, $limit, $limitstart, $orderBy, $orderDir);
		
		$images = array();
		foreach($r as $c){
			$images[]=new gallery_image($c['id']);
		}
		
		return $images;
	}
	
	public function getAllImagesForGalleryXML($galleryId, $dom = '', $limit = 50, $limitstart = 0){
		if($dom == '')
			$dom = new DOMDocument('1.0', 'utf-8');
		$xmlImages = $dom->appendChild($dom->createElement('images'));
		
		$images = $this->getImagesForGallery($galleryId, $limit, $limitstart);
		foreach($images as $image){
			$xmlImages->appendChild($image->get('main', $dom));
		}
		
		return $xmlImages;
	}
	
	

}

?>

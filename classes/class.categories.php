<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class categories extends default_class 
{
	public function getContents($categoryId, $limit = 50, $limitstart = 0){
		global $db;
		$content = new content();
		
		$r = $db->_getData($content->getDBTable(), array('id'), "category_id=".$categoryId, $limit, $limitstart);
		
		$contents = array();
		foreach($r as $c){
			$contents[]=new content($c['id']);
		}
		
		return $contents;
	}
	
	public function getAllCategories($limit = 50, $limitstart = 0){
		global $db;
		$category = new category();
		
		$r = $db->_getData($category->getDBTable(), array('id'), '', $limit, $limitstart);
		
		$categories = array();
		foreach($r as $c){
			$categories[]=new category($c['id']);
		}
		
		return $categories;
	}
	
	public function getAllCategoriesXML($dom = '', $limit = 50, $limitstart = 0){
		if($dom == '')
			$dom = new DOMDocument('1.0', 'utf-8');
		$xmlCategories = $dom->appendChild($dom->createElement('categories'));
		
		$categories = $this->getAllCategories($limit, $limitstart);
		foreach($categories as $category){
			$xmlCategories->appendChild($category->get('head',$dom));
		}
		
		return $xmlCategories;
	}

}

?>

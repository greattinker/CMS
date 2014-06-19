<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class menus extends default_class 
{
	public function getAllMenus($depth = 0){
		global $db;
		$menu = new menu();
		
		$r = $db->_getData($menu->getDBTable(), array('DISTINCT(type)'), '');
		
		$menus = array();
		foreach($r as $c){
			$menus[]=new menu($c['type'], $depth);
		}
		
		return $menus;
	}
	
	public function getAllMenusXML($dom = '', $depth = 0){
		if($dom == '')
			$dom = new DOMDocument('1.0', 'utf-8');
		$xmlMenus = $dom->appendChild($dom->createElement('menus'));
		
		$menus = $this->getAllMenus($depth);
		foreach($menus as $menu){
			$xmlMenus->appendChild($menu->get('head', $dom));
		}
		
		return $xmlMenus;
	}
	
	public function getEntriesForMenu($type, $depth = 0, $limit = 0, $limitstart = 0, $orderBy = 'ordering', $orderDir = 'ASC', $conditions = array()){
		global $db;
		$menu = new menu();
		$cond = "type='".$type."' AND parent_id=0";
		foreach($conditions as $field => $value){
			$cond.=" AND ".$field."=".$value."";
		}
		$r = $db->_getData($menu->getDBTable(), array('id'), $cond, $limit, $limitstart, $orderBy, $orderDir);
		
		$entries = array();
		foreach($r as $c){
			$entries[]=new menu_entry($c['id'], (($depth ==-1) ? -1 : ($depth-1)));
		}
		
		return $entries;
	}
	
	public function getChildrenOfEntry($id, $depth = 0, $limit = 50, $limitstart = 0, $orderBy = 'ordering', $orderDir = 'ASC', $conditions = array())
	{
		global $db;
		$me = new menu_entry(0);
		$cond = "parent_id=".$id;
		foreach($conditions as $field => $value){
			$cond.=" AND ".$field."=".$value."";
		}
		$r = $db->_getData($me->getDBTable(), array('id'), $cond, $limit, $limitstart, $orderBy, $orderDir);
		
		$entries = array();
		foreach($r as $c){
			$entries[]=new menu_entry($c['id'], $depth);
		}
		
		return $entries;
	}
	
	public function getMenuEntryForLink($link = '', $menu = 'all'){
		global $db;
		$menu_entry = new menu_entry();
		$cond="link='".$link."'";
		if($menu != 'all') $cond.=" AND type='".$menu."'";
		
		$r = $db->_getData($menu_entry->getDBTable(), array('id'), $cond);
		if(count($r) == 0 || count($r)>1) return new menu_entry();
				
		return new menu_entry($r[0]['id']);
	}

}

?>

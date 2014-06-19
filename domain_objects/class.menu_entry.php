<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class menu_entry extends default_domain_object
{
	protected $name;
	protected $type;
	protected $state;
	protected $link;
	protected $color;
	protected $image;
	protected $parent;
	protected $componentid;
	protected $params;
	
	protected $children = array();
	
	
	public function __construct($id = 0, $depth = 0, $loadFromDB = true, $limit = 50, $limitstart = 0, $orderBy = 'ordering', $orderDir = 'ASC', $conditions = array()){
		$this->dbTable = TABLE_MENUS;
		parent::__construct($id, $loadFromDB);
		
		$menus = new menus();
		if($depth>0) $this->children = $menus->getChildrenOfEntry($id, $depth-1, $limit, $limitstart, $orderBy, $orderDir, $conditions);
		elseif($depth == -1) $this->children = $menus->getChildrenOfEntry($id, -1, $limit, $limitstart, $orderBy, $orderDir, $conditions);
	}
	
	public function getName(){ return $this->name;}
	public function getType(){ return $this->type;}
	public function getState(){ return $this->state;}
	public function getLink(){ return $this->link;}
	public function getColor(){ return $this->color;}
	public function getImage(){ return $this->image;}
	public function getParent(){ return $this->parent;}
	public function getComponentId(){ return $this->componentid;}
	public function getParams(){ return $this->params;}
	
	function getXML($dom = ''){		
		if($dom == '')
			$dom = new DOMDocument('1.0', 'utf-8');
		$xml = $dom->createElement('menu_entry');
		
		$xml->appendChild($dom->createElement('id', $this->id));
		$xml->appendChild($dom->createElement('name', $this->getName()));
		$xml->appendChild($dom->createElement('type', $this->getType()));
		$xml->appendChild($dom->createElement('state', $this->getState()));
		$xml->appendChild($dom->createElement('link', $this->getLink()));
		$xml->appendChild($dom->createElement('color', $this->getColor()));
		$xml->appendChild($dom->createElement('image', $this->getImage()));
		$xml->appendChild($dom->createElement('parent', $this->getParent()));
		$xml->appendChild($dom->createElement('componentid', $this->getComponentId()));
		$xml->appendChild($dom->createElement('params', $this->getParams()));
		
		
		$children = $xml->appendChild($dom->createElement('children'));
		
		foreach($this->children as $child){
			$children->appendChild($child->get('main', $dom));
		}
		
		return $xml;
	}
	
	function getXMLHead($dom = ''){		
		return $this->getXML($dom);
	}
}
?>

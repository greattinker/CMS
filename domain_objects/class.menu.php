<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class menu extends default_domain_object
{
	protected $type;
	protected $entries = array();
	
	
	public function __construct($type = '', $depth = 0, $loadFromDB = true, $limit = 50, $limitstart = 0, $orderBy = 'ordering', $orderDir = 'ASC', $conditions = array()){
		$this->dbTable = TABLE_MENUS;
		$this->type = $type;
		parent::__construct(0,false);
		
		$temp = new menus();
		if($depth!=0) $this->entries = $temp->getEntriesForMenu($this->type, $depth, $limit, $limitstart, $orderBy, $orderDir, $conditions);
		
	}
	
	public function getType(){ return $this->type;}
	
	function getXML($dom = ''){		
		if($dom == '')
			$dom = new DOMDocument('1.0', 'utf-8');
		$xml = $dom->createElement('menu');
		
		$xml->appendChild($dom->createElement('type', $this->getType()));
		$xmlEntries = $xml->appendChild($dom->createElement('menu_entries'));
		
		
		foreach($this->entries as $entry){
			$xmlEntries->appendChild($entry->getXMLHead($dom));
		}
		
		return $xml;
	}
	
	function getXMLHead($dom = ''){		
		if($dom == '')
			$dom = new DOMDocument('1.0', 'utf-8');
		$xml = $dom->createElement('menu_head');
		
		$xml->appendChild($dom->createElement('type', $this->getType()));
		
		return $xml;
	}
}
?>

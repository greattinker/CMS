<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class category extends default_domain_object
{
	protected $title;
	protected $name;
	protected $state;
	protected $created;
	protected $created_by;
	protected $modified;
	protected $modified_by;
	protected $image;
	protected $sectionid;
	protected $description;
	protected $ordering;
	protected $parent_id;
	
	
	public function __construct($id, $loadFromDB = true){
		$this->dbTable = 'mos_categories';
		parent::__construct($id, $loadFromDB);
	}
	
	public function getTitle(){ return $this->title;}
	public function getName(){ return $this->name;}
	public function getState(){ return $this->state;}
	public function getCreated(){ return $this->created;}
	public function getCreatedBy(){ return $this->created_by;}
	public function getModified(){ return $this->modified;}
	public function getModifiedBy(){ return $this->modified_by;}
	public function getSectionId(){ return $this->sectionid;}
	public function getDescription(){ return $this->description;}
	public function getOrdering(){ return $this->ordering;}
	public function getParentId(){ return $this->parent_id;}
	public function getImage(){ return $this->image;}
	
	function getXML($dom = '', $limit = 50, $limitstart = 0){		
		if($dom == '')
			$dom = new DOMDocument('1.0', 'utf-8');
		$xml = $dom->createElement('category');
		
		$xml->appendChild($dom->createElement('id', $this->Id));
		$xml->appendChild($dom->createElement('title', $this->getTitle()));
		$xml->appendChild($dom->createElement('name', $this->getName()));
		$xml->appendChild($dom->createElement('state', $this->getState()));
#		$xml->appendChild($dom->createElement('created', $this->getCreated()));
#		$xml->appendChild($dom->createElement('created_by', $this->getCreatedBy()));
#		$xml->appendChild($dom->createElement('modified', $this->getModified()));
#		$xml->appendChild($dom->createElement('modified_by', $this->getModifiedBy()));
		$xml->appendChild($dom->createElement('description', $this->getDescription()));
		$xml->appendChild($dom->createElement('ordering', $this->getOrdering()));
		$xml->appendChild($dom->createElement('sectionid', $this->getSectionId()));
		$xml->appendChild($dom->createElement('image', $this->getImage()));
		
		$xmlContents = $xml->appendChild($dom->createElement('contents'));
		
		$temp = new categories();
		$contents = $temp->getContents($this->id, $limit, $limitstart);
		foreach($contents as $content){
			$xmlContents->appendChild($content->getXMLHead($dom));
		}
		
		return $xml;
	}
	
	function getXMLHead($dom = ''){		
		if($dom == '')
			$dom = new DOMDocument('1.0', 'utf-8');
		$xml = $dom->createElement('category_head');
		
		$xml->appendChild($dom->createElement('id', $this->Id));
		$xml->appendChild($dom->createElement('title', $this->getTitle()));
		$xml->appendChild($dom->createElement('name', $this->getName()));
		$xml->appendChild($dom->createElement('state', $this->getState()));
#		$xml->appendChild($dom->createElement('created', $this->getCreated()));
#		$xml->appendChild($dom->createElement('created_by', $this->getCreatedBy()));
#		$xml->appendChild($dom->createElement('modified', $this->getModified()));
#		$xml->appendChild($dom->createElement('modified_by', $this->getModifiedBy()));
		$xml->appendChild($dom->createElement('description', $this->getDescription()));
		$xml->appendChild($dom->createElement('ordering', $this->getOrdering()));
		$xml->appendChild($dom->createElement('sectionid', $this->getSectionId()));
		$xml->appendChild($dom->createElement('image', $this->getImage()));
		
		return $xml;
	}
}
?>

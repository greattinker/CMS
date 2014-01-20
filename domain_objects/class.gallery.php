<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class gallery extends default_domain_object 
{
	protected $name;
	protected $parent;
	protected $description;
	protected $date;
	protected $state;
	protected $ordering;
	protected $hits;
	protected $params;
	protected $created;
	protected $created_by;
	protected $modified;
	protected $modified_by;
	
	public function __construct($id = 0, $loadFromDB = true){
		$this->dbTable = 'mos_rsgallery2_galleries';
		parent::__construct($id, $loadFromDB);
	}
	
	public function getName(){ return $this->name;}
	public function getDescription(){ return $this->description;}
	public function getParent(){ return $this->parent;}
	public function getState(){ return $this->state;}
	public function getOrdering(){ return $this->ordering;}
	public function getCreated(){ return $this->created;}
	public function getCreatedBy(){ return $this->created_by;}
	public function getModified(){ return $this->modified;}
	public function getModifiedBy(){ return $this->modified_by;}
	public function getDate(){ return $this->date;}
	public function getHits(){ return $this->hits;}
	public function getParams(){ return $this->params;}
	public function getCountImages(){ return 0; }
	
	function getXML($dom = '', $limit = 50, $limitstart = 0){		
		if($dom == '')
			$dom = new DOMDocument('1.0', 'utf-8');
		$xml = $dom->createElement('gallery');
		
		$xml->appendChild($dom->createElement('id', $this->id));
		$xml->appendChild($dom->createElement('name', $this->getName()));
		$xml->appendChild($dom->createElement('description', $this->getDescription()));
		$xml->appendChild($dom->createElement('state', $this->getState()));
		$xml->appendChild($dom->createElement('created', $this->getCreated()));
		$xml->appendChild($dom->createElement('created_by', $this->getCreatedBy()));
		$xml->appendChild($dom->createElement('modified', $this->getModified()));
		$xml->appendChild($dom->createElement('modified_by', $this->getModifiedBy()));
		$xml->appendChild($dom->createElement('date', $this->getDate()));
		$xml->appendChild($dom->createElement('hits', $this->getHits()));
		$xml->appendChild($dom->createElement('params', $this->getParams()));
		$xml->appendChild($dom->createElement('ordering', $this->getOrdering()));
		$xml->appendChild($dom->createElement('number_images', $this->getCountImages()));
		$xml->appendChild($dom->createElement('limit', $limit));
		$xml->appendChild($dom->createElement('limitstart', $limitstart));
		
		
		$gallery_images = new gallery_images();
		$xmlImages = $xml->appendChild($gallery_images->getAllImagesForGalleryXML($this->id, $dom, $limit, $limitstart));
		
		
		
		return $xml;
	}
	
	function getXMLHead($dom = ''){		
		if($dom == '')
			$dom = new DOMDocument('1.0', 'utf-8');
		$xml = $dom->createElement('gallery_head');
		
		$xml->appendChild($dom->createElement('id', $this->id));
		$xml->appendChild($dom->createElement('name', $this->getName()));
		$xml->appendChild($dom->createElement('description', $this->getDescription()));
		$xml->appendChild($dom->createElement('state', $this->getState()));
		$xml->appendChild($dom->createElement('created', $this->getCreated()));
		$xml->appendChild($dom->createElement('created_by', $this->getCreatedBy()));
		$xml->appendChild($dom->createElement('modified', $this->getModified()));
		$xml->appendChild($dom->createElement('modified_by', $this->getModifiedBy()));
		$xml->appendChild($dom->createElement('date', $this->getDate()));
		$xml->appendChild($dom->createElement('hits', $this->getHits()));
		$xml->appendChild($dom->createElement('params', $this->getParams()));
		$xml->appendChild($dom->createElement('ordering', $this->getOrdering()));
		$xml->appendChild($dom->createElement('number_images', $this->getCountImages()));
		
		
		return $xml;
	}
}
?>

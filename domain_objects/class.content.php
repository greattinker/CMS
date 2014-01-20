<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class content extends default_domain_object
{
	protected $title;
	protected $title_alias;
	protected $state;
	protected $created;
	protected $created_by;
	protected $modified;
	protected $modified_by;
	protected $catid;
	protected $sectionid;
	protected $introtext;
	protected $fulltext;
	
	
	public function __construct($id = 0, $loadFromDB = true){
		$this->dbTable = 'mos_content';
		parent::__construct($id, $loadFromDB);
	}
	
	public function getTitle(){ return $this->title;}
	public function getTitle_Alias(){ return $this->title_alias;}
	public function getState(){ return $this->state;}
	public function getCreated(){ return $this->created;}
	public function getCreatedBy(){ return $this->created_by;}
	public function getModified(){ return $this->modified;}
	public function getModifiedBy(){ return $this->modified_by;}
	public function getCategoryId(){ return $this->catid;}
	public function getSectionId(){ return $this->sectionid;}
	public function getIntroText(){ return $this->introtext;}
	public function getFullText(){ return $this->fulltext;}
	
	function getXML($dom = ''){		
		if($dom == '')
			$dom = new DOMDocument('1.0', 'utf-8');
		$xml = $dom->createElement('content');
		
		$xml->appendChild($dom->createElement('id', $this->id));
		$xml->appendChild($dom->createElement('title', $this->getTitle()));
		$xml->appendChild($dom->createElement('title_alias', $this->getTitle()));
		$xml->appendChild($dom->createElement('state', $this->getState()));
		$xml->appendChild($dom->createElement('created', $this->getCreated()));
		$xml->appendChild($dom->createElement('created_by', $this->getCreatedBy()));
		$xml->appendChild($dom->createElement('modified', $this->getModified()));
		$xml->appendChild($dom->createElement('modified_by', $this->getModifiedBy()));
		$xml->appendChild($dom->createElement('introtext', $this->getIntroText()));
		$xml->appendChild($dom->createElement('fulltext', $this->getFullText()));
		$xml->appendChild($dom->createElement('sectionid', $this->getSectionId()));
		$xml->appendChild($dom->createElement('categoryid', $this->getCategoryId()));
		
		
		return $xml;
	}
	
	function getXMLHead($dom = ''){		
		if($dom == '')
			$dom = new DOMDocument('1.0', 'utf-8');
		$xml = $dom->createElement('content_head');

		$xml->appendChild($dom->createElement('id', $this->id));
		$xml->appendChild($dom->createElement('title', $this->getTitle()));
		$xml->appendChild($dom->createElement('title_alias', $this->getTitle()));
		$xml->appendChild($dom->createElement('state', $this->getState()));
		$xml->appendChild($dom->createElement('created', $this->getCreated()));
		$xml->appendChild($dom->createElement('created_by', $this->getCreatedBy()));
		$xml->appendChild($dom->createElement('modified', $this->getModified()));
		$xml->appendChild($dom->createElement('modified_by', $this->getModifiedBy()));
		$xml->appendChild($dom->createElement('sectionid', $this->getSectionId()));
		$xml->appendChild($dom->createElement('categoryid', $this->getCategoryId()));
		
		return $xml;
	}
}
?>

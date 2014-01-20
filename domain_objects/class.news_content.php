<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class news_content extends default_domain_object 
{
	protected $title;
	protected $description;
	protected $type;
	protected $date;
	protected $state;
	protected $created;
	protected $created_by;
	protected $modified;
	protected $modified_by;
	
	public function __construct($id = 0, $loadFromDB = true){
		$this->dbTable = 'mos_news';
		parent::__construct($id, $loadFromDB);
	}
	
	public function getTitle(){ return $this->title;}
	public function getDescription(){ return $this->description;}
	public function getType(){ return $this->type;}
	public function getState(){ return $this->state;}
	public function getCreated(){ return $this->created;}
	public function getCreatedBy(){ return $this->created_by;}
	public function getModified(){ return $this->modified;}
	public function getModifiedBy(){ return $this->modified_by;}
	public function getDate(){ return $this->date;}
	
	function getXML($dom = ''){		
		if($dom == '')
			$dom = new DOMDocument('1.0', 'utf-8');
		$xml = $dom->createElement('news_content');
		
		$xml->appendChild($dom->createElement('id', $this->id));
		$xml->appendChild($dom->createElement('title', $this->getTitle()));
		$xml->appendChild($dom->createElement('description', $this->getDescription()));
		$xml->appendChild($dom->createElement('state', $this->getState()));
		$xml->appendChild($dom->createElement('created', $this->getCreated()));
		$xml->appendChild($dom->createElement('created_by', $this->getCreatedBy()));
		$xml->appendChild($dom->createElement('modified', $this->getModified()));
		$xml->appendChild($dom->createElement('modified_by', $this->getModifiedBy()));
		$xml->appendChild($dom->createElement('type', $this->getType()));
		
		
		return $xml;
	}
	
	function getXMLHead($dom = ''){		
		return $this->getXML($dom);
	}
}
?>

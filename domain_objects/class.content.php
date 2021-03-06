<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class content extends default_domain_object
{
	protected $title;
	protected $category_id;
	protected $access_id;
	protected $state;
	
	protected $created;
	protected $created_by;
	protected $modified;
	protected $modified_by;
	
	protected $params;
	protected $ordering;
	
	protected $commentsAllowed;
	protected $commentsAccess;
	
	protected $parts = array();
	
	
	
	public function __construct($id = 0, $loadFromDB = true, $limit = 50, $limitstart = 0, $orderBy = 'ordering', $orderDir = 'ASC', $conditions = array()){
		$this->dbTable = TABLE_CONTENTS;
		parent::__construct($id, $loadFromDB);
		
		$content_parts = new content_parts();
		$this->parts = $content_parts->getContentParts($this->id, $limit, $limitstart, $orderBy, $orderDir, $conditions);
	}
	
	public function getTitle(){ return $this->title;}
	public function getCategoryId(){ return $this->category_id;}
	public function getState(){ return $this->state;}
	
	public function getCreated(){ return $this->created;}
	public function getCreatedBy(){ return $this->created_by;}
	public function getModified(){ return $this->modified;}
	public function getModifiedBy(){ return $this->modified_by;}
	
	public function getParams(){ return $this->params;}
	public function getOrdering(){ return $this->ordering;}
	
	public function getCommentsAllowed(){ return $this->commentsAllowed;}
	public function getCommentsAccess(){ return $this->commentsAccess;}
	
	public function getXML($dom = ''){		
		if($dom == '')
			$dom = new DOMDocument('1.0', 'utf-8');
		$xml = $dom->createElement('content');
		
		$xml->appendChild($dom->createElement('id', $this->id));
		$xml->appendChild($dom->createElement('title', $this->getTitle()));	
		$xml->appendChild($dom->createElement('state', $this->getState()));
		$xml->appendChild($dom->createElement('created', $this->getCreated()));
		$xml->appendChild($dom->createElement('created_by', $this->getCreatedBy()));
		$xml->appendChild($dom->createElement('modified', $this->getModified()));
		$xml->appendChild($dom->createElement('modified_by', $this->getModifiedBy()));
		$xml->appendChild($dom->createElement('category_id', $this->getCategoryId()));
		
		$xmlParts = $xml->appendChild($dom->createElement('parts'));
		foreach($this->parts as $part){
			$xmlParts->appendChild($part->getXMLHead($dom));
		}
		
		return $xml;
	}
	
	public function getXMLHead($dom = ''){		
		if($dom == '')
			$dom = new DOMDocument('1.0', 'utf-8');
		$xml = $dom->createElement('content_head');

		$xml->appendChild($dom->createElement('id', $this->id));
		$xml->appendChild($dom->createElement('title', $this->getTitle()));
		$xml->appendChild($dom->createElement('state', $this->getState()));
		$xml->appendChild($dom->createElement('created', $this->getCreated()));
		$xml->appendChild($dom->createElement('created_by', $this->getCreatedBy()));
		$xml->appendChild($dom->createElement('modified', $this->getModified()));
		$xml->appendChild($dom->createElement('modified_by', $this->getModifiedBy()));
		$xml->appendChild($dom->createElement('category_id', $this->getCategoryId()));
		
		return $xml;
	}
	
	public function setParams($params = array()){
		if(count($params) == 0) return false;
		
		foreach($params as $key=>$value){
			switch($key){
				case 'title':
				case 'state':
				case 'category_id':
				case 'params':
				case 'ordering':
					if($value != '')
						$this->{$key} = $value;
					break;
				default:
					break;
			}
		}
	}
	
	public function updateParams($params = array()){
		if(count($params) == 0) return false;
		
		foreach($params as $key=>$value){
			switch($key){
				case 'title':
				case 'state':
				case 'category_id':
				case 'params':
				case 'ordering':
					if($value != '')
						$this->{$key} = $value;
					break;
				default:
					break;
			}
		}
	}
}
?>

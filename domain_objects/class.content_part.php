<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class content_part extends default_domain_object
{
	protected $title;
	protected $content_id;
	protected $access_id;
	
	protected $created;
	protected $created_by;
	protected $modified;
	protected $modified_by;
	
	protected $params;
	protected $ordering;
		
	protected $text;
	
	protected $access;
	
	
	public function __construct($id = 0, $loadFromDB = true){
		$this->dbTable = TABLE_CONTENT_PARTS;
		parent::__construct($id, $loadFromDB);
		$this->access = new access($this->access_id);
	}
	
	public function getTitle(){ return $this->title;}
	public function getContentId(){ return $this->content_id;}
#	public function getState(){ return $this->state;}
	
	public function getCreated(){ return $this->created;}
	public function getCreatedBy(){ return $this->created_by;}
	public function getModified(){ return $this->modified;}
	public function getModifiedBy(){ return $this->modified_by;}
	
	public function getParams(){ return $this->params;}
	public function getOrdering(){ return $this->ordering;}
	public function getText(){ return $this->text;}
	
	
	function getXML($dom = ''){		
		if($dom == '')
			$dom = new DOMDocument('1.0', 'utf-8');
		$xml = $dom->createElement('content_part');
		
		$xml->appendChild($dom->createElement('id', $this->id));
		$xml->appendChild($dom->createElement('title', $this->getTitle()));
	
#		$xml->appendChild($dom->createElement('state', $this->getState()));
		$xml->appendChild($dom->createElement('created', $this->getCreated()));
		$xml->appendChild($dom->createElement('created_by', $this->getCreatedBy()));
		$xml->appendChild($dom->createElement('modified', $this->getModified()));
		$xml->appendChild($dom->createElement('modified_by', $this->getModifiedBy()));
		$xml->appendChild($dom->createElement('content_id', $this->getContentId()));
		$xml->appendChild($dom->createElement('params', $this->getParams()));
		$xml->appendChild($dom->createElement('text', $this->getText()));
		
		
		return $xml;
	}
	
	function getXMLHead($dom = ''){		
		if($dom == '')
			$dom = new DOMDocument('1.0', 'utf-8');
		$xml = $dom->createElement('content_part_head');
		
		$xml->appendChild($dom->createElement('id', $this->id));
		$xml->appendChild($dom->createElement('title', $this->getTitle()));
	
#		$xml->appendChild($dom->createElement('state', $this->getState()));
		$xml->appendChild($dom->createElement('content_id', $this->getContentId()));
		
		
		return $xml;
	}
	
	public function setParams($params = array()){
		if(count($params) == 0) return false;
		
		foreach($params as $key=>$value){
			switch($key){
				case 'title':
				case 'state':
				case 'content_id':
				case 'params':
				case 'ordering':
				case 'text':
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
				case 'content_id':
				case 'params':
				case 'ordering':
				case 'text':
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

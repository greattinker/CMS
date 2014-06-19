<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class token extends default_domain_object
{
	protected $token;
	protected $user_id;
	protected $client_ip;
	protected $creation;
	
	
	public function __construct($id = '', $loadFromDB = true){
		$this->dbTable = TABLE_TOKENS;
		parent::__construct($id, $loadFromDB);
		
	}
	
	public function getToken(){ return $this->token;}
	public function getUserId(){ return $this->user_id;}
	public function getClientIp(){ return $this->client_ip;}
	public function getCreation(){ return $this->creation;}
	
	public function createToken(){
		$this->token = md5($this->user_id.$this->creation.$this->client_ip);
	}
	
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

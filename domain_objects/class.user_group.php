<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class user_group extends default_domain_object
{
	protected $name;
	protected $description;
	protected $access_id;
		
	
	public function __construct($id = 0, $loadFromDB = true){
		$this->dbTable = TABLE_USER_GROUPS;
		parent::__construct($id, $loadFromDB);
	}
	
	public function getName(){ return $this->name;}
	public function getDescription(){ return $this->description;}	
			
	function getXML($dom = ''){		
				
		return '';
	}
	
	function getXMLHead($dom = ''){		
		return '';
	}
}
?>

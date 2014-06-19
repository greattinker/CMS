<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class user_role extends default_domain_object
{
	protected $name = 'Public';
	protected $level = 1;
	
	
	
	public function __construct($id = 1, $loadFromDB = true){
		$this->dbTable = TABLE_USER_ROLES;
		parent::__construct($id, $loadFromDB);
	}
	
	public function getName(){ return $this->name;}
	public function getLevel(){ return $this->level;}	
			
	function getXML($dom = ''){		
				
		return '';
	}
	
	function getXMLHead($dom = ''){		
		return '';
	}
}
?>

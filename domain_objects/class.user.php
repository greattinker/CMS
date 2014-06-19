<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class user extends default_domain_object
{
	protected $username;
	protected $name;
	protected $password;
	protected $email;
	protected $role_id;
	
	
	protected $groups = array();
	protected $role;
	
	
	public function __construct($id = 0, $loadFromDB = true){
		$this->dbTable = TABLE_USERS;
		parent::__construct($id, $loadFromDB);
		if(isset($this->role_id)) $this->role = new user_role($this->role_id);
		else $this->role = new user_role();
	}
	
	public function getUsername(){ return $this->username;}
	public function getName(){ return $this->name;}
	public function getPassword(){ return $this->password;}	
	public function getEmail(){ return $this->email;}
	public function getRoleId(){ return $this->role_id;}
	
	public function getRole(){ return $this->role;}
	public function getGroups(){ return $this->groups;}
	public function getGroup($groupId){ 
		if(isset($this->groups[$groupId]))
			return $this->groups[$groupId];
		else 
			return false;
	}
			
	function getXML($dom = ''){		
				
		return '';
	}
	
	function getXMLHead($dom = ''){		
		return '';
	}
}
?>

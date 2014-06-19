<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class access extends default_domain_object
{
	protected $READ = 1;
	protected $WRITE = 100;
	protected $DELETE = 100;
	protected $EDIT = 100;
	protected $user_group = 0;
	
	
	public function __construct($id = 0, $loadFromDB = true){
		$this->dbTable = 'access';
		parent::__construct($id, $loadFromDB);
	}
	
	
	public function setAccess($READ, $WRITE, $DELETE, $EDIT, $user_group){
		
		$this->READ = $READ;
		$this->WRITE = $WRITE;
		$this->DELETE = $DELETE;
		$this->EDIT = $EDIT;
		$this->user_group = $user_group;
	}
	
	
	public function canRead($user){
		if(!$user instanceof user) return false;
		if($user->getRole()->getLevel() >= $this->READ && (($this->user_group != 0 && $user->getGroup($this->user_group) != false) || $this->user_group ==0)){
			return true;
		}
		return false;
	}
	
	public function canWrite($user){
		if(!$user instanceof user) return false;
		if($user->getRole()->getLevel() >= $this->WRITE && (($this->user_group != 0 && $user->getGroup($this->user_group) != false) || $this->user_group ==0)){
			return true;
		}
		return false;
	}
	public function canEdit($user){
		if(!$user instanceof user) return false;
		if($user->getRole()->getLevel() >= $this->EDIT && (($this->user_group != 0 && $user->getGroup($this->user_group) != false) || $this->user_group ==0)){
			return true;
		}
		return false;
	}
	public function canDelete($user){
		if(!$user instanceof user) return false;
		if($user->getRole()->getLevel() >= $this->DELETE && (($this->user_group != 0 && $user->getGroup($this->user_group) != false) || $this->user_group ==0)){
			return true;
		}
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

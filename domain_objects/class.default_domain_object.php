<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class default_domain_object
{
	protected $id;
	protected $dbTable;
	
	protected $access = null;
	protected $exists = false;
	
	public function __construct($id = 0, $loadFromDB = true){
		$this->id = $id;
		
		if($loadFromDB && $id > 0) $this->getData();
		
		if($this->access == null && (get_called_class() != 'access')){
			$this->access = new access(0, false);
			$this->access->setAccess(0,100,100,100,0);
		}
	}
	
	public function doesExist(){
		return $this->exists;
	}
	
	public function getDBTable(){
		return $this->dbTable;
	}
	public function getId(){ return $this->id; }
	
	public function get($type ='main', $dom = ''){
		global $user;
		
		if(!$this->access instanceof access) return;
		if(!$this->access->canRead($user)) return false;
		
		switch($type){
			case 'head':
				return $this->getXMLHead($dom);
				break;
			default:
				return $this->getXML($dom);
				break;
		}
	}
	public function getXML($dom = ''){}
	public function getXMLHead($dom = ''){}
	
	public function getData(){
		global $db;
		
		if($this->id <= 0) return;
		$data = $db->_getData($this->dbTable, array('*'), "`id`='".$this->id."'");
		if(count($data) == 0) return;
		$this->exists = true;
		foreach($data[0] as $property => $value){
			if(property_exists($this, $property)){
				$this->{$property} = $value;	
			}
		} 
		
		if(property_exists($this, 'access_id')){
			if($this->access_id>0){
				$this->access = new access($this->access_id);
			}
		}
	}
	
	public function setParams($params = array()){}
	
	public function set($params = array()){
		global $user;
	
		if(count($params) == 0) return false;		
		if(!$this->access->canWrite($user)) return false;
		
		$this->setParams($params);
		
		
		$success = $this->setData();
		
		$this->specialSettings();
		
		return $success;
	}
	
	protected function setData(){
		global $db,$user;
		$cols = $db->_getColumns($this->dbTable);
		
		$valuesArray = array();
		foreach($cols as $col){
			if(property_exists($this, $col)){
				$valuesArray[$col] = "'".$this->{$col}."'";
			}
			
		}
		
		if(count($valuesArray) == 0) return false;
		
		$this->specialActionsSet();
		
		$this->id = $db->_setData($this->dbTable, $valuesArray);		
		return true;
	}
	protected function specialActionsSet(){}
	
	
	public function updateParams($params = array()){}
	
	public function update($params = array()){
		global $user;
		
		if($this->id <= 0) return false;
		if(count($params) == 0) return false;
		if(!$this->access->canEdit($user)) return false;
		
		
		$this->updateParams($params);
		
		return $this->updateData();
	}
	
	protected function updateData(){
		global $db,$user;
		$cols = $db->_getColumns($this->dbTable);
		
		$valuesArray = array();
		foreach($cols as $col){
			if($col != 'id' && property_exists($this, $col)){
				$valuesArray[$col] = $this->{$col};
			}
			
		}
		
		if(count($valuesArray) == 0) return false;
		
		$this->specialActionsUpdate();
		
		$db->_updateData($this->dbTable, $valuesArray, "`id`='".$this->id."'");
		return true;
	}
	protected function specialActionsUpdate(){}
	
	
	public function delete(){
		global $user;
		
		if($this->id <= 0) return false;
		if(!$this->access->canDelete($user)) return false;
		
				
		return $this->deleteData();
	}
	
	protected function deleteData(){
		global $db,$user;
		
		if($this->id <= 0) return;
		
		$this->specialActionsDelete();
		
		$db->_deleteData($this->dbTable, "`id`='".$this->id."'");
		
	}
	
	protected function specialActionsDelete(){}
	
	public function countObjects(){
		global $db;
		
		$data = $db->_getData($this->dbTable, array('COUNT(id) as c'));
		if(isset($data[0])) return $data[0]['c'];
		return 0;
	}
	
	public function setFields($fieldArray = array()){
		if(count($fieldArray) == 0) return;
		
		foreach($fieldArray as $key => $value){
			if($key != 'id' && property_exists($this, $key)){
				
				$this->{$key}=$value;
			}
		}
	}
	
	
	protected function uploadFile($name,$destination){		
		if(move_uploaded_file($_FILES[$name]['tmp_name'], $destination)) {
			chmod($destination, 0777);
			return true;
		}
		else{
			//an error occured
			echo false;
		}
	}
	
}

?>

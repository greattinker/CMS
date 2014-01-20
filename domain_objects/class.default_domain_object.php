<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class default_domain_object
{
	protected $id;
	protected $dbTable;
	
	public function __construct($id = 0, $loadFromDB = true){
		$this->id = $id;
		
		if($loadFromDB && $id > 0) $this->getData();
	}
	
	public function getDBTable(){
		return $this->dbTable;
	}
	
	public function getData(){
		global $db;
		
		if($this->id <= 0) return;
		$data = $db->_getData($this->dbTable, array('*'), "`id`='".$this->id."'");
		
		foreach($data[0] as $property => $value){
			if(property_exists($this, $property)){
				$this->{$property} = $value;				
			}
		} 
	}
}

?>

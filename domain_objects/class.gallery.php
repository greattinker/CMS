<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class gallery extends default_domain_object 
{
	protected $name;
	protected $parent;
	protected $description;
	protected $date;
	protected $state;
	protected $ordering;
	protected $hits;
	protected $params;
	protected $created;
	protected $created_by;
	protected $modified;
	protected $modified_by;
	
	protected $images = array();
	
	public function __construct($id = 0, $loadFromDB = true, $limit = 50, $limitstart = 0, $orderBy = 'ordering', $orderDir = 'ASC', $conditions = array()){
		$this->dbTable = TABLE_GALLERIES;
		parent::__construct($id, $loadFromDB);
		
		$gallery_images = new gallery_images();
		$this->images = $gallery_images->getImagesForGallery($this->id, $limit, $limitstart, $orderBy, $orderDir, $conditions);
	}
	
	public function getName(){ return $this->name;}
	public function getDescription(){ return $this->description;}
	public function getParent(){ return $this->parent;}
	public function getState(){ return $this->state;}
	public function getOrdering(){ return $this->ordering;}
	public function getCreated(){ return $this->created;}
	public function getCreatedBy(){ return $this->created_by;}
	public function getModified(){ return $this->modified;}
	public function getModifiedBy(){ return $this->modified_by;}
	public function getDate(){ return $this->date;}
	public function getHits(){ return $this->hits;}
	public function getParams(){ return $this->params;}
	public function getCountImages(){ return 0; }
	
	function getXML($dom = ''){		
		if($dom == '')
			$dom = new DOMDocument('1.0', 'utf-8');
		$xml = $dom->createElement('gallery');
		
		$xml->appendChild($dom->createElement('id', $this->id));
		$xml->appendChild($dom->createElement('name', $this->getName()));
		$xml->appendChild($dom->createElement('description', $this->getDescription()));
		$xml->appendChild($dom->createElement('state', $this->getState()));
		$xml->appendChild($dom->createElement('created', $this->getCreated()));
		$xml->appendChild($dom->createElement('created_by', $this->getCreatedBy()));
		$xml->appendChild($dom->createElement('modified', $this->getModified()));
		$xml->appendChild($dom->createElement('modified_by', $this->getModifiedBy()));
		$xml->appendChild($dom->createElement('date', $this->getDate()));
		$xml->appendChild($dom->createElement('hits', $this->getHits()));
		$xml->appendChild($dom->createElement('params', $this->getParams()));
		$xml->appendChild($dom->createElement('ordering', $this->getOrdering()));
		$xml->appendChild($dom->createElement('number_images', $this->getCountImages()));
		
		
		$xmlImages = $xml->appendChild($dom->createElement('images'));
		foreach($this->images as $image){
			$xmlImages->appendChild($image->getXML($dom));
		}
		
		
		return $xml;
	}
	
	function getXMLHead($dom = ''){		
		if($dom == '')
			$dom = new DOMDocument('1.0', 'utf-8');
		$xml = $dom->createElement('gallery_head');
		
		$xml->appendChild($dom->createElement('id', $this->id));
		$xml->appendChild($dom->createElement('name', $this->getName()));
		$xml->appendChild($dom->createElement('description', $this->getDescription()));
		$xml->appendChild($dom->createElement('state', $this->getState()));
		$xml->appendChild($dom->createElement('created', $this->getCreated()));
		$xml->appendChild($dom->createElement('created_by', $this->getCreatedBy()));
		$xml->appendChild($dom->createElement('modified', $this->getModified()));
		$xml->appendChild($dom->createElement('modified_by', $this->getModifiedBy()));
		$xml->appendChild($dom->createElement('date', $this->getDate()));
		$xml->appendChild($dom->createElement('hits', $this->getHits()));
		$xml->appendChild($dom->createElement('params', $this->getParams()));
		$xml->appendChild($dom->createElement('ordering', $this->getOrdering()));
		$xml->appendChild($dom->createElement('number_images', $this->getCountImages()));
		
		
		return $xml;
	}
	
	public function specialSettings(){
		$dir = _GALLERIES_ROOT_PATH.$this->id;
		if (!is_dir($dir)) {
			mkdir($dir, 0777);
		}
	}
	
	public function importZipfile(){
#		$this->upload();
#		$zip=zip_open($this->directory.'/'.$_POST['gallery_id'].'/'.$_FILES['image']['name']);
#		if (is_resource($zip)) {
#		  while ($zip_entry = zip_read($zip)) {
#			$fp = fopen($this->directory.'/'.$_POST['gallery_id'].'/'.zip_entry_name($zip_entry), "w");
#			if (zip_entry_open($zip, $zip_entry, "r")) {
#			  $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
#			  fwrite($fp,"$buf");
#			  zip_entry_close($zip_entry);
#			  fclose($fp);
#			  chmod($this->directory.'/'.$_POST[gallery_id].'/'.zip_entry_name($zip_entry),0777);
#			image::thumbnail($this->directory.'/'.$_POST[gallery_id].'/'.zip_entry_name($zip_entry), $this->directory.'/'.$_POST["gallery_id"].'/th_'.zip_entry_name($zip_entry), $GLOBALS['config_gallery_thumbnail_width'], $GLOBALS['config_gallery_thumbnail_height'], True);
#			chmod($this->directory.'/'.$_POST["gallery_id"].'/th_'.zip_entry_name($zip_entry),0777);
#			   $ordering=mysql_fetch_array(mysql_query("SELECT * from mos_rsgallery2_files WHERE gallery_id='".$_POST['gallery_id']."' ORDER BY ordering DESC LIMIT 1"));
#		if($this->entrycheck(array(),array(array('gallery_id',$_POST['gallery_id']),array('name',zip_entry_name($zip_entry)),array('title',$this->getimagetitle(zip_entry_name($zip_entry))),array('date',date("Y-m-d H:i:s",time())),array('userid',$this->userid),array('approved','1'),array('published','1'),array('ordering',($ordering[ordering]+1)),array('descr','')))=='true'){
#		$data=new com_gallery_files_data($this->id,$this->itemid);
#		$data->getdata();
#		}
#			}
#		  }//while
#		  zip_close($zip);
#		  unlink($this->directory.'/'.$_POST['gallery_id'].'/'.$_FILES['image']['name']);
#		}//if

	}
	
	
	public function setParams($params = array()){
		if(count($params) == 0) return false;
		
		foreach($params as $key=>$value){
			switch($key){
				case 'name':
				case 'state':
				case 'parent':
				case 'date':
				case 'params':
				case 'ordering':
				case 'description':
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
				case 'name':
				case 'state':
				case 'parent':
				case 'date':
				case 'params':
				case 'ordering':
				case 'description':
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

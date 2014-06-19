<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class gallery_image extends default_domain_object 
{
	protected $title;
	protected $filename;
	protected $description;
	protected $gallery_id;
	protected $date;
	protected $state;
	protected $ordering;
	protected $hits;
	protected $rating;
	protected $votes;
	protected $params;
	protected $created;
	protected $created_by;
	protected $modified;
	protected $modified_by;
	
	public function __construct($id = 0, $loadFromDB = true){
		$this->dbTable = TABLE_GALLERY_IMAGES;
		parent::__construct($id, $loadFromDB);
	}
	
	public function getTitle(){ return $this->title;}
	public function getFilename(){ return $this->filename;}
	public function getDescription(){ return $this->description;}
	public function getGalleryId(){ return $this->gallery_id;}
	public function getState(){ return $this->state;}
	public function getOrdering(){ return $this->ordering;}
	public function getCreated(){ return $this->created;}
	public function getCreatedBy(){ return $this->created_by;}
	public function getModified(){ return $this->modified;}
	public function getModifiedBy(){ return $this->modified_by;}
	public function getDate(){ return $this->date;}
	public function getHits(){ return $this->hits;}
	public function getRating(){ return $this->rating;}
	public function getVotes(){ return $this->votes;}
	public function getParams(){ return $this->params;}
	
	function getXML($dom = ''){		
		if($dom == '')
			$dom = new DOMDocument('1.0', 'utf-8');
		$xml = $dom->createElement('image');
		
		$xml->appendChild($dom->createElement('id', $this->id));
		$xml->appendChild($dom->createElement('title', $this->getTitle()));
		$xml->appendChild($dom->createElement('filename', $this->getFilename()));
		$xml->appendChild($dom->createElement('gallery_id', $this->getGalleryId()));
		$xml->appendChild($dom->createElement('description', $this->getDescription()));
		$xml->appendChild($dom->createElement('state', $this->getState()));
		$xml->appendChild($dom->createElement('created', $this->getCreated()));
		$xml->appendChild($dom->createElement('created_by', $this->getCreatedBy()));
		$xml->appendChild($dom->createElement('modified', $this->getModified()));
		$xml->appendChild($dom->createElement('modified_by', $this->getModifiedBy()));
		$xml->appendChild($dom->createElement('date', $this->getDate()));
		$xml->appendChild($dom->createElement('hits', $this->getHits()));
		$xml->appendChild($dom->createElement('rating', $this->getRating()));
		$xml->appendChild($dom->createElement('votes', $this->getVotes()));
		$xml->appendChild($dom->createElement('params', $this->getParams()));
		$xml->appendChild($dom->createElement('ordering', $this->getOrdering()));
		$xml->appendChild($dom->createElement('thumbnail_path', '/images/galleries/'.$this->getGalleryId().'/th_'.$this->getFilename()));
		$xml->appendChild($dom->createElement('image_path', '/images/galleries/'.$this->getGalleryId().'/'.$this->getFilename()));
		
		return $xml;
	}
	
	function getXMLHead($dom = ''){		
		return $this->getXML($dom);
	}
	
	
	public function uploadImage(){
#		print_r($_FILE);
		$this->update(array('filename' => basename($_FILES['image']['name'])));
#		$this->filename = $_FILE['image']['name'];
		
		$target_path = _GALLERIES_ROOT_PATH.$this->gallery_id.'/';
		$target_path = $target_path . $this->getFilename();
		
		$this->uploadFile('image', $target_path);
#		chmod(_GALLERIES_ROOT_PATH.$this->getGalleryId().'/'.$this->getFilename(), 0777);
		
		image::thumbnail(	_GALLERIES_ROOT_PATH.$this->getGalleryId().'/'.$this->getFilename(),
							_GALLERIES_ROOT_PATH.$this->getGalleryId().'/th_'.$this->getFilename(), 
							_GALLERIES_THUMBNAIL_WIDTH, 
							_GALLERIES_THUMBNAIL_HEIGHT, 
							True);
						
		chmod(_GALLERIES_ROOT_PATH.$this->getGalleryId().'/th_'.$this->getFilename(), 0777);
	}
	
	
	
	public function setParams($params = array()){
		if(count($params) == 0) return false;
		
		foreach($params as $key=>$value){
			switch($key){
				case 'title':
				case 'state':
				case 'gallery_id':
				case 'params':
				case 'ordering':
				case 'description':
				case 'filename':
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
				case 'gallery_id':
				case 'params':
				case 'ordering':
				case 'description':
				case 'filename':
					if($value != '')
						$this->{$key} = $value;
					break;
				default:
					break;
			}
		}
	}
	
	protected function specialActionsDelete(){
		$target_path = _GALLERIES_ROOT_PATH.$this->gallery_id.'/';
		$target_path_th = $target_path .'th_'. $this->getFilename();
		$target_path_image = $target_path . $this->getFilename();
		if(file_exists($target_path_image)) unlink($target_path_image);
		if(file_exists($target_path_th)) unlink($target_path_th);
	}
}
?>

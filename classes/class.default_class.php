<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));
class default_class 
{

	function getXML(){}
	
	function getXMLHead(){}
	
	protected function uploadFile($destination){		
		if(move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
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

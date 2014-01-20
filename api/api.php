<?php

function api(){
	$request = explode('/', rtrim(substr($_SERVER['REQUEST_URI'], 1), '/'));
	#print_r($request);
	$accept = ($_SERVER['ACCEPT'] == '' || $_SERVER['ACCEPT'] == 'text/html') ? 'text/xml' : $_SERVER['ACCEPT'];


	switch ($_SERVER['REQUEST_METHOD']) {
		case 'PUT':
			rest_put($request, $accept);  
			break;
	  	case 'POST':
			rest_post($request, $accept);  
			break;
	  	case 'GET':
			rest_get($request, $accept);  
			break;
	  	case 'DELETE':
			rest_delete($request, $accept);  
			break;
	  	default:
			rest_error($request, $accept);  
			break;
	}
}

function rest_error($request, $accept){
	
}


function rest_get($request, $accept){
	if(!isset($request[1]) || $request[1]==''){
		header("HTTP/1.1 403 Forbidden");
		return;
	}
	
	switch($accept){
		case 'text/xml': {
			Header('Content-type: text/xml');
			$doc = new DomDocument('1.0');
			switch($request[1]){
				case 'contents':{
					switch(isset($request[2]) && !isset($request[3])){
						case true:
							$content=new content($request[2]);
							$doc->appendChild($content->getXML($doc));
							break;
						default:
							$contents=new contents();
							$limit = (isset($request[2])) ? $request[2] : 50;
							$limitstart = (isset($request[3])) ? $request[3] : 0;
							$doc->appendChild($contents->getAllContentsXML($doc, $limit, $limitstart));
							break;
					}
					break;
				}
	
				case 'galleries':{
					switch(isset($request[2]) && !isset($request[3])){
						case true:
							$gallery=new gallery($request[2]);
							$doc->appendChild($gallery->getXML($doc));
							break;
							
						default:
							switch($request[3]){
								case 'image':
									$image_id = (isset($request[4])) ? $request[4] : 0;
									$image = new gallery_image($image_id);
									$doc->appendChild($image->getXML($doc));
									break;
								
								default:							
									$galleries=new galleries();
									$limit = (isset($request[2])) ? $request[2] : 50;
									$limitstart = (isset($request[3])) ? $request[3] : 0;
									$doc->appendChild($galleries->getAllGalleriesXML($doc, $limit, $limitstart));
									break;
							}
							break;
					}
					break;
				}
			}
			$xml_string = $doc->saveXML() ;
			echo $xml_string;
			break;			
		}
		
		case 'json':
			break;
			
		case 'text/html':
			
			break;
		default: 
			header("HTTP/1.1 406 Not Acceptable");
			break;
	}
}


function rest_put($request, $accept){

}
function rest_post($request, $accept){

}
function rest_delete($request, $accept){

}

?>

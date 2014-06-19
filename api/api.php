<?php

function api($method = '', $requestURI = '', $requestParams = array(), $accept = ''){
	global $token,$user;
	$token = null;
	$user = null;
	if(isset($_SESSION['token'])){ 
		$tokens = new tokens();
		$token = $tokens->getToken($_SESSION['token']);
		if(is_object($token)) $user = new user($token->getUserId());
	}
	if($user == null){
		$user = new user();
	}
	
	if($method == '') $method = $_SERVER['REQUEST_METHOD'];
	
	if($requestURI == '') $requestURI = explode('/', rtrim(substr($_SERVER['REQUEST_URI'], 1), '/'));
#	$requestParams = array();
	#print_r($request);
	$accept = ($accept == '') ? $_SERVER['HTTP_ACCEPT'] : $accept;
	$accept = ($accept == '') ? 'text/xml' : $accept;
	
#	$accept = 'json';
#	$accept = 'text/html';
	
	$response = "";
	
	switch ($method) {
		case 'PUT':
			parse_str(file_get_contents('php://input'), $requestParams);
			$requestParams = escapeRequestParams($requestParams);
			$response = rest_put($requestURI, $requestParams, $accept);  
			break;
	  	case 'POST':
	  		$requestParams = $_POST;
			$requestParams = escapeRequestParams($requestParams);
			$response = rest_post($requestURI, $requestParams, $accept);  
			break;
	  	case 'GET':
	  		$requestParams = $_GET;
			$requestParams = escapeRequestParams($requestParams);
			$response = rest_get($requestURI, $requestParams, $accept);  
			break;
	  	case 'DELETE':
			parse_str(file_get_contents('php://input'), $requestParams);
			$requestParams = escapeRequestParams($requestParams);
			$response = rest_delete($requestURI, $requestParams, $accept);  
			break;
	  	default:
			$requestParams = escapeRequestParams($requestParams);
			$response = rest_error($requestURI, $requestParams, $accept);  
			break;
	}
	
	$response = replacetemplates($response);
	
	return $response;
}

function escapeRequestParams($params){
	global $db;
	
	$conn = $db->_getConn();

	foreach($params as $key => $value){
		if(is_array($value)){
			$params[$key]=escapeRequestParams($value);
		}else{
			$params[$key]=mysqli_real_escape_string($conn, htmlspecialchars($value));
		}
	}
	return $params;
}

function rest_error($requestURI, $requestParams, $accept){
	
}


function rest_get($requestURI, $requestParams, $accept){
	if(!isset($requestURI[1]) || $requestURI[1]==''){
		header("HTTP/1.1 403 Forbidden");
		return;
	}
	
	switch($accept)
	{
		case 'text/xml':
		{
			Header('Content-type: text/xml');
			$doc = new DomDocument('1.0');
			switch($requestURI[1])
			{
				case 'empty':
				{
					
					break;
				}
				
				
				case 'categories':
				{
					switch(isset($requestURI[2]) && !isset($requestURI[3]))
					{
						case true:
							if(!is_numeric($requestURI[2])) return;
							$category=new category($requestURI[2]);
							$doc->appendChild($category->get('main', $doc));
							break;
						default:
							$categories=new categories();
							$limit = (isset($requestParams['limit'])) ? $requestParams['limit'] : 50;
							$limitstart = (isset($requestParams['limitstart'])) ? $requestParams['limitstart'] : 0;
							$doc->appendChild($categories->getAllCategoriesXML($doc, $limit, $limitstart));
							break;
					}
					break;
				}
				
				
				case 'contents':
				{
					switch(isset($requestURI[2]) && (!isset($requestURI[3]) || strpos($requestURI[3],'?') === 0))
					{
						case true:
							if(!is_numeric($requestURI[2])) return;
							$limit = (isset($requestParams['limit'])) ? $requestParams['limit'] : 50;
							$limitstart = (isset($requestParams['limitstart'])) ? $requestParams['limitstart'] : 0;
							$orderBy = (isset($requestParams['orderBy'])) ? $requestParams['orderBy'] : 'ordering';
							$orderDir = (isset($requestParams['orderDir'])) ? $requestParams['orderDir'] : 'ASC';
							$conditions = $requestParams;
							unset($conditions['limit']);
							unset($conditions['limitstart']);
							unset($conditions['orderBy']);
							unset($conditions['orderDir']);
							$content=new content($requestURI[2], true, $limit, $limitstart, $orderBy, $orderDir, $conditions);
							$doc->appendChild($content->get('main', $doc));
							break;
						default:
							switch($requestURI[3])
							{
								case 'part':
									if(!is_numeric($requestURI[4])) return;
									$part_id = (isset($requestURI[4])) ? $requestURI[4] : 0;
									$content_part=new content_part($part_id);
									$doc->appendChild($content_part->get('main', $doc));
									break;
								
								default:
									$contents=new contents();
									$limit = (isset($requestParams['limit'])) ? $requestParams['limit'] : 50;
									$limitstart = (isset($requestParams['limitstart'])) ? $requestParams['limitstart'] : 0;
									$doc->appendChild($contents->getAllContentsXML($doc, $limit, $limitstart));
									break;
							}
							break;
					}
					break;
				}
				
	
				case 'galleries':
				{
					switch(isset($requestURI[2]) && (!isset($requestURI[3]) || strpos($requestURI[3],'?') === 0))
					{
						case true:
							if(!is_numeric($requestURI[2])) return;
							$limit = (isset($requestParams['limit'])) ? $requestParams['limit'] : 50;
							$limitstart = (isset($requestParams['limitstart'])) ? $requestParams['limitstart'] : 0;
							$orderBy = (isset($requestParams['orderBy'])) ? $requestParams['orderBy'] : 'ordering';
							$orderDir = (isset($requestParams['orderDir'])) ? $requestParams['orderDir'] : 'ASC';
							$conditions = $requestParams;
							unset($conditions['limit']);
							unset($conditions['limitstart']);
							unset($conditions['orderBy']);
							unset($conditions['orderDir']);
							$gallery=new gallery($requestURI[2], true, $limit, $limitstart, $orderBy, $orderDir, $conditions);
							$doc->appendChild($gallery->get('main', $doc));
							break;
							
						default:
							switch($requestURI[3])
							{
								case 'image':
									if(!is_numeric($requestURI[4])) return;
									$image_id = (isset($requestURI[4])) ? $requestURI[4] : 0;
									$image = new gallery_image($image_id);
									if($image_id == 0) $image->setFields(array('gallery_id' => $requestURI[2]));
									$doc->appendChild($image->getXML($doc));
									break;
								
								default:							
									$galleries=new galleries();
									$limit = (isset($requestParams['limit'])) ? $requestParams['limit'] : 50;
									$limitstart = (isset($requestParams['limitstart'])) ? $requestParams['limitstart'] : 0;
									$doc->appendChild($galleries->getAllGalleriesXML($doc, $limit, $limitstart));
									break;
							}
							break;
					}
					break;
				}
				
				case 'menus':
				{
					switch(isset($requestURI[2]))
					{
						case true:
							switch($requestURI[3]){
								case 'link':
									$link = (isset($requestParams['link'])) ? $requestParams['link'] : '';
									$menu = $requestURI[2];
									$menus = new menus();
									$doc->appendChild($menus->getMenuEntryForLink($link, $menu)->get('main', $doc));									
									break;
									
								case 'menu_entry':
									$limit = (isset($requestParams['limit']) && is_numeric($requestParams['limit'])) ? $requestParams['limit'] : 0;
									$limitstart = (isset($requestParams['limitstart']) && is_numeric($requestParams['limitstart'])) ? $requestParams['limitstart'] : 0;
									$orderBy = (isset($requestParams['orderBy'])) ? $requestParams['orderBy'] : 'ordering';
									$orderDir = (isset($requestParams['orderDir'])) ? $requestParams['orderDir'] : 'ASC';
									$conditions = $requestParams;
									unset($conditions['limit']);
									unset($conditions['limitstart']);
									unset($conditions['orderBy']);
									unset($conditions['orderDir']);
									unset($conditions['depth']);
									if(!is_numeric($requestURI[4])) return;
									$depth = (isset($requestParams['depth']) && is_numeric($requestParams['depth'])) ? $requestParams['depth'] : -1;
									$entry = new menu_entry($requestURI[4], $depth, true, $limit, $limitstart, $orderBy, $orderDir, $conditions);
									$doc->appendChild($entry->get('main', $doc));
									break;
									
								default:
									$limit = (isset($requestParams['limit']) && is_numeric($requestParams['limit'])) ? $requestParams['limit'] : 0;
									$limitstart = (isset($requestParams['limitstart']) && is_numeric($requestParams['limitstart'])) ? $requestParams['limitstart'] : 0;
									$orderBy = (isset($requestParams['orderBy'])) ? $requestParams['orderBy'] : 'ordering';
									$orderDir = (isset($requestParams['orderDir'])) ? $requestParams['orderDir'] : 'ASC';
									$conditions = $requestParams;
									unset($conditions['limit']);
									unset($conditions['limitstart']);
									unset($conditions['orderBy']);
									unset($conditions['orderDir']);
									unset($conditions['depth']);
									$depth = (isset($requestParams['depth']) && is_numeric($requestParams['depth'])) ? $requestParams['depth'] : -1;
									$menu=new menu($requestURI[2], $depth, true, $limit, $limitstart, $orderBy, $orderDir, $conditions);
									$doc->appendChild($menu->get('main', $doc));
									break;
							}
							break;
							
						default:
							$menus=new menus();
							$depth = (isset($requestParams['depth']) && is_numeric($requestParams['depth'])) ? $requestParams['depth'] : -1;
							$doc->appendChild($menus->getAllMenusXML($doc, $depth));
							break;
					}
					break;
				}
				
				
				case 'formmailer':
				{
					switch(isset($requestURI[2]) && !isset($requestURI[3]))
					{
						case true:
							if(!is_numeric($requestURI[2])) return;
							$formmailer=new formmailer($requestURI[2]);
							$doc->appendChild($formmailer->get('main', $doc));
							break;
						default:
							
							break;
					}
					break;
				}
				
				default:
					header("HTTP/1.1 404 Not Found");
					break;
				
			}
			$xml_string = $doc->saveXML() ;
			return $xml_string;
			break;			
		}
		
		case 'image/*':
		{
			Header('Content-type: image/*');
			switch($requestURI[1])
			{
				case 'galleries':
				{
					switch(isset($requestURI[2]) && isset($requestURI[3]) && $requestURI[3]=='image' && isset($requestURI[4]))
					{
						case true:
							if(!is_numeric($requestURI[4])) return;
							$image_id = (isset($requestURI[4])) ? $requestURI[4] : 0;
							$image = new gallery_image($image_id);
							$doc->appendChild($image->getXML($doc));
							break;
							
						default:
							
							break;
					}
					break;
				}			
				break;
				
				default: break;
			}
		}
		
		case 'json':{
			Header('Content-type: json');
			$xml = rest_get($requestURI, $requestParams, 'text/xml');
			$fileContents = str_replace(array("\n", "\r", "\t"), '', $xml);
			$fileContents = trim(str_replace('"', "'", $fileContents));
			$simpleXml = simplexml_load_string($fileContents);
			$json_string = json_encode($simpleXml);
			Header('Content-type: json');
			
			return $json_string;
			break;			
		}
			
		case 'text/html':
		{
			Header('Content-type: text/html');
			if(isset($requestURI[1]) && isset($requestURI[2]) && $requestURI[1] == 'template')
				$templatename = $requestURI[2];
			else return '';
			
			$xml = new DOMDocument('1.0');
				
			if(!isset($requestParams['apiRequests'])){
				$xmlRequest = $requestURI;
				unset($xmlRequest[1]);
				unset($xmlRequest[2]);
				$xmlRequest = array_values($xmlRequest);
			
				$xml->loadXML(rest_get($xmlRequest, $requestParams, 'text/xml'));
			}else{
				$xmlRequests = array();
				$xmlRequests = $requestParams['apiRequests'];
				$xml->loadXML('<combined></combined>');
				
				foreach($xmlRequests as $req){
					$parts = parse_url($req);
					parse_str($parts['query'], $query);
					$query = escapeRequestParams($query);
					$path = explode('/', rtrim(substr($parts['path'], 1), '/'));
					
					$xmlTemp = new DOMDocument;
					$xmlTemp->loadXML(rest_get($path, $query, 'text/xml'));
					$importNode = $xml->importNode($xmlTemp->documentElement,TRUE);
					$xml->documentElement->appendChild($importNode);
					unset($xmlTemp);
				}
			}
			
			$xsl = new DOMDocument;
			$xsl->load(_TEMPLATES.$templatename);

			// Prozessor instanziieren und konfigurieren
			$proc = new XSLTProcessor;
			$proc->importStyleSheet($xsl); // XSL Document importieren
			
			Header('Content-type: text/html');
			return $proc->transformToXML($xml);
			break;
		}
			
		default: 
			return rest_get($requestURI, $requestParams, 'text/xml');
#			header("HTTP/1.1 406 Not Acceptable");
			break;
	}
}



/*
/*	REST-POST	
*/

function rest_post($requestURI, $requestParams, $accept){
	if(!isset($requestURI[1]) || $requestURI[1]==''){
		header("HTTP/1.1 403 Forbidden");
		return;
	}
	
	switch($requestURI[1])
	{
		case 'login':{
			global $token;
			
			$username = $requestParams['username'];
			$password = md5($requestParams['password']);
			$client_ip = $_SERVER['REMOTE_ADDR'];
			
			$users = new users();
			$token = $users->login($username, $password, $client_ip);
			if($token != ''){
				$_SESSION['token'] = $token->getToken();
			}
			break;
		}
		
		case 'logout':{
			global $token;
			
			if(is_object($token)) $token->deleteData();
			unset($_SESSION['token']);
			session_destroy();
			break;
		}
		
		case 'categories':
		{
			
			
			$category = new category();
			
			if($category->set($requestParams)){
				header("HTTP/1.1 200 Ok");			
			}else{
				header("HTTP/1.1 403 Forbidden");				
			}
			break;
		}
		
		
		case 'contents':
		{
			if(!isset($requestURI[2])){
			
			}elseif($requestURI[2] == 'parts'){
				
			}
			break;
		}

		case 'galleries':
		{
			if(!isset($requestURI[2])){
				$gallery = new gallery();
			
				if($gallery->set($requestParams)){
					header("HTTP/1.1 200 Ok");			
				}else{
					header("HTTP/1.1 403 Forbidden");				
				}	
			}elseif($requestURI[2]=='images'){
				if(!isset($requestURI[4])){
					$gallery_image = new gallery_image();
			
					if($gallery_image->set($requestParams)){
						header("HTTP/1.1 200 Ok");			
						echo $gallery_image->getId();
					}else{
						header("HTTP/1.1 403 Forbidden");				
					}	
				}elseif($requestURI[4]=='fileupload'){
					$id = (isset($requestURI[3]) && is_numeric($requestURI[3])) ? $requestURI[3] : 0;
					$gallery_image = new gallery_image($id);
					$gallery_image->uploadImage();
					
				}			
			}
			
			
			
			break;
		}
		
		case 'menus':
		{
			
			break;
		}
		
		case 'formmailer':
		{
			if(!isset($requestURI[2])){
			
			}elseif($requestURI[2] == 'sendmail'){
				$id = (isset($requestParams['id'])) ? $requestParams['id'] : 0;
				$mailParams = $requestParams;
				unset($mailParams['id']);
				$formmailer = new formmailer($id);
				switch($formmailer->sendMail($mailParams)){
					case true:
						header("HTTP/1.1 200 Ok");									
						break;
					default:
						header("HTTP/1.1 403 Forbidden");			
						break;
				}
			}			
			break;
		}
		
		default:
			header("HTTP/1.1 404 Not Found");
			break;
		
	}
}


function rest_put($requestURI, $requestParams, $accept){
	if(!isset($requestURI[1]) || $requestURI[1]==''){
		header("HTTP/1.1 403 Forbidden");
		return;
	}
	
	switch($requestURI[1])
	{
				
		case 'categories':
		{
			$id = (isset($requestParams['id']) && is_numeric($requestParams['id'])) ? $requestParams['id'] : 0;
		
			if($id == 0) return;
			$category = new category($id);
			if($content->update($requestParams)){
				header("HTTP/1.1 200 Ok");
			}
			else{
				header("HTTP/1.1 403 Forbidden");				
			}
			break;
		}
		
		
		case 'contents':
		{	
			$id = (isset($requestParams['id']) && is_numeric($requestParams['id'])) ? $requestParams['id'] : 0;
			
			if(!isset($requestURI[2])){
				$content = new content($id);
				if($content->doesExist() === true){
					$content->update($requestParams);
				}else{
					$content->set($requestParams);					
				}			
			}elseif($requestURI[2]=='parts'){
				$content_part = new content_part($id);
				if($content_part->doesExist() === true){
					$content_part->update($requestParams);
				}else{
					$content_part->set($requestParams);					
				}				
			}
			break;
		}

		case 'galleries':
		{
			$id = (isset($requestParams['id']) && is_numeric($requestParams['id'])) ? $requestParams['id'] : 0;
			
			if(!isset($requestURI[2])){
				$gallery = new gallery($id);
				if($gallery->doesExist() === true){
					$gallery->update($requestParams);
				}else{
					$gallery->set($requestParams);					
				}			
			}elseif($requestURI[2]=='images'){
				$gallery_image = new gallery_image($id);
				if($gallery_image->doesExist() === true){
					$gallery_image->update($requestParams);
				}else{
					$gallery_image->set($requestParams);					
				}				
			}
			break;
		}
		
		case 'menus':
		{
			
			break;
		}
		
		default:
			header("HTTP/1.1 404 Not Found");
			break;
		
	}
}

function rest_delete($requestURI, $requestParams, $accept){

}

?>

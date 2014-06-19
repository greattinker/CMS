<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class users extends default_class 
{
	public function getAllUsers($limit = 50, $limitstart = 0){
		global $db;
		$luser = new user();
		
		$r = $db->_getData($luser->getDBTable(), array('id'), '', $limit, $limitstart);
		
		$users = array();
		foreach($r as $c){
			$users[]=new user($c['id']);
		}
		
		return $users;
	}
	
	public function login($username, $password, $client_ip){
		$luser = new user();
		$users = $this->getAllUsers($luser->countObjects(), 0);
		
		foreach($users as $u){
			if($u->getUsername() == $username && $u->getPassword() == $password){
				$token = new token();
				$token->setFields(array('user_id'=>$u->getId(), 'client_ip'=>$client_ip, 'creation'=>time()));
				$token->createToken();
				$token->setData();
				return $token;
			}
		}
	}
}

?>

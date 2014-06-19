<?
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class formmailer extends default_domain_object
{
	protected $name;
	protected $email_addresses;
	
	protected $email_addresses_array = array();
	
	
	public function __construct($id = 0, $loadFromDB = true){
		$this->dbTable = TABLE_FORMMAILERS;
		parent::__construct($id, $loadFromDB);
		$this->access = new access($this->access_id);
		if($this->doesExist()) $this->email_addresses_array = json_decode($this->email_addresses);
	}
	
	public function getName(){ return $this->name;}
	public function getEmailAddresses(){ return $this->email_addresses;}
	
	
	function getXML($dom = ''){		
		if($dom == '')
			$dom = new DOMDocument('1.0', 'utf-8');
		$xml = $dom->createElement('formmailer');
		
		$xml->appendChild($dom->createElement('id', $this->id));
		$xml->appendChild($dom->createElement('name', $this->getName()));
		$xmlEmailAddresses = $xml->appendChild($dom->createElement('email_addresses'));
		foreach($this->email_addresses_array as $addr){
			$xmlEmailAddresses->appendChild($dom->createElement('address', $addr));
		}
		
		
		return $xml;
	}
	
	function getXMLHead($dom = ''){		
		return $this->getXML($dom);
	}
	
	public function setParams($params = array()){
		if(count($params) == 0) return false;
		
		foreach($params as $key=>$value){
			switch($key){
				case 'name':
				case 'email_addresses':
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
				case 'email_addresses':
					if($value != '')
						$this->{$key} = $value;
					break;
				default:
					break;
			}
		}
	}
	
	public function sendmail($mailParams = array()){
		if(!$this->doesExist()) return false;
		
		if(!isset($mailParams['email']) || !filter_var($mailParams['email'], FILTER_VALIDATE_EMAIL)) return false;
		$senderMailAddress = $mailParams['email'];
#		unset($mailParams['email']);
		
		$this->sendConfirmationMail($senderMailAddress, $this->getMailBody('confirm_title', $mailParams), $this->getMailBody('confirm', $mailParams));
		
		$this->sendReceiversMail($this->getMailBody('receivers_title', $mailParams), $this->getMailBody('receivers', $mailParams));
	
		return true;
	}
	
	private function getMailBody($type, $mailParams){
		$doc = new DOMDocument('1.0');
		$xml = $doc->createElement('formmailer_params');
		
		foreach($mailParams as $key => $value){
			$xml->appendChild($doc->createElement($key, $value));
		}
		$xml->appendChild($doc->createElement('current_datetime', date("Y-M-D H:i:s", time())));
		$doc->appendChild($xml);
				
		$xsl = new DOMDocument;
		$xsl->load(_TEMPLATES.'formmailer_'.$this->id.'_'.$type.'.xslt');

		// Prozessor instanziieren und konfigurieren
		$proc = new XSLTProcessor;
		$proc->importStyleSheet($xsl); // XSL Document importieren
		
		return utf8_decode($proc->transformToXML($doc));
	}
	
	private function sendConfirmationMail($senderMailAddress, $title, $mail_body){
		$header  = 'MIME-Version: 1.0' . "\r\n";
		$header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$header .= "From: ".SERVER_MAIL_ADDRESS."\r\n";
				
		mail($senderMailAddress, $title, $mail_body, $header);
	}
	
	
	private function sendReceiversMail($title, $mail_body){
		$header  = 'MIME-Version: 1.0' . "\r\n";
		$header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$header .= "From: ".SERVER_MAIL_ADDRESS."\r\n";
		
		
		$combined_address = '';
		foreach($this->email_addresses_array as $address){
			if($combined_address != '') $combined_address .= ', ';
			$combined_address .= $address;
		}
		mail($combined_address, $title, $mail_body, $header);
	}
}
?>

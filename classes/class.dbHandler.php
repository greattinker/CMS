<?php
/**
 * Klasse dbHandler
 */
defined('_VALID_CALL') || (header("HTTP/1.1 403 Forbidden") & die('403.14 - Directory listing denied.'));

class dbHandler {

	/**
	 * Datenbank-Query zur Ausgabe/Kontrolle
	 * @var string
	 */
	public $_query;
	
	/**
	 * Fehler-String bei falschem Sql-Command-Query
	 * @var string
	 */
	public $_lastError;

	/**
	 * Angabe Datenbank-Host
	 * @var string
	 */
	private $_DbHost;

	/**
	 * Datenbank-Benutzer
	 * @var string
	 */
	private $_DbUser;

	/**
	 * Passwort zum Datenbank-Benutzer
	 * @var string
	 */
	private $_DbPassword;

	/**
	 * Name der verwendeten Datenbank
	 * @var string
	 */
	private $_DbDatabase;

	/**
	 * Standard- Konstruktor
	 * @param string $DbHost Datenbank-Host
	 * @param string $DbUser Datenbank-Benutzer
	 * @param string $DbPassword Datenbank-Passwort zum Benutzer
	 * @param string $DbDatabase Name der Datenbank
	 */
	function __construct($DbHost, $DbUser, $DbPassword, $DbDatabase) {
		$this->_DbHost = $DbHost;
		$this->_DbUser = $DbUser;
		$this->_DbPassword = $DbPassword;
		$this->_DbDatabase = $DbDatabase;
	} //__construct()
	
	
	function _getConn(){
		$mysqli = mysqli_connect($this->_DbHost, $this->_DbUser, $this->_DbPassword, $this->_DbDatabase);
		return $mysqli;
	}
	/**
	 * Daten updaten
	 * @param string $TableName Tabellen-Name
	 * @param Array $sql_values neue Werte (array('name'=>value, ...))
	 * @param string $requirement Bedingungen ('spalte = 'sowas' OR spalte = 'sonstwas')
	 * @return bool $updated
	 */
	function _updateData($TableName, $sql_values, $requirement = '') {
		$mysqli = new mysqli($this->_DbHost, $this->_DbUser, $this->_DbPassword, $this->_DbDatabase);
		$mysqli->set_charset("utf8");
		
		$colvalues = '';
		
		// foreach ($sql_values as $colvalue) {$colvalues.= $colvalue.",";}
		//_d($sql_values);
		foreach ($sql_values as $key => $colvalue) {$colvalues.=  $key."='".$colvalue."',";}
		if ($colvalues) {
			$colvalues = substr($colvalues, 0, mb_strlen($colvalues)-1);
		} else {return;}
		$SqlQuery = "UPDATE " .$TableName . " SET " .$colvalues;
		if ($requirement != '') $SqlQuery .= " WHERE " .$requirement;
		
		$updated = $mysqli->query($SqlQuery);

		$this->_query = $SqlQuery;
		
		$mysqli->close();
		
		return $updated;
	
	} //_updateData()
	
	/**
	 * Werte speichern
	 * @param string $tablename Tabellen-Name
	 * @param Array $sql_array ('spalte'=>Wert, ...)
	 * @return int $newId Id des neuen Datensatzes oder 0
	 */
	function _setData($tablename, $sql_array) {
		$mysqli = new mysqli($this->_DbHost, $this->_DbUser, $this->_DbPassword, $this->_DbDatabase);
		$mysqli->set_charset("utf8");
		
		$query = "INSERT INTO " .$tablename ." (";
		
		foreach($sql_array as $key=>$value) {$query .= $key .",";}
		$query = substr($query, 0, mb_strlen($query)-1). ") VALUES (";
		foreach($sql_array as $key=>$value) {$query .= $value .",";}
		$query = substr($query, 0, mb_strlen($query)-1). ")";

		$inserted = $mysqli->query($query);
		
		$this->_query = $query;
		
		$this->_lastError = $mysqli->error;
		if (!empty($this->_lastError)) trigger_error($this->_lastError, E_USER_WARNING);

		$newId = ($inserted) ? $mysqli->insert_id : 0;
		$mysqli->close();

		return $newId;
	
	} //_setData()
	
	/**
	 * Funktion zur Abfrage von Daten
	 * @param string $tablename Tabellen-Name
	 * @param Array $sql_colname_array ('spalte'=>wert, ...)
	 * @param string $requirement Bedingungen (spalte='1' OR spalte=2)
	 * @param bool $single einzelnes Ergenis (nicht verwendet)
	 * @return Array $arrReturn
	 */
	function _getColumns($tablename) {

		$mysqli = new mysqli($this->_DbHost, $this->_DbUser, $this->_DbPassword, $this->_DbDatabase);
		$mysqli->set_charset("utf8");
		
		$arrReturn = array();

		
		$query = "SHOW COLUMNS FROM " .$tablename;
		
		$this->_query = $query;
		
		$result1 = $mysqli->query($query);
		$this->_lastError = $mysqli->error;
		if (!empty($this->_lastError)) trigger_error($this->_lastError, E_USER_WARNING);
		if ($result1) {
			while ($row = $result1->fetch_assoc()) {
				$arrReturn[] = $row['Field'];
			}			
		}
		return $arrReturn;
	}
	
		
	/**
	 * Funktion zur Abfrage von Daten
	 * @param string $tablename Tabellen-Name
	 * @param Array $sql_colname_array ('spalte'=>wert, ...)
	 * @param string $requirement Bedingungen (spalte='1' OR spalte=2)
	 * @param bool $single einzelnes Ergenis (nicht verwendet)
	 * @return Array $arrReturn
	 */
	function _getData($tablename, $sql_colname_array, $requirement='', $limit = 0, $limitstart = 0, $orderBy = '', $orderDir = '') {

		$mysqli = new mysqli($this->_DbHost, $this->_DbUser, $this->_DbPassword, $this->_DbDatabase);
		$mysqli->set_charset("utf8");
		
		$arrReturn = array();

		$colnames = '';
		foreach ($sql_colname_array as $colname) {$colnames.= $colname.", ";}
		if($requirement!='') {$requirement = " WHERE ".$requirement. "";}
		$limits = '';
		if($limit > 0){$limits = 'LIMIT '.$limitstart.', '.$limit.' ';}
		$order = '';
		if($orderBy != '' && ($orderDir == 'ASC' || $orderDir == 'DESC')){ $order = ' ORDER BY '.$orderBy.' '.$orderDir.' ';}
		$query = "SELECT " .substr($colnames, 0, -2) ." FROM " .$tablename ." " .$requirement ." ".$order.$limits;
		
#		echo $query;
		$this->_query = $query;
		
		$result1 = $mysqli->query($query);
		$this->_lastError = $mysqli->error;
#		if (!empty($this->_lastError)) trigger_error($this->_lastError, E_USER_WARNING);
		if ($result1) {
			while ($row = $result1->fetch_assoc()) {
				$arrReturn[] = $row;
			}			
		}
		
		// if ($result = $mysqli->query($query)) {
			
			// $this->_lastError = $mysqli->error;
			// if (!empty($this->_lastError)) trigger_error($this->_lastError, E_USER_WARNING);

			// while ($row = $result->fetch_assoc()) {
				// $arrReturn[] = $row;
			// }
		// }

		$mysqli->close();
		return $arrReturn;
		// $this->db_handler = mysql_connect($this->_DbHost, $this->_DbUser, $this->_DbPassword);
		// $this->db_found = mysql_select_db($this->_DbDatabase, $this->db_handler);
		// mysql_set_charset('utf8', $this->db_handler);

		// $colnames = '';
		// foreach ($sql_colname_array as $colname) {$colnames.= $colname.", ";}
		// if($requirement!='') {$requirement = " WHERE ".$requirement. "";}
		// $query = "SELECT " .substr($colnames, 0, -2) ." FROM " .$tablename ." " .$requirement ." ";
		
		// $this->_query = $query;
		
		// //echo ('<br />------<br />' .$query .'<br />------<br />');
		
		// $arrReturn = array();
		// if ($result = mysql_query($query)) {
			// while ($row = mysql_fetch_assoc($result)) {
				// array_push($arrReturn, $row);
			// }
		// }
		// //mysql_close($this->db_handler);

		// return $arrReturn;
	
	} //_getData()
	
	/**
	 * Datensaetze loeschen
	 * @param string $tablename Tabellen-Name
	 * @param string $requirement Bedingungen (spalte='1' OR spalte=2)
	 * @return bool $delete Ergebnis des Vorganges
	 */
	function _deleteData ($tablename,  $requirement) {
		// $this->db_handler = mysql_connect($this->_DbHost, $this->_DbUser, $this->_DbPassword);
		// $this->db_found = mysql_select_db($this->_DbDatabase, $this->db_handler);
		
		$mysqli = new mysqli($this->_DbHost, $this->_DbUser, $this->_DbPassword, $this->_DbDatabase);
		$mysqli->set_charset("utf8");

		if ($requirement == '')  return '0';
		
		$query = "DELETE FROM " .$tablename." WHERE ".$requirement;
		$this->_query = $query;
		
		$delete = $mysqli->query($query);
		$this->_lastError = $mysqli->error;
		
		$mysqli->close();
		
		return $delete;
	
	} //_deleteData()
	
	/**
	 * Standard-Destruktor
	 */
	function __destruct() {}
}
?>

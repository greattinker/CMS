<?php
/**
 * Tools zur Ausgabe von Debug-Informationen in Dateien oder in den Html-Code
 *
 * @author		Michael Bauer michael.bauer@sell2you.de
 * @category	functions
 * @package		Sell2You
 * @subpackage	functions_debug
 * @version		1.00
 * @copyright	Copyright (c) 2012 Sell2You GmbH.
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 * Kurzform fuer die Funktion __debug()
 * @param mixed
 * @param string Ueberschrift der Ausgabe
 * @param bool true|false Ausgabe der Aufruf-Historie, Standard = true
 * @see __debug()
 */
function _d ($data, $text='', $withBackTrace = true) {
	__debug($data, $text, $withBackTrace);
} //_d()

/**
 * Ausgabe der Debug-Informationen zu verschiedenen Server-Variablen
 * @param mixed $data auszugebene Daten
 * @param string Ueberschrift der Ausgabe
 * @param bool true|false Ausgabe der Aufruf-Historie, Standard = true
 */
function __debug ($data, $text='', $withBackTrace = true) {
	
	$backtrace = debug_backtrace();
	
	echo '<div class=\'___debug\'>';
	echo '<pre><code>';
	if (isset($backtrace[1]) && $withBackTrace) {
		print_r($backtrace[1]['file'] .', Zeile: ' .$backtrace[1]['line'] .':<br/>');
	}
	if (!empty($text)) echo '<b>' .$text .':</b><br />';
	print_r($data);
	echo '</code></pre>';
	echo '<HR SIZE="1" />';
	echo '</div>';

} //__debug()

/**
 * Ausgabe von Informationen in eine Log-Datei, das Speichern der Datei erfolgt fest in den Pfad /WEB_ROOT/logs/DATUM_debug_logging.txt
 * @param mixed $LogObj Objekt, was geloggt werden soll, Umwandlungen auf der Basis der Funtionen "var2str()" und "arr2str_recursive()"
 * @param string $title Speichern des Eintrages mit diesem Titel (Ausgabe am Beginn und am Ende)
 * @param bool $withBackTrace Ausgabe erfolgt mit Aufruf-Historie (debug_backtrace())
 * @todo weitere Verfeinerungen fuer die Ausgabe und die Umwandlung einbauen
 */
function _dLog($LogObj, $title = '', $withBackTrace = true) {
	
	$backtrace = debug_backtrace();
	
	$datei = fopen(_SRV_WEBROOT ."logs/" .date('Y-m-d') ."_debug_logging.txt", "a+");
	$argList = func_get_args();

	if (!empty($title)) fwrite($datei, date('Y-m-d h:m:s') .': --- Anfang ' .$title . ' ---' .PHP_EOL);

	if (!is_scalar($LogObj)) {

		if (is_object($LogObj)) {
			fwrite($datei, date('Y-m-d h:m:s') .": " .'	Objekt: ' .get_class($LogObj) .PHP_EOL);				
			fwrite($datei, arr2str_recursive(get_object_vars($LogObj)));
		} else {
			fwrite($datei, date('Y-m-d h:m:s') .": " .'	Typ: ' .gettype($LogObj) .PHP_EOL);
			if (is_array($LogObj)) {
				foreach ($LogObj as $key => $value)
					fwrite($datei, date('Y-m-d h:m:s') .":	" .$key .':' .$value .PHP_EOL);
			}
		}
		
	} else {
		if (is_bool($LogObj)) {
			if ($LogObj === true) {
				fwrite($datei, date('Y-m-d h:m:s') .": true" .PHP_EOL);
			} else {fwrite($datei, date('Y-m-d h:m:s') .": false" .PHP_EOL);}
		} else {
			fwrite($datei, date('Y-m-d h:m:s') .": " .$LogObj .PHP_EOL);
		}
	}
	
	if (isset($backtrace[1]) && $withBackTrace) {
		fwrite($datei, date('Y-m-d h:m:s') .": in " .$backtrace[1]['file'] .', Zeile: ' .$backtrace[1]['line'] .PHP_EOL);
	}
	
	if (!empty($title)) {
		fwrite($datei, date('Y-m-d h:m:s') .': ---- Ende ' .$title . ' ----' .PHP_EOL .PHP_EOL);
	} else {
		fwrite($datei, PHP_EOL);
	}

	fclose($datei);
	
} //_dLog()

/**
 * Ermittelt, um welchen Variablen-Typ es sich handelt
 * @param mixed Variable
 * @return string Variablen-Typ-Name
 * @see arr2str_recursive()
 */
function var2str($var) {
	if (is_bool($var)) {
		return ($var) ? 'true' : 'false';
	} else {
		return (is_scalar($var)) ? strval($var) : gettype($var);
	}
} //var2str

/**
 * uebergebenes Array wird zur Ausgabe rekursiv durchlaufen und als String formatiert
 * @param $arr Array
 * @param int Ebenentiefe?
 * @return string gebildeter String aus dem Array
 */
function arr2str_recursive($arr, $depth=0) {
	$str = '';
	foreach ($arr as $key => $val) {
		if (is_array($val)) {
			$str .= arr2str_recursive($val, $depth + 1);
		} else {
			$var = var2str($val);
			$str .= str_repeat('	', $depth + 1) .$key . ': ' .$var .PHP_EOL;
		}
	}
	
	return $str;
	
} //arr2str_recursive()

/**
 * Ausgabe der Debug-Informationen zu verschiedenen Server-Variablen
 * @see __debug
 */
function _show_debug_values() {
	echo('<style>.___debug {font-size:12px;}</style>');
	echo('<div style="clear: both; display: block; height: 20px; font-weight: bold"></div>');
	echo('<div style="padding-top: 10px; border-top: 3px solid red; clear: both; display: block; height: 20px; font-weight: bold">+++ DEBUG +++</div>');
	__debug($_GET, 'GET', false);
	__debug($_POST, 'POST', false);
	if (isset($_SESSION)) __debug($_SESSION, 'SESSION', false);
	if (isset($_COOKIE)) __debug($_COOKIE, 'COOKIE', false);
	if (isset($_FILES)) __debug($_FILES, 'FILES', false);
	
	__debug ($_SERVER, 'SERVER', false);
	//phpinfo();

} //_show_debug_values()
?>

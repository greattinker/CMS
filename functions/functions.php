<?php
function replacetemplates($content){
	$content=templateschreibenofstring($content,array('domain'),array('/'));
	return $content;
}

function settemplates($content){
	$content=templateschreibenofnormalstring($content,array($GLOBALS['config_domain']),array('{domain}'));
	return $content;
}

function templateschreibenofstring($file,$repla,$new) {
  $tpl = $file;

    for($i=0; $i<count($repla); $i++) {
     $tpl = preg_replace("#{".$repla[$i]."}#", $new[$i], $tpl);
  }
  return $tpl;
}
function templateschreibenofnormalstring($file,$repla,$new) {
  $tpl = $file;

    for($i=0; $i<count($repla); $i++) {
     $tpl = preg_replace("#".$repla[$i]."#", $new[$i], $tpl);
  }
  return $tpl;
}
?>

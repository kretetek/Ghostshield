<?php
echo '<form><input type="text" name="search" '.(isset($_REQUEST["search"])?' value="'.addslashes($_REQUEST["search"]).'"':'').' /><input type="submit" /></form>';

include 'spellcorrector.php';

if(isset($_REQUEST["search"])){
	$oldstr = $_REQUEST["search"];
	$str = explode(" ",$oldstr);
	for($i=0;$i<count($str);$i++){
		$str[$i] = SpellCorrector::correct($str[$i]);
	}
	$newstr = implode(" ",$str);
	if ($oldstr != $newstr) {
		echo '<p>Showing results for <em>'.$newstr.'</em><br /><span>Search instead for <em>'.$oldstr.'</em></span></p>';
	}else echo '<p>Showing results for <em>'.$oldstr.'</em>';
}


?>
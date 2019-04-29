<?php

require_once("lib/page.class.php");
require_once("lib/dictionary.class.php");
$page = new page();

if (isset($_REQUEST["word"]) && isset($_REQUEST["ajax"])) {

	$word = new definition($_REQUEST["word"]);
	if (isset($_REQUEST["mobile"])) {
		echo strip_tags(str_replace("</h3>", "\n\n", str_replace("</p>", "\n\n", $word->definition())));
	} else echo $word->definition();
	
	exit();

}

/*echo '<html>';

//var_dump($word->response);

$word = new definition("thixotropic");
echo '<hr>Define "thixotropic" '.$word->definition();

$word = new definition("silane");
echo '<hr>Define "silane" '.$word->definition();

$word = new definition("oleophobic");
echo '<hr>Define "oleophobic" '.$word->definition();

$word = new definition("ingress");
echo '<hr>Define "ingress" '.$word->definition();
*/
<?php

header('Accept-Ranges: bytes');

require_once("lib/page.class.php");
$page = new page();
$page->addScript("clicklink.js");
$page->addScript("product-finder.min.js");
$page->addScript("index.js");
$page->addCSS("index.css");



echo $page->header('Water, Oil &amp; Salt Repellent Concrete Sealer', 'index');

$squares = $page->getCacheData("squares");

var_dump($squares);

$new_squares = array();

foreach($squares as $key => $square) {

	$keys = array(
		"left-edge" => "top-left",
		"left-top" => "top-center",
		"left-bottom" => "bottom-right",
		"right-edge" => "bottom-center",
		"right-top" => "top-right",
		"right-bottom" => "middle-right",
		"center" => "middle-center",
		"why-medium" => "bottom-left",
		"why-large" => "middle-left"
	);
	
	$new_squares[$keys[$key]] = $square;
	$new_squares[$keys[$key]]["position"] = $keys[$key];
	
	

}

db::_insert("cache", ["site" => "pub", "var" => "squares", "data" => serialize($new_squares), 'type' => 'cms_content']);

echo $page->footer(); //require_once("inc/footer.php");

?>
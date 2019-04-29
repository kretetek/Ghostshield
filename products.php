<?php

/*

JSON-FORMATTED DATA
PRODUCTS & VARIATIONS

*/

require_once("lib/page.class.php");
$page = new page();

$products = [];
$i = 0;

foreach($page->data("products")->attr as $product) {

	$model = $product["model"];

	$image = "";
	if (file_exists('assets/img/bottle/'.$model.'.png')) {
		$image = '//assets.'.$page->host.'/img/bottle/'.$model.'.png';
	} elseif (file_exists('assets/img/bucket/'.$model.'.png')) {
		$image = '//assets.'.$page->host.'/img/bucket/'.$model.'.png';
	} else $image = $product["image"]["file"];

	$products[$model] = [
		"name" => $product["name"],
		"image" => $image,
		"available" => $product["availability"]
	];
	
	$i = 0;
	if (isset($product["variations"])) {

		foreach($product["variations"] as $variant) {

			$variation = $page->data("productVariations")->attr[$variant];
			$products[$model]["variations"][$i]["name"] = $variation["name"];
			$products[$model]["variations"][$i]["model"] = $product["model"];
			$products[$model]["variations"][$i]["type"] = $variation["type"];
			$products[$model]["variations"][$i]["msrp"] = $variation["msrp"];
			$products[$model]["variations"][$i]["map"] = $variation["map"];
			$products[$model]["variations"][$i]["sku"] = $variation["sku"];
			$products[$model]["variations"][$i]["coverage"] = $variation["coverage"];
			$products[$model]["variations"][$i]["available"] = $product["availability"];
			$products[$model]["variations"][$i]["finish"] = $product["attributes"]["finish"];
			$products[$model]["variations"][$i]["distributorDiscountPercentage"] = $variation["distributorDiscountPercentage"];
			$products[$model]["variations"][$i]["availableToDistributors"] = $variation["availableToDistributors"];

			$i++;

		}

	}

}

exit(json_encode($products));

?>
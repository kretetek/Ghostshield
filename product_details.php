<?php

/*echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
	<title>GhostShield</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
	<script src="https://api.tiles.mapbox.com/mapbox-gl-js/v0.14.3/mapbox-gl.js"></script>
	<script src="assets/ghostshield.js"></script>
	<meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />
	<link href="assets/ghostshield.css" rel="stylesheet" />
	<link href="https://api.tiles.mapbox.com/mapbox-gl-js/v0.14.3/mapbox-gl.css" rel="stylesheet" />
<body>
	<div class="errors">
		<pre>';

error_reporting(E_ALL);
ini_set('display_errors',"On");
ini_set('display_startup_errors',"On");*/

require_once "../lib/sys/syntax.php";

require_once "lib/parsedown.php";
$parsedown = new Parsedown();


// This function grabs the definition of a word in XML format.
/*function grab_xml_definition ($word, $ref, $key) {
		$uri = "http://www.dictionaryapi.com/api/v1/references/" . urlencode($ref) . "/xml/" . 
					urlencode($word) . "?key=" . urlencode($key);
                		return file_get_contents($uri);
};

$xdef = grab_xml_definition("test", "collegiate", "1e472b1e-b5ce-46dd-9906-5db2bac32e5e");*/


function getContentfulData($dataType, $offset = 0, $limit = 100, $data = null) {
	$ch = curl_init();
//"https://cdn.contentful.com/spaces/muyees5bu8n0/content_types/productComparisonDetails?access_token=access_token"
	curl_setopt($ch, CURLOPT_URL, "https://api.contentful.com/spaces/muyees5bu8n0/content_types/".$dataType);
	#curl_setopt($ch, CURLOPT_URL, "https://api.contentful.com/spaces/muyees5bu8n0/content_types");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);

	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer eb52623d3747deb29c7d5bb3f3925e345d653b2b6657fe6c9b011cbb7c0cb814"));

	$response = curl_exec($ch);
	curl_close($ch);

	$json = json_decode($response);
	$data[] = $json;
	
	if ($json->total > $limit+$offset) {
		return getContentfulData($offset+$limit, $limit, $data);
	} else {
		return $data;
	}
	
}



if (isset($_REQUEST["model"])) {
	$model = $_REQUEST["model"];
} else $model = "9500";


$comparisonDetails = getContentfulData("productComparisonDetails");

syntax::better_var_dump($comparisonDetails);

/*
$assets = array();
$squares = array();
$productsById = array();

$count = 0;
foreach ($data_sets as $data) {
$count++;
	foreach($data->includes->Asset as $key => $item) {
		if ($item->sys->type == "Asset") {
			$assets[$item->sys->id] = [
				"updated" 	=> $item->sys->updatedAt,
				"title"		=> $item->fields->title->{"en-US"},
				"file"		=> $item->fields->file->{'en-US'}->url,
				"size"		=> $item->fields->file->{'en-US'}->details->size,
				"filename"	=> $item->fields->file->{'en-US'}->fileName,
				"type"		=> $item->fields->file->{'en-US'}->contentType,
			];
			if (isset($item->fields->file->{'en-US'}->details->image->width)) {
				$assets[$item->sys->id]["width"] = $item->fields->file->{'en-US'}->details->image->width;
				$assets[$item->sys->id]["height"] = $item->fields->file->{'en-US'}->details->image->height;
			}
		}
	}

	foreach($data->items as $key => $item) {
		if ($item->sys->contentType->sys->id == "squares" && isset($item->fields->position->{"en-US"})) {
			$squares[$item->fields->position->{"en-US"}] = [
				"updated"	=> $item->sys->updatedAt,
				"title"		=> $item->fields->title->{"en-US"},
				"blurb"		=> isset($item->fields->blurb->{"en-US"}) ? $parsedown->text($item->fields->blurb->{"en-US"}) : '',
				"position"	=> $item->fields->position->{"en-US"}
			];
			if (isset($item->fields->squareImage))
				$squares[$item->fields->position->{"en-US"}]["image"] = $assets[$item->fields->squareImage->{'en-US'}->sys->id];
			if (isset($item->fields->media))
				$squares[$item->fields->position->{"en-US"}]["media"] = $assets[$item->fields->media->{'en-US'}->sys->id];
			if (isset($item->fields->mediaLink))
				$squares[$item->fields->position->{"en-US"}]["mediaLink"] = $item->fields->mediaLink->{'en-US'};
			if (isset($item->fields->productAssociations)) {
				foreach ($item->fields->productAssociations->{"en-US"} as $product) {
					$squares[$item->fields->position->{"en-US"}]["products"][] = $product->sys->id;
				}
			} else $squares[$item->fields->position->{"en-US"}]["products"] = [];
		}
	
		if ($item->sys->contentType->sys->id == "page" && isset($item->fields->url->{"en-US"})) {
			$pages[$item->fields->url->{"en-US"}] = [
				"updated"	=> $item->sys->updatedAt,
				"id"		=> $item->sys->id,
				"title"		=> $item->fields->title->{"en-US"},
				"url"		=> $item->fields->url->{"en-US"},
				"content"	=> $parsedown->text($item->fields->content->{"en-US"})
			];
			if(isset($item->fields->headerImage->{'en-US'}->sys->id))
			$pages[$item->fields->url->{"en-US"}]["image"] = $assets[$item->fields->headerImage->{'en-US'}->sys->id];
		}
	
		if ($item->sys->contentType->sys->id == "graph") {
			$graphs[$item->sys->id] = [
				"updated" 	=> $item->sys->updatedAt,
				"id" 		=> $item->sys->id,
				"title" 	=> $item->fields->title->{"en-US"},
				"subtitle" 	=> $item->fields->subtitle->{"en-US"},
				"items" 	=> [
					"a" => ["name" => $item->fields->itemA->{"en-US"}, "value" => $item->fields->itemAValue->{"en-US"}],
					"b" => ["name" => $item->fields->itemB->{"en-US"}, "value" => $item->fields->itemBValue->{"en-US"}],
					"c" => ["name" => $item->fields->itemC->{"en-US"}, "value" => $item->fields->itemCValue->{"en-US"}],
					"d" => ["name" => $item->fields->itemD->{"en-US"}, "value" => $item->fields->itemDValue->{"en-US"}],
					"e" => ["name" => $item->fields->itemE->{"en-US"}, "value" => $item->fields->itemEValue->{"en-US"}],
				]
			];
		}
	
		if ($item->sys->contentType->sys->id == "productComparisonDetails") {
			$comparisonDetails[$item->sys->id] = [
				"updated" 	=> $item->sys->updatedAt,
				"id" 		=> $item->sys->id
			];
			foreach ($item->fields as $name => $field) {
				if ($name != "product") $comparisonDetails[$item->sys->id][$name] = $field->{"en-US"};
			}
			$comparisonDetailsByProduct[$item->fields->product->{'en-US'}->sys->id] = $comparisonDetails[$item->sys->id];
		}
	
		if ($item->sys->contentType->sys->id == "additionalInformation") {
			$additional[$item->sys->id] = [
				"updated"	=> $item->sys->updatedAt,
				"id"		=> $item->sys->id,
				"title"		=> $item->fields->title->{"en-US"},
				"label"		=> $item->fields->tabLabel->{"en-US"},
				"content"	=> $parsedown->text($item->fields->content->{"en-US"})
			];
		}
	
		if ($item->sys->contentType->sys->id == "colors") {
			$colors[$item->sys->id] = [
				"updated"	=> $item->sys->updatedAt,
				"id"		=> $item->sys->id,
				"name"		=> $item->fields->name->{"en-US"},
				"hex"		=> $item->fields->colorHex->{"en-US"}
			];
		}
	
		if ($item->sys->contentType->sys->id == "distributorsOnline") {
			$onlineDistributor[$item->sys->id] = [
				"updated"	=> $item->sys->updatedAt,
				"id"		=> $item->sys->id,
				"name"		=> $item->fields->name->{"en-US"},
				"logo"		=> $assets[$item->fields->logo->{"en-US"}->sys->id],
				"url"		=> $item->fields->url->{"en-US"},
				"search"	=> $item->fields->searchUrl->{"en-US"}
			];
		}
	
		if ($item->sys->contentType->sys->id == "product") {
			$m = $item->fields->modelNumber->{"en-US"};
			$products[$m] = [
				"updated"			=> $item->sys->updatedAt,
				"id"				=> $item->sys->id,
				"name"				=> $item->fields->name->{"en-US"},
				"model"				=> $m
			];
			if (isset($item->fields->photos)) foreach($item->fields->photos->{"en-US"} as $photo) {
				$products[$m]["squares"][] = $assets[$photo->sys->id];
			}
			if ($m == $model) {
				$products[$m] = array_merge($products[$m], [
					"overview"			=> $parsedown->text($item->fields->shortDescription->{"en-US"}),
					"attributes"		=> [
						"coverage"		=> $item->fields->squareFootagePerGallon->{"en-US"},
						"coats"			=> $item->fields->coats->{"en-US"},
						"voc"			=> $item->fields->vocContent->{"en-US"},
						"finish"		=> $item->fields->finish->{"en-US"},
						"color"			=> $item->fields->color->{"en-US"},
						"concentrated"	=> $item->fields->concentrated->{"en-US"},
						"warranty"		=> isset($item->fields->warranty->{"en-US"}) ? $item->fields->warranty->{"en-US"} : ''
						],
					"msrp"				=> isset($item->fields->msrp->{"en-US"}) ? $item->fields->msrp->{"en-US"} : '',
					"applications_yes"	=> $parsedown->text($item->fields->applications->{"en-US"}),
					"applications_no"	=> isset($item->fields->applicationsIncompatible) ? $parsedown->text($item->fields->applicationsIncompatible->{"en-US"}) : '',
					"description"		=> $parsedown->text($item->fields->description->{"en-US"}),
					"benefits"			=> [
						"provides"		=> $parsedown->text($item->fields->benefitsProvides->{"en-US"}),
						"prevents"		=> $parsedown->text($item->fields->benefitsPrevents->{"en-US"}),
						"reduces"		=> $parsedown->text($item->fields->benefitsReduces->{"en-US"})
						],
					"technical"			=> $parsedown->text($item->fields->technicalInformation->{"en-US"}),
					"graph"				=> isset($item->fields->graph) ? $item->fields->graph->{"en-US"}->sys->id : ''
				]);
				if (isset($item->fields->dataSheets)) foreach($item->fields->dataSheets->{"en-US"} as $dataSheet) {
					$products[$m]["dataSheets"][] = $assets[$dataSheet->sys->id];
				}
				echo '<p>'.$m.':'.$count;var_dump($products[$m]["squares"]);
				if (isset($item->fields->additionalInformation)) foreach($item->fields->additionalInformation->{"en-US"} as $addon) {
					$products[$m]["additional"][] = $addon->sys->id;
				}
				if (isset($item->fields->distributors)) foreach($item->fields->distributors->{"en-US"} as $distributor) {
					$products[$m]["distributors"][] = $distributor->sys->id;
				}
				if (isset($item->fields->comparisons)) foreach($item->fields->comparisons->{"en-US"} as $comparison) {
					$products[$m]["comparisons"][] = $comparison->sys->id;
				}
				if(isset($item->fields->headerImage->{'en-US'}->sys->id))
				$products[$m]["image"] = $assets[$item->fields->headerImage->{'en-US'}->sys->id];
			}
			
			$productsById[$item->sys->id] = $products[$m];
		}
	
		if ($item->sys->contentType->sys->id == "page" && isset($item->fields->url->{"en-US"})) {
			$pages[$item->fields->url->{"en-US"}] = [
				"updated"	=> $item->sys->updatedAt,
				"id"		=> $item->sys->id,
				"title"		=> $item->fields->title->{"en-US"},
				"url"		=> $item->fields->url->{"en-US"},
				"content"	=> $parsedown->text($item->fields->content->{"en-US"})
			];
			if(isset($item->fields->headerImage->{'en-US'}->sys->id))
			$pages[$item->fields->url->{"en-US"}]["image"] = $assets[$item->fields->headerImage->{'en-US'}->sys->id];
		}
	
		if ($item->sys->contentType->sys->id == "distributors" && isset($item->fields->location->{"en-US"})) {
			$distributors[$item->sys->id] = [
				"updated"	=> $item->sys->updatedAt,
				"name"		=> $item->fields->name->{"en-US"},
				"url"		=> $item->fields->url->{"en-US"},
				"blurb"		=> preg_replace("/\r|\n/","",$parsedown->text($item->fields->information->{"en-US"})),
				"logo"		=> $assets[$item->fields->logo->{'en-US'}->sys->id],
				"longitude"	=> $item->fields->location->{"en-US"}->lon,
				"latitude"	=> $item->fields->location->{"en-US"}->lat,
				"address"	=> $item->fields->address->{"en-US"},
				"phone"		=> $item->fields->phoneNumber->{"en-US"}
			];
		}
	}
}

krsort($products);

$location = pathinfo($_SERVER["REQUEST_URI"]);

if ($location['filename']=="ghostshield") $location['filename'] = "index";

echo '
		</pre>
	</div>
	<div id="container" class="'.$location['filename'].'">
	
	<header>
		<h1><a href="/ghostshield/">GhostShield &reg; <small>Nanotechnology Advances in Concrete Protection&trade;</small></a></h1>
	</header>
	
	<div id="main_nav_container">
		<nav class="main_nav" id="main_nav">
			<div id="main_nav_open"><span>Navigation</span></div>
			<ul>
				<li'.( $location['filename'] == "index" ? ' class="active"' : '' ).'><a href="index.php"><span>Home</span></a></li>
				<li'.( $location['filename'] == "product" ? ' class="active"' : '' ).'>
					<a href="product.php"><span>Sealers &amp; Coatings</span></a>
					<ul class="submenu">';

foreach ($products as $product) {
	echo '
						<li><a href="/ghostshield/product.php?model='.$product["model"].'">'.$product["name"].'</a></li>';
}

echo '
					</ul>
				</li>
				<li><a href="#"><span>Commercial Line</a></li>
				<li><a href="#"><span>Pro Line</a></li>
				<li'.( $location['filename'] == "interior" ? ' class="active"' : '' ).'><a href="interior.php"><span>Distributors</span></a></li>
				<li'.( $location['filename'] == "interior" ? ' class="active"' : '' ).'><a href="index.php"><span>Ask An Expert</span></a></li>
			</ul>
		</nav>
	</div>';
*/
?>
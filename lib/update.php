<?php

/*

UPDATE CONTENTFUL DATA...

*/

error_reporting(E_ERROR);
ini_set('display_errors',"On");
ini_set('display_startup_errors',"On");


require_once "../../lib/sys/syntax.php";
require_once("db.php");

require_once("parsedown.php");
$parsedown = new Parsedown();

/*
require_once("../ciconia/vendor/autoload.php");

use Ciconia\Ciconia;
use Ciconia\Extension\Gfm;

$ciconia = new Ciconia();
$ciconia->addExtension(new Gfm\FencedCodeBlockExtension());
$ciconia->addExtension(new Gfm\TaskListExtension());
$ciconia->addExtension(new Gfm\InlineStyleExtension());
$ciconia->addExtension(new Gfm\WhiteSpaceExtension());
$ciconia->addExtension(new Gfm\TableExtension());
$ciconia->addExtension(new Gfm\UrlAutoLinkExtension());
*/

function parse_markdown($str) {
	global $parsedown;
	// return $ciconia->render($str);
	return $parsedown->text($str);
}


// This function grabs the definition of a word in XML format.
/*function grab_xml_definition ($word, $ref, $key) {
		$uri = "http://www.dictionaryapi.com/api/v1/references/" . urlencode($ref) . "/xml/" . 
					urlencode($word) . "?key=" . urlencode($key);
                		return file_get_contents($uri);
};

$xdef = grab_xml_definition("test", "collegiate", "1e472b1e-b5ce-46dd-9906-5db2bac32e5e");*/


function getContentfulData($offset = 0, $limit = 100, $data = null) {
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "https://api.contentful.com/spaces/muyees5bu8n0/public/entries?order=sys.createdAt&skip=".$offset);
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


$data_sets = getContentfulData();

#syntax::better_var_dump($data_sets);

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
				"blurb"		=> isset($item->fields->blurb->{"en-US"}) ? parse_markdown($item->fields->blurb->{"en-US"}) : '',
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
				"content"	=> parse_markdown($item->fields->content->{"en-US"})
			];
			if(isset($item->fields->headerImage->{'en-US'}->sys->id))
			$pages[$item->fields->url->{"en-US"}]["image"] = $assets[$item->fields->headerImage->{'en-US'}->sys->id];
		}
	/* duplicate?
		if ($item->sys->contentType->sys->id == "page" && isset($item->fields->url->{"en-US"})) {
			$pages[$item->fields->url->{"en-US"}] = [
				"updated"	=> $item->sys->updatedAt,
				"id"		=> $item->sys->id,
				"title"		=> $item->fields->title->{"en-US"},
				"url"		=> $item->fields->url->{"en-US"},
				"content"	=> parse_markdown($item->fields->content->{"en-US"})
			];
			if(isset($item->fields->headerImage->{'en-US'}->sys->id))
			$pages[$item->fields->url->{"en-US"}]["image"] = $assets[$item->fields->headerImage->{'en-US'}->sys->id];
		}*/
	
		if ($item->sys->contentType->sys->id == "commercialPage") {
			$commercial = [
				"updated"	=> $item->sys->updatedAt,
				0 => [
					"header"	=> $item->fields->introductionHeader->{"en-US"},
					"content"	=> $item->fields->introductionContent->{"en-US"}
				],
				1 => [
					"link"		=> $item->fields->section1LinkTitle->{"en-US"},
					"header"	=> $item->fields->section1Header->{"en-US"},
					"content"	=> $item->fields->section1Content->{"en-US"}
				],
				2 => [
					"link"		=> $item->fields->section2LinkTitle->{"en-US"},
					"header"	=> $item->fields->section2Header->{"en-US"},
					"content1"	=> $item->fields->section2Content1->{"en-US"},
					"content2"	=> $item->fields->section2Content2->{"en-US"}
				],
				3 => [
					"link"		=> $item->fields->section3LinkTitle->{"en-US"},
					"header"	=> $item->fields->section3Header->{"en-US"},
					"content"	=> $item->fields->section3Content->{"en-US"}
				],
				4 => [
					"link"		=> $item->fields->section4LinkTitle->{"en-US"},
					"header"	=> $item->fields->section4Header->{"en-US"},
					"content"	=> $item->fields->section4Content->{"en-US"}
				],
				5 => [
					"link"		=> $item->fields->section5LinkTitle->{"en-US"},
					"header"	=> $item->fields->section5Header->{"en-US"},
					"subhead1"	=> $item->fields->section5Subhead1->{"en-US"},
					"content1"	=> $item->fields->section5Content1->{"en-US"},
					"subhead2"	=> $item->fields->section5Subhead2->{"en-US"},
					"content2"	=> $item->fields->section5Content2->{"en-US"},
					"subhead3"	=> $item->fields->section5Subhead3->{"en-US"},
					"content3"	=> $item->fields->section5Content3->{"en-US"}
				]
			];
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
		
		if ($item->sys->contentType->sys->id == "formattedData") {
			$formattedData[$item->fields->name->{"en-US"}] = [
				"updated" 	=> $item->sys->updatedAt,
				"id" 		=> $item->sys->id,
				"name" 		=> $item->fields->name->{"en-US"},
				"value" 	=> $item->fields->value->{"en-US"}
			];
		}
		
		if ($item->sys->contentType->sys->id == "globals") {
			$globals[$item->sys->id] = [
				"updated" 	=> $item->sys->updatedAt,
				"id" 		=> $item->sys->id,
				"key" 		=> $item->fields->key->{"en-US"},
				"text" 		=> $item->fields->text->{"en-US"}
			];
		}
		
		if ($item->sys->contentType->sys->id == "productCategories") {
			$productCategories[$item->fields->{"parent"}->{"en-US"}][] = [
				"updated" 	=> $item->sys->updatedAt,
				"id" 		=> $item->sys->id,
				"category" 	=> $item->fields->category->{"en-US"},
				"parent" 	=> $item->fields->{"parent"}->{"en-US"}
			];
		}
		
		if ($item->sys->contentType->sys->id == "productCategoryPhotos") {
			$productCategoryPhotos[$item->fields->category->{"en-US"}->sys->id][$item->sys->id] = [
				"updated" 		=> $item->sys->updatedAt,
				"id" 			=> $item->sys->id,
				"product" 		=> $item->fields->product->{"en-US"}->sys->id,
				"category" 		=> $item->fields->category->{"en-US"}->sys->id,
				"blurb" 		=> $item->fields->blurb->{"en-US"},
				"applications" 	=> $item->fields->applications->{"en-US"}
			];
			foreach ($item->fields->images->{"en-US"} as $img) {
				$productCategoryPhotos[$item->fields->category->{"en-US"}->sys->id][$item->sys->id]["images"][] = $img->sys->id;
			}
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
				"content"	=> parse_markdown($item->fields->content->{"en-US"})
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
				"logoWhite"	=> $assets[$item->fields->logoWhite->{"en-US"}->sys->id],
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
	//		if ($m == $model) {
				$products[$m] = array_merge($products[$m], [
					"overview"			=> parse_markdown($item->fields->shortDescription->{"en-US"}),
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
					"applications_yes"	=> parse_markdown($item->fields->applications->{"en-US"}),
					"applications_no"	=> isset($item->fields->applicationsIncompatible) ? parse_markdown($item->fields->applicationsIncompatible->{"en-US"}) : '',
					"description"		=> parse_markdown($item->fields->description->{"en-US"}),
					"benefits"			=> [
						"provides"		=> parse_markdown($item->fields->benefitsProvides->{"en-US"}),
						"prevents"		=> parse_markdown($item->fields->benefitsPrevents->{"en-US"}),
						"reduces"		=> parse_markdown($item->fields->benefitsReduces->{"en-US"})
						],
					"technical"			=> parse_markdown($item->fields->technicalInformation->{"en-US"}),
					"graph"				=> isset($item->fields->graph) ? $item->fields->graph->{"en-US"}->sys->id : '',
					"tinyDescription"	=> parse_markdown($item->fields->tinyDescription->{"en-US"}),
					"tinyDetails"		=> parse_markdown($item->fields->tinyDetails->{"en-US"}),
					"tinyApplications"	=> parse_markdown($item->fields->tinyApplications->{"en-US"})
				]);
				if (isset($item->fields->dataSheets)) foreach($item->fields->dataSheets->{"en-US"} as $dataSheet) {
					$products[$m]["dataSheets"][] = $assets[$dataSheet->sys->id];
				}
				//echo '<p>'.$m.':'.$count;var_dump($products[$m]["squares"]);
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
			//}
			
			$productsById[$item->sys->id] = $products[$m];
		}
	
		if ($item->sys->contentType->sys->id == "distributors" && isset($item->fields->location->{"en-US"})) {
			$distributors[$item->sys->id] = [
				"updated"	=> $item->sys->updatedAt,
				"name"		=> $item->fields->name->{"en-US"},
				"url"		=> $item->fields->url->{"en-US"},
				"blurb"		=> preg_replace("/\r|\n/","",parse_markdown($item->fields->information->{"en-US"})),
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


db::credentials("concrfe5_gs", "concrfe5_gs", "4EwgifF8aeP!");
db::_insert("cache", ["var" => "assets", "data" => serialize($assets)]);
db::_insert("cache", ["var" => "globals", "data" => serialize($globals)]);
db::_insert("cache", ["var" => "squares", "data" => serialize($squares)]);
db::_insert("cache", ["var" => "commercial", "data" => serialize($commercial)]);
db::_insert("cache", ["var" => "formattedData", "data" => serialize($formattedData)]);
db::_insert("cache", ["var" => "graphs", "data" => serialize($graphs)]);
db::_insert("cache", ["var" => "comparisonDetails", "data" => serialize($comparisonDetails)]);
db::_insert("cache", ["var" => "comparisonDetailsByProduct", "data" => serialize($comparisonDetailsByProduct)]);
db::_insert("cache", ["var" => "additional", "data" => serialize($additional)]);
db::_insert("cache", ["var" => "colors", "data" => serialize($colors)]);
db::_insert("cache", ["var" => "onlineDistributor", "data" => serialize($onlineDistributor)]);
db::_insert("cache", ["var" => "products", "data" => serialize($products)]);
db::_insert("cache", ["var" => "productsById", "data" => serialize($productsById)]);
db::_insert("cache", ["var" => "productCategories", "data" => serialize($productCategories)]);
db::_insert("cache", ["var" => "productCategoryPhotos", "data" => serialize($productCategoryPhotos)]);
db::_insert("cache", ["var" => "pages", "data" => serialize($pages)]);
db::_insert("cache", ["var" => "distributors", "data" => serialize($distributors)]);

header("Location: ../index.php");

?>
<?php

/*

UPDATE CONTENTFUL DATA...

- download Contentful data
- parse Markdown
- add glossary hooks
- sanitize data
- gather data about images (where necessary)
- cache locally

*/

error_reporting(E_ERROR);
ini_set('display_errors',"On");
ini_set('display_startup_errors',"On");

require_once "../lib/sys/syntax.php";
require_once("db.php");

db::credentials("concrfe5_gs", "concrfe5_gs", "4EwgifF8aeP!");

$dev = $_REQUEST["publish"] ? false : true;

require_once("parsedown.php");
$parsedown = new Parsedown();


function dictionary($text) {
	return preg_replace("/\{([A-z0-9\ \-]+)\}/", "<dfn>$1</dfn>", $text);
}

function parse_markdown($str) {
	global $parsedown;
	// return $ciconia->render($str);
	return dictionary($parsedown->text($str));
}

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

db::_delete("blog", ["site" => ($dev?"dev":"pub")]);
db::_delete("blog_tags", ["site" => ($dev?"dev":"pub")]);
db::_delete("blog_topics", ["site" => ($dev?"dev":"pub")]);
db::_delete("blog_products", ["site" => ($dev?"dev":"pub")]);

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
				"updated"		=> $item->sys->updatedAt,
				"title"			=> $item->fields->title->{"en-US"},
				"file"			=> $item->fields->file->{'en-US'}->url,
				"size"			=> $item->fields->file->{'en-US'}->details->size,
				"width"			=> $item->fields->file->{'en-US'}->details->image->width,
				"height"		=> $item->fields->file->{'en-US'}->details->image->height,
				"filename"		=> $item->fields->file->{'en-US'}->fileName,
				"type"			=> $item->fields->file->{'en-US'}->contentType,
			];
			if (isset($item->fields->file->{'en-US'}->details->image->width)) {
				$assets[$item->sys->id]["width"] = $item->fields->file->{'en-US'}->details->image->width;
				$assets[$item->sys->id]["height"] = $item->fields->file->{'en-US'}->details->image->height;
			}
		}
	}

	foreach($data->items as $key => $item) {
	//var_dump($item);
		if ($item->sys->contentType->sys->id == "squares" && isset($item->fields->position->{"en-US"})) {
			$squares[$item->fields->position->{"en-US"}] = [
				"updated"		=> $item->sys->updatedAt,
				"title"			=> $item->fields->title->{"en-US"},
				"link"			=> $item->fields->link->{"en-US"},
				"caption1"		=> $item->fields->caption1->{"en-US"},
				"caption2"		=> $item->fields->caption2->{"en-US"},
				"blurb"			=> isset($item->fields->blurb->{"en-US"}) ? parse_markdown($item->fields->blurb->{"en-US"}) : '',
				"position"		=> $item->fields->position->{"en-US"}
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
				"updated"		=> $item->sys->updatedAt,
				"id"			=> $item->sys->id,
				"title"			=> parse_markdown($item->fields->title->{"en-US"}),
				"url"			=> $item->fields->url->{"en-US"},
				"content"		=> parse_markdown($item->fields->content->{"en-US"})
			];
			if (isset($item->fields->headerImage->{'en-US'}->sys->id))
			$pages[$item->fields->url->{"en-US"}]["image"] = $assets[$item->fields->headerImage->{'en-US'}->sys->id];
		}
		
		if ($item->sys->contentType->sys->id == "commercial") {
			$commercial[$item->fields->sort->{"en-US"}] = [
				"header"		=> $item->fields->header->{"en-US"},
				"content"		=> $item->fields->content->{"en-US"},
				"productLink"	=> $item->fields->productLink->{"en-US"}->sys->id,
				"menuText"		=> $item->fields->pageMenuLinkText->{"en-US"},
				"showInMenu"	=> $item->fields->showInPageMenu->{"en-US"},
				"orientation"	=> $item->fields->orientation->{"en-US"},
				"bgImage"		=> $assets[$item->fields->bgImage->{"en-US"}->sys->id],
				"bgClass"		=> $item->fields->bgDisposition->{"en-US"},
				"video"			=> $item->fields->video->{"en-US"},
				"sort"			=> $item->fields->sort->{"en-US"}
			];
		}
		
		if ($item->sys->contentType->sys->id == "containers") {
			$containers[$item->sys->id] = [
				"name"			=> $item->fields->name->{"en-US"},
				"volume"		=> $item->fields->volume->{"en-US"}
			];
		}
	
		if ($item->sys->contentType->sys->id == "graph") {
			$graphs[$item->sys->id] = [
				"updated" 		=> $item->sys->updatedAt,
				"id" 			=> $item->sys->id,
				"title" 		=> $item->fields->title->{"en-US"},
				"subtitle" 		=> $item->fields->subtitle->{"en-US"},
				"xAxisLabel"	=> $item->fields->xAxisLabel->{"en-US"},
				"yAxisLabel"	=> $item->fields->yAxisLabel->{"en-US"},
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
				"updated" 		=> $item->sys->updatedAt,
				"id" 			=> $item->sys->id,
				"name" 			=> $item->fields->name->{"en-US"},
				"value" 		=> $item->fields->value->{"en-US"},
				"valueRext" 	=> $item->fields->valueText->{"en-US"}
			];
		}
		
		if ($item->sys->contentType->sys->id == "globals") {
			$globals[$item->sys->id] = [
				"updated" 		=> $item->sys->updatedAt,
				"id" 			=> $item->sys->id,
				"key" 			=> $item->fields->key->{"en-US"},
				"text" 			=> $item->fields->text->{"en-US"}
			];
		}
		
		if ($item->sys->contentType->sys->id == "productCategories") {
			$productCategories[$item->fields->{"parent"}->{"en-US"}][] = [
				"updated" 		=> $item->sys->updatedAt,
				"id" 			=> $item->sys->id,
				"category" 		=> $item->fields->category->{"en-US"},
				"seosuffix" 	=> $item->fields->seoSuffix->{"en-US"},
				"shortname" 	=> $item->fields->categoryShortName->{"en-US"},
				"parent" 		=> $item->fields->{"parent"}->{"en-US"},
				"image"			=> $assets[$item->fields->image->{"en-US"}->sys->id],
				"description" 	=> $item->fields->{"description"}->{"en-US"}
			];
		}
		
		if ($item->sys->contentType->sys->id == "productCategoryPhotos") {
			$sort = isset($item->fields->sort->{"en-US"}) ? $item->fields->sort->{"en-US"} : "9999";
			$productCategoryPhotos[$item->fields->category->{"en-US"}->sys->id][$sort."-".$item->sys->id] = [
				"updated" 		=> $item->sys->updatedAt,
				"id" 			=> $item->sys->id,
				"sort" 			=> $sort,
				"product" 		=> $item->fields->product->{"en-US"}->sys->id,
				"category" 		=> $item->fields->category->{"en-US"}->sys->id,
				"blurb" 		=> parse_markdown($item->fields->blurb->{"en-US"}),
				"intro" 		=> parse_markdown($item->fields->intro->{"en-US"}),
				"description_full" => $item->fields->{"description"}->{"en-US"},
				"description" 	=> parse_markdown($item->fields->{"description"}->{"en-US"}->data->content[0]->content[0]->value)
			];
			foreach ($item->fields->images->{"en-US"} as $img) {
				$productCategoryPhotos[$item->fields->category->{"en-US"}->sys->id][$sort."-".$item->sys->id]["images"][] = $img->sys->id;
			}
		}
	
		if ($item->sys->contentType->sys->id == "productComparisonDetails") {
			$comparisonDetails[$item->sys->id] = [
				"updated" 		=> $item->sys->updatedAt,
				"id" 			=> $item->sys->id
			];
			foreach ($item->fields as $name => $field) {
				if ($name != "product") $comparisonDetails[$item->sys->id][$name] = $field->{"en-US"};
			}
			$comparisonDetailsByProduct[$item->fields->product->{'en-US'}->sys->id] = $comparisonDetails[$item->sys->id];
		}
	
		if ($item->sys->contentType->sys->id == "additionalInformation") {
			$additional[$item->sys->id] = [
				"updated"		=> $item->sys->updatedAt,
				"id"			=> $item->sys->id,
				"title"			=> $item->fields->title->{"en-US"},
				"label"			=> $item->fields->tabLabel->{"en-US"},
				"content"		=> parse_markdown($item->fields->content->{"en-US"})
			];
		}
	
		if ($item->sys->contentType->sys->id == "colors") {
			$colors[$item->sys->id] = [
				"updated"		=> $item->sys->updatedAt,
				"id"			=> $item->sys->id,
				"name"			=> $item->fields->name->{"en-US"},
				"hex"			=> $item->fields->colorHex->{"en-US"},
				"forProduct"	=> $item->fields->forProduct->{"en-US"}
			];
		}
	
		if ($item->sys->contentType->sys->id == "distributorsOnline") {
			$onlineDistributor[$item->sys->id] = [
				"updated"		=> $item->sys->updatedAt,
				"id"			=> $item->sys->id,
				"name"			=> $item->fields->name->{"en-US"},
				"logo"			=> $assets[$item->fields->logo->{"en-US"}->sys->id],
				"logoWhite"		=> $assets[$item->fields->logoWhite->{"en-US"}->sys->id],
				"url"			=> $item->fields->url->{"en-US"},
				"search"		=> $item->fields->searchUrl->{"en-US"}
			];
			
			if ($item->fields->home->{"en-US"}) {
				$homeDistributor[$item->sys->id] = [
					"updated"	=> $item->sys->updatedAt,
					"id"		=> $item->sys->id,
					"name"		=> $item->fields->name->{"en-US"},
					"logo"		=> $assets[$item->fields->logo->{"en-US"}->sys->id],
					"logoWhite"	=> $assets[$item->fields->logoWhite->{"en-US"}->sys->id],
					"url"		=> $item->fields->url->{"en-US"},
					"search"	=> $item->fields->searchUrl->{"en-US"}
				];
			}
		}
	
		if ($item->sys->contentType->sys->id == "productVariation") {
			$productVariations[$item->sys->id] = [
				"updated"		=> $item->sys->updatedAt,
				"id"			=> $item->sys->id,
				"name"			=> $item->fields->name->{"en-US"},
				"type"			=> $item->fields->variationType->{"en-US"},
				"coverage"		=> $item->fields->coverage->{"en-US"},
				"sku"			=> $item->fields->sku->{"en-US"},
				"msrp"			=> $item->fields->msrp->{"en-US"},
				"map"			=> $item->fields->map->{"en-US"},
				"distributorDiscountPercentage" => $item->fields->distributorDiscountPercentage->{"en-US"},
				"availableToDistributors" => $item->fields->availableToDistributors->{"en-US"},
				"notes"			=> $item->fields->notes->{"en-US"}
			];
		}
	
		if ($item->sys->contentType->sys->id == "product") {
			$m = $item->fields->modelNumber->{"en-US"};
			$products[$m] = [
				"updated"		=> $item->sys->updatedAt,
				"id"			=> $item->sys->id,
				"name"			=> $item->fields->name->{"en-US"},
				"descriptor"	=> $item->fields->productNameDescriptor->{"en-US"},
				"type"			=> $item->fields->type->{"en-US"},
				"model"			=> $m,
				"sku"			=> $item->fields->sku->{"en-US"}
			];
			if (isset($item->fields->photos)) foreach($item->fields->photos->{"en-US"} as $photo) {
				$products[$m]["squares"][] = $assets[$photo->sys->id];
			}
	//		if ($m == $model) {
				$products[$m] = array_merge($products[$m], [
					"overview"			=> parse_markdown($item->fields->shortDescription->{"en-US"}),
					"availability"		=> $item->fields->availability->{"en-US"},
					"color"				=> $item->fields->labelColor->{"en-US"},
					"simple"			=> $item->fields->simpleDescription->{"en-US"},
					"residential"		=> $item->fields->residential->{"en-US"},
					"commercial"		=> $item->fields->commercial->{"en-US"},
					"repelsWater"		=> $item->fields->waterproofs->{"en-US"},
					"repelsOil"			=> $item->fields->repelsOil->{"en-US"},
					"repelsSalt"		=> $item->fields->repelsSalt->{"en-US"},
					"repelsChems"		=> $item->fields->repelsChems->{"en-US"},
					"base"				=> $item->fields->base->{"en-US"},
					"attributes"		=> [
						"coverage"		=> $item->fields->coverage->{"en-US"},
						"coats"			=> $item->fields->coats->{"en-US"},
						"voc"			=> $item->fields->vocContent->{"en-US"},
						"finish"		=> $item->fields->finish->{"en-US"},
						"color"			=> $item->fields->color->{"en-US"},
						"concentrated"	=> $item->fields->concentrated->{"en-US"},
						"warranty"		=> isset($item->fields->warranty->{"en-US"}) ? $item->fields->warranty->{"en-US"} : ''
						],
					"perGallon"			=> $item->fields->squareFootagePerGallon->{"en-US"},
					"msrp"				=> isset($item->fields->msrp->{"en-US"}) ? $item->fields->msrp->{"en-US"} : '0',
					"applications_yes"	=> parse_markdown($item->fields->applications->{"en-US"}),
					"applications_no"	=> isset($item->fields->applicationsIncompatible) ? parse_markdown($item->fields->applicationsIncompatible->{"en-US"}) : '',
					"description"		=> parse_markdown($item->fields->description->{"en-US"}),
					"videoTutorial"		=> $item->fields->videoTutorial->{"en-US"},
					"benefits"			=> [
						"provides"		=> parse_markdown($item->fields->benefitsProvides->{"en-US"}),
						"prevents"		=> parse_markdown($item->fields->benefitsPrevents->{"en-US"}),
						"reduces"		=> parse_markdown($item->fields->benefitsReduces->{"en-US"})
						],
					"technical"			=> parse_markdown($item->fields->technicalInformation->{"en-US"}),
					"graph"				=> isset($item->fields->graph) ? $item->fields->graph->{"en-US"}->sys->id : '',
					"showInComparisons"	=> parse_markdown($item->fields->showInComparisons->{"en-US"})
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
				if (isset($item->fields->containers)) foreach($item->fields->containers->{"en-US"} as $container) {
					$products[$m]["containers"][] = $container->sys->id;
				}
				if (isset($item->fields->comparisons)) foreach($item->fields->comparisons->{"en-US"} as $comparison) {
					$products[$m]["comparisons"][] = $comparison->sys->id;
				}
				if (isset($item->fields->variations)) foreach($item->fields->variations->{"en-US"} as $variation) {
					$products[$m]["variations"][] = $variation->sys->id;
				}
				if(isset($item->fields->image->{'en-US'}->sys->id))
				$products[$m]["image"] = $assets[$item->fields->image->{'en-US'}->sys->id];
			//}
			
			$productsById[$item->sys->id] = $products[$m];
		}
	
		if ($item->sys->contentType->sys->id == "distributors") {
			$sort = $item->fields->sort->{"en-US"} > 0 ? $item->fields->sort->{"en-US"} : 100;
			$distributors[$item->sys->id] = [
				"updated"		=> $item->sys->updatedAt,
				"name"			=> $item->fields->name->{"en-US"},
				"url"			=> $item->fields->url->{"en-US"},
				"blurb"			=> preg_replace("/\r|\n/","",parse_markdown($item->fields->information->{"en-US"})),
				"logo"			=> $assets[$item->fields->logo->{'en-US'}->sys->id],
				"longitude"		=> $item->fields->location->{"en-US"}->lon,
				"latitude"		=> $item->fields->location->{"en-US"}->lat,
				"address"		=> $item->fields->address->{"en-US"},
				"address2"		=> $item->fields->address2->{"en-US"},
				"phone"			=> $item->fields->phoneNumber->{"en-US"},
				"sort"			=> $sort
			];
			$distributorsSorted[$sort."-".$item->sys->id] = $distributors[$item->sys->id];
		}
	
		if ($item->sys->contentType->sys->id == "applicators") {
			$sort = $item->fields->sort->{"en-US"} > 0 ? $item->fields->sort->{"en-US"} : 100;
			$applicators[$item->sys->id] = [
				"updated"		=> $item->sys->updatedAt,
				"name"			=> $item->fields->name->{"en-US"},
				"url"			=> $item->fields->url->{"en-US"},
				"blurb"			=> preg_replace("/\r|\n/","",parse_markdown($item->fields->information->{"en-US"})),
				"logo"			=> $assets[$item->fields->logo->{'en-US'}->sys->id],
				"longitude"		=> $item->fields->location->{"en-US"}->lon,
				"latitude"		=> $item->fields->location->{"en-US"}->lat,
				"address"		=> nl2br($item->fields->address->{"en-US"}),
				"phone"			=> $item->fields->phone->{"en-US"},
				"sort"			=> $sort
			];
			$applicatorsSorted[$sort."-".$item->sys->id] = $applicators[$item->sys->id];
		}
	
		if ($item->sys->contentType->sys->id == "glossary") {
			$glossary[$item->fields->word->{"en-US"}] = [
				"word"			=> $item->fields->word->{"en-US"},
				"definition"	=> $item->fields->definition->{"en-US"},
				"dictionary"	=> $item->fields->dictionary->{"en-US"},
				"source"		=> $item->fields->source->{"en-US"}
			];
		}

		/* COMMERCIAL PORTFOLIO */

		if ($item->sys->contentType->sys->id == "commercialPortfolio") {
			$commercialPortfolio[$item->sys->id] = [
				"updated"		=> $item->sys->updatedAt,
				"id"			=> $item->sys->id,
				"title"			=> $item->fields->projectTitle->{'en-US'},
				"image"			=> $assets[$item->fields->projectImage->{'en-US'}->sys->id],
				"info"			=> $item->fields->projectInfo->{'en-US'},
				"video"			=> $item->fields->video->{'en-US'}
			];
		}
	
		if ($item->sys->contentType->sys->id == "blog" && $item->fields->urloverride->{"en-US"}) {
			
			$width = null;
			$height = null;
			$new_width = null;
			$new_height = null;
			
			if (isset($item->fields->image->{'en-US'}->sys->id)) {
				list($width, $height) = getimagesize("http:".$assets[$item->fields->image->{'en-US'}->sys->id]["file"]);
			}
			
			$new_width = $width;
			$new_height = $height;
			
			if ($width > 1600) {
				$new_height = round($height / ($width / 1600));
				$new_width = 1600;
			}
			if ($new_height > 1200) {
				$new_height = 1200;
				$new_width = round($width / ($height / 1200));
			}
		
			$blog[$item->fields->urloverride->{"en-US"}] = [
				"updated"		=> $item->sys->updatedAt,
				"title"			=> $item->fields->title->{"en-US"},
				"url"			=> $item->fields->urloverride->{"en-US"},
				"article"		=> preg_replace("/\r|\n/","",parse_markdown($item->fields->article->{"en-US"})),
				"image"			=> $assets[$item->fields->image->{'en-US'}->sys->id],
				"published"		=> $item->fields->published->{"en-US"}
			];
			//db::debug();
			$id = db::_insert("blog", [
				"site"			 => ($dev?"dev":"pub"),
				"url"			 => $item->fields->urloverride->{"en-US"},
				"title"			 => $item->fields->title->{"en-US"},
				"summary"		 => $item->fields->summary->{"en-US"},
				"article"		 => preg_replace("/\r|\n/","",parse_markdown($item->fields->article->{"en-US"})),
				"quickTip"		 => preg_replace("/\r|\n/","",parse_markdown($item->fields->quickTip->{"en-US"})),
				"quickTip2"		 => preg_replace("/\r|\n/","",parse_markdown($item->fields->quickTip2->{"en-US"})),
				"whyGhostshield" => preg_replace("/\r|\n/","",parse_markdown($item->fields->whyGhostshield->{"en-US"})),
				"image"			 => $assets[$item->fields->image->{'en-US'}->sys->id]["file"],
				"image_dim"		 => ($width > $height ? 'landscape' : 'portrait'),
				"image_wm"		 => $item->fields->watermark->{'en-US'},
				"image_size"	 => ($width ? $width."x".$height : NULL),
				"image_resize"	 => ($new_width ? $new_width."x".$new_height : NULL),
				"_modified"		 => db::timestamp($item->sys->updatedAt)
			]);
			foreach ($item->fields->tags->{"en-US"} as $tag) {
				db::_insert("blog_tags", [
					"site"		=> ($dev?"dev":"pub"),
					"post_id"	=> $id,
					"tag"		=> strtolower($tag)
				]);
			}
			foreach ($item->fields->topics->{"en-US"} as $topic) {
				db::_insert("blog_topics", [
					"site"		=> ($dev?"dev":"pub"),
					"post_id"	=> $id,
					"topic"		=> strtolower($topic)
				]);
			}
			foreach ($item->fields->products->{"en-US"} as $p) {
				db::_insert("blog_products", [
					"site"		=> ($dev?"dev":"pub"),
					"post_id"	=> $id,
					"product"	=> $p->sys->id
				]);
			}
			db::_update("blog", $id, ["_created"	=> db::timestamp($item->fields->published->{"en-US"})]);
		}
	}
}

krsort($products);
ksort($commercial);

foreach($productCategoryPhotos as $k => $a) {
	ksort($productCategoryPhotos[$k],SORT_NUMERIC);
}

ksort($applicatorsSorted,SORT_NUMERIC);
ksort($distributorsSorted,SORT_NUMERIC);

//var_dump($productCategoryPhotos);


db::_query("DELETE FROM cache WHERE `_created` < NOW() - INTERVAL 3 DAY AND `site` = '".($dev?"dev":"pub")."' AND `type` = 'cms_content'");
db::_query("DELETE FROM cache WHERE `site` = '".($dev?"dev":"pub")."' AND `type` = 'glossary'");

db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "assets", "data" => serialize($assets), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "globals", "data" => serialize($globals), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "squares", "data" => serialize($squares), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "commercial", "data" => serialize($commercial), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "formattedData", "data" => serialize($formattedData), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "graphs", "data" => serialize($graphs), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "comparisonDetails", "data" => serialize($comparisonDetails), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "comparisonDetailsByProduct", "data" => serialize($comparisonDetailsByProduct), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "additional", "data" => serialize($additional), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "colors", "data" => serialize($colors), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "onlineDistributor", "data" => serialize($onlineDistributor), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "homeDistributor", "data" => serialize($homeDistributor), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "products", "data" => serialize($products), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "productVariations", "data" => serialize($productVariations), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "productsById", "data" => serialize($productsById), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "productCategories", "data" => serialize($productCategories), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "productCategoryPhotos", "data" => serialize($productCategoryPhotos), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "pages", "data" => serialize($pages), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "distributors", "data" => serialize($distributors), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "applicators", "data" => serialize($applicators), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "distributorsSorted", "data" => serialize($distributorsSorted), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "applicatorsSorted", "data" => serialize($applicatorsSorted), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "blog", "data" => serialize($blog), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "glossary", "data" => serialize($glossary), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "containers", "data" => serialize($containers), 'type' => 'cms_content']);
db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "commercialPortfolio", "data" => serialize($commercialPortfolio), 'type' => 'cms_content']);


/*****

	VIMEO VIDEOS

*****/



//open connection
$ch = curl_init();

$vimeo_client_id = "445c9aec4240baa6101c580bbbc8573ad29111da";
$vimeo_client_secret = "L/elHhrPHfnSHMfxdtl7+zFfJxWm3pZHeiMr0TtWp8njarxqLyS32E9YaPeTITkddzfw5X3DSJ+a2O50ALaKhU1C7xU4CkkYIHOUfwm0gBOLotWnNkzKQOUUvfIQl8bx";
$vimeo_access_token = "054edda7818face545222673e2ac42aa"; // public // expires august 2019???

//set the url, number of POST vars, POST data
curl_setopt($ch, CURLOPT_URL, "https://api.vimeo.com/oauth/authorize/client");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
	"Authorization: basic " . base64_encode($vimeo_client_id . ":" . $vimeo_client_secret)
]);

//execute post
$result = curl_exec($ch);


//close connection
curl_close($ch);

$data = json_decode($result);


if (is_object($data)) {

	//$vimeo_channel = file_get_contents("https://api.vimeo.com/channels/1263592/videos?access_token=".$vimeo_access_token);//$data->access_token);

	$vimeo_channel = file_get_contents("https://api.vimeo.com/me/videos?filter=embeddable&filter_embeddable=true&access_token=".$vimeo_access_token);

	$channel = json_decode($vimeo_channel);

	foreach ($channel->data as $id => $video) {
		$videos[$id] = $video;
	}

}


db::_insert("cache", ["site" => ($dev?"dev":"pub"), "var" => "videos", "data" => serialize($videos), 'type' => 'cms_content']);


/*****

	SEARCH

*****/

$a = microtime();
unlink(__DIR__."/lib/website.txt");
unlink(__DIR__."/lib/serialized_dictionary.txt");
$fp = fopen(__DIR__."/lib/website.txt","a");

$parsed_xml = simplexml_load_file('https://'.( $dev ? 'dev.' : '' ).'ghostshield.com/sitemap.xml') or die("Fatal error: cannot create XML object.");

$json = json_encode( $parsed_xml );
$xml_array = json_decode( $json, true );

$url_array = array();
foreach ($xml_array as $array_item) {
	foreach ($array_item as $list) {
		$url_array[] = array('url' => $list['loc']);
	}
}

for ($i = 0; $i < count($url_array); $i++) {
	if ($html = file_get_contents($url_array[$i]['url'])) {
		$dom = new DOMDocument;
		if (@$dom->loadHTML($html)) {
			$raw_title = $dom->getElementsByTagName("title");
			if($raw_title->length > 0) {
				$title = $raw_title->item(0)->nodeValue;
				$stripped_title = strstr($title, ' // Ghostshield®', true);
			}
			$meta = $dom->getElementsByTagName("meta");
			$classname = "page_content";
			$xpath = new DomXPath($dom);
			$raw_body = $xpath->query('//div[contains(@class, "'.$classname.'")]'); // instance of DOMNodeList
			$description = $xpath->evaluate('string(//meta[@name="description"]/@content)');
			if ($raw_body->length > 0) {
				$body = $raw_body->item(0)->nodeValue;
				$body = strstr($body, $stripped_title);
				$body = str_replace($stripped_title, "", $body);
				if (strpos($body, 'Article Search ') !== false) {
					$body = str_replace("Article Search ", "", $body);
				}
			}
		}
	}

	$query = "INSERT INTO `search_index` (`url`, `title`, `content`, `description`, `site`, `_created`) VALUES ('".db::_cleanse($url_array[$i]['url'])."', '".db::_cleanse($stripped_title)."', '".db::_cleanse($body)."', '".db::_cleanse($description)."', '".($dev?"dev":"pub")."', NOW()) ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), description = VALUES(description), site = VALUES(site);";

	db::_query($query);
	
	fwrite($fp,$stripped_title."\n\n".$body."\n\n\n\n");

	//echo 'INSERT #'.$i.": ".$stripped_title."\n";
}
fclose($fp);
$b = microtime();

//echo 'INSERT ELAPSED TIME: '.date("H:i:s", $b-$a);



/****

	NOW GO AWAY

****/

header("Location: ".$_SERVER['HTTP_REFERER']);

//var_dump($commercial);
?>
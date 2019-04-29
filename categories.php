<?php

require_once("lib/page.class.php");

$page = new page();

$page_title = "Explore the Many Uses";
$page->setDescription("Patio or driveway? Brick or cinderblock? Browse the Ghostshield Pro Line by project application or substrate.");
$page->addScript("product-finder.min.js");
$page->addCSS("product-finder.css");
$page->addCSS("linklist.css");

$reqcat = isset($_REQUEST["category"]) ? str_replace("-", "/",  $_REQUEST["category"]) : false;



//$productCategories = get_cache_data("productCategories", ($page->dev?"dev":"pub"));
//$productCategoryPhotos = get_cache_data("productCategoryPhotos", ($page->dev?"dev":"pub"));

$cat_list = array();
$cat = array();
$containersById = $page->getCacheData("containers", $page->env);
$cat_list = array();
$cat = array();

foreach ($page->data("productCategories")->attr as $key => $subCategory) {

	//$cat[$subCategory["parent"]][$subCategory["category"]] .= "<h2>".$subCategory["parent"].": ".$subCategory["category"]."</h2>";

	foreach ($subCategory as $c) {
		//var_dump($c);
		$cat_list[strtolower($key)][] = $c["category"];

		$cat[strtolower($c["parent"])][strtolower($c["category"])] = "";
		$cat[strtolower($c["parent"])][strtolower($c["category"])."-title"] = '<h1 class="centered">'.$c["category"].'</h1>';
		$cat[strtolower($c["parent"])][strtolower($c["category"])."-pagetitle"] = $c["category"]." Sealer";

		//if (isset($c["description"])) 
		$cat[strtolower($c["parent"])][strtolower($c["category"])."-description"] = $c["description"];
		if (isset($c["intro"])) $cat[strtolower($c["parent"])][strtolower($c["category"])."-intro"] = $c["intro"];
	
		if (isset($page->data("productCategoryPhotos")->attr[$c["id"]])) foreach($page->data("productCategoryPhotos")->attr[$c["id"]] as $item) {
		
			$cat[strtolower($c["parent"])][strtolower($c["category"])] .= '
				<div class="product_listing">
					<div class="images">
						<img src="'. ( isset($page->data("assets")->attr[$item["images"][0]]) ? '/images/500x320-scalecrop/'.str_replace("//","external:",str_replace("downloads.","images.",$page->data("assets")->attr[$item["images"][0]]["file"])) : '' ) .'" />
						<img src="'. ( isset($page->data("assets")->attr[$item["images"][1]]) ? '/images/500x320-scalecrop/'.str_replace("//","external:",str_replace("downloads.","images.",$page->data("assets")->attr[$item["images"][1]]["file"])) : '' ) .'" />
					</div>
					<div class="product_info">
						<div class="product_image"><img src="';

			if (file_exists('assets/img/bottle/'.$page->data("productsById")->attr[$item["product"]]["model"].'.png')) {
				$cat[strtolower($c["parent"])][strtolower($c["category"])] .=  '//assets.'.$page->host.'/img/bottle/'.$page->data("productsById")->attr[$item["product"]]["model"].'.png';
			} elseif (file_exists('assets/img/bucket/'.$page->data("productsById")->attr[$item["product"]]["model"].'.png')) {
				$cat[strtolower($c["parent"])][strtolower($c["category"])] .=  '//assets.'.$page->host.'/img/bucket/'.$page->data("productsById")->attr[$item["product"]]["model"].'.png';
			} elseif (isset($page->data("productsById")->attr[$item["product"]]["image"]["file"])) $cat[strtolower($c["parent"])][strtolower($c["category"])] .=  $page->data("productsById")->attr[$item["product"]]["image"]["file"];

			$cat[strtolower($c["parent"])][strtolower($c["category"])] .= '" width="200" height="200" /></div>
						<div class="blurb">
							<h3 class="product_name selected '.str_replace(" ","-",strtolower($page->data("productsById")->attr[$item["product"]]["name"])).'">'.$page->data("productsById")->attr[$item["product"]]["name"].'</h3>
							<div class="attribute_icons">';

			if ($page->data("productsById")->attr[$item["product"]]["repelsWater"]) {
				$cat[strtolower($c["parent"])][strtolower($c["category"])] .= '<div class="icon repelsWater">Water Repellent</div>';
			}

			if ($page->data("productsById")->attr[$item["product"]]["repelsOil"]) {
				$cat[strtolower($c["parent"])][strtolower($c["category"])] .= '<div class="icon repelsOil">Oil Repellent</div>';
			}

			if ($page->data("productsById")->attr[$item["product"]]["repelsSalt"]) {
				$cat[strtolower($c["parent"])][strtolower($c["category"])] .= '<div class="icon repelsSalt">Salt Repellent</div>';
			}

			if ($page->data("productsById")->attr[$item["product"]]["repelsChems"]) {
				$cat[strtolower($c["parent"])][strtolower($c["category"])] .= '<div class="icon repelsChems">Chemical Resistant</div>';
			}

			if ($page->data("productsById")->attr[$item["product"]]["base"]) {
				$cat[strtolower($c["parent"])][strtolower($c["category"])] .= '<div class="icon '.strtolower($page->data("productsById")->attr[$item["product"]]["base"]).'">'.$page->data("productsById")->attr[$item["product"]]["base"].'</div>';
			}
			
			// this is sort of ugly but ... wtf
			$kit = false;
			$concentrate = false;
			$ready = false;
			foreach($page->data("productsById")->attr[$item["product"]]["containers"] as $container){
				if (strpos($containersById[$container]["name"],"Kit")>-1) {
					$kit = true;
				}else if (strpos($containersById[$container]["name"],"Concentrate")>-1) {
					$concentrate = true;
				}else $ready = true;
			}

			if ($ready) {
				$cat[strtolower($c["parent"])][strtolower($c["category"])] .= '<div class="icon readyToUse">Ready-to-use</div>';
			}
			if ($concentrate) {
				$cat[strtolower($c["parent"])][strtolower($c["category"])] .= '<div class="icon concentrated">Concentrated</div>';
			}
			if ($kit) {
				$cat[strtolower($c["parent"])][strtolower($c["category"])] .= '<div class="icon twoPart">Two-component</div>';
			}
			
			$cat[strtolower($c["parent"])][strtolower($c["category"])] .= '
					
							</div>
							'.$item["intro"].'
							'.((isset($item["description"]) ? $item["description"] : isset($item["blurb"]) ? $item["blurb"] : '')).'
							<ul class="actions">
								<li><a class="hex-button xsmall" href="/product/'.$page->data("productsById")->attr[$item["product"]]["model"].'/"><span class="valign">'.$GLOBALS["view_button_text"].'</span></a></li>
								<li><a class="hex-button xsmall" href="https://concretesealersupply.com/product/'.str_replace(" ","-",strtolower($page->data("productsById")->attr[$item["product"]]["name"])) .'/" target="_blank"><span class="valign">'.$GLOBALS["buy_button_text"].'</span></a></li>
							</ul>
							<div class="clear"></div>
						</div>
					</div>
				</div>';

		//	$cat[$c["parent"]][$c["category"]] .= '</div>';
	
		}
	
	}
	
	sort($cat_list[strtolower($key)]);
	reset($cat_list[strtolower($key)]);

}

if (isset($reqcat) && isset($_REQUEST["parent"])) {

	$page_title = "Explore the Many Uses // ". $cat[strtolower($_REQUEST["parent"])][strtolower($reqcat)."-pagetitle"];
	if (isset($cat[strtolower($_REQUEST["parent"])][strtolower($reqcat)."-description"])) $page->setDescription($cat[strtolower($_REQUEST["parent"])][strtolower($reqcat)."-description"]);

}

echo $page->header($page_title, "browse");

echo '
	<div id="fb-root"></div>';

echo '
	<div id="page_nav_container"></div>
	<div class="categories_page">';

echo '
		<div class="link_list">
			<div class="filters hook">Filters</div>
			<div class="link_list_container">';

$i=1;

foreach ($cat_list as $key => $cats) {
	
	if ($key != "featured") {
		
		if ($key == "applications") echo '
				<div class="link_list_interior accordian'.(!$reqcat?" active origin":"").'" id="cat-acc0">
					<h3>Browse</h3>
					<ul><li><a href="/browse"'.(!$reqcat?' class="active"':"").'>Featured Products</a></li></ul>
				</div>';
	
		$cat_html = "";
		$active = false;
		foreach ($cats as $c) {
		
			$add = "";
			if (isset($reqcat) && (strtolower($c) == $reqcat || $c == $reqcat)) {
				$add = ' class="active"';
				$active = true;
			}
		
			$cat_html .= '
						<li><a href="/browse/'.urlencode($key).'/'.urlencode(str_replace("/","-",strtolower($c))).'/"'.$add.'>'.str_replace("/","<span>/</span>",$c).'</a></li>';
	
		}
	
		echo '
				<div class="link_list_interior accordian'.($active?" active origin":"").'" id="cat-acc'.$i.'">
					<h3>'.ucwords($key).'</h3>
					<ul>'.$cat_html.'
					</ul>
				</div>';
		
		$i++;
	
	}

}
/*
foreach ($page->data("productCategories")->attr as $key => $subCategory) {

	//$cat[$subCategory["parent"]][$subCategory["category"]] .= "<h2>".$subCategory["parent"].": ".$subCategory["category"]."</h2>";
	$count=0;
	foreach ($subCategory as $c) {
		
		$cat_list[strtolower($key)][] = $c["category"];

		$cat[strtolower($c["parent"])][strtolower($c["category"])] = "";
		$cat[strtolower($c["parent"])][strtolower($c["category"])."-title"] = '<h1 class="centered">'.$c["category"].' Sealers</h1>';
		$cat[strtolower($c["parent"])][strtolower($c["category"])."-pagetitle"] = $c["category"]." Sealer";
		if (isset($c["description"])) $cat[strtolower($c["parent"])][strtolower($c["category"])."-description"] = $c["description"];
		if (isset($c["image"])) $cat[strtolower($c["parent"])][strtolower($c["category"])."-image"] = $c["image"];
	
		if (isset($page->data("productCategoryPhotos")->attr[$c["id"]])) foreach($page->data("productCategoryPhotos")->attr[$c["id"]] as $item) {
		
			$cat[strtolower($c["parent"])][strtolower($c["category"])] .= '
				<div class="product_listing num'.$count.'">
					<!--div class="images">
						<img src="'. ( isset($page->data("assets")->attr[$item["images"][1]]) ? $page->data("assets")->attr[$item["images"][1]]["file"] : '' ) .'?w=500&h=320&fit=fill" />
					</div-->
					<div class="product_image" style="background-image:url('. ( isset($page->data("assets")->attr[$item["images"][0]]) ? $page->data("assets")->attr[$item["images"][0]]["file"] : '' ) .'?w=480&h=330&fit=fill)">
						<img src="';

			if (file_exists('assets/img/bottle/'.$page->data("productsById")->attr[$item["product"]]["model"].'.png')) {
				$cat[strtolower($c["parent"])][strtolower($c["category"])] .=  '//assets.'.$page->host.'/img/bottle/'.$page->data("productsById")->attr[$item["product"]]["model"].'.png';
			} elseif (file_exists('assets/img/bucket/'.$page->data("productsById")->attr[$item["product"]]["model"].'.png')) {
				$cat[strtolower($c["parent"])][strtolower($c["category"])] .=  '//assets.'.$page->host.'/img/bucket/'.$page->data("productsById")->attr[$item["product"]]["model"].'.png';
			} else $cat[strtolower($c["parent"])][strtolower($c["category"])] .=  $page->data("productsById")->attr[$item["product"]]["image"]["file"];

			$cat[strtolower($c["parent"])][strtolower($c["category"])] .= '" width="160" height="160" />
					</div>
					<div class="product_info">
						<h3 class="product_name selected '.str_replace(" ","-",strtolower($page->data("productsById")->attr[$item["product"]]["name"])).'">'.$page->data("productsById")->attr[$item["product"]]["name"].'</h3>
						<div class="attribute_icons">';

			if ($page->data("productsById")->attr[$item["product"]]["repelsWater"]) {
				$cat[strtolower($c["parent"])][strtolower($c["category"])] .= '<div class="icon repelsWater">Water Repellent</div>';
			}

			if ($page->data("productsById")->attr[$item["product"]]["repelsOil"]) {
				$cat[strtolower($c["parent"])][strtolower($c["category"])] .= '<div class="icon repelsOil">Oil Repellent</div>';
			}

			if ($page->data("productsById")->attr[$item["product"]]["repelsSalt"]) {
				$cat[strtolower($c["parent"])][strtolower($c["category"])] .= '<div class="icon repelsSalt">Salt Repellent</div>';
			}

			if ($page->data("productsById")->attr[$item["product"]]["repelsChems"]) {
				$cat[strtolower($c["parent"])][strtolower($c["category"])] .= '<div class="icon repelsChems">Chemical Resistant</div>';
			}

			if ($page->data("productsById")->attr[$item["product"]]["base"]) {
				$cat[strtolower($c["parent"])][strtolower($c["category"])] .= '<div class="icon '.strtolower($page->data("productsById")->attr[$item["product"]]["base"]).'">'.$page->data("productsById")->attr[$item["product"]]["base"].'</div>';
			}
			
			// this is sort of ugly but ... wtf
			$kit = false;
			$concentrate = false;
			$ready = false;
			foreach($page->data("productsById")->attr[$item["product"]]["containers"] as $container){
				if (strpos($containersById[$container]["name"],"Kit")>-1) {
					$kit = true;
				}else if (strpos($containersById[$container]["name"],"Concentrate")>-1) {
					$concentrate = true;
				}else $ready = true;
			}

			if ($ready) {
				$cat[strtolower($c["parent"])][strtolower($c["category"])] .= '<div class="icon readyToUse">Ready-to-use</div>';
			}
			if ($concentrate) {
				$cat[strtolower($c["parent"])][strtolower($c["category"])] .= '<div class="icon concentrated">Concentrated</div>';
			}
			if ($kit) {
				$cat[strtolower($c["parent"])][strtolower($c["category"])] .= '<div class="icon twoPart">Two-component</div>';
			}
			
			
			
			$cat[strtolower($c["parent"])][strtolower($c["category"])] .= '
						</div>
						<div class="blurb">'.$page->data("productsById")->attr[$item["product"]]["applications_yes"].'</div>
					</div>
					
					<ul class="actions">
						<li><a href="/product/'.$page->data("productsById")->attr[$item["product"]]["model"].'/"><span class="valign">'.$GLOBALS["view_button_text"].'</span></a></li>
						<li><a href="http://concretesealersolutions.com/?s=ghostshield+'.$page->data("productsById")->attr[$item["product"]]["model"].'" target="_blank"><span class="valign">'.$GLOBALS["buy_button_text"].'</span></a></li>
					</ul>
				</div>';

		//	$cat[$c["parent"]][$c["category"]] .= '</div>';
			$count++;
		}
	
	}
	
	sort($cat_list[strtolower($key)]);
	reset($cat_list[strtolower($key)]);

}

if (isset($reqcat) && isset($_REQUEST["parent"])) {

	$page_title = "Explore the Many Uses // ". $cat[strtolower($_REQUEST["parent"])][strtolower($reqcat)."-pagetitle"];
	if (isset($cat[strtolower($_REQUEST["parent"])][strtolower($reqcat)."-description"])) $page->setDescription($cat[strtolower($_REQUEST["parent"])][strtolower($reqcat)."-description"]);

}

//require_once("inc/header.php");
echo $page->header($page_title, "browse");

echo '
	<div id="fb-root"></div>

	<div id="banner">
		<div class="video_wrapper"><img src="//assets.'.$page->host.'/img/collage-dark.jpg" /></div>
	</div>';


echo '
	<div class="categories_page">
		<section>
			<h2 class="centered mobile">Explore the many uses of Ghostshield</h2>
		</section>';

echo '
		<div class="link_list">';

foreach ($cat_list as $key => $cats) {
	
	if ($key != "finish") {
	
		echo '
			<div class="link_list_interior">';
		
		if ($key == "applications") echo '
				<h3>Categories</h3>
				<ul><li><a href="/browse" class="'.(!$reqcat?"active":"").'">Featured Products</a></li></ul>';
		
		echo '
				<h3>'.ucwords($key).'</h3>
				<ul>';
	
		foreach ($cats as $c) {
		
			$add = "";
			if (isset($reqcat) && (strtolower($c) == $reqcat || $c == $reqcat)) $add = ' class="active"';
		
			echo '
					<li><a href="/browse/'.urlencode($key).'/'.urlencode(str_replace("/","-",strtolower($c))).'/"'.$add.'>'.str_replace("/","<span>/</span>",$c).'</a></li>';
	
		}
	
		echo '
				</ul>
			</div>';
	
	}

}*/


echo '
			</div>
		</div>

		<!--div class="pfbutton">Need more assistance? <a href="/product-finder/" class="button"><span class="ghostshield">Ghostshield</span> Product&nbsp;Finder</a></div-->

		<section class="category_results">';

if (isset($reqcat) && isset($_REQUEST["parent"])) {

	echo $cat[strtolower($_REQUEST["parent"])][strtolower($reqcat)."-title"];
	if ($cat[strtolower($_REQUEST["parent"])][strtolower($reqcat)."-description"]) echo '<div class="description">'.$cat[strtolower($_REQUEST["parent"])][strtolower($reqcat)."-description"].'</div><p style="height:40px"></p>';
	if(isset($cat[strtolower($_REQUEST["parent"])][strtolower($reqcat)."-image"]["file"]))echo '<div style="margin:0 auto;width:1080px;height:280px;background:url('.$cat[strtolower($_REQUEST["parent"])][strtolower($reqcat)."-image"]["file"].') center center no-repeat;background-size:cover;"></div>';
	echo $cat[strtolower($_REQUEST["parent"])][strtolower($reqcat)];

} else {

	echo '
			<h1 class="centered desktop">Featured Products</h1>';
	if ($cat["featured"]["index-description"]) echo '<div class="description">'.$cat["featured"]["index-description"].'</div><p style="height:40px"></p>';
/*
			<div class="collage"><img src="//assets.'.$host.'/img/collage-mobile.jpg" width="100%" id="explore_collage" /></div>';*/

	if (isset($cat["featured"]) && isset($cat["featured"]["index"])) echo $cat["featured"]["index"];
	
}

echo '
		</section>
	</div>

	<a name="product_finder" id="product_finder_anchor" class="anchor"></a>
	<a name="pf" id="pf_anchor" class="anchor"></a>

	<section id="product_finder">';

include_once("product_finder.php");

echo '
	</section>

	<section id="buy" class="black large">
		<h2 class="sub centered">'. $GLOBALS["buy_section_title"] .'</h2>
		<div class="column full">
			<div class="column_interior">
				<ul class="width'.count($page->data("homeDistributor")->attr).'">';

$logo_count = 1;

foreach ($page->data("homeDistributor")->attr as $distributor) {
	echo '
					<li data-id="'.$distributor["id"].'" class="animate-in fade-in delay-'.$logo_count.'"><a href="'.str_replace("%s",'ghostshield+',$distributor["search"]).'" class="'.str_replace(" ","",strtolower($distributor["name"])).'" target="_blank" style="background-image:url('.$distributor["logoWhite"]["file"].')">'.$distributor["name"].'</a></li>';
	$logo_count++;

}

/*
	echo '
					<li class="animate-in fade-in delay-'.$logo_count.'"><a href="#" class="diy" target="_blank" style="background-image:url(assets/img/logo/diy-white.svg)">D.i.Y. Home Center</a></li>';
*/					
	echo '
				</ul>
			</div>
		</div>
	</section>';

echo $page->footer();

?>
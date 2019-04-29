<?php

//$admin_email = array("benjamin@ghostshield.com", "jeff@ghostshield.com");
//$show_dates_for_bydate_sort = false;

define("APP_PREFIX",	"gallery");
define("APP_PATH",		"/gallery/admin/");
define("LIB_DIR",		"../lib/");

require_once("lib/page.class.php");
$page = new page(); //require_once("inc/load.php");
$page->setDescription("Browse or upload pictures or videos of your Ghostshield sealed patio, pool deck, garage, driveway, fountain, or other concrete project.");

//require_once("db.php");
require_once("lib/gallery.class.php");

db::credentials("concrfe5_gs", "concrfe5_gs", "4EwgifF8aeP!");
$gallery = new gallery();

$uri = preg_replace('/#[\w]+/', "", preg_replace('/\?.*/', "", $_SERVER['REQUEST_URI']));

//$gallery_count = 1;

if (!isset($_REQUEST["dropzone"])) {
	//db::debug();
	echo $page->header("Gallery"); //require_once("inc/header.php");
	
	if (isset($page->model) && !isset($page->product)) {
		
		echo '
	<div id="banner">
		<div class="video_wrapper">
			<img src="//assets.ghostshield.com/img/bg/polygon-concrete-short.jpg" />
		</div>
	</div>
	<section id="full_page_content" class="white article">
		<div class="column full">
			<div class="column_interior">
				<h1 style="font-size:32px;">Error 404: Not Found</h1>
				<p class="date">Sorry, a product with model number "'.$page->model.'" could not be found. The page could not be served.</p>
				<address></address>
			</div>
		</div>
	</section>';
		
		echo $page->footer(); //require_once("inc/footer.php");
		exit();
	}

	$array = ["_approved" => 1];
	if (isset($page->model)) $array["product"] = $page->model;

	echo '
	<div id="banner">
		<div class="video_wrapper"><img src="//assets.'.$page->host.'/img/bottle-bg/hydrophobic.jpg" /></div>
	</div>
	
	<a name="gallery" id="gallery_anchor" class="anchor"></a>

	<section id="gallery">
		<h1>Customer Photo Gallery</h1>
		<ul class="text_menu">
			<li>'.((!isset($_REQUEST["sort"]))?'<span>By Date</span>':'<a href="/gallery/">By Date</a>').'
			<li>'.((isset($_REQUEST["sort"]) && $_REQUEST["sort"] == "product")?'<span>By Product</span>':'<a href="/gallery/product/">By Product</a>').'
			<!--li>'.((isset($_REQUEST["sort"]) && $_REQUEST["sort"] == "date")?'<span>By Calendar</span>':'<a href="/gallery/date/">By Calendar</a>').'-->
		</ul>';

	$limit["residential"] = ""; 
	$limit["commercial"] = "";
	foreach ($page->data["products"] as $product) {
		if ($product["residential"]) $limit["residential"] .= ($limit["residential"] == "" ? "" : " OR ") . '`product`="'.$product["model"].'" OR `product2`="'.$product["model"].'"';
		if ($product["commercial"]) $limit["commercial"] .= ($limit["commercial"] == "" ? "" : " OR ") .'`product`="'.$product["model"].'" OR `product2`="'.$product["model"].'"';
	}

	if (!isset($_REQUEST["sort"])){
		$gallery_items = db::_query('SELECT * FROM `gallery'.($page->dev?"-dev":"").'` WHERE ('.$limit["residential"].') AND `_approved`=1 ORDER BY `_created` DESC');
		if (sizeof($gallery_items) > 0) echo $gallery->create($gallery_items, "Default", "residential");
	
	} elseif ($_REQUEST["sort"] == "date") {
	
		echo '
		<div class="cpgallery" itemscope itemtype="http://schema.org/ImageGallery">';
		
		$beg_y = 2017;
		$cur_y = date("Y");
		
		for ($y = $beg_y; $y >= $beg_y; $y--) {
		
			$beg_m = 1;
			$cur_m = date("n");
			
			for ($m = $cur_m; $m >= $beg_m; $m--) {
				$gallery_items = null;
				
				if ($m == 12) {
					$next = ($y + 1)."-01-01";
				} else {
					$next = $y."-".str_pad($m + 1, 2, "0", STR_PAD_LEFT)."-01";
				}
				
				$current = $y.'-'.str_pad($m, 2, "0", STR_PAD_LEFT).'-01';
				
				$gallery_items = db::_query('SELECT * FROM `gallery'.($dev?"-dev":"").'` WHERE ('.$limit["residential"].') AND `_approved`=1 AND `_created` >= "'.$current.' 00:00:00" AND `_created` < "'.$next.' 00:00:00" ORDER BY `_created` DESC');
				
				if (sizeof($gallery_items) > 0) {
					$name = date("F Y", strtotime($current));
					echo '<h3>'.$name.'</h3>';
					echo $gallery->create($gallery_items, $name, "residential", false, false);
				}
			
			}
		
		}
			
		echo '
		</div>';
		
		/* old non-sorted:
		$gallery_items = db::_select("gallery".($dev?"-dev":""), ["where" => $array]);
		if (sizeof($gallery_items) < 1) {
			//
		} else echo $gallery->create($gallery_items);*/
	} elseif ($_REQUEST["sort"] == "product") {
		foreach ($page->data["products"] as $m => $product) {
			$array1 = array_merge($array, ["product" => $m]);
			$array2 = array_merge($array, ["product2" => $m]);
			$gallery_items = null;
			$gallery_items1 = db::_select("gallery".($page->dev?"-dev":""), ["where" => $array1]);
			$gallery_items2 = db::_select("gallery".($page->dev?"-dev":""), ["where" => $array2]);
			$gallery_items = array_merge($gallery_items1, $gallery_items2);
			if (sizeof($gallery_items) > 0) {
				echo '<h3 class="product_name selected '.str_replace(" ","-",strtolower($product["name"])).'">'.$product["name"].'</h3>';
				echo $gallery->create($gallery_items, $product["name"], ($product["residential"] ? "residential" : "commercial"));
			}
		}
	}

	echo  '
		<div id="gsdz-email-confirm" class="inactive"></div>
		<form id="gsdropzone" action="'. $uri .'" class="dropzone dz-clickable" enctype="multipart/form-data">
			<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
			<div class="email_input">
				<div class="email_input_inner">
					<p><p>To share media of your project please enter your email:</p>
					<span><input type="text" name="email" value="" placeholder="your@email.com" id="email_input_input" /></span>
					<div class="group">
						<select name="model" id="model_selector">
							<option value="0">Product</option>';
	foreach ($page->data["products"] as $m => $r) {
		echo '
							<option value="'.$m.'">'.$r["name"].'</option>';
	}
	echo '
						</select>
						<span id="email_input_button"></span>
					</div>
					<p><small>This email will only be used in regard to shared media. <a href="/upload-legal">More Info</a></small></p>
				</div>
			</div>
			<div id="gsdz_add" class="gsdz_hidden dz-clickable">+</div>
			<div id="gallery_save"><div id="gallery_save_text" class="dz-clickable"></div></div>
			<div class="fallback">
				<input name="file" type="file" multiple />
				<input type="submit" value="Upload Image" class="xsmall hex-button" />
			</div>
		</form>
		
		<div id="gsdz_confirm" class="gsdz_hidden">
			<div><p>We review all submissions before they appear on the site. Thank you for your patience.</p></div>
		</div>';

}

$gallery_item = new galleryItem($_REQUEST);
$gallery_item->upload();

if (!isset($_REQUEST["dropzone"])) {

	echo '
		<div id="gallery_status" data-total="0" data-error="0"></div>
		<form id="gallery_captions" method="POST"></form>

	</section>';
	
	echo $gallery->overlay();
	
	echo '
	<div id="page_footer_before"></div>';

	echo $page->footer(); //require_once("inc/footer.php");

}
?>
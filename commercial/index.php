<?php

$path = "../";
require_once($path."lib/page.class.php");
require_once($path."lib/gallery.class.php");

$page = new page("../../");
$page->setDescription("Ghostshield Iso-Tek commercial concrete sealant is unmatched, capable of penetrating one inch into 14,000 PSI high-density concrete.");
$page->model("8501");
$page->addScript("clicklink.js");
$page->addScript("gallery.js");
$page->addCSS("gallery.css");
$page->addCSS("clf.css");

$gallery = new gallery(); 
// needs to be a query instead to get all commercial items
$gallery_items1 = db::_select("gallery".($page->dev?"-dev":""), ["where" => ["product" => "8501", "_approved" => 1], "order_by" => "_created", "order_direction" => "DESC"]);
$gallery_items2 = db::_select("gallery".($page->dev?"-dev":""), ["where" => ["product2" => "8501", "_approved" => 1], "order_by" => "_created", "order_direction" => "DESC"]);
$gallery_items = array_merge($gallery_items1, $gallery_items2);

$menuLi = "";
$content = "";
$css = "";

foreach ($page->data("commercial")->attr as $item) {

	$link = preg_replace("/[^A-z0-9]+/", "-", strtolower($item["menuText"]));

	if ($item["showInMenu"] == "yes" || $item["showInMenu"] == true) {
		$menuLi .= '
				<li><a href="#'.$link.'"><span>'. $item["menuText"] .'</span></a></li>';
		$content .= "\n\n".'			<a name="'.$link.'" id="'.$link.'_anchor" class="anchor"></a>';
	}
	
	$productLink = $item["productLink"] ? $page->data("productsById")->attr[$item["productLink"]] : false;
	$productName = $productLink ? preg_replace("/[^a-z0-9]+/", "-", strtolower($productLink["name"])) : false;

	if ($item["bgClass"] == "tri-column") {
	
		$content .= "\n".'
			<section id="section-'.$link.'" class="tricol">
				<div class="column full white_text">
					<div class="column_interior">
						<h2 class="sub">'. $item["header"] .'</h2>
						'.$item["content"].'
					</div>
				</div>
			</section>';

		$page->addStyles('section#section-'.$link.' { background:#7c7c7c url("'.$item["bgImage"]["file"].'") center bottom no-repeat; background-size:cover; }');
	
	} elseif ($item["video"]) {
	
		$video = '
			<div class="youtube_video">
				<iframe src="'. $item["video"] .'" width="640" height="480" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
			</div>';
		
		$text = '
			<div class="column_interior">
				<h2>'. $item["header"] .'</h2>
				<div class="content"><p>'. $item["content"] .'</p></div>
			</div>';
	
		$content .= "\n".'
	<section id="section-'.$link.'" class="'.$item["bgClass"].' '.$item["bgClass"].'_bg video">
		<div class="column left">'.($item["orientation"] ? $text : $video).'
		</div>
		<div class="column right">'.($item["orientation"] ? $video : $text).'
		</div>
		<div class="clear"></div>
	</section>';
	
	
	} else {

		$content .= "\n".'
			<section id="section-'.$link.'" class="'.($productLink ? 'click_link ' : '').'custom">
				<div class="simple_'.($item["orientation"] ? "left" : "right").'">
					<div class="'.$item["bgClass"].'_block">
						<h2>'. $item["header"] .'</h2>
						<div class="standout"></div>
						<div class="content">
							<p>'. $item["content"] .'</p>';
		if ($productLink) $content .= '
							<p><a href="/product/'.$productName.'/" class="hex-button xsmall">View '.$productLink["model"].' Product Page</a></p>';
		$content .= '
						</div>
					</div>
				</div>';
		if ($productLink) $content .= '
				<a class="click_link_fallback clf_abs" href="/product/'.$productName.'/">Links to '.$productLink["name"].' Product</a>';
		$content .= '
			</section>';

		$page->addStyles('section#section-'.$link.':before { background:url("'.$item["bgImage"]["file"].'") center center no-repeat; background-size:cover; }');
	
	}

}

echo $page->header("Commercial");

echo '

	<div id="banner">
		<div class="video_wrapper">
			<img src="//assets.'. $page->host .'/img/commercial/bridge8.jpg" />
		</div>
	</div>

	<section id="welcome">
		<div class="column full">
			<div class="column_interior">
				<h1 class="centered">'. $GLOBALS["commercial_page_title"] .'</h1>';
/*if (sizeof($page->data("commercialOld")->attr[0]["content"]) > 0) echo '
				<p id="intro">'. $page->data("commercialOld")->attr[0]["content"] .'</p>';*/
echo '
			</div>
		</div>
	</section>

	<div id="page_nav_container">
		<nav class="page_nav" id="page_nav">
			<div id="page_nav_open"><span>Page Sections</span></div>
			<ul>'.$menuLi.'
			</ul>
		</nav>
	</div>';


echo $content;

echo '

	<section id="isotek_gallery">
		<div id="gallery">
			<h2>Gallery</h2>
			'.$gallery->create($gallery_items, "Commercial", "commercial", true).'
		</div>
	</section>

	<div id="page_footer_before"></div>';

echo $gallery->overlay();

echo $page->footer();

?>
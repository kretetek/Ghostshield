<?php

require_once("lib/page.class.php");
require_once("lib/gallery.class.php");

$page = new page();
$page->model("8501");
$page->addScript("clicklink.js");
$page->addScript("gallery.js");

$gallery = new gallery(); 
// needs to be a query instead to get all commercial items
$gallery_items1 = db::_select("gallery".($page->dev?"-dev":""), ["where" => ["product" => "8501", "_approved" => 1]]);
$gallery_items2 = db::_select("gallery".($page->dev?"-dev":""), ["where" => ["product2" => "8501", "_approved" => 1]]);
$gallery_items = array_merge($gallery_items1, $gallery_items2);

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
				<h2 class="centered">'. $page->data("commercialOld")->attr[0]["header"] .'</h2>';
if (sizeof($page->data("commercialOld")->attr[0]["content"]) > 0) echo '
				<p id="intro">'. $page->data("commercialOld")->attr[0]["content"] .'</p>';
echo '
			</div>
		</div>
	</section>

	<div id="page_nav_container">
		<nav class="page_nav" id="page_nav">
			<div id="page_nav_open"><span>Page Sections</span></div>
			<ul>
				<li><a href="#isotek1"><span>'. $page->data("commercialOld")->attr[1]["link"] .'</span></a></li>
				<li><a href="#isotek2"><span>'. $page->data("commercialOld")->attr[2]["link"] .'</span></a></li>
				<li><a href="#isotek3"><span>'. $page->data("commercialOld")->attr[3]["link"] .'</span></a></li>
				<li><a href="#isotek4"><span>'. $page->data("commercialOld")->attr[4]["link"] .'</span></a></li>
				<li><a href="#isotek5"><span>'. $page->data("commercialOld")->attr[5]["link"] .'</span></a></li>
			</ul>
		</nav>
	</div>

	<a name="isotek" id="isotek1_anchor" class="anchor"></a>

	<section id="isotek1" class="click_link">
		<div class="simple_right">
			<div class="white_block">
				<h2>'. $page->data("commercialOld")->attr[1]["header"] .'</h2>
				<div class="standout"></div>
				<div class="content">
					<p>'. $page->data("commercialOld")->attr[1]["content"] .'</p>
					<p><a href="/product/iso-tek-8501/" class="hex-button xsmall">View 8501 Product Page</a></p>
				</div>
			</div>
		</div>
		<a class="click_link_fallback clf_abs" href="/product/iso-tek-8501/">Links to Iso-Tek 8501 Product</a>
	</section>

	<a name="isotek2" id="isotek2_anchor" class="anchor"></a>

	<section id="isotek2">
		<div class="simple_left white_text">
			<div class="gray_block">
				<h2>'. $page->data("commercialOld")->attr[2]["header"] .'</h2>
				<div class="standout"></div>
				<div class="content">
					<p>'. $page->data("commercialOld")->attr[2]["content1"] .'</p>
				</div>
			</div>
		</div>
	</section>

	<a name="isotek3" id="isotek3_anchor" class="anchor"></a>

	<section id="isotek3">
		<div class="simple_right">
			<div class="white_block">
				<h2>'. $page->data("commercialOld")->attr[3]["header"] .'</h2>
				<div class="standout"></div>
				<div class="content">
					<p>'. $page->data("commercialOld")->attr[3]["content"] .'</p>
				</div>
			</div>
		</div>
	</section>';

if (isset($page->data("commercialOld")->attr[7]) && $page->data("commercialOld")->attr[7]["header"] > "") echo '

	<!-- illuminated building image -->
	<a name="isotek6" id="isotek6_anchor" class="anchor"></a>

	<section id="isotek6">
		<div class="simple_left white_text">
			<div class="black_block">
				<h2>'. $page->data("commercialOld")->attr[7]["header"] .'</h2>
				<div class="standout"></div>
				<div class="content">
					<p>'. $page->data("commercialOld")->attr[7]["content"] .'</p>
				</div>
			</div>
		</div>
	</section>';

if (isset($page->data("commercialOld")->attr[8]) && $page->data("commercialOld")->attr[8]["header"] > "") echo '

	<!-- polygonal building image -->
	<a name="isotek7" id="isotek7_anchor" class="anchor"></a>

	<section id="isotek7">
		<div class="simple_right white_text">
			<div class="gray_block">
				<h2>'. $page->data("commercialOld")->attr[8]["header"] .'</h2>
				<div class="standout"></div>
				<div class="content">
					<p>'. $page->data("commercialOld")->attr[8]["content"] .'</p>
				</div>
			</div>
		</div>
	</section>';

echo '

	<!-- rebar image -->
	<a name="isotek4" id="isotek4_anchor" class="anchor"></a>

	<section id="isotek4">
		<div class="simple_left white_text">
			<div class="black_block">
				<h2>'. $page->data("commercialOld")->attr[4]["header"] .'</h2>
				<div class="standout"></div>
				<div class="content">
					<p>'. $page->data("commercialOld")->attr[4]["content"] .'</p>
				</div>
			</div>
		</div>
	</section>

	<a name="isotek5" id="isotek5_anchor" class="anchor"></a>

	<section id="isotek5">
		<div class="column full white_text">
			<div class="column_interior">
				<h2 class="sub">'. $page->data("commercialOld")->attr[5]["header"] .'</h2>
				<div class="tri_column left">
					<h4>'. $page->data("commercialOld")->attr[5]["subhead1"] .'</h4>
					<div class="content">'. $page->data("commercialOld")->attr[5]["content1"] .'</div>
				</div>
				<div class="tri_column middle">
					<h4>'. $page->data("commercialOld")->attr[5]["subhead2"] .'</h4>
					<div class="content">'. $page->data("commercialOld")->attr[5]["content2"] .'</div>
				</div>
				<div class="tri_column right">
					<h4>'. $page->data("commercialOld")->attr[5]["subhead3"] .'</h4>
					<div class="content">'. $page->data("commercialOld")->attr[5]["content3"] .'</div>
				</div>
			</div>
		</div>
	</section>

	<a name="gallery" id="gallery_anchor" class="anchor"></a>

	<section id="isotek_video" class="black black_bg">
		<div class="column left">
			<div class="column_interior">
				<h2>'. $page->data("commercialOld")->attr[6]["header"] .'</h2>
				<div class="content"><p>'. $page->data("commercialOld")->attr[6]["content"] .'</p></div>
			</div>
		</div>
		<div class="column right">
			<div class="youtube_video">
				<iframe src="'. $page->data("commercialOld")->attr[6]["link"] .'" width="640" height="480" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
			</div>
		</div>
		<div class="clear"></div>
	</section>

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
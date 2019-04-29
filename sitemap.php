<?php

/*
	<url>
		<loc>https://'.$page->host.'/</loc>
        <lastmod>2006-11-18</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
	</url>

*/
require_once("lib/page.class.php");
$page = new page();

$xml = (isset($_REQUEST["type"]) && $_REQUEST["type"] == "xml") ? true : false;

if ($xml) echo '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="https://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="https://www.sitemaps.org/schemas/sitemap/0.9 https://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

if ($xml) echo '
	<url>
		<loc>https://'.$page->host.'/</loc>
        <lastmod>'.date("Y-m-01").'</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
	</url>';

if (!$xml) echo $page->header("Site Map");

if (!$xml) echo '
			<div id="banner">
				<div class="video_wrapper">
					<img src="//assets.'.$page->host.'/img/collage-dark.jpg" />
				</div>
			</div>

			<section id="full_page_content" class="white">
				<div class="column full">
					<div class="column_interior">
						<h2>Site Map</h2>
						<ul class="sitemap">
							<li><a href="//'.$page->host.'/">Home</a></li>';

/* * * * * * . PRODUCTS . * * * * * */
if (!$xml) echo '
							<li>Products
								<ul>
									<li><a href="//'.$page->host.'/product-finder/">Product Finder</a></li>';
foreach ($page->data("products")->attr as $m => $p) {
	if ($xml) echo '
	<url>
		<loc>https://'.$page->host.'/product/'.preg_replace("/[^a-z0-9\-]+/", "-", strtolower($p["name"])).'/</loc>
        <lastmod>'.date("Y-m-d", strtotime($p["updated"])).'</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
	</url>';
	
	if (!$xml) echo '
									<li><a href="//'.$page->host.'/product/'.preg_replace("/[^a-z0-9\-]+/", "-", strtolower($p["name"])).'/">'.$p["name"]." ".$p["type"].'</a></li>';
}

if (!$xml) echo '
								</ul>
							</li>';

/* * * * * * . BROWSE . * * * * * */
if (!$xml) echo '
							<li><a href="//'.$page->host.'/browse/">Browse Products</a>
								<ul>';
if ($xml) echo '
	<url>
		<loc>https://'.$page->host.'/browse/</loc>
        <lastmod>'.date("Y-m-01").'</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.4</priority>
	</url>';
foreach ($page->data("productCategories")->attr as $key => $subCategory) {
	foreach ($subCategory as $c) {
		//$cat_list[strtolower($key)][] = $c["category"];
//foreach ($cat_list as $key => $cats) {
		if (strtolower($key) != "finish") {
			if ($xml) echo '
	<url>
		<loc>https://'.$page->host.'/browse/'.strtolower(urlencode($key)).'/'.urlencode(str_replace("/","-",strtolower($c["category"]))).'-sealers/</loc>
        <lastmod>'.date("Y-m-d", strtotime($c["updated"])).'</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.3</priority>
	</url>';
			if (!$xml) echo '
									<li><a href="//'.$page->host.'/browse/'.strtolower(urlencode($key)).'/'.urlencode(str_replace("/","-",strtolower($c["category"]))).'-sealers/">'.$key.": ".$c["category"].'</a></li>';
		}
	}
}
if (!$xml) echo '
								</ul>
							</li>';

/* * * * * * . GALLERY . * * * * * */
if (!$xml) echo '
							<li><a href="//'.$page->host.'/gallery/">Customer Gallery</a></li>
							<li><a href="//'.$page->host.'/commercial/gallery">Commercial Gallery</a></li>';
if ($xml) echo '
	<url>
		<loc>https://'.$page->host.'/gallery/</loc>
        <lastmod>'.date("Y-m-d").'</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.5</priority>
	</url>
	<url>
		<loc>https://'.$page->host.'/commercial/gallery/</loc>
        <lastmod>'.date("Y-m-d").'</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.5</priority>
	</url>';

/* * * * * * . COMMERCIAL/APPLICATORS/DISTRIBUTORS . * * * * * */
if (!$xml) echo '
							<li><a href="//'.$page->host.'/commercial/">Commercial Iso-Tek Overview</a></li>
							<li><a href="//'.$page->host.'/applicators/">Applicators</a></li>
							<li><a href="//'.$page->host.'/distributors/">Distributors</a></li>
							<li><a href="//'.$page->host.'/videos/">Videos</a></li>';
if ($xml) echo '
	<url>
		<loc>https://'.$page->host.'/commercial/</loc>
        <lastmod>'.date("Y-m-01").'</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.3</priority>
	</url>
	<url>
		<loc>https://'.$page->host.'/applicators/</loc>
        <lastmod>'.date("Y-m-01").'</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.1</priority>
	</url>
	<url>
		<loc>https://'.$page->host.'/distributors/</loc>
        <lastmod>'.date("Y-m-01").'</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.1</priority>
	</url>';

/* * * * * * . OTHER . * * * * * */
if (!$xml) echo '
							<li><a href="//'.$page->host.'/about/">About KreteTek Industries</a></li>
							<li><a href="//sds.ghostshield.com/">Material Safety Data Sheets</a></li>';
if ($xml) echo '
	<url>
		<loc>https://'.$page->host.'/about/</loc>
        <lastmod>'.date("Y-m-01").'</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.1</priority>
	</url>
	<url>
		<loc>https://'.$page->host.'sds.ghostshield.com/</loc>
        <lastmod>'.date("Y-m-01").'</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.1</priority>
	</url>';

/* * * * * * . ARTICLES . * * * * * */
if (!$xml) echo '
							<li><a href="//'.$page->host.'/articles/">Articles</a>
								<ul>';
if ($xml) echo '
	<url>
		<loc>https://'.$page->host.'/articles/</loc>
        <lastmod>'.date("Y-m-01").'</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.4</priority>
	</url>';
$articles = $page->articles();
foreach ($articles as $a) {
	if ($xml) echo '
	<url>
		<loc>https://'.$page->host.'/articles/'.$a["url"].'</loc>
        <lastmod>'.date("Y-m-d", strtotime($a["_modified"])).'</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.3</priority>
	</url>';
	
	if (!$xml) echo '
									<li><a href="//'.$page->host.'/articles/'.$a["url"].'">'.$a["title"].'</a></li>';
}
if (!$xml) echo '
								</ul>
							</li>';

if ($xml) echo '
</urlset>';

if (!$xml) echo '
							<li><a href="/upload-legal">Customer Uploads: Terms, Privacy, Rights</a></li>
							<li><a href="/privacy/">Privacy Policy</a></li>
							<li><a href="/warranty/">Warranty</a></li>
						</ul>
					</div>
				</div>
			</section>

			<div id="page_footer_before"></div>'.$page->footer();

?>
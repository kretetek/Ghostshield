<?php

require_once("lib/page.class.php");

$page = new page();

$uri = trim($_SERVER["REQUEST_URI"], "/");

if (isset($page->data("blog")->attr[$uri])) {

	$pc = $page->data("blog")->attr[$uri];
	$pc["content"] = $pc["article"];
	header($_SERVER["SERVER_PROTOCOL"]." 301 Moved Permanently"); 
	header("Location: http://".$page->host."/articles".$_SERVER["REQUEST_URI"]); 

} elseif (isset($page->data("pages")->attr[$uri])) {

	$pc = $page->data("pages")->attr[$uri];
	$page_title = $pc["title"];

} elseif (isset($page->data("pages")->attr["/".$uri])) {

	$pc = $page->data("pages")->attr["/".$uri];
	$page_title = $pc["title"];

} elseif (isset($page->data("pages")->attr["/".$uri."/"])) {

	$pc = $page->data("pages")->attr["/".$uri."/"];
	$page_title = $pc["title"];

} else {

	header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
	$page_title = "404";

}

//require_once("inc/header.php");
echo $page->header($page_title);

echo '
	<div id="banner">
		<div class="video_wrapper">
			<img src="//assets.ghostshield.com/img/bottle-bg/hydrophobic.jpg" />
		</div>
	</div>';
if (isset($pc)) {

	echo '
	<section id="full_page_content" class="white article">
		<div class="column full">
			<div class="column_interior">
				<h1 class="title">'.$pc["title"].'</h1>';

	if (isset($pc["published"])) echo '
				<p class="date"><i>Published '.date('l jS \of F Y h:i:s A', strtotime($pc["published"])).'</i></p>';

	echo '
				<p>'.$pc["content"].'</p>
			</div>
		</div>
	</section>';

} else {

	echo '
	<section id="full_page_content" class="white article">
		<div class="column full">
			<div class="column_interior">
				<h1 class="title">Error 404: Not Found</h1>
				<p class="date">The requested URL "<quote>'. $_SERVER['REQUEST_URI'] .'</quote>" was not found on this server.</p>
				<address></address>
			</div>
		</div>
	</section>';

}

echo '
	<div id="page_footer_before"></div>';

echo $page->footer();

?>
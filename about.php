<?php

require_once("lib/page.class.php");

$page = new page();

echo $page->header("About Company");
$page->setDescription("The story of KreteTek Industries, manufacturer of the Ghostshield line of concrete sealers, durable coatings, and related products.");

$square = $page->data("pages")->attr["/about"];

echo '

	<div id="banner">
		<div class="video_wrapper">';

$media = "//assets.".$page->host."/img/logo-sign.jpg";
if (isset($square["mediaLink"]) && strstr($square["mediaLink"],"vimeo") > -1) {
	$video_id = preg_replace("/https?\:\/\/vimeo.com\/([\d]+)\/.*/", "\\1", $square["mediaLink"]);
	$media = '<iframe src="https://player.vimeo.com/video/'.$video_id.'?color=ff9933&title=0&byline=0&portrait=0" width="700" height="400" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
} else if (isset($square["media"]) && strstr($square["media"]["type"],"video") > -1) {
	$media = '<video controls><source src="http:'.$square["media"]["file"].'" type="'.$square["media"]["type"].'" /> Your browser does not support this video.</video>';
} else {
	if (isset($square["media"]) && strstr($square["media"]["type"],"image") > -1) $media = 'http:'.$square["media"]["file"];
	$media = "//assets.".$page->host."/img/logo-sign.jpg";
	echo '<img src="'.$media.'" />';
}

echo '
		</div>
	</div>
	
	<section id="full_page_content" class="white">
		<div class="column full">
			<div class="column_interior">
				<h2 class="title">'.strip_tags($square["title"]).'</h2>
				<p>'.$square["content"].'</p>
			</div>
		</div>
	</section>

	<div id="page_footer_before"></div>';

//require_once("inc/footer.php");
echo $page->footer();

?>
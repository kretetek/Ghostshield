<?php

require_once("inc/header2.php");

$square = $squares["top-center"];

?>
	
	<div id="banner">
		<div class="video_wrapper">
			<?php

$media = "assets/img/g.jpg";
if (isset($square["mediaLink"]) && strstr($square["mediaLink"],"vimeo") > -1) {
	$video_id = preg_replace("/https?\:\/\/vimeo.com\/([\d]+)\/.*/","\\1",$square["mediaLink"]);
	$media = '<iframe src="https://player.vimeo.com/video/'.$video_id.'?color=ff9933&title=0&byline=0&portrait=0" width="700" height="400" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
} else if (isset($square["media"]) && strstr($square["media"]["type"],"video") > -1) {
	$media = '<video controls><source src="http:'.$square["media"]["file"].'" type="'.$square["media"]["type"].'" /> Your browser does not support this video.</video>';
} else {
	if (isset($square["media"]) && strstr($square["media"]["type"],"image") > -1) $media = 'http:'.$square["media"]["file"];
	echo '<img src="'.$media.'" />';
}

?>
		</div>
	</div>
	
	<section id="full_page_content" class="white">
		<div class="column full">
			<div class="column_interior">
				<h2 class="title"><?php echo $square["title"] ?></h2>
				<p><?php echo $square["blurb"] ?></p>
			</div>
		</div>
	</section>

<?php

require_once("inc/footer.php");

?>
<?php

$per = 3; // number of videos per line

require_once("lib/page.class.php");

$page = new page();

echo $page->header("Videos"); //require_once("inc/header.php");

$width = (1200 - (12 * ($per-1))) / $per;
$height = $width * 5/9;

/*
//open connection
$ch = curl_init();

$vimeo_client_id = "445c9aec4240baa6101c580bbbc8573ad29111da";
$vimeo_client_secret = "L/elHhrPHfnSHMfxdtl7+zFfJxWm3pZHeiMr0TtWp8njarxqLyS32E9YaPeTITkddzfw5X3DSJ+a2O50ALaKhU1C7xU4CkkYIHOUfwm0gBOLotWnNkzKQOUUvfIQl8bx";
$vimeo_access_token = "02c5eee918c6fb60c36faa7276eb1869"; // public

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

	$vimeo_channel = file_get_contents("https://api.vimeo.com/channels/1263592/videos?access_token=".$data->access_token);

	$channel = json_decode($vimeo_channel);
	
	foreach ($channel->data as $id => $video) {
	
		$videos[$id] = $video;
	
	}
	
}
*/


echo '
	<div id="banner">
		<div class="video_wrapper">
			<img src="//assets.'.$page->host.'/img/collage-dark.jpg" />
		</div>
	</div>
	
	<section id="full_page_content" class="white">
		<div class="column full">
			<div class="column_interior">
				<h2 class="title">Videos</h2>
				<div class="videos-container">';

foreach ($page->data("videos")->attr as $video) {

	if ($video->privacy->embed == "public") echo '
					<div class="video">
						<iframe src="https://player.vimeo.com'.str_replace("/videos", "/video", $video->uri).'"  frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen alt="'.str_replace('"', '&quot;', $video->description).'"></iframe>
						<div class="description">
							<h4>'.$video->name.'</h4>
							<p>'.nl2br($video->description).'</p>
						</div>
					</div>';
	// name, description, duration, width, height, embed->html, created_time, modified_time

}
//var_dump( $channel );

/*echo '
					<iframe src="https://player.vimeo.com/video/157637131" width="'.$width.'" height="'.$height.'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
					<iframe src="https://player.vimeo.com/video/157634863" width="'.$width.'" height="'.$height.'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
					<iframe src="https://player.vimeo.com/video/157631845" width="'.$width.'" height="'.$height.'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
*/
echo '
				</div>
			</div>
		</div>
	</section>

	<div id="page_footer_before"></div>';

echo $page->footer(); //require_once("inc/footer.php");

?>
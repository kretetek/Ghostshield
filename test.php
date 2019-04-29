<?php
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
}

var_dump($channel);

?>
<?php

/*

this file is out of control.
my intention is to merge it with the class in gallery_admin.php

*/

define("APP_PREFIX",	"gallery");
define("APP_PATH",		"/gallery/admin/");
define("LIB_DIR",		"../lib/");

//require_once("db.php");
//db::credentials("concrfe5_gs", "concrfe5_gs", "4EwgifF8aeP!");

require_once("lib/page.class.php");
$page = new page();
$page->addCSS("gallery_edit.css");
$page->addScript("gallery_edit.js");

//$dev = strpos($_SERVER["HTTP_HOST"],"dev.") > -1 ? true : false;

$admin_email = array("benjamin@ghostshield.com", "jeff@ghostshield.com");

if (isset($_REQUEST["e"]) && isset($_REQUEST["k"])){
	$secretHash = "25c6c7ff35b6979b151f2136cd13b0ff";
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	$e = $_REQUEST["e"];
	if ($_REQUEST["e-encoded"]) $e = urldecode($_REQUEST["e"]);
	$iv = substr($e, 0, $iv_size);
	$email = openssl_decrypt(substr($e, $iv_size), "AES-256-CBC", $secretHash, 0, $iv);
	$md5 = openssl_decrypt($_REQUEST["k"], "AES-256-CBC", $secretHash, 0, $iv);
	//db::debug();
	$upload = db::_select("gallery".($page->dev?"-dev":""), ["where" => ["md5" => $md5, "email" => $email]]);
}

if (!isset($_REQUEST["ajax"])) echo $page->header("Upload Management"); //require_once("inc/header.php");

if (isset($upload) && is_array($upload) && sizeof($upload) > 0) {
	
	$uploadfile = 'uploads/'.$upload[0]["product"].'/'.$upload[0]["md5"].'.'.$upload[0]["format"];

	if ($upload[0]["format"] == "png" || $upload[0]["format"] == "jpg" || $upload[0]["format"] == "bmp" || $upload[0]["format"] == "gif") {
		$filetype = "image";
	} elseif ($upload[0]["format"] == "mp4" || $upload[0]["format"] == "m4v" || $upload[0]["format"] == "mov") {
		$filetype = "video";
	}

	if (isset($_REQUEST["rotate"]) && $_REQUEST["rotate"] != $upload[0]["rotate"]) {

		$rotate = $_REQUEST["rotate"];
		if ($rotate == 360 || $rotate > 360) $rotate -= 360;

		db::_update("gallery".($page->dev?"-dev":""), $upload[0]["id"], ["rotate" => $rotate]);
		
		$oldrotate = $upload[0]["rotate"];
		$upload[0]["rotate"] = $rotate;

	}

	if (isset($_REQUEST["ajax"]) && isset($_POST) && sizeof($_POST) > 0) {

		if (!isset($_REQUEST["remove"])) {

			require_once("inc/gallery_edit_captions.php");

		} elseif (isset($_POST["remove"]) || $_REQUEST["remove"]) {

			if ($upload[0]["_approved"] == 0 || $upload[0]["_approved"] == 4) {

				$headers = 'From: Ghostshield <'.$_SERVER["SERVER_ADMIN"].'>'.
					"\nReply-To: ".$email.
					"\nMessage-ID: <".$md5."-admin-".microtime()."@".$_SERVER["SERVER_NAME"].">".
					"\nIn-Reply-To: <".$md5."-admin@".$_SERVER["SERVER_NAME"].">".
					"\nReferences: <".$md5."-admin@".$_SERVER["SERVER_NAME"].">".
					"\nMIME-Version: 1.0".
					"\nContent-Type:text/html;charset=\"iso-8859-1\"";

				$message = '
<p>This upload has been removed on '.date("F j, Y").' at '.date("H:i").' by uploader '.$email.'.</p>
<p><img src="http://'.$_SERVER["SERVER_NAME"].'/images/150x150-scale/'.$uploadfile.'?rotate='.$upload[0]["rotate"].'" style="margin:5px 0" /></p>';

				if (is_array($admin_email)) {
					foreach($admin_email as $ae) {
						mail( $ae, "Re: WWW Ghostshield Gallery Upload", $message, $headers );
					}
				} else mail( $admin_email, "Re: WWW Ghostshield Gallery Upload", $message, $headers );
			
				db::_update("gallery".($page->dev?"-dev":""), ["md5" => $md5, "email" => $email], ["_approved" => 4]);
				$response = ["removal_confirmation" => "The file has been removed.", "remove" => "true"];
				echo json_encode($response);
			
			} else {
			
				$headers = 'From: Ghostshield <'.$_SERVER["SERVER_ADMIN"].'>'.
					"\nReply-To: ".$email.
					"\nMessage-ID: <".$md5."-admin-".microtime()."@".$_SERVER["SERVER_NAME"].">".
					"\nIn-Reply-To: <".$md5."-admin@".$_SERVER["SERVER_NAME"].">".
					"\nReferences: <".$md5."-admin@".$_SERVER["SERVER_NAME"].">".
					"\nMIME-Version: 1.0".
					"\nContent-Type:text/html;charset=\"iso-8859-1\"";

				$message = '
<p>An upload removal request has been made on '.date("F j, Y").' at '.date("H:i").' by '.$email.':</p>
<p>'.strip_tags($_REQUEST["remove_comments"]).'</p>
<p><img src="http://'.$_SERVER["SERVER_NAME"].'/images/150x150-scale/'.$uploadfile.'?rotate='.$upload[0]["rotate"].'" style="margin:5px 0" /></p>
<p><a href="http://'.$_SERVER["SERVER_NAME"].'/gallery_admin.php?action=remove&id='.$upload[0]["id"].'&md5='.$md5.'">Remove the upload</a>?</p>';

				if (is_array($admin_email)) {
					foreach($admin_email as $ae) {
						mail( $ae, "Re: WWW Ghostshield Gallery Upload", $message, $headers );
					}
				} else mail( $admin_email, "Re: WWW Ghostshield Gallery Upload", $message, $headers );

				$response = ["removal_confirmation" => "Your request for removal has been received.</p><p>You will receive a response from us regarding your request."];

				echo json_encode($response);

			}

		} else echo json_encode(["error" => "No parameters."]);

	} else {

		if ($upload[0]["_approved"] < 4) {

			echo '
		<div id="banner">
			<div class="video_wrapper"><img src="/images/600x200-scalecrop-grayscale/'.$uploadfile.'?string='.$upload[0]["original"].'&rotate='.$upload[0]["rotate"].'" width="1200" height="400" class="dim" /></div>
		</div>
		
		<section class="gallery" id="gallery">
			<div class="content">
				<h2>Edit '.($filetype=="video"?"Video":"Photo").' Information</h2>
				<form method="post" action="/gallery/edit" id="gallery_captions">
					<div class="gallery_success">
						<div class="gallery_uploaded">';

			if ($filetype == "image") {
				echo '
							<img src="/images/400x300-scale/'.$uploadfile.'?rotate='.$upload[0]["rotate"].'" alt="uploaded image" />';

			} elseif ($filetype == "video") {
				echo '
							<video width="400" height="300" controls src="/'.$uploadfile.'" type="video/'.($upload[0]["format"] == "mov" ? "quicktime" : $upload[0]["format"]).'">
								<source src="/'.$uploadfile.'" type="video/'.$upload[0]["format"].'" />
								Your browser does not support the video tag.
							</video>';

			}

			echo '
							<div class="gallery_actions">';

			if (!isset($_REQUEST["remove"])) {
				if ($upload[0]["_approved"] == 1 || $upload[0]["_approved"] == 2) {
					echo '
								<a href="?e='.urlencode($e).'&k='.urlencode($_REQUEST["k"]).'&remove" id="request_removal" class="hex-button xxsmall r_'.$filetype.'">Request Removal</a>';

				} elseif ($upload[0]["_approved"] == 0) {
					echo '
								<a href="?e='.urlencode($e).'&k='.urlencode($_REQUEST["k"]).'&remove" id="request_removal" class="hex-button xxsmall r_'.$filetype.'">Delete</a>';

				}
			}

			if ($upload[0]["_approved"] == 0) echo '
								<a href="?e='.urlencode($e).'&k='.urlencode($_REQUEST["k"]).'&rotate='.($upload[0]["rotate"]+90).'" id="rotate" class="hex-button xxsmall r_'.$filetype.'">Rotate</a>';

			echo '
							</div>
						</div>
						<div class="gallery_caption_input">
							<input type="hidden" name="e-encoded" value="true" />
							<input type="hidden" name="e" value="'.urlencode($e).'" />
							<input type="hidden" name="k" value="'.$_REQUEST["k"].'" />
							<input type="hidden" name="upload_email" value="'.$upload[0]["email"].'" />';

			if (!isset($_REQUEST["remove"])) echo '
							<div><label for="upload_credit-'.$upload[0]["md5"].'">'.($filetype=="video"?"Video":"Photo").' Credit:</label>	<input type="text" id="upload_credit-'.$upload[0]["md5"].'" name="upload_credit-'.$upload[0]["md5"].'" value="'.$upload[0]["credit_edit"].'" /></div>
							<div><label for="upload_location-'.$upload[0]["md5"].'">'.($filetype=="video"?"Video":"Photo").' Location:</label>	<input type="text" id="upload_location-'.$upload[0]["md5"].'" name="upload_location-'.$upload[0]["md5"].'" value="'.$upload[0]["location_edit"].'" /></div>
							<div><label for="upload_caption-'.$upload[0]["md5"].'">'.($filetype=="video"?"Video":"Photo").' Caption:</label>	<textarea id="upload_caption-'.$upload[0]["md5"].'" name="upload_caption-'.$upload[0]["md5"].'">'.$upload[0]["caption_edit"].'</textarea></div>
							<div id="gallery_save" class="active">
								<input type="submit" value="Save Information" class="hex-button xsmall" id="gallery_save_captions" />
							</div>';

			if (isset($_REQUEST["remove"])) {
				if ($upload[0]["_approved"] == 0 || $upload[0]["_approved"] == 4) {
					echo '
							<p>This upload is eligible for immediate removal. Are you sure you wish to remove it?</p>
							<input type="hidden" name="remove" value="true" />
							<input type="submit" value="Confirm Removal" class="hex-button xsmall" />';

				} else {
					echo '
							<p>We\'re sorry you feel that way! While we reserve the right to deny the removal of uploaded files from the site, we take all requests seriously.</p>
							<p>Please feel free to comment on your request for the removal of this '.$filetype.':</p>
							<div><textarea name="remove_comments"></textarea></div>
							<input type="hidden" name="remove" value="true" />
							<input type="submit" value="Confirm Removal Request" class="hex-button xsmall" />';

				}
			}

			echo '
						</div>
					</div>
					<div class="clear"></div>
				</form>
			</div>
		</section>';

		} else {

			echo '
		<div id="banner">
			<div class="video_wrapper"><img src="//assets.'.$_SERVER["SERVER_NAME"].'/img/bottle-bg/hydrophobic.jpg" /></div>
		</div>

		<section>
			<div class="column full">
				<div class="column_interior">
					<h2>Upload Removed</h2>
					<p>This upload was removed '.date("F j, Y").' at '.date("H:i").' by '.($upload[0]["_approved"] == 4 ? 'the uploader' : "an administrator").'.</p>
					<p>If you have questions about this, please use the contact form below.</p>
				</div>
			</div>
		</section>';

		}

	}

} else {

	echo '
		<div id="banner">
			<div class="video_wrapper"><img src="//assets.'.$_SERVER["SERVER_NAME"].'/img/bottle-bg/hydrophobic.jpg" /></div>
		</div>
	
		<section>
			<div class="column full">
				<div class="column_interior">
					<h2>Nothing to see here.</h2>
					<p>Move along, folks.</p>
				</div>
			</div>
		</section>';

}

if (!isset($_REQUEST["ajax"])) echo $page->footer(); //require_once("inc/footer.php");

?>
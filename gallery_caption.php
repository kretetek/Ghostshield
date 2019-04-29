<?php

require_once("db.php");
db::credentials("concrfe5_gs", "concrfe5_gs", "4EwgifF8aeP!");

if (isset($_REQUEST["model"]) && is_numeric($_REQUEST["model"]) && $_REQUEST["model"] > "") {
	$model = $_REQUEST["model"];
} else $model = "9500";

if (isset($_REQUEST["id"])) $upload = db::_select("gallery", {"file" => "uploads/".$_REQUEST["file_id"]});

require_once("inc/header.php");

echo '
	<div id="banner">
		<div class="video_wrapper"><img src="assets/img/bottle-bg/none3-lighter.jpg" /></div>
	</div>';

if ( isset($_REQUEST["action"]) && isset($_REQUEST["md5"]) && isset($_REQUEST["id"]) && is_array($upload) ) {

	$id = $_REQUEST["id"];
	$action = $_REQUEST["action"];
	$md5 = $_REQUEST["md5"];
	
	db::_update("gallery", $id, ["_approved" => ($action=="approve"?1:2)]);
	
	echo '
	<section class="gallery">
		<div class="content">
			<h2>Are you sure?</h2>
			<p>You just '.( $action=="deny" ? "denied" : "approved" ).' the following image. Would you like to <a href="?id='.$id.'&md5='.$md5.'&action='.( $action=="deny" ? "approve" : "deny" ).'">undo this action</a>?</p>
			<img src="'.$upload[0]["file"].'" />
		</div>
	</section>';
	
	

} else {

	echo '
	<section>
		<div class="content">
			<h2>Nothing to see here.</h2>
			<p>Move along, folks.</p>
		</div>
	</section>';

}


if (isset($_FILES['file'])) {
	
	$semi_rand = md5(time());
	$fileatt_type = mime_content_type($_FILES['file']['tmp_name']);
	$extension = preg_replace("/.+?\//", "", $fileatt_type);

	if (!is_dir('uploads/'.$model)) mkdir('uploads/'.$model);
	$uploaddir = 'uploads/'.$model.'/';

	//$uploadfile = $uploaddir . basename($_FILES['file']['name']);
	$uploadfile = $uploaddir . $semi_rand . "." . str_replace("jpeg", "jpg", $extension);

	if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {

		echo "File is valid, and was successfully uploaded.\n";

		$upload_id = db::_insert("gallery", ["product" => $model, "file" => $uploadfile, "md5" => $semi_rand]);

		$image = file_get_contents('./'.$uploadfile, true);

		$mime_boundary = "MIME_Boundary_{$semi_rand}";

		$headers = 'From: '.$_SERVER["SERVER_ADMIN"]."\nMessage-ID: <".$semi_rand."@".$_SERVER["SERVER_NAME"].">"."\nMIME-Version: 1.0\n".
		"Content-Type: multipart/mixed; ". "boundary=\"{$mime_boundary}\"";

		$message = "<p>The following image was uploaded on ".date("F j, Y")." at ".date("H:i").'.</p><p><a href="http://'.$_SERVER["SERVER_NAME"].'/gallery_admin.php?action=approve&id='.$upload_id.'&md5='.$semi_rand.'">Approve</a> or <a href="http://'.$_SERVER["SERVER_NAME"].'/gallery_admin.php?action=deny&id='.$upload_id.'&md5='.$semi_rand.'">Deny</a></p>';

		$email_message = "This is a multi-part message in MIME format.\n\n".
			"--{$mime_boundary}\n".
			"Content-Type:text/html;charset=\"iso-8859-1\"\n".
			"Content-Transfer-Encoding:7bit\n\n".$message."\n\n";

		//foreach ($this->labels as $name=>$image) {
		$data = chunk_split(base64_encode($image));
		$email_message .= "--{$mime_boundary}\n".
		"Content-Type:{$fileatt_type}; ".
		"name=\"{".$_FILES['file']['name']."}\"\n";
		// if(get_option('wi_email_sendasattachment')==true){
		// $email_message.="Content-Disposition:attachment;\n".
		// "	filename=\"{$name}\"\n";
		// }
		$email_message .= "Content-Transfer-Encoding:base64\n\n".$data."\n\n";
		//}
		
		$email_message .= "--{$mime_boundary}--\n";



		mail( $admin_email, "WWW Ghostshield Gallery Upload", $email_message, $headers );
		
	
	} else {
	
		echo "Possible file upload attack!\n";
	
	}

	echo 'Here is some more debugging info:';
	print_r($_FILES);

}

/*foreach ($_FILES["pictures"]["error"] as $key => $error) {
    if ($error == UPLOAD_ERR_OK) {
        $tmp_name = $_FILES["pictures"]["tmp_name"][$key];
        // basename() may prevent filesystem traversal attacks;
        // further validation/sanitation of the filename may be appropriate
        $name = basename($_FILES["pictures"]["name"][$key]);
        move_uploaded_file($tmp_name, "data/$name");
    }
}*/

echo '
	</section>';

require_once("inc/footer.php");

?>
<?php

require_once("lib/page.class.php");
$page = new page();

if (!isset($_REQUEST["ajax"])) {

	//require_once("inc/header.php");
	echo $page->header("Contact");

	echo '
	<div id="banner">
		
	</div>
	
	<section id="content" class="white">
		<div class="column full">
			<div class="column_interior">
				<h2>Contact Us</h2>';

}

if (isset($_REQUEST["contact_email"]) &&
	filter_var($_REQUEST["contact_email"], FILTER_VALIDATE_EMAIL)) {
	
	$email = $_REQUEST["contact_email"];
	
	if (isset($_REQUEST["contact_name"])) {
	
		$name = $_REQUEST["contact_name"];
	
	} else $name = "Anonymous";
	
	
	if (isset($_REQUEST["contact_comment"])) {
	
		$comment = $_REQUEST["contact_comment"];
	
	} else $comment = "[Empty message]";
	
	$message = $comment. "\n\n".
		"Name: ".stripslashes($name). "\n".
		"Email: ".$email ."\n\n".
		"IP Address: ". $_SERVER['REMOTE_ADDR']."\n".
		"Location: ".(isset($_SERVER["HTTP_REFERER"])?$_SERVER["HTTP_REFERER"]:"ghostshield.com")."\n".
		"Browser: ".$_SERVER["HTTP_USER_AGENT"];
	
	$html = '<p><b>From</b>: '.stripslashes($name).' &lt;'.$email.'&gt;</p>';
	
	$subject = "Web Contact: ".$name;
	
	foreach ($_REQUEST as $k => $item) {

		if (stripos($k, "contact_") !== false && $k != "contact_email" && $k != "contact_name" && $k != "contact_comment") {
			
			$key = ucwords(str_replace("_", " ", str_replace("contact_", "", $k)));
			$message .= "\n" . $key . ': ' . stripslashes($item);
			$html .= '
				<p><b>'. $key .'</b>: '. stripslashes($item) .'</p>';
			
			if ($k == "contact_page") {
				$subject = "Web ".ucwords($item)." Contact: ".$name;
			}
		
		}
	
	}
	
	$html .= '
				<p>'.$comment.'</p>';
	
	if (mail("support@ghostshield.com", $subject, $message, "From: ".$email)) { // , "From: ".$email
	
		echo '
				<p>Thank you. We will do our best to reply to your inquiry within one business day.</p>
				'.$html;

	} else {
	
		echo '
				<!--ERROR-->
				<p class="contact_error"><b>Error</b>: Your message did not reach us!</p>';
	
	}

} else {
	
	echo '
				<!--ERROR-->
				<p class="contact_error"><b>Error</b>: Invalid email.</p>';

}


if (!isset($_REQUEST["ajax"])) {

	echo '
			</div>
		</div>
	</section>';
	
	//require_once("inc/footer.php");
	echo $page->footer();

}

?>
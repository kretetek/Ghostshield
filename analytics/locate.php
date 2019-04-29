<?php

$dev = strpos($_SERVER["HTTP_HOST"], "dev.") > -1 ? true : false;

if (!$dev) {
	require_once("../../lib/sys/db.php");
	db::credentials("concrfe5_gs", "concrfe5_gs", "4EwgifF8aeP!");
	session_start();

	if (isset($_SESSION["gsa-id"]) && is_numeric($_SESSION["gsa-id"])) {
		$ip = $_SERVER['REMOTE_ADDR'];
		$details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
		db::_update("analytics", $_SESSION["gsa-id"], [
			"city" => $details->city,
			"region" => $details->region,
			"country" => $details->country
		]);
	}
}

?>
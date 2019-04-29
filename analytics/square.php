<?php

$dev = strpos($_SERVER["HTTP_HOST"], "dev.") > -1 ? true : false;

if (!$dev) {
	require_once("../../lib/sys/db.php");
	db::credentials("concrfe5_gs", "concrfe5_gs", "4EwgifF8aeP!");
	session_start();
	
	$sid = db::_insert("analytics", [
		"ip"		=> $_SERVER["REMOTE_ADDR"],
		"url"		=> isset($_REQUEST["square"]) ? $_REQUEST["square"]."?square" : $_SERVER["REQUEST_URI"],
		"agent"		=> $_SERVER["HTTP_USER_AGENT"],
		"referrer"	=> $_SERVER["HTTP_REFERER"],
		"location"	=> gethostbyaddr($_SERVER["REMOTE_ADDR"]),
		"origin"	=> isset($_SESSION["gsa-id"]) ? $_SESSION["gsa-id"] : "null",
		"bot"		=> stristr($_SERVER["HTTP_USER_AGENT"], "bot") > -1 ? 1 : 0
	]);
}

?>
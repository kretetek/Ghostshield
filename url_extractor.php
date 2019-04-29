<?php

require_once("../lib/sys/db.php");
db::credentials("concrfe5_gs", "concrfe5_gs", "4EwgifF8aeP!");
db::debug();
echo '<pre>';
$a = microtime();
error_reporting(E_ALL);
ini_set('display_errors','On');
ini_set('display_startup_errors','On');
//    require_once("../../lib/sys/db.php");
    
//    $conn = mysqli_connect("localhost", "concrfe5_dpadmin", "$!l0XA-tek", "concrfe5_distributors");
//    if (mysqli_connect_error()) {
//        die ("Database Connection Error");
//    }

//    $query = "DELETE FROM `page_search`;";
//    mysqli_query($conn, $query);

unlink(__DIR__."/lib/website.txt");
unlink(__DIR__."/lib/serialized_dictionary.txt");
$fp = fopen(__DIR__."/lib/website.txt","a");

$parsed_xml = simplexml_load_file('https://'.( strpos($_SERVER["HTTP_HOST"], "dev.") > -1 ? 'dev.' : '' ).'ghostshield.com/sitemap.xml') or die("Fatal error: cannot create XML object.");

$json = json_encode( $parsed_xml );
$xml_array = json_decode( $json, true );

$url_array = array();
foreach ($xml_array as $array_item) {
	foreach ($array_item as $list) {
		$url_array[] = array('url' => $list['loc']);
	}
}

for ($i = 0; $i < count($url_array); $i++) {
	if ($html = file_get_contents($url_array[$i]['url'])) {
		$dom = new DOMDocument;
		if (@$dom->loadHTML($html)) {
			$raw_title = $dom->getElementsByTagName("title");
			if($raw_title->length > 0) {
				$title = $raw_title->item(0)->nodeValue;
				$stripped_title = strstr($title, ' // Ghostshield®', true);
			}
			$meta = $dom->getElementsByTagName("meta");
			$classname = "page_content";
			$xpath = new DomXPath($dom);
			$raw_body = $xpath->query('//div[contains(@class, "'.$classname.'")]'); // instance of DOMNodeList
			$description = $xpath->evaluate('string(//meta[@name="description"]/@content)');
			if ($raw_body->length > 0) {
				$body = $raw_body->item(0)->nodeValue;
				$body = strstr($body, $stripped_title);
				$body = str_replace($stripped_title, "", $body);
				if (strpos($body, 'Article Search ') !== false) {
					$body = str_replace("Article Search ", "", $body);
				}
			}
		}
	}

	$query = "INSERT INTO `search_index` (`id`, `url`, `title`, `content`, `description`) VALUES ('".$i."', '".db::_cleanse($url_array[$i]['url'])."', '".db::_cleanse($stripped_title)."', '".db::_cleanse($body)."', '".db::_cleanse($description)."') ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), description = VALUES(description);";

	db::_query($query);
	
	fwrite($fp,$stripped_title."\n\n".$body."\n\n\n\n");

	echo 'INSERT #'.$i.": ".$stripped_title."\n";
}
fclose($fp);
$b = microtime();

echo 'INSERT ELAPSED TIME: '.date("H:i:s", $b-$a);

?>

<?php

require_once("lib/page.class.php");

$page = new page();
require_once("lib/gallery.class.php");

db::credentials("concrfe5_gs", "concrfe5_gs", "4EwgifF8aeP!");

require_once('lib/spellcorrector.php');

if (isset($_REQUEST["search"])) {
	$oldstr = $_REQUEST["search"];
	$str = explode(" ",$oldstr);
	for ($i=0;$i<count($str);$i++) {
		$str[$i] = SpellCorrector::correct($str[$i]);
	}
	$newstr = implode(" ",$str);
	$searchstring = $oldstr;
	if ($oldstr != $newstr) {
		if (isset($_REQUEST["force"])) {
			$result_text = '<div class="search_result_string">Showing results for <a href="/search/forced/'.urlencode($oldstr).'"><em>'.$oldstr.'</em></a><br /><span>Did you mean "<a href="/search/'.urlencode($newstr).'"><em>'.$newstr.'</em></a>"?</span></div>';
		} else {
			$searchstring = $newstr;
			$result_text = '<div class="search_result_string">Showing results for <a href="/search/'.urlencode($newstr).'"><em>'.$newstr.'</em></a><br /><span>Search instead for <a href="/search/forced/'.urlencode($oldstr).'"><em>'.$oldstr.'</em></a></span></div>';
		}
	}else $result_text = '<div class="search_result_string">Showing results for <em>'.$oldstr.'</em></div>';
}

echo $page->header("Search: &quot;".$searchstring."&quot;");

echo '
	<div id="site-search" style="background-image:url(//assets.'.$page->host.'/img/bg/hydrophobic-concrete-3.jpg)">
		<form method="GET" action="/search/" class="ignore site_search_form">
			<h3>Site Search</h3>
			<input type="text" name="search" value="'.(isset($searchstring)?$searchstring:'').'" /> <input type="submit" class="search_small" value="Go" />
		</form>
	</div>';
	echo '
	
	
	<section id="full_page_content" class="white article">
		<div class="column full">
			<div class="column_interior">';



/*$conn = mysqli_connect("localhost", "concrfe5_dpadmin", "$!l0XA-tek", "concrfe5_distributors");
if (mysqli_connect_error()) {
	die ("Database Connection Error");
}*/

$searchstring = db::_cleanse($searchstring);

/* Capture Search Analytics */
// Get IP
//whether ip is from share internet
if (!empty($_SERVER['HTTP_CLIENT_IP']))   
  {
    $ip_address = $_SERVER['HTTP_CLIENT_IP'];
  }
//whether ip is from proxy
elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))  
  {
    $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
  }
//whether ip is from remote address
else
  {
    $ip_address = $_SERVER['REMOTE_ADDR'];
  }

// Get refferrer url if exists
if  (!empty($_SERVER['HTTP_REFERER'])) {
    $referrer = $_SERVER['HTTP_REFERER'];
} else {
    $referrer = '';
}

/* Check database for most recent search term to compare */
$query = "SELECT count(*) AS count FROM `search_analytics` 
WHERE `ip` = '".$ip_address."'
AND `search_term` = '".$searchstring."'
AND `_created` >= DATE_SUB(NOW(), INTERVAL 1 MINUTE);";
    
$result = db::_query($query);

/* Disallow entry into database if same search happens within the same minute */
if ($result[0]['count'] == 0) {
    db::_insert('search_analytics', ['ip'=>$ip_address, 'search_term'=>$searchstring, 'referrer'=>$referrer]);
}
/* End Search Analytics */

$query = "SELECT * FROM `search_index` WHERE `site` = '".($page->dev?"dev":"pub")."' AND (`title` LIKE '%".$searchstring."%' OR `content` LIKE '%".$searchstring."%')
	ORDER BY CASE
		WHEN `title` = '".$searchstring."' THEN 1
        WHEN `title` LIKE '".$searchstring."%' THEN 2
        WHEN `title` LIKE '%".$searchstring."%' THEN 3
		WHEN `title` LIKE '%".$searchstring."' THEN 4
        WHEN `content` LIKE '".$searchstring."%' THEN 5
		WHEN `content` LIKE '%".$searchstring."%' THEN 6
        WHEN `content` LIKE '%".$searchstring."%' THEN 7
	END;";
    
$result = db::_query($query);

//$result = mysqli_query($conn, $query);

//echo '<div class="container">';
echo $result_text;

if(count($result) !== 0){
	echo '
				<ul class="search_results">';
	foreach ($result as $row) {
        $pattern = '/('.$searchstring.')/i';
        $replacement = '<strong>'.$searchstring.'</strong>';
        echo '
					<li>
						<h4><a class="search_link" href="'.$row['url'].'">'.$row['title'].'</a></h4>
						<span class="link">'.str_replace("https://ghostshield.com/","/",$row['url']).'</span>
						<div class="description">'.preg_replace($pattern, $replacement, $row["description"]).'</div>
					</li>';
	}
	echo '
				</ul>';

}else{
	echo '
				<div>
					<h4>No matches for "'.$searchstring.'". Try a different term.</h4>
				</div>';

}
echo '
			</div>
		</div>
	</section>';

echo '
	<div id="page_footer_before"></div>';
echo $page->footer();
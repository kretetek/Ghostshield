<?php

$per_page = 10;
$words_in_summary = 100;
if (isset($_REQUEST["all"])) {
	$per_page = 1000;
	$words_in_summary = 0;
}
$tag_list_limit = 10;

require_once("lib/page.class.php");

$page = new page();
$page->addCSS("linklist.css");
$page->addCSS("product-finder.css");
$page->setDescription("Articles, tutorials and helpful tips about sealing your concrete patio, driveway, garage, basement, fountain, and more.");

$tag = null;
$topic = null;
$search = null;
$article_ids = null;
$article = null;

if (isset($_REQUEST["article"])) {
	
	$article = $_REQUEST["article"];

	if ($page->article(trim($article, "/"))) {

		$page->addBodyAttr('itemscope itemtype="http://schema.org/Article"');
		$page->meta("type", "article");
		$page->meta("fb:admins", "556289717749690");

		$pc = $page->article(trim($article, "/"));
		$page_title = $pc["title"];
		$page->setDescription(substr(strip_tags($pc["article"]), 0, strpos(wordwrap(strip_tags($pc["article"]), 120), "\n"))."...");

	} else $page_title = "Articles";

} elseif (isset($_REQUEST["search"])) {
	
	$search = strtolower($_REQUEST["search"]);

	$article_ids_1 = db::_query("SELECT id as post_id FROM `blog` WHERE `site`='".$page->env."' AND MATCH(`title`,`article`) AGAINST('".db::_cleanse(str_replace(" ", "* ", $search))."*' IN BOOLEAN MODE)");
	
	$article_ids_f = array_merge($article_ids_1, db::_query("SELECT id as post_id FROM `blog` WHERE `site`='".$page->env."' AND `article` LIKE('%".db::_cleanse($search)."%')"));

	//$article_ids = db::_query("SELECT id as post_id, MATCH(`article`, `title`) AGAINST('".db::_cleanse($search)."' IN BOOLEAN MODE) as score FROM `blog` WHERE `site`='".$page->env."' AND MATCH(`article`, `title`) AGAINST('".db::_cleanse($search)."' IN BOOLEAN MODE) ORDER BY score DESC");
	
	foreach ($article_ids_f as $id) {
		$article_ids[$id["post_id"]] = $id;
	}
	//$article_ids = array_unique($article_ids_2);
	//var_dump($article_ids);
	$page_title = 'Article Search: '.ucwords($search);

} elseif (isset($_REQUEST["topic"])) {
	
	$topic = strtolower($_REQUEST["topic"]);
	$article_ids = db::_select("blog_topics", ["where" => ["topic" => $topic]]);
	$page_title = 'Article Topic: '.ucwords($topic);
	
} elseif (isset($_REQUEST["tag"])) {
	
	#$tag = strtolower($_REQUEST["tag"]);
	#$article_ids = db::_select("blog_tags", ["where" => ["tag" => $tag]]);
	#$page_title = 'Articles Tagged "'.ucwords($tag).'"';

	header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
	$page_title = "404: Page Not Found";

} else $page_title = "Articles";

echo $page->header($page_title);

echo '
			<div id="article-search" style="background-image:url(//assets.'.$page->host.'/img/bg/hydrophobic-concrete-3.jpg)">
				<form method="GET" action="/articles/" class="ignore">
					<h3>Article Search</h3>
					<input type="text" name="search" value="'.(isset($search)?$search:'').'" /> <input type="submit" class="search_small" value="Go" />
				</form>
			</div>';

#$dtopics = db::_query("SELECT DISTINCT `topic` FROM `blog_topics` WHERE `site` = '".$page->env."' ORDER BY `topic` ASC");
#$dtags = db::_query("SELECT `tag`, COUNT(*) AS num FROM `blog_tags` WHERE `site` = '".$page->env."' GROUP BY `tag` ORDER BY COUNT(*) DESC LIMIT ".$tag_list_limit);

if (isset($pc)) {

	#$tags = db::_select("blog_tags", ["where" => ["post_id" => $pc["id"]]]);
	
	#$tag_html = "";
	#foreach ($tags as $tag) {
	#	if ($tag_html > "") $tag_html .= ", ";
	#	$tag_html .= '<a href="/articles/tag/'.strtolower(str_replace(" ", "+", $tag["tag"])).'">'.$tag["tag"].'</a>';
	#}
	
	#$topics = db::_select("blog_topics", ["where" => ["post_id" => $pc["id"]]]);
	
	#$topic_html = "";
	#foreach ($topics as $topic) {
	#	if ($topic_html > "") $topic_html .= ", ";
	#	$topic_html .= '<a href="/articles/topic/'.strtolower($topic["topic"]).'">'.$topic["topic"].'</a>';
	#}
	
	$products = db::_select("blog_products", ["where" => ["post_id" => $pc["id"]]]);
	
	$similar = db::_query('SELECT a1.id, GROUP_CONCAT(DISTINCT a2.id, "|", a2.url, "|", a2.title separator "||") AS related_articles
		FROM blog AS a1
		JOIN blog_tags AS t1 ON a1.id = t1.post_id
		JOIN blog_tags AS t2 ON t2.tag = t1.tag AND t2.post_id != t1.post_id
		JOIN blog AS a2 ON a2.id = t2.post_id
		WHERE a1.id = '.$pc["id"].'
		GROUP BY a1.id');
	
	$similar_html = "";
	if (sizeof($similar) > 0) {
		$similar_articles = explode("||", $similar[0]['related_articles']);
		$scount = 0;
		foreach ($similar_articles as $sarticle) {
			list($sa_id, $sa_url, $sa_title) = explode("|", $sarticle);
			if ($sa_url != $pc["url"]) {
				$similar_html .= '
							<li><a href="/articles/'.$sa_url.'">'.$sa_title.'</a></li>';
				$scount++;
			}
			if ($scount > 4) break;
		}
	}

	echo '
			<section id="full_page_content" class="white">
		<div class="column full">
				<div class="noprimary article nocolumn noleft">
					<div class="column_interior">
						<h1 class="title" itemprop="headline"><span itemprop="mainEntityOfPage">'.$pc["title"].'</span></h1>
						'.$page->meta("name", $pc["title"], true).'
						'.$page->meta("wordCount", str_word_count(strip_tags($pc["article"])), true).'
						'.$page->meta("sourceOrganization", "KreteTek Industries, Inc.", true).'
						'.$page->meta("author", "Ghostshield.com", true).'
						'.$page->meta("dateCreated", $pc["_created"], true).'
						'.$page->meta("datePublished", $pc["_created"], true).'
						'.$page->meta("dateModified", $pc["_modified"], true);

	echo '
						<div class="article_content article_hasimage_'.$pc["image_dim"].'">
							<div class="article_text" itemprop="articleBody">';
	if ($pc["summary"] > "") echo '
								<div class="summary">'.$pc["summary"].'</div>';

	if ($pc["image"]) {
		echo '
							<div class="article_image '.$pc["image_dim"].'" itemprop="image" itemscope itemtype="http://schema.org/ImageObject">
								<img itemprop="url" src="/images/'.$pc["image_resize"]."-scale/".str_replace("//images.ctfassets.net/", "cms".($pc["image_wm"] ? "wm" : "").":", $pc["image"]).'" width="400" class="'.($pc["image_dim"] == 'landscape' ? 'valign' : '').'" alt="'.str_replace('"', '&quot;', strip_tags($pc["title"])).'" />
								<meta itemprop="width" content="'.(preg_replace("/x[0-9]+/", "", $pc["image_size"])).'">
								<meta itemprop="height" content="'.(preg_replace("/[0-9]+x/", "", $pc["image_size"])).'">
							</div>';
	} else echo '
							<div class="hidden_article_image '.$pc["image_dim"].'" itemprop="image" itemscope itemtype="http://schema.org/ImageObject">
								<img itemprop="url" src="https://assets.ghostshield.com/img/bg/water-droplets-small.jpg" width="400" class="valign" alt="" />
								<meta itemprop="width" content="1529">
								<meta itemprop="height" content="1133">
							</div>';//$page->meta("image", "http://assets.ghostshield.com/img/bg/water-droplets-small.jpg", true);
	
	$article_text = $pc["article"];
	
	
	// process article
/*	foreach($products as $product) {
		$data = $page->data("productsById")->attr[$product["product"]];
		
		if(preg_match("/\=\=".$data["model"]."\=\=/", $article_text)) {
			$name = str_replace(" ", "-", strtolower($data["name"]));
			$replacement = '
						<div class="article_products">
							<a href="/product/'.$name.'" class="product">
								<img src="//assets.'.$page->host.'/img/bottle/angled/'.$data["model"].'.png" height="120" />
								<h3 class="product_name selected '.$name.'">'.$data["name"].'</h3>
								<p>'.$data["simple"].'</p>
								<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"  viewBox="0 0 17.3 25.7" style="enable-background:new 0 0 17.3 25.7;" xml:space="preserve">
									<polygon class="st0" points="0,24.6 16,12.9 0,1.1" />
								</svg>
							</a>
						</div>';
			$article_text = preg_replace("/\=\=".$data["model"]."\=\=/", $replacement, $article_text);
		}
	
	}*/

	if (preg_match("/\=\=tip1\=\=/i", $article_text)) {
		$replacement = '<div id="quickTip1" class="quickTip"><div>'.$pc["quickTip"].'</div></div>';
		$article_text = preg_replace("/\=\=tip1\=\=/i", $replacement, $article_text);
	}

	if (preg_match("/\=\=tip2\=\=/i", $article_text)) {
		$replacement = '<div id="quickTip2" class="quickTip"><div>'.$pc["quickTip2"].'</div></div>';
		$article_text = preg_replace("/\=\=tip2\=\=/i", $replacement, $article_text);
	}

	// end processing
	
	echo '
								'.$article_text;
	if (isset($pc["whyGhostshield"]) && $pc["whyGhostshield"] > "") echo '
								<h2>Why Ghostshield?</h2>
								'.$pc["whyGhostshield"];
	echo '
							</div>
							<div class="article_products">';

	if (sizeof($products) > 0) {
		foreach($products as $product) {
			$data = $page->data("productsById")->attr[$product["product"]];
			$name = str_replace(" ", "-", strtolower($data["name"]));
			echo '
								<a href="/product/'.$name.'" class="product">
									<img src="//assets.'.$page->host.'/img/bottle/angled/'.$data["model"].'.png" height="120" />
									<h3 class="product_name selected '.$name.'">'.$data["name"].'</h3>
									<p>'.$data["simple"].'</p>
									<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"  viewBox="0 0 17.3 25.7" style="enable-background:new 0 0 17.3 25.7;" xml:space="preserve">
										<polygon class="st0" points="0,24.6 16,12.9 0,1.1" />
									</svg>
								</a>';
		}
	}

	echo '
							</div>
						</div>
						<p class="date">Published '.date('l jS \of F Y', strtotime($pc["_created"])).($pc["_modified"] != $pc["_created"] ? ' <span>//</span> Updated '.date('l jS \of F Y', strtotime($pc["_modified"])) : '').'</p>';
	/*if ($topic_html > "") echo '
						<p class="topics">Topics: '.$topic_html.'</p>';
	if ($tag_html > "") echo '
						<p class="tags">Tags: '.$tag_html.'</p>';*/
	echo '
						<div class="publisher" itemtype="https://schema.org/Organization" itemscope="itemscope" itemprop="publisher">
							<span itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
								<meta itemprop="url" content="https://assets.ghostshield.com/img/logo/ghostshield-g-orange.png">
								<meta itemprop="width" content="420">
								<meta itemprop="height" content="420">
							</span>
							<link itemprop="url" href="https://ghostshield.com/" /> 
							<!--span itemprop="name">KreteTek Industries, Inc.</span> <span class="delimiter">//</span> <a href="https://www.facebook.com/GhostshieldSealers" target="_blank" itemprop="sameAs">Facebook</a> <span class="delimiter">//</span> <a href="https://www.twitter.com/kretetek" target="_blank" itemprop="sameAs">Twitter</a> <span class="delimiter">//</span> <a href="https://plus.google.com/+Ghostshield" target="_blank" itemprop="sameAs" rel="publisher">Google+</a-->
						</div>
						<div class="article_footer"><em><a href="/articles/"'.(!$page->fresh ? ' class="back_link"' : '').'>&#8592; Return to Ghostshield Articles</a></em></div>
					</div>
				</div>
				</div>
				<!--div class="column secondary right"-->';
	//var_dump($page->data("glossary")->attr["etching"]);
/*	if ($similar_html > "") echo '
					<div class="link_list">
						<h3>Related Articles</h3>
						<ul>'.$similar_html.'
						</ul>
					</div>';*/


} else {

	if ($tag || $topic || $search) {
		$total = count($article_ids);
	} else {
		$get_total = db::_query("SELECT COUNT(*) AS total FROM blog WHERE `site` = '".$page->env."'");
		$total = $get_total[0]["total"];
	}
	//echo '<pre>';var_dump($article_ids);echo '</pre>';
	echo '
			<section id="full_page_content" class="white">
		<div class="column full">
				<h1 class="title">'.($tag || $topic || $search ? $page_title : 'Ghostshield Articles').'</h1>
				<div class=" article  notleft">
					<div class="column_interior">';
//				<table border="0" class="ctable">';

//	$total = count($page->data("blog")->attr);

	echo $page->pagination( $total / $per_page, false);

	$count = 0;
	$article_list = $page->articles($per_page, $article_ids, $search);
	if (count($article_list) > 0) foreach ($article_list as $article) {
		$count++;
//		echo '
//					<tr><td><a href="'.$article["url"].'">'.$article["title"].'</a><td>'.date('Y-m-d', strtotime($article["_created"]));
		echo '
						<a href="/articles/'.$article["url"].'" class="synopsis">';

		if ($article["image"] > "") {
			echo '
							<div class="image_thumb valign"><img src="/images/200x200-crop/'.str_replace("//images.ctfassets.net/", "cms:", $article["image"]).'" /></div>
							<div class="synopsis_text">';
		} else echo '
							<div class="synopsis_text no_image">';

		$summary = ($article["summary"] > "" ? $article["summary"] : $article["article"]);

		echo '
								<h3>'.$article["title"].'</h3>
								<p>'.($words_in_summary > 0 ? ($words_in_summary < strlen($summary) ? substr(strip_tags($summary), 0, strpos(wordwrap(strip_tags($summary), $words_in_summary), "\n")).'...':$summary).' <span class="readmore">Read More</span>':'').'</p>
								<!-- '.strip_tags($summary).' -->
							</div>
						</a>';
	}

//	echo '
//				</table>';

	echo $page->pagination( $total / $per_page, false);

	echo '
					</div>
				</div> </div>
				<!--div class="column secondary right"-->';

}

/*
echo '
					<div class="link_list">
						<h3>Article Search</h3>
						<form method="GET" action="/articles/" class="ignore">
							<input type="text" name="search" value="'.(isset($search)?$search:'').'" /> <!--input type="image" src="//assets.ghostshield.com/img/go.svg" height="24" /--> <input type="submit" class="search_small" value="Go" />
						</form>
					</div>
					<div class="link_list">
						<h3>Topics</h3>
						<ul>
							<li><a href="/articles/"'.(!$tag && !$topic ?' class="active"':'').'>All</a></li>';
	foreach ($dtopics as $dtopic) {
		echo '
							<li><a href="/articles/topic/'.str_replace("%20", "+", urlencode(strtolower($dtopic["topic"]))).(strtolower($dtopic["topic"])==$topic?'" class="active':'').'">'.ucwords($dtopic["topic"]).'</a></li>';
	}
	echo '
						</ul>
					</div>
					<div class="link_list">
						<h3>Popular Tags</h3>
						<ul>';
	foreach ($dtags as $dtag) {
		echo '
							<li><a href="/articles/tag/'.str_replace("%20", "+", urlencode(strtolower($dtag["tag"]))).(strtolower($dtag["tag"])==$tag?'" class="active':'').'">'.ucwords($dtag["tag"]).'</a></li>';
	}
	echo '
						</ul>
					</div>';
*/
	echo '
				<!--/div>
				<div class="clear"></div-->
			</section>

			<!--div id="page_footer_before"></div-->

	<a name="product_finder" id="product_finder_anchor" class="anchor"></a>
	<a name="pf" id="pf_anchor" class="anchor"></a>

	<section id="product_finder">';

include_once("product_finder.php");

echo '
	</section>

	<section id="buy" class="black large">
		<h2 class="sub centered">'. $GLOBALS["buy_section_title"] .'</h2>
		<div class="column full">
			<div class="column_interior">
				<ul class="width'.count($page->data("homeDistributor")->attr).'">';

$logo_count = 1;

foreach ($page->data("homeDistributor")->attr as $distributor) {
	echo '
					<li data-id="'.$distributor["id"].'" class="animate-in fade-in delay-'.$logo_count.'"><a href="'.str_replace("%s",'ghostshield+',$distributor["search"]).'" class="'.str_replace(" ","",strtolower($distributor["name"])).'" target="_blank" style="background-image:url('.$distributor["logoWhite"]["file"].')">'.$distributor["name"].'</a></li>';
	$logo_count++;

}

/*
	echo '
					<li class="animate-in fade-in delay-'.$logo_count.'"><a href="#" class="diy" target="_blank" style="background-image:url(assets/img/logo/diy-white.svg)">D.i.Y. Home Center</a></li>';
*/					
	echo '
				</ul>
			</div>
		</div>
	</section>';

echo $page->footer();

?>
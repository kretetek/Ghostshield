<?php

header('Accept-Ranges: bytes');

require_once("lib/page.class.php");
$page = new page();
$page->keywords("concrete sealer, concrete sealant, concrete sealers, seal driveway, seal basement, seal patio, seal floor");
$page->addScript("clicklink.js");
$page->addScript("product-finder.min.js");
$page->addScript("index.js");
$page->addCSS("home.css");
$page->addCSS("clf.css");
$page->addCSS("product-finder.css");

/*if ($mobile) {

	$page->setBanner('
	<div id="banner">
		<div class="video_wrapper">
			<img src="//assets.'.$page->host.'/img/bg/waterproof.jpg" alt="waterproof concrete sealers header image" />
		</div>
	</div>');

} else {

	$page->setBanner('
	<div id="banner">
		<div class="video_wrapper video_container" id="index_video" data-src-mp4="//assets.'.$page->host.'/video/shutterstock_v9529028_slow.mp4" data-attr="muted autoplay loop"></div>
	</div>');

}*/


/*$page->setBanner('
	<div id="banner">
		<div class="video_wrapper">
			<img src="//assets.'.$page->host.'/img/bg/gs-smoke.jpg" alt="background image" />
		</div>
	</div>');*/

echo $page->header('Ghostshield Concrete Sealers // Water, Oil &amp; Salt Repellent', 'index');

echo '
	<section id="splash">
		<div class="splash_overlay grid">
			<div class="logo"><img src="//assets.'.$page->host.'/img/logo/ghostshield-logo-white-nt.svg" alt="Ghostshield Concrete Sealers" /></div>
			<div class="upper_half">
				<h3><span class="orange">Penetrating. Waterproofing.<br /><span class="nobreak">Oil Resistant</span>. <span class="nobreak">Salt Repellent</span>.</span><br /><span class="nobreak">Concrete Sealers</span> <span class="nobreak"><span class="gray">&amp;</span> Epoxy Coatings</span></h3>';
		if (isset($page->data("pages")->attr["/home/section3"])) echo '
					<!--h1>'.str_replace('<em>concrete sealer</em>', '<em class="plural">concrete sealer</em>', $page->data("pages")->attr["/home/welcome"]["title"]).'</h1-->';
		echo '
				<div id="calltoaction"><a href="#product_finder" class="btn hex-button tall">Find Your Product</a></div>
			</div>
			<div class="lower_half">
				<img src="//assets.'.$page->host.'/img/bottle/gs-family.png" />
			</div>
		</div>
	</section>';

/*$squares = get_cache_data("squares", ($page->dev?"dev":"pub"));
$homeDistributor = get_cache_data("homeDistributor", ($page->dev?"dev":"pub"));

/*$dcount = 0;
$map_features = "";
foreach ($distributors as $id => $distributor) {
	$map_features .= '{
        "type": "Feature",
        "properties": {
            "description": "<h3 class=\"marker-title\">'.$distributor["name"].'</h3><div class=\"contact\"><img src=\"'.$distributor["logo"]["file"].'\" /><address>'.preg_replace("/([^\,]+)\,([\S\s]+)/","\\1<br />\\2",$distributor["address"]).'<br />'.$distributor["phone"].'</address><a href=\"'.$distributor["url"].'\" target=\"_blank\">'.$distributor["url"].'</a></div><p>'.$distributor["blurb"].'</p>"
        },
        "geometry": {
            "type": "Point",
            "coordinates": ['.$distributor["longitude"].', '.$distributor["latitude"].']
        }
    }';
    if ($dcount < count($distributors)) $map_features .= ', ';
}*/

//if (isset($_REQUEST["tools"])) echo '<a href="javascript:alert($(window).width());">WINDOW SIZE</a>';
	
/*echo '
	<section id="welcome">
		<div class="column full">
			<div class="column_interior">
				<div id="intro">
					<h1>'.$page->data("pages")->attr["/home/welcome"]["title"].'</h1>
					'.preg_replace("/(\<\/?)em\>/", "$1span>", $page->data("pages")->attr["/home/welcome"]["content"]).'
				</div>
			</div>
		</div>
		<div class="buttons">
			<a href="/browse/" class="hex-button">Browse <small>the Many Uses</small></a> <span>&nbsp;</span> <a href="#product_finder" class="hex-button">Find <small>the Right Product</small></a>
		</div>
	</section>';*/

/*if (isset($page->data("pages")->attr["/home/section3"])) {
	
	echo '
	<section id="welcome">
				<h1>'.$page->data("pages")->attr["/home/welcome"]["title"].'</h1>
				'.preg_replace("/(\<\/?)em\>/", "$1span>", $page->data("pages")->attr["/home/welcome"]["content"]).'
				<a href="/browse/" class="hex-button tall">Browse the Many Uses</a>
	</section>';

/*	echo '
	<section id="responsibility"'.(isset($page->data("pages")->attr["/home/section3"]["image"]["file"])?' style="background-image:url(\''.$page->data("pages")->attr["/home/section3"]["image"]["file"].'\')"':'').'>
		<div class="column left">
			<div class="column_interior welcome">
				<h1>'.$page->data("pages")->attr["/home/welcome"]["title"].'</h1>
				'.preg_replace("/(\<\/?)em\>/", "$1span>", $page->data("pages")->attr["/home/welcome"]["content"]).'
				<a href="/browse/" class="hex-button tall">Browse</a>
			</div>
		</div>
		<div class="column right">
<!--		<div class="youtube_video"><iframe width="100%" height="100%" src="https://www.youtube.com/embed/CeIIDHN7stc" frameborder="0" allowfullscreen></iframe></div>-->
			<div class="youtube_video">
				<iframe src="https://player.vimeo.com/video/196756453" width="100%" height="100%" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>

			</div>
		</div>
		<div class="clear"></div>
	</section>';* /

}*/
/*
echo '
	<!--div id="page_nav_container">
		<nav class="page_nav" id="page_nav">
			<div id="page_nav_open"><span>Page Menu</span></div>
			<ul>
				<li><a href="#top"><span>Top</span></a></li>
				<li><a href="#gallery"><span>Gallery</span></a></li>
				<li><a href="#how"><span>Waterproof</span></a></li>
				<li><a href="#uses"><span>Uses</span></a></li>
				<li><a href="#isotek"><span>Commercial</span></a></li>
				<li><a href="#product_finder"><span>Product Finder</span></a></li>
			</ul>
		</nav>
	</div-->';
*/
/*

echo '
	<a name="squares" id="squares_anchor" class="anchor"></a>
	
	<section id="squares" class="animate-in fade-in">';

makeSquare($page->data("squares")->attr["top-left"], $page, 0);
makeSquare($page->data("squares")->attr["top-center"], $page, 1);
makeSquare($page->data("squares")->attr["top-right"], $page, 2);

echo '
		<div id="top_row_accordion_content" class="accordion_content"></div>';

makeSquare($page->data("squares")->attr["middle-left"], $page, 0);
makeSquare($page->data("squares")->attr["middle-center"], $page, 1);
makeSquare($page->data("squares")->attr["middle-right"], $page, 2);

echo '
		<div id="middle_row_accordion_content" class="accordion_content"></div>';

makeSquare($page->data("squares")->attr["bottom-left"], $page, 0);
makeSquare($page->data("squares")->attr["bottom-center"], $page, 1);
makeSquare($page->data("squares")->attr["bottom-right"], $page, 2);

echo '
		<div id="bottom_row_accordion_content" class="accordion_content"></div>';

function makeSquare($square, $page, $delay) {

	$media = "";
	if (isset($square["mediaLink"]) && strstr($square["mediaLink"],"vimeo") > -1) {
		$video_id = preg_replace("/https?\:\/\/vimeo.com\/([\d]+)\/.*
/","\\1",$square["mediaLink"]);
		$media = '<iframe src="https://player.vimeo.com/video/'.$video_id.'?color=ff9933&title=0&byline=0&portrait=0" width="700" height="400" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
	} else if (isset($square["media"]) && strstr($square["media"]["type"],"image") > -1) {
		$media = '<img src="http:'.$square["media"]["file"].'" alt="article image" />';
	} else if (isset($square["media"]) && strstr($square["media"]["type"],"video") > -1) {
		$media = '<video controls><source src="http:'.$square["media"]["file"].'" type="'.$square["media"]["type"].'" /> Your browser does not support this video.</video>';
	}

	if (isset($square["products"][0])) {

		$line2 = '
						<span class="line2">'.$page->data("productsById")->attr[$square["products"][0]]["name"].'</span>
						<span class="line3 out">'.$GLOBALS["view_button_text"].'</span>';
		$link = "product/".$page->data("productsById")->attr[$square["products"][0]]["model"];

		$content = '
						<span class="title">'.$square["title"].'</span>';

	} else if (isset($square["link"])) {

		$line2 = '
						<span class="line3 out">Visit Page</span>';
		$link = $square["link"];

		$content = '
						<span class="title">'.$square["title"].'</span>';

	} else {

		$line2 = '
						<span class="line3 in">Read More</span>';
		$link = "#";

		$content = '
						<h2 class="title">'.$square["title"].'</h2>
						<p>'.$square["blurb"].'</p>';

	}

	echo '
		<div id="'.$square["position"].'" class="square gradient delay-'.$delay.'" data-href="'.$link.'" data-position="'.$square["position"].'">
			<div class="button">
				<div class="square-bg" style="background-image:url(\'http:'.str_replace("downloads.","images.",$square["image"]["file"]).'?w=800&h=600&fit=fill\')"></div>
				<img src="//assets.ghostshield.com/img/placeholder.png" alt="placeholder" />
				<div class="title">
					<div class="title_inner">
						<span class="line1">'.$square["title"].'</span>'.$line2.'
					</div>
				</div>
			</div>
			<div class="mobile_accordion_content">
				<div class="close"></div>
				<div class="scroller">
					<div class="accordion_interior">'.
						$content.'
					</div>
				</div>
			</div>
		</div>';

}

echo '
		<div class="clear"></div>
	</section>';
*/
//if (isset($page->data("pages")->attr["/home/section3"])) {
	echo '
	<a name="why" id="why_anchor" class="anchor"></a>

	<section id="why" class="ben white animate-in fade-in">
		<div class="wrapper1">
			<div class="wrapper2">
				<div class="video_wrapper video_container" id="index_video" data-src-mp4="//assets.'.$page->host.'/video/drops.mp4" data-attr="muted autoplay loop"></div>
				<h2 class="sub">Concrete Results.<br /><span class="g g'.rand(1,5).'">Cemented Reputation.</span></h2>
				<p class="blurb">'.preg_replace("/(\<\/?)em\>/", "$1span>", $page->data("pages")->attr["/home/welcome"]["content"]).'</p>
				<p><!-- bben a href="/browse/" class="hollow-button">
					Browse the Many Uses
					<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 17.3 25.7" style="enable-background:new 0 0 17.3 25.7;" xml:space="preserve">
						<polygon class="st0" points="0,24.6 16,12.9 0,1.1"/>
					</svg>
				</a--><a href="/browse/" class="btn hex-button tall">Browse the Many Uses</a></p>
			</div>
		</div>
	</section>';
//}

/*

			<!--div class="tri_column no_padding left animate-in fade-in delay-2">
				<div class="illustration"></div>
				<h3>Untreated Concrete is&nbsp;at&nbsp;Risk</h3>
				<p>Untreated, concrete wears and deteriorates: pitting, <dfn>spalling</dfn>, dusting, <dfn>efflorescence</dfn>, and cracking are inevitable characteristics of unsealed concrete.</p>
			</div>

			<div class="tri_column no_padding middle animate-in fade-in delay-3">
				<div class="illustration"></div>
				<h3>Apply Ghostshield for&nbsp;Protection</h3>
				<p>Ghostshield concrete sealers penetrate deepest, filling pores and <dfn>capillaries</dfn> within the concrete to densify and protect while preserving the concrete’s natural appearance.</p>
			</div>

			<div class="tri_column no_padding right animate-in fade-in delay-4">
				<div class="illustration"></div>
				<h3>Treated Concrete Lasts&nbsp;Longer</h3>
				<p>Water is nature’s universal solvent. It&nbsp;also transports salt and other materials that damage concrete from within. Ghostshield repels water, making concrete more durable.</p>
			</div-->
			<div id="fo
			*/
echo '



	<a name="how" id="how_anchor" class="anchor"></a>

	<section id="how" class="white animate-in fade-in">
		<h2 class="sub centered">Waterproof, Protect <small>&amp;</small> Preserve:<br /> <span class="g g'.rand(1,5).'">How Concrete Sealer Works</span></h2>
		<div class="column_container">
			<!--div id="four_blocks">
				<div id="block_1">
					<div class="step">1</div>
					<p>Untreated concrete aborbs water, causing damage to aesthetics and integrity over time.</p>
				</div>
				<div id="block_2">
					<div class="step">2</div>
					<p>Concrete sealer fills the pores and cavities in the surface of the concrete.</p>
				</div>
				<div id="block_3">
					<div class="step">3</div>
					<p>Vapors breathe out easily, but external water is deflected from the surface.</p>
				</div>
				<div id="block_4">
					<div class="step">4</div>
					<p>Treated concrete resists water, snow, salt - even <a href="#">motor oil</a>.</p>
				</div>
			</div-->
			<ol>
				<li>Untreated concrete aborbs water, causing damage to aesthetics and integrity over time.</li>
				<li>Concrete sealer fills the pores and cavities in the surface of the concrete.</li>
				<li>Vapors breathe out easily, but external water is deflected from the surface.</li>
				<li>Treated concrete resists water, snow, salt - even <a href="#">motor oil</a>.</li>
			</ol>
		</div>
	</section>
	
	<a name="easy" id="easy_anchor" class="anchor"></a>

	<section id="easy" class="white animate-in fade-in">
		<h2 class="sub centered">Easy <span class="g g'.rand(1,5).'">to Apply</span></h2>
		<video autoplay="" muted="" playsinline="" loop="">
			<source src="//assets.'.$page->host.'/video/diy.mp4" type="video/mp4" />
		</video>
	</section>

	<a name="gallery" id="gallery_anchor" class="anchor"></a>

	<section id="gallery" class="animate-in fade-in">
		<h2 class="centered"><span class="g g'.rand(1,5).'">Photos</span> From You</h2>
		<div id="gallery_frame">
			<div id="gallery_full">
				<div id="gallery_item1" data-id="1" class="gallery_item gallery_center" style="background-image:url(\'/images/1600x800-scalecrop/'.str_replace("//","external:",str_replace("downloads.","images.",$page->data("squares")->attr["gallery-1"]["image"]["file"])).'\')"></div>
				<div id="gallery_item2" class="gallery_item gallery_right"></div>
			</div>
		</div>
		<div id="gallery_controls">
			<div id="gc_left"></div>
			<div id="gc_right"></div>
		</div>
		<div id="gallery_caption" class="visible">
			'.$page->data("squares")->attr["gallery-1"]["caption1"].'<br />
			'.$page->data("squares")->attr["gallery-1"]["caption2"].'
		</div>
		<div id="gallery_thumbs" class="'.(isset($page->data("squares")->attr["gallery-6"]["image"])?'gallery6':'gallery5').'">
			<div id="gallery_thumb1" class="gallery_thumb active" data-id="1" data-src="/images/1600x800-scalecrop/'.str_replace("//","external:",str_replace("downloads.","images.",$page->data("squares")->attr["gallery-1"]["image"]["file"])).'">
				<img src="/images/300x187-scalecrop/'.str_replace("//","external:",str_replace("downloads.","images.",$page->data("squares")->attr["gallery-1"]["image"]["file"])).'" />
				<div id="gallery_caption1" class="gallery_caption" data-id="1">
					'.$page->data("squares")->attr["gallery-1"]["caption1"].'<br />
					'.$page->data("squares")->attr["gallery-1"]["caption2"].'
				</div>
			</div>
			<div id="gallery_thumb2" class="gallery_thumb" data-id="2" data-src="/images/1600x800-scalecrop/'.str_replace("//","external:",str_replace("downloads.","images.",$page->data("squares")->attr["gallery-2"]["image"]["file"])).'">
				<img src="/images/300x187-scalecrop/'.str_replace("//","external:",str_replace("downloads.","images.",$page->data("squares")->attr["gallery-2"]["image"]["file"])).'" />
				<div id="gallery_caption2" class="gallery_caption" data-id="2">
					'.$page->data("squares")->attr["gallery-2"]["caption1"].'<br />
					'.$page->data("squares")->attr["gallery-2"]["caption2"].'
				</div>
			</div>
			<div id="gallery_thumb3" class="gallery_thumb" data-id="3" data-src="/images/1600x800-scalecrop/'.str_replace("//","external:",str_replace("downloads.","images.",$page->data("squares")->attr["gallery-3"]["image"]["file"])).'">
				<img src="/images/300x187-scalecrop/'.str_replace("//","external:",str_replace("downloads.","images.",$page->data("squares")->attr["gallery-3"]["image"]["file"])).'" />
				<div id="gallery_caption3" class="gallery_caption" data-id="3">
					'.$page->data("squares")->attr["gallery-3"]["caption1"].'<br />
					'.$page->data("squares")->attr["gallery-3"]["caption2"].'
				</div>
			</div>
			<div id="gallery_thumb4" class="gallery_thumb" data-id="4" data-src="/images/1600x800-scalecrop/'.str_replace("//","external:",str_replace("downloads.","images.",$page->data("squares")->attr["gallery-4"]["image"]["file"])).'">
				<img src="/images/300x187-scalecrop/'.str_replace("//","external:",str_replace("downloads.","images.",$page->data("squares")->attr["gallery-4"]["image"]["file"])).'" />
				<div id="gallery_caption4" class="gallery_caption" data-id="4">
					'.$page->data("squares")->attr["gallery-4"]["caption1"].'<br />
					'.$page->data("squares")->attr["gallery-4"]["caption2"].'
				</div>
			</div>
			<div id="gallery_thumb5" class="gallery_thumb" data-id="5" data-src="/images/1600x800-scalecrop/'.str_replace("//","external:",str_replace("downloads.","images.",$page->data("squares")->attr["gallery-5"]["image"]["file"])).'">
				<img src="/images/300x187-scalecrop/'.str_replace("//","external:",str_replace("downloads.","images.",$page->data("squares")->attr["gallery-5"]["image"]["file"])).'" />
				<div id="gallery_caption5" class="gallery_caption" data-id="5">
					'.$page->data("squares")->attr["gallery-5"]["caption1"].'<br />
					'.$page->data("squares")->attr["gallery-5"]["caption2"].'
				</div>
			</div>';

if (isset($page->data("squares")->attr["gallery-6"]["image"])) {
	echo '
			<div id="gallery_thumb6" class="gallery_thumb" data-id="6" data-src="/images/1600x800-scalecrop/'.str_replace("//","external:",str_replace("downloads.","images.",$page->data("squares")->attr["gallery-6"]["image"]["file"])).'">
				<img src="/images/300x187-scalecrop/'.str_replace("//","external:",str_replace("downloads.","images.",$page->data("squares")->attr["gallery-6"]["image"]["file"])).'" />
				<div id="gallery_caption6" class="gallery_caption" data-id="6">
					'.$page->data("squares")->attr["gallery-6"]["caption1"].'<br />
					'.$page->data("squares")->attr["gallery-6"]["caption2"].'
				</div>
			</div>';
}

echo '
		</div>
	</section>';

echo '


	<a name="uses" id="uses_anchor" class="anchor"></a>

	<span style="display:none">
		<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
			<defs>
				<clipPath id="hexagonClipPath">
					<path class="st0" d="M147.4,0.7h-92c-3.1,0-5.9,1.6-7.4,4.3l-46,79.7c-1.5,2.7-1.5,5.9,0,8.6l46,79.7c1.5,2.7,4.4,4.3,7.4,4.3h92c3.1,0,5.9-1.6,7.4-4.3l46-79.7c1.5-2.7,1.5-5.9,0-8.6L154.9,5C153.3,2.4,150.5,0.7,147.4,0.7z"/>
				</clipPath>
			</defs>
		</svg>
	</span>

	<section id="hexagons" class="white animate-in fade-in">

		<div id="hex-blurb">
			<h2 class="centered"><span class="g g'.rand(1,5).'">Browse the</span> Many Uses'/*.str_ireplace("Many Uses",'<span class="g g'.rand(1,5).'">Many Uses</span>',strip_tags($page->data("pages")->attr["/home/browse"]["title"]))*/.'</h2>
			<p>';
if (isset($page->data("pages")->attr["/home/browse"]["content"])) echo preg_replace("/(\<\/?)em\>/", "$1span>", $page->data("pages")->attr["/home/browse"]["content"]);
echo '</p>
		</div>

		<div id="hexagon_scaler">

			<div class="flip-in delay-1 animate-in">
				<div class="hexagon" id="hexagon1">
					<div class="flip">
						<div class="front" style="background-image:url(/images/560x500-scalecrop-mask/'.str_replace("//","external:",str_replace("downloads.","images.",$page->data("squares")->attr["left-edge"]["image"]["file"]).'?w=560&h=500&fit=fill').')" /></div>
						<div class="back">'.hexCaption($page->data("squares")->attr["left-edge"], $page).'</div>
					</div>
				</div>
			</div>

			<div class="flip-in delay-2 animate-in">
				<div class="hexagon" id="hexagon2">
					<div class="flip">
						<div class="front" style="background-image:url(/images/560x500-scalecrop-mask/'.str_replace("//","external:",str_replace("downloads.","images.",$page->data("squares")->attr["left-top"]["image"]["file"]).'?w=560&h=500&fit=fill').')" /></div>
						<div class="back">'.hexCaption($page->data("squares")->attr["left-top"], $page).'</div>
					</div>
				</div>
			</div>

			<div class="flip-in delay-3 animate-in">
				<div class="hexagon" id="hexagon3">
					<div class="flip">
						<div class="front" style="background-image:url(/images/560x500-scalecrop-mask/'.str_replace("//","external:",str_replace("downloads.","images.",$page->data("squares")->attr["left-bottom"]["image"]["file"]).'?w=560&h=500&fit=fill').')" /></div>
						<div class="back">'.hexCaption($page->data("squares")->attr["left-bottom"], $page).'</div>
					</div>
				</div>
			</div>

			<div class="flip-in delay-4 animate-in">
				<div class="hexagon" id="hexagon4">
					<div class="flip">
						<div class="front" style="background-image:url(/images/560x500-scalecrop-mask/'.str_replace("//","external:",str_replace("downloads.","images.",$page->data("squares")->attr["center"]["image"]["file"]).'?w=560&h=500&fit=fill').')" /></div>
						<div class="back">'.hexCaption($page->data("squares")->attr["center"], $page).'</div>
					</div>
				</div>
			</div>

			<div class="flip-in delay-5 animate-in">
				<div class="hexagon" id="hexagon5">
					<div class="flip">
						<div class="front" style="background-image:url(/images/560x500-scalecrop-mask/'.str_replace("//","external:",str_replace("downloads.","images.",$page->data("squares")->attr["right-top"]["image"]["file"]).'?w=560&h=500&fit=fill').')" /></div>
						<div class="back">'.hexCaption($page->data("squares")->attr["right-top"], $page).'</div>
					</div>
				</div>
			</div>

			<div class="flip-in delay-6 animate-in">
				<div class="hexagon" id="hexagon6">
					<div class="flip">
						<div class="front" style="background-image:url(/images/560x500-scalecrop-mask/'.str_replace("//","external:",str_replace("downloads.","images.",$page->data("squares")->attr["right-bottom"]["image"]["file"]).'?w=560&h=500&fit=fill').')" /></div>
						<div class="back">'.hexCaption($page->data("squares")->attr["right-bottom"], $page).'</div>
					</div>
				</div>
			</div>

			<div class="flip-in delay-7 animate-in">
				<div class="hexagon" id="hexagon7">
					<div class="flip">
						<div class="front" style="background-image:url(/images/560x500-scalecrop-mask/'.str_replace("//","external:",str_replace("downloads.","images.",$page->data("squares")->attr["right-edge"]["image"]["file"]).'?w=560&h=500&fit=fill').')" /></div>
						<div class="back">'.hexCaption($page->data("squares")->attr["right-edge"], $page).'</div>
					</div>
				</div>
			</div>

			<!--div class="hexagon" id="hexagon11"><div class="front"></div></div>
			<div class="hexagon" id="hexagon12"><div class="front"></div></div>
			<div class="hexagon" id="hexagon13"><div class="front"></div></div>
			<div class="hexagon" id="hexagon14"><div class="front"></div></div>
			<div class="hexagon" id="hexagon15"><div class="front"></div></div>
			<div class="hexagon" id="hexagon16"><div class="front"></div></div>
			<div class="hexagon" id="hexagon17"><div class="front"></div></div-->

		</div>

	</section>

';

function hexCaption($square, $page) {

	if (isset($square["products"][0])) {

		$model = $page->data("productsById")->attr[$square["products"][0]]["model"];
		$modelname = $page->data("productsById")->attr[$square["products"][0]]["name"];

		$line2 = '
						<span class="line1">'.$square["title"].'</span>
						<span class="line2">'.preg_replace("/([0-9]+)/", "<em>$1</em>", $page->data("productsById")->attr[$square["products"][0]]["name"]).'</span>
						<span class="line3 out">'.$GLOBALS["view_button_text"].'</span>';
/*		if (file_exists('assets/img/bottle/'.$model.'.png')) {
			$image = '
						<img src="//assets.'.$page->host.'/img/bottle/'.$model.'.png" alt="'.$modelname.' Product Image" />';
		} elseif (file_exists('assets/img/bucket/'.$model.'.png')) {
			$image = '
						<img src="//assets.'.$page->host.'/img/bucket/'.$model.'.png" alt="'.$modelname.' Product Image" />';
		}*/
		
		// right here
		$image = '
						<div class="line0">Best sealed with</div>
						<span itemprop="name" class="product_name selected '.str_replace(" ", "-", strtolower($modelname)).'">' . str_replace(" ".$model, '<span class="super">&reg;</span> <span class="bold" itemprop="model">'.$model.'</span>', $modelname) . '</span>
						<div class="line1"><!--span class="hex-button xxsmall"-->'.$square["title"].'<!--/span--></div>';
		$link = "product/".$page->data("productsById")->attr[$square["products"][0]]["model"];

		$content = '
						<span class="title">'.$square["title"].'</span>';

	} else if (isset($square["link"])) {

		$line2 = '
						<span class="line1">'.$square["title"].'</span>
						<span class="line3 out">Visit Page</span>';
		$link = $square["link"];

		$image = '
						<span class="line1">'.$square["title"].'</span>
						<div class="line3 out"><span class="hex-button xxsmall">'.$GLOBALS["read_button_text"].'</span></div>';

		$content = '
						<span class="title">'.$square["title"].'</span>';

	} else {

		$line2 = '
						<span class="line3 in">'.$GLOBALS["read_button_text"].'</span>';
		$link = "#";

		$image = '
						<span class="line1">'.$square["title"].'</span>';

		$content = '
						<h2 class="title">'.$square["title"].'</h2>
						<p>'.$square["blurb"].'</p>';

	}
	$html = '
';
	if (isset($link)) $html .= '						<a href="'.$link.'">
	';
	//$html .= '					<span class="line1">'.$square["title"].'</span>'.$line2;
	$html .= $image;
	if (isset($link)) $html .= '
							</a>';

	return $html;

}


echo '

	<a name="isotek" id="isotek_anchor" class="anchor"></a>
	
	<section id="isotek" class="animate-in fade-in" !--click_link" data-href="/product/iso-tek-8501/"-->
		<div id="isotek_text">
			<div class="h2"></div>
			<div class="content"><h2 class="sub">'.str_ireplace("Concrete Sealer,", '<span class="g">Concrete Sealer,</span>',strip_tags($page->data("pages")->attr["/home/isotek"]["title"])).'</h2>
				'.$page->data("pages")->attr["/home/isotek"]["content"].'
			</div>
		</div>
		<!--div class="isotek-button"><a class="hollow-button" href="/product/iso-tek-8501/">Thixotropic Concrete Sealer
			<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 17.3 25.7" style="enable-background:new 0 0 17.3 25.7;" xml:space="preserve">
				<polygon class="st0" points="0,24.6 16,12.9 0,1.1"/>
			</svg></a>
		</div-->
				<!--div class="bar_graph animate-in fade-in delay-3">';

/*if (isset($page->data("products")->attr[8501]["graph"])) {

	$max = 0;
	$count1 = 0;
	foreach ($page->data("graphs")->attr[$page->data("products")->attr[8501]["graph"]]["items"] as $letter => $data) {
		if ($data["value"]) {
			$max = $data["value"] > $max ? $data["value"] : $max;
			$count1++;
		}
	}


	echo '
					<h3 class="title">'.$page->data("graphs")->attr[$page->data("products")->attr[8501]["graph"]]["title"].' <small>'.$page->data("graphs")->attr[$page->data("products")->attr[8501]["graph"]]["subtitle"].'</small></h3>';

	$count2 = 0;
	foreach ($page->data("graphs")->attr[$page->data("products")->attr[8501]["graph"]]["items"] as $letter => $data) {

		if ($data["value"]) {
			$count2++;
			$class_letter = $count2 == $count1 ? "e" : $letter;
			echo '
					<div class="section product_'.$letter.'"><div class="name">'. str_ireplace("Ghostshield", "Ghostshield<br />", $data["name"]) .'</div> <div class="bar" style="height:'.($data["value"] == $max ? 100 : floor($data["value"] / $max * 100)).'%">'.$data["value"].'%</div></div>';
		}

	}

} else echo '
					<p><em>Error</em>: Graph data not found for 8501 product!</p>';*/

echo '
				</div>
		<a class="click_link_fallback clf_abs" href="/product/iso-tek-8501/">Links to Iso-Tek 8501 Product</a-->
	</section>';

/*
echo '


	<a name="isotek" id="isotek_anchor" class="anchor"></a>

	<section id="isotekgrid" class="black animate-in fade-in">

		<figure id="igi1" class="grid_item left_wide click_link" data-href="/commercial/">
			<div class="isotekcover">
				<h2>Commercial<br /> Specialty Sealers</h2>
				<div class="isoteklogo">
				<div class="isotekbtn"><a href="/commercial/" class="hex-button xsmall">Learn More</a></div></div>
			</div>
			<a class="click_link_fallback clf_abs" href="/commercial/">Links to Commercial Overview</a>
		</figure>

		<figure id="igi2" class="grid_item right_narrow click_link" data-href="/commercial/">
			<figcaption>432 Park Avenue, <acronym title="New York City">NYC</acronym></figcaption>
			<a class="click_link_fallback clf_abs" href="/commercial/">Links to Commercial Overview</a>
		</figure>

		<figure id="igi3" class="grid_item left_narrow click_link" data-href="/commercial/">
			<figcaption>The Grove at Grand Bay, Miami</figcaption>
			<a class="click_link_fallback clf_abs" href="/commercial/">Links to Commercial Overview</a>
		</figure>

		<figure id="igi4" class="grid_item right_wide click_link" data-href="/commercial/">
			<figcaption>Orlando Sanford International Airport</figcaption>
			<a class="click_link_fallback clf_abs" href="/commercial/">Links to Commercial Overview</a>
		</figure>

	</section>';
*/


echo '

	<a name="nano" id="nano_anchor" class="anchor clear"></a>';

//<div id="bottle_teaser"'.(isset($page->data("pages")->attr["/home/section2"]["image"]["file"])?' style="background-image:url(\''.$page->data("pages")->attr["/home/section2"]["image"]["file"].'\')"':'').'>
//if (isset($page->data("pages")->attr["/home/section2"])) {

	//$page->data("pages")->attr["/home/section2"]["title"]
	echo '
    <section id="nano" class="site-section">
        <div class="nano-cta">
        	<div class="content">
				<h2><span>The Science</span> <span class="g">behind it</span></h2>
				<p>The latest advances in nanotechnology are what drive the Ghostshield formulas.<br/> Smaller particles penetrate deeper, resulting in longer lasting performance.</p>
				<a class="btn hex-button tall" href="/nanotechnology">Concrete Sealer Technology</a>
			</div>
        </div>
        <div class="grid grid-3">
            <div class="spacer" id="nano-spacer"></div>
            <div class="micro-img-container" id="micro-img-1">
                <div class="untreated-image"></div>
                <p><em>Fig. 1</em> shows a micro-crack within a concrete pore at 3000x magnification</p>
            </div>
            <div class="micro-img-container" id="micro-img-2">
                <div class="treated-image"></div>
                <p><em>Fig. 2</em> shows Ghostshield nano-particles filling the concrete pore</p>
            </div>
        </div>
	</section>';

//}

echo '
	<section id="video">
		<h2 class="sub centered">Video <span class="g">Showcase</span></h2>
		<div id="video-link-container">
			<div class="video-link">
				<div class="video-wrapper">
					<a class="portfolio-miami-thumb"></a>
					<iframe src="//player.vimeo.com/video/301287239?byline=0&amp;portrait=0" width="500" height="400" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
				</div>
				<div class="description">
					<h4>Commercial Concrete Sealer for Parking Garages</h4>
					<p>Iso-Tek 8540VOC was used on the Caoba Miami Worldcenter and is the best concrete sealer to use on a parking garage since it is able to protect against high-traffic, cracking and deicing salts.</p>
				</div>
			</div>
			<div class="video-link">
				<div class="video-wrapper">
					<a class="portfolio-432-thumb"></a>
					<iframe src="//player.vimeo.com/video/294027790?byline=0&amp;portrait=0" width="500" height="400" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
				</div>
				<div class="description">
					<h4>The Best Concrete Sealer for Skyscrapers</h4>
					<p>The Iso-Tek 8501 is a thixotropic, waterproofing concrete sealer. Our flagship commercial concrete sealer was used to seal the tallest residential building in the Western Hemisphere, 432 Park Avenue.</p>
				</div>
			</div>
			<div class="video-link">
				<div class="video-wrapper">
					<a class="portfolio-home-thumb"></a>
					<iframe src="//player.vimeo.com/video/294027927?byline=0&amp;portrait=0" width="500" height="400" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
				</div>
				<div class="description">
					<h4>How to Seal and Clean Stamped Concrete</h4>
					<p>The Micro-Degreaser 1100 will remove tough grease and stains while the Siloxa-Tek 8510 is a waterproofing concrete sealer that is able to resist oil and deicing salts.</p>
				</div>
			</div>
		</div>
		<div class="actions"><a href="/videos" class="btn hex-button tall vid-btn">Case Studies &amp; How To Videos</a></div>
	</section>';

/*echo '	
	<a name="video" id="video_anchor" class="anchor"></a>';

if (isset($page->data("pages")->attr["/home/section3"])) {

	echo '
	<section id="responsibility"'.(isset($page->data("pages")->attr["/home/section3"]["image"]["file"])?' style="background-image:url(\''.$page->data("pages")->attr["/home/section3"]["image"]["file"].'\')"':'').'>
		<div class="column left">
			<div class="column_interior">
				<h2>'.strip_tags($page->data("pages")->attr["/home/section3"]["title"]).'</h2>
				<div class="content">'.$page->data("pages")->attr["/home/section3"]["content"].' <script src="https://apis.google.com/js/platform.js"></script>
<div class="g-ytsubscribe" data-channelid="UC9X3Cl8uRVG5X9XlXfmUryw" data-layout="default" data-count="hidden"></div></div>
			</div>
		</div>
		<div class="column right">
<!--		<div class="youtube_video"><iframe width="100%" height="100%" src="https://www.youtube.com/embed/CeIIDHN7stc" frameborder="0" allowfullscreen></iframe></div>-->

			<div class="youtube_video">
				<iframe src="https://player.vimeo.com/video/196756453" width="100%" height="100%" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>

			</div>
		</div>
		<div class="clear"></div>
	</section>';

}*/

//if (isset($homeDistributor)) {

echo '

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
//}

echo '
	
	<a name="contact" id="contact_anchor" class="anchor"></a>';

echo $page->footer(); //require_once("inc/footer.php");

?>
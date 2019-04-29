<?php

//$admin_email = array("benjamin@ghostshield.com", "jeff@ghostshield.com");

define("APP_PREFIX",	"gallery");
define("APP_PATH",		"/gallery/admin/");
define("LIB_DIR",		"../lib/");

//require_once("inc/load.php");
//require_once("db.php");
require_once("lib/page.class.php");
require_once("lib/gallery.class.php");

$page = new page();

db::credentials("concrfe5_gs", "concrfe5_gs", "4EwgifF8aeP!");
$gallery = new gallery();
$page->addCSS("files.css");
$page->addCSS("gallery.css");
$page->addScript("gallery.js");
$page->addScript("clicklink.js");
$page->addScript("vimeoEmbed.js");

//$page->addBodyAttr('itemscope itemtype="http://schema.org/Product"');
$page->pageTitle($page->product["name"]." ".$page->product["type"]);
$page->setDescription(strip_tags($page->product["overview"]));

$uri = preg_replace('/#[\w]+/', "", preg_replace('/\?.*/', "", $_SERVER['REQUEST_URI']));

if (!isset($_REQUEST["dropzone"])) {
	//db::debug();
	
	//require_once("inc/header.php");
	echo $page->header();
	
	if (!isset($page->model) || !isset($page->product)) {
		
		echo '
		<section id="full_page_content" class="white article">
			<div class="column full">
				<div class="column_interior">
					<h2 style="font-size:32px;">Error 404: Not Found</h2>
					<p class="date">Sorry, a product with model number "'.$_REQUEST["model"].'" could not be found. The page could not be served.</p>
					<address></address>
				</div>
			</div>
		</section>
		<div id="page_footer_before"></div>';
		
		echo $page->footer();
		exit();
	}

	$gallery_items1 = db::_select("gallery".($page->dev?"-dev":""), ["where" => ["product" => $page->model, "_approved" => 1]]);
	$gallery_items2 = db::_select("gallery".($page->dev?"-dev":""), ["where" => ["product2" => $page->model, "_approved" => 1]]);
	$gallery_items = array_merge($gallery_items1, $gallery_items2);
	/*$page->data("colors", get_cache_data("colors", ($page->dev?"dev":"pub")));
	$page->data("additional", get_cache_data("additional", ($page->dev?"dev":"pub")));
	$page->data("comparisonDetails", get_cache_data("comparisonDetails", ($page->dev?"dev":"pub")));
	$page->data("comparisonDetailsByProduct", get_cache_data("comparisonDetailsByProduct", ($page->dev?"dev":"pub")));*/

	echo '
	
	<a name="product" id="product_anchor" class="anchor"></a>
	
	<section id="product" itemscope itemtype="http://schema.org/Product">
		<div class="product">
			<div class="info">
				<h1><span itemprop="name" class="product_name selected '.str_replace(" ", "-", strtolower($page->product["name"])).'">' . str_replace(" ".$page->model, '<span class="super">&reg;</span> <span class="bold" itemprop="model">'.$page->model.'</span>', $page->product["name"]) .  ' <small>'.$page->product["type"].'</small></span></h1>
				<p itemprop="description">'.(($page->product["availability"]==0)?'<span class="flavor_text">This product has been discontinued.</span> ':'').str_replace("<p>","",str_replace("</p>","",$page->product["overview"])).'</p>

				<div class="buy-now-button">';
	if ($page->product["residential"]) {
		echo '
					<a href="https://concretesealersupply.com/product/'.str_replace(" ","-",strtolower($page->product["name"])) .'/" class="hex-button large big">'.$GLOBALS["buy_button_text"].' <!--small>via Concrete Sealer Solutions</small--></a>';
	} else {
		echo '
					<a href="tel:1'.preg_replace("/[^0-9]/", "", $GLOBALS["phone"]).'" class="hex-button large big">Call Us <!--small>Contact KreteTek for purchasing.</small--></a>';
	}
	
	//						<input id="project_product" type="hidden" name="project_product" value="'.urlencode(serialize($page->product)).'" />
	echo '
				</div>
			</div>';
			
	/*echo '
					<meta itemprop="priceCurrency" content="USD" />
					<meta itemprop="price" content="'.$page->product["msrp"].'" />';*/
	echo '
			<ul class="icons">';

	$colors_html = "";
	if (sizeof($page->data("colors")->attr) > 0) foreach ($page->data("colors")->attr as $color) {
		$colors_html .= '<div class="swatch" style="background:#'.$color["hex"].'" title="'.$color["name"].'"></div>';
	}

	$attributes = array(
		"coverage" => [ "title" => "Coverage", "description" => "Up to %s square feet" ],
		"coats" => [ "title" => "Coats", "description" => "%s" ],
		"voc" => [ "title" => '<acronym title="Volatile Organic Compounds">VOC</acronym> Content', "description" => "%s g/L" ],
		"finish" => [ "title" => "Finish", "description" => "%s" ],
		"concentrated" => [ "title" => "Concentrated", "description" => "%s" ],
		"warranty" => [ "title" => "Warranty", "description" => "%s Year Warranty" ],
		"color" => [ "title" => "Colors", "description" => $colors_html ]
	);

	if (isset($page->product["attributes"])) {
		foreach ($page->product["attributes"] as $attribute => $value) {
			if ($value > "" || $value === 0) {
				$class = $attribute;
				if (is_numeric($value)) {
					$class .= " ".$attribute.$value;
					$value = number_format($value);
				}
				echo '
				<li class="'.$class.'"><div class="valign"><span class="title">'.$attributes[$attribute]["title"].'</span> '.sprintf($attributes[$attribute]["description"],$value).'</div></li>';
			}
		}
	}

	echo '
			</ul>'."\n";

	if (isset($page->product["sku"])) {
		echo "\n\t\t\t\t".$page->meta("sku", $page->product["sku"], true);
		echo "\n\t\t\t\t".$page->meta("gtin12", $page->product["sku"], true);
		echo "\n\t\t\t\t".$page->meta("gtin13", "0".$page->product["sku"], true);
	}
	echo "\n\t\t\t\t".$page->meta("brand", "Ghostshield", true);
	echo "\n\t\t\t\t".$page->meta("manufacturer", "KreteTek Industries, Inc.", true);
	echo "\n\t\t\t\t".$page->meta("category", $page->product["type"], true);

	echo '

			<div id="product_img">';
	if (file_exists('assets/img/bottle/'.$page->model.'.png')) {
		echo '<img itemprop="image" src="//assets.'.$page->host.'/img/bottle/'.$page->model.'.png" width="483" height="473" alt="'.$page->model.' Concrete Sealer Bottle Image"';
		if ($page->product["residential"] == "no") echo ' id="product_image"';
		echo ' />';
	} elseif (file_exists('assets/img/bucket/'.$page->model.'.png')) {
		echo '<img itemprop="image" src="//assets.'.$page->host.'/img/bucket/'.$page->model.'.png" width="483" height="473" alt="'.$page->model.' Concrete Sealer Bucket Image" />';
	} else echo '<div itemprop="image" class="custom_image" style="background-image:url('.$page->product["image"]["file"].')"></div>';
	echo '</div>

			<div class="calculator">
				<form method="GET">
					<input id="project_coverage" type="hidden" name="project_coverage" value="'.$page->product["perGallon"].'" />
					<input id="project_coats" type="hidden" name="project_coats" value="'.$page->product["attributes"]["coats"].'" />
					<input id="project_model" type="hidden" name="project_model" value="'.$page->product["model"].'" />
					<input id="project_containers" type="hidden" name="project_containers" value="'.urlencode(serialize($page->product["containers"])).'" />
					<h3>Project Calculator<span>*</span></h3>
					<label class="formula" for="project_sqft">Square Feet</label>
					<input type="number" min="1" name="project_sqft" id="project_sqft" />
					<div class="calculation">
						<input type="submit" value="Calculate" class="inverse" />';
//if (isset($_REQUEST["project_sqft"]) && is_numeric($_REQUEST["project_sqft"]) && $_REQUEST["project_sqft"] > 0)  echo " = ";
	include_once("sqftcalculate.php");
	echo '
					</div>
				</form>
				
			</div>
		</div>
	</section>
	
	<div id="page_nav_container">
		<nav class="page_nav" id="page_nav">
			<div id="page_nav_open"><span>Page Sections</span></div>
			<ul>
				<li><a href="#top"><span>Overview</span></a></li>
				<li><a href="#applications"><span>Information</span></a></li>';
	if (!$page->product["showInComparisons"]) echo '
				<li><a href="#benefits"><span>Benefits</span></a></li>';
	echo '
				<li><a href="#technical"><span>Specifications</span></a></li>
				<li><a href="#gallery"><span>Gallery</span></a></li>';
	if ($page->product["showInComparisons"]) echo '
				<li><a href="#comparisons"><span>Comparisons</span></a></li>';
	echo '
			</ul>
		</nav>
	</div>

	<a name="applications" id="applications_anchor" class="anchor"></a>

	<div class="product_background" style="background-image:url(\''.$page->product["squares"][0]["file"].'?w=1680&h=720&fit=fill\')">
		<img src="'.$page->product["squares"][0]["file"].'?w=1680&h=720&fit=fill" class="mobile_only" alt="'.$page->product["name"].' Concrete Sealer marketing image" />
		<section class="product_description">
			<div class="content">
				<h2>Description</h2>
				'.$page->product["description"].'
			</div>
			<div class="product_img_1" style="background-image:url(\''.$page->product["squares"][1]["file"] .'?w=500&h=320&fit=fill\')"></div>
			<div class="product_img_2" style="background-image:url(\''.$page->product["squares"][2]["file"] .'?w=500&h=320&fit=fill\')"></div>
		</section>
	</div>
	
	<section class="product_applications">
		<div class="content">
			<h2>Applications</h2>
			Exceptional when used for:
			<div class="ok">';

	echo $page->product["applications_yes"];
	echo '
			</div>';
	if (strlen($page->product["applications_no"])>1) {
		echo '
			Not for use on:
			<div class="no">
				'.$page->product["applications_no"].'
			</div>';
	}

	echo '
		</div>
	</section>
	
	<a name="benefits" id="benefits_anchor" class="anchor"></a>
	
	<section id="benefits" class="gray_bg">
		<div class="column full">
			<div class="column_interior">
				<h2 class="sub centered">Benefits</h2>
				<div class="tri_column left">
					<h3>'.$GLOBALS["product_column1"].'</h3>
					<div class="content">'.$page->product["benefits"]["provides"].'</div>
				</div>
				<div class="tri_column middle">
					<h3>'.$GLOBALS["product_column2"].'</h3>
					<div class="content">'.$page->product["benefits"]["prevents"].'</div>
				</div>
				<div class="tri_column right">
					<h3>'.$GLOBALS["product_column3"].'</h3>
					<div class="content">'.$page->product["benefits"]["reduces"].'</div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
	</section>
	
	<section id="product_data" class="black_bg">
		<div class="multi_column">
			<div class="column left">
				'.$page->product["technical"].'
			</div>
			<div class="column right">';
	if (isset($page->product["graph"]) && $page->product["graph"] > "") {
		echo '
				<div class="bar_graph">
					<div class="y_axis_label">'.($page->data("graphs")->attr[$page->product["graph"]]["yAxisLabel"]>""?$page->data("graphs")->attr[$page->product["graph"]]["yAxisLabel"]:"Improvement").'</div>';

		//235/500 * 10
		$max = 0;
		$count1 = 0;
		foreach ($page->data("graphs")->attr[$page->product["graph"]]["items"] as $letter => $data) {
			if ($data["value"]) {
				$max = $data["value"] > $max ? $data["value"] : $max;
				$count1++;
			}
		}

		echo '
						<h3 class="title">'.$page->data("graphs")->attr[$page->product["graph"]]["title"].' <small>'.$page->data("graphs")->attr[$page->product["graph"]]["subtitle"].'</small></h3>';

		$count2 = 0;
		foreach ($page->data("graphs")->attr[$page->product["graph"]]["items"] as $letter => $data) {
			if ($data["value"]) {
				$count2++;
				$class_letter = $count2 == $count1 ? "e" : $letter;
				echo '
						<div class="section product_'.$class_letter.'"><div class="name">'. str_replace("Ghostshield ","",$data["name"]) .'</div> <div class="bar" style="height:'.($data["value"] == $max ? 100 : floor($data["value"] / $max * 100)).'%">'.$data["value"].'%</div></div>';
			}
		}

		echo '
					<div class="x_axis_label">'.($page->data("graphs")->attr[$page->product["graph"]]["xAxisLabel"]>""?$page->data("graphs")->attr[$page->product["graph"]]["xAxisLabel"]:"Sealer Product").'</div>
				</div>';
	}
	echo '
			</div>
		</div>
	</section>

	<a name="technical" id="technical_anchor" class="anchor"></a>';

	//if ((isset($page->product["dataSheets"]) && count($page->product["dataSheets"]) > 0) || $page->product["residential"] > 0) {

		echo '

	<section id="tech_docs">
		<div class="column full">
			<div class="column_interior">
				<h2 class="sub"><span class="g">Technical</span> Documents</h2>
				<ul class="files">';

		if (isset($page->product["dataSheets"]) && count($page->product["dataSheets"]) > 0) {
			foreach ($page->product["dataSheets"] as $datasheet) {
				$type = "Document";
				if ($datasheet["type"] == "application/msword") $type = "DOC";
				if ($datasheet["type"] == "application/ms-word") $type = "DOC";
				if ($datasheet["type"] == "application/vnd.openxmlformats-officedocument.wordprocessingml.document") $type = "DOC";
				if ($datasheet["type"] == "application/vnd.ms-excel") $type = "XLS";
				if ($datasheet["type"] == "application/ms-excel") $type = "XLS";
				if ($datasheet["type"] == "application/pdf") $type = "PDF";
				echo '
					<li><a href="'.$datasheet["file"].'" class="file '.strtolower($type).'"><span class="novalign"><span class="name">'.$datasheet["title"].'</span> <span class="details"><span class="parenthesis">(</span>'.$type.', '.number_format($datasheet["size"]/1000).'k<span class="parenthesis">)</span></span></span></a>';

			}
		}
		
		if ($page->product["residential"] > 0) echo '
					<li><a href="//ghostshield.com/identity/Ghostshield_Pro_Line_Brochure_2018.pdf" class="file pdf"><span class="novalign"><span class="name">Ghostshield Pro Line Brochure</span> <span class="details"><span class="parenthesis">(</span>PDF, 8.1mb<span class="parenthesis">)</span></span></span></a>';
		if ($page->product["commercial"] > 0) echo '
					<li><a href="//ghostshield.com/identity/Ghostshield_Commercial_Line_Brochure_2018.pdf" class="file pdf"><span class="novalign"><span class="name">Ghostshield Commercial Line Brochure</span> <span class="details"><span class="parenthesis">(</span>PDF, 4.1mb<span class="parenthesis">)</span></span></span></a>';
/*					<!--li><a href="http://ghostshield.com/wp-content/uploads/2015/06/Lithi-Tek-LS-9500AI.pdf" class="file pdf"><span class="name">Application Instructions</span> <span class="details"><span class="parenthesis">(</span>PDF, 764k<span class="parenthesis">)</span></span></a>
					<li><a href="http://ghostshield.com/wp-content/uploads/2015/01/9500-MSDS1.pdf" class="file pdf"><span class="name">Material Safety Data Sheet (MSDS)</span> <span class="details"><span class="parenthesis">(</span>PDF, 1mb<span class="parenthesis">)</span></span></a-->*/
					
		echo '
				</ul>
				<div class="clear"></div>
			</div>
		</div>
	</section>';

	//}

	echo '

	<section id="more" class="white off_white_bg">
		<div class="column full">
			<div class="column_interior">
				<h2><span class="g">Auxiliary</span> Information</h2>
				<ul class="tabs">';

	$count = 0;
	$tabs = '';
	if (isset($page->product["additional"]) && count($page->product["additional"]) > 0) foreach ($page->product["additional"] as $item) {
		$count++;
		// may need to replace tab label spaces with nbsp
		echo '
					<li id="tab-container'.$count.'" class="tab">
						<input type="radio" name="tabs" id="tab'.$count.'" '.($count==1?'checked="checked" ':'').'/>
						<label for="tab'.$count.'" id="tab_label'.$count.'" class="label-num'.$count.'">'.$page->data("additional")->attr[$item]["label"].'</label>
						<div id="tab-content'.$count.'" class="tab-content'.($count==1?' initial':'').'">
							<div class="tab-interior">
								<h3>'.$page->data("additional")->attr[$item]["label"].'</h3>
								'.$page->data("additional")->attr[$item]["content"].'
							</div>
						</div>
					</li>';

	}

	$articles = db::_query('SELECT `blog`.* FROM `blog_products` LEFT JOIN `blog` ON `blog_products`.`post_id` = `blog`.`id` WHERE `blog_products`.`site` = "'.$page->env.'" AND `blog_products`.`product` = "'.$page->product["id"].'"');

	if (sizeof($articles) > 0) {
		$count++;

		echo '
					<li id="tab-container'.$count.'" class="tab">
						<input type="radio" name="tabs" id="tab'.$count.'" />
						<label for="tab'.$count.'" id="tab_label'.$count.'" class="label-num'.$count.'">Related Articles</label>
						<div id="tab-content'.$count.'" class="tab-content">
							<div class="tab-interior">
								<h3>Related Articles</h3>
								<ul>';
		foreach ($articles as $article) {
			echo '
									<li><a href="/articles/'.$article["url"].'">'.$article["title"].'</a>';
		}
		echo '
								</ul>
							</div>
						</div>
					</li>';

	}

	echo '
				</ul>
				<div class="more_footer"></div>
			</div>
		</div>
	</section>

	<a name="gallery" id="gallery_anchor" class="anchor"></a>

	<section id="gallery">
		<h2>Customer <span class="g">Gallery</span></h2>';

	echo $gallery->create($gallery_items, $page->product["name"], (!$page->product["residential"]?"commercial":"residential"));

	echo  '
		<div id="gsdz-email-confirm" class="inactive"></div>
		<form id="gsdropzone" action="'. $uri .'" class="dropzone dz-clickable" enctype="multipart/form-data">
			<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
			<div class="email_input">
				<div class="email_input_inner">
					<p><p>To share media of your '.$page->product["name"].' project please enter your email:</p>
					<span><input type="text" name="email" value="" placeholder="your@email.com" id="email_input_input" /></span> <span id="email_input_button"></span>
					<p><small>This email will only be used in regard to shared media. <a href="/upload-legal">More Info</a></small></p>
				</div>
			</div>
			<div id="gsdz_add" class="gsdz_hidden dz-clickable">+</div>
			<div id="gallery_save"><div id="gallery_save_text" class="dz-clickable"></div></div>
			<div class="fallback">
				<input type="hidden" name="model" value="'.$page->model.'" />
				<input name="file" accept="image/*,video/*" type="file" multiple />
				<input type="submit" value="Upload Image" class="xsmall hex-button" />
			</div>
		</form>
		<div id="gsdz_confirm" class="gsdz_hidden">
			<div><p>We review all submissions before they appear on the site. Thank you for your patience.</p></div>
		</div>';

}

$gallery_item = new galleryItem($_REQUEST);
$gallery_item->upload();

if (!isset($_REQUEST["dropzone"])) {

	echo '
		<div id="gallery_status" data-total="0" data-error="0"></div>
		<form id="gallery_captions" method="POST"></form>

	</section>';
	
	if ($page->product["waterproofs"]) echo '
	
	<section id="videos" class="white videos">
		<div class="column full">
			<div class="column_interior">
				<h2 class="title centered">Ghostshield <span class="g">in Action</span></h2>
				<div class="videos">
					<iframe src="https://player.vimeo.com/video/208700776" width="392" height="218" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen=""></iframe>
					<iframe src="https://player.vimeo.com/video/208700470" width="392" height="218" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen=""></iframe>
					<iframe src="https://player.vimeo.com/video/208700232" width="392" height="218" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen=""></iframe>
				</div>
			</div>
		</div>
	</section>';


	if (isset($page->product["videoTutorial"]) && $page->product["videoTutorial"] > "") {

		if (strpos($page->product["videoTutorial"], "youtube.com")) {
			$video = '
	<section id="video_feature">
		<iframe width="100%" height="100%" src="'.$page->product["videoTutorial"].'" frameborder="0" allowfullscreen class="youtube"></iframe>
		<div class="clear"></div>
	</section>';
			echo $video;
		} elseif (strpos($page->product["videoTutorial"], "vimeo.com")) {

			$video = @file_get_contents('https://vimeo.com/api/oembed.json?url='.$page->product["videoTutorial"]);

			// Get oEmbed iframe
			if ($video !== false) {
				$vimeoJSON = json_decode($vimeo);
	/*echo '
	<section id="video_feature">
		/*<iframe src="'.$page->product["videoTutorial"].'" width="100%" height="100%" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen class="vimeo"></iframe>
		<div class="clear"></div>
	</section>';*/

				echo '		
	<section id="video_feature">
		'.$vimeoJSON->html.'
		<div class="clear"></div>
	</section>';
			}
		}

		if ($video) echo '
	<div id="subscribe_and_like">
		<script src="https://apis.google.com/js/platform.js"></script>
		<div id="subscribe_button" >Subscribe for more Ghostshield videos:</div> <div id="subscribe_button_container"><div class="g-ytsubscribe" data-channelid="UC9X3Cl8uRVG5X9XlXfmUryw" data-layout="default" data-count="hidden"></div></div>
	</div>';

	}

	if ($page->product["showInComparisons"]) {

		echo '
	
	<a name="comparisons" id="comparisons_anchor" class="anchor"></a>
	
	<section id="comparison" class="white white_bg">
		<h2 class="centered sub"><span class="g">Product</span> Comparisons</h2>
		<div class="horizontal_scroll">
			<table cellpadding="0" cellspacing="0">
				<thead>
					<tr valign="bottom">
						<th>
						<th class="product_1"><!--img src="//assets.'.$page->host.'/img/bottles/'.str_replace(" ","-",strtolower($page->product["name"])).'.png" class="bottle" /--> <div class="pc_name"><div class="'.str_replace(" ","-",strtolower($page->product["name"])).' product_name selected selected"><span>'.str_replace(" ","-",strtolower($page->product["name"])).'</span></div></div>
						<th class="product_2">';

		$menu = "";
		$class = "unselected";
		foreach ($page->data("products")->attr as $product) {
			if ($page->model != $product["model"] && $product["showInComparisons"]) {
				$menu .= '
										<option value="'.$product["id"].'"'; // class="product_name '.strtolower(str_replace(" ", "-", $product["name"]));
				if (isset($page->product["comparisons"][0]) && $page->data("comparisonDetails")->attr[$page->product["comparisons"][0]]["name"]==$product["name"]) {
					$menu .= ' selected';
					$class = "product_name selected ".str_replace(" ","-",strtolower($product["name"]));
				}
				//echo $products[$page->model]["comparisons"][0]."-".$product["id"];
				$menu .= ' data-id="'.$product["id"].'">'.$product["name"].'</option>'; // add class "selected"
			}
		}

		echo '
							<div class="pc_name">
								<div class="'.$class.'">
									<select name="comarison1" id="selector1" class="comparison">
										<option value="" class="product_name select_product selected">Select Product</option>'.$menu.'
									</select>
								</div>
							</div>';

		$menu = "";
		$class = "unselected";
		foreach ($page->data("products")->attr as $product) {
			if ($page->model != $product["model"] && $product["showInComparisons"]) {
				$menu .= '
												<option value="'.$product["id"].'"';
				if (isset($page->product["comparisons"][1]) && $page->product["comparisons"][1] == $product["id"]) {
					$menu .= ' selected';
					$class = "product_name selected ".str_replace(" ","-",strtolower($product["name"]));
				}
				$menu .= ' data-id="'.$product["id"].'">'.$product["name"].'</option>'; // add class "selected"
			}
		}

		echo '
					<th class="product_3">
						<div class="pc_name">
							<div class="'.$class.'">
								<select name="comarison2" id="selector2" class="comparison">
									<option value="" class="product_name select_product selected">Select Product</option>'.$menu.'
								</select>
							</div>
						</div>
					</div>
				</thead>
				<tbody id="productComparisonTBody">';

		$productComparisonLabels = array(
			"uses" => "Uses",
			"chemistry" => "Chemistry",
			"carrier" => "Carrier",
			"finish" => "Finish",
			"color" => "Color",
			"availability" => "Availability",
			"packaging" => "Packaging",
			"coverage" => "Coverage",
			"reconstitutionRatio" => "Reconstitution Ratio",
			"applicationMethod" => "Application Method",
			"applicationTemperatureLimit" => "Application Temperature Limit",
			"numberOfCoats" => "Number of Coats",
			"timeBetweenCoats" => "Time Between Coats",
			"dryingTime" => "Drying Time",
			"dryingTimeFullCure" => "Drying Time (Full-Cure)",
			"timeBeforeAfterRain" => "Time Before/After Rain",
			//"applicationNote" => "Application Note",
			"newConcrete" => "New Concrete",
			"maintenance" => "Maintenance",
			"storageShelfLife" => "Storage Shelf-Life",
			"reCoatable" => "Re-coatable",
			"paintable" => "Paintable",
			"vocContent" => "VOC Content",
		/*	"Warranty" => "warranty",*/
			"costPerSquareFoot" => "Cost Per Square Foot",
		);

		$thisProductDetails = $page->data("comparisonDetailsByProduct")->attr[$page->product["id"]];
		foreach ($thisProductDetails as $detailName => $detail1) {
			$detail2 = (isset($page->product["comparisons"][0]) && isset($page->data("comparisonDetails")->attr[$page->product["comparisons"][0]][$detailName]) ? $page->data("comparisonDetails")->attr[$page->product["comparisons"][0]][$detailName] : '');
			$detail3 = (isset($page->product["comparisons"][1]) && isset($page->data("comparisonDetails")->attr[$page->product["comparisons"][1]][$detailName]) ? $page->data("comparisonDetails")->attr[$page->product["comparisons"][1]][$detailName].'' : '');
	
			if (isset($productComparisonLabels[$detailName]) && $detail1 > "") echo '
					<tr id="pc_row_'.$detailName.'">
						<td class="label">'.$productComparisonLabels[$detailName].'</td>
						<td class="product_1">'.$detail1.'</td>
						<td class="product_2'.($detail2 > "" && $detail2 !== $detail1 ? ' different' : '').'">'.$detail2.'</td>
						<td class="product_3'.($detail3 > "" && $detail3 !== $detail1 ? ' different' : '').'">'.$detail3.'</td>';
		}

		echo '
				</tbody>
			</table>
		</div>
	</section>';

		echo '
	<script type="text/javascript">
		var comparisonData = {';
		$new = true;
		foreach($page->data("comparisonDetailsByProduct")->attr as $m => $details) {
			echo ( $new ? '' : ',' ).'
			id'.$m.': {';
			$new2 = true;
			foreach($details as $name => $value){
				echo ( $new2 ? '' : ',' ).'
				'.$name.': "'.$value.'"';
				$new2 = false;
			}
			echo '
			}';
			$new = false;
		}
		echo '
		}
	</script>';

	}

	if (isset($page->product["distributors"])) {
		echo '

	<section id="buy" class="black">
		<div class="column full">
			<div class="column_interior">
				<h2 class="sub centered">'. $GLOBALS["buy_section_title"] .'</h2>
				<ul class="width'.(count($page->product["distributors"])).'">';

		foreach ($page->product["distributors"] as $id) {
			echo '
					<li><a href="'.str_replace("%s", 'ghostshield+'.$page->model, $page->data("onlineDistributor")->attr[$id]["search"]).'" class="'.str_replace(" ", "", strtolower($page->data("onlineDistributor")->attr[$id]["name"])).'" target="_blank" style="background-image:url('.$page->data("onlineDistributor")->attr[$id]["logoWhite"]["file"].')">'.$page->data("onlineDistributor")->attr[$id]["name"].'</a></li>';
		}

		echo '
				</ul>
			</div>
		</div>
	</section>';
	} else echo '
	<div id="page_footer_before"></div>';

	echo $gallery->overlay();

	echo $page->footer();

}
?>
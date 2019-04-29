<?php

$pf_html = "";

if (!isset($page)) {

	require_once("lib/page.class.php");
	$page = new page();
	$page->addCSS("product_finder_new.css");


	$included = false;

	if (!isset($_REQUEST["ajax"])) $pf_html .= $page->header("Product Finder");

} else $included = true;

if ($cms_data = /*json_decode(*/$page->data("formattedData")->attr["product-finder-questions"]["value"]/*, true)*/) {
	$finder = $cms_data;
} else {
	$pf_html .= '<p>An error occurred. You may still use the Product Finder but it may be slightly out-of-date.</p>';
	
	$handle = fopen("product_finder.json", "rb");
	$contents = stream_get_contents($handle);
	fclose($handle);
	
	$finder = json_decode($contents, true);
	
}

if (!$included) {

	if (!isset($_REQUEST["ajax"])) $pf_html .= '
		<div id="banner">
			<div class="video_wrapper">
				<img src="//assets.'.$page->host.'/img/collage-dark.jpg" />
			</div>
		</div>
		
		<a name="product_finder" id="product_finder_anchor" class="anchor"></a>

		<section id="product_finder" class="non_ajax">';

}

$pf_html .= '
			<div class="pf_interior">
				<div class="valign animate-in">';

function val($val) {
	$val = preg_replace_callback('/[^A-z][a-z]/', function($m) { return strtoupper($m[0]); }, urldecode($val));
	return ucwords($val);
}

if (!isset($_REQUEST["pf"])) {
	$pf_html .= '
					<h2 class="always_centered">Product&nbsp;Finder</h2>
					<a href="/product-finder/?pf" class="splash arrow link">
						<!--h2 class="not_always_centered">Pro Line<br /> Product Finder</h2-->
						<div class="status">
								<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 17.3 25.7" style="enable-background:new 0 0 17.3 25.7;" xml:space="preserve">
									<polygon class="st0" points="0,24.6 16,12.9 0,1.1 "></polygon>
								</svg>
								Find your product
						</div>
					</a>
					<p class="splash-subtext"><span>Not sure which of our products best fits the needs of your project?</span>
					<span>Answer questions about your specific application for a product recommendation.</span></p>';

} else if (isset($_REQUEST["pf"])) {

	$answers = [];
	$answer_txt = [];
	$query = isset($_REQUEST["ajax"]) ? "&ajax" : "";
	$form = isset($_REQUEST["ajax"]) ? '<input type="hidden" name="ajax" value="true" />' : "";
	$tier = $finder;
	$index = 1;
	$sqft = isset($_REQUEST["sqft"]) ? $_REQUEST["sqft"] : false;
	
	for ($i = 1; $i < 11; $i++) {
		if (isset($_REQUEST["q".$i])) {
		
			if (strpos($_REQUEST["q".$i], "sqft=") === 0) {
				
				$sqft = str_replace("sqft=", "", $_REQUEST["q".$i]);
				
			} else {
		
				$val = val($_REQUEST["q".$i]);
			
				$answers[$i] = $val;
				if (isset($tier->answers->{$val}->noun)) $answer_txt[$i] = $tier->answers->{$val}->noun;
				$query .= "&q".$i."=".urlencode($val);
				$form .= '<input type="hidden" name="q'.$i.'" value="'.$val.'" />';
			
				$tier = isset($tier->answers->{$val}) ? $tier->answers->{$val} : '';
				$index = $i + 1;
			
			}
		
		}
	}
	
	if (isset($tier->question)) {

		$pf_html .= '
					<h3>Question #'.$index.'</h3>
					<p>'.$tier->question.'</p>
					<ul class="answers">';
		
		//sort($tier->answers);
		$answershtml = array();
		foreach ($tier->answers as $answer => $tier2) {
			$file = "assets/img/icons/applications/". str_replace(" ","_",strtolower(str_replace("zOther","Other",$answer))) .".svg";
			$answershtml[] = '
						<li><a rel="nofollow" href="/product-finder/?pf'.$query.'&q'.$index.'='.urlencode($answer).'"'.(file_exists($file) && $index == 1?' class="has_image" style="background-image:url('.str_replace("assets/","//assets.".$page->host."/",$file).')"':'').'><span class="valign">'.$answer.'</span></a></li>';
		}
		sort($answershtml);
		foreach ($answershtml as $answerhtml) { $pf_html .= str_replace("zOther","Other",$answerhtml); }
		
		$pf_html .= '
					</ul>';
	
	} else if (isset($tier->product)) {
	
		if (is_numeric($sqft)) {
	
			$project_title = "";
			$temp_query = "";
			for($i = 1; isset($answers[$i]); $i++) {
				if (strtolower($answers[$i]) != "no") {
					if ($i > 1) $project_title .= " <span>//</span> ";
					$project_title .= '<a href="/product-finder/?pf'.$temp_query.'">';
					$project_title .= isset($answer_txt[$i]) ? $answer_txt[$i] : $answers[$i];
					$project_title .= '</a>';
				}
				$temp_query .= '&q'.$i.'='.urlencode($answers[$i]);
			}
	
			$pf_html .= '
					<p class="prelude">We have a product recommendation for your project needs:</p>
					<h3 class="project_name">'.$project_title.'</h3>';
		
			if (isset($tier->primer)) {
				$pf_html .= '
					<div class="pf_product pf_primer">
						<img src="//assets.'.$page->host.'/img/bottle/angled/'.$tier->primer.'.png" height="120" />
						<h3 class="product_name selected '.str_replace(" ","-",strtolower($page->data("products")->attr[$tier->primer]["name"])).'">'.$page->data("products")->attr[$tier->primer]["name"].'</h3>
						<div class="pf_description">
							<p>'.$tier->primer_note.'';
				
				if ($sqft > 0) {
					$psqft = array();
					$psqft["project_sqft"] = $sqft;
					$psqft["project_coats"] = $page->data("products")->attr[$tier->primer]["attributes"]["coats"];
					$psqft["project_coverage"] = $page->data("products")->attr[$tier->primer]["perGallon"];
					$psqft["project_model"] = $page->data("products")->attr[$tier->primer]["model"];
					$psqft["project_containers"] = array();
					$containersById = $page->getCacheData("containers", $page->env);
					foreach ($page->data("products")->attr[$tier->primer]["containers"] as $container) {
						$psqft["project_containers"]["".$containersById[$container]["volume"]] = $containersById[$container]["name"];
					}
					include("sqftcalculate.php");
				}
				
				$pf_html .= '</p>
						</div>
						<ul class="actions">
							<li><a href="/product/'.$tier->primer.'/" target="_top" class="link_out"><span class="valign">'.$GLOBALS["view_button_text"].'</span></a></li>
							<li><a href="https://concretesealersolutions.com/product/'.str_replace(" ","-",strtolower($page->data("products")->attr[$tier->primer]["name"])) .'/" target="_blank" class="link_out"><span class="valign">'.$GLOBALS["buy_button_text"].'</span></a></li>
						</ul>
					</div>';
			}
		
			$pf_html .= '
					<div class="pf_product">';

			if (file_exists('assets/img/bottle/angled/'.$tier->product.'.png')) {
				$pf_html .= '
						<img src="//assets.'.$page->host.'/img/bottle/angled/'.$tier->product.'.png" height="120" alt="'.$tier->product.' Concrete Sealer Bucket Image" />';
			} elseif (file_exists('assets/img/bucket/'.$tier->product.'.png')) {
				$pf_html .= '
						<img src="//assets.'.$page->host.'/img/bucket/'.$tier->product.'.png" height="120" alt="'.$tier->product.' Concrete Sealer Bucket Image" />';
			}

			$pf_html .= '
						<h3 class="product_name selected '.str_replace(" ","-",strtolower($page->data("products")->attr[$tier->product]["name"])).'">'.$page->data("products")->attr[$tier->product]["name"].'</h3>
						<div class="pf_description">
							<p>'.$tier->product_note.'';
				
			if ($sqft > 0) {
				$psqft = array();
				$psqft["project_sqft"] = $sqft;
				$psqft["project_coats"] = $page->data("products")->attr[$tier->product]["attributes"]["coats"];
				$psqft["project_coverage"] = $page->data("products")->attr[$tier->product]["perGallon"];
				$psqft["project_model"] = $page->data("products")->attr[$tier->product]["model"];
				$psqft["project_containers"] = array();
				$containersById = $page->getCacheData("containers", $page->env);
				foreach ($page->data("products")->attr[$tier->product]["containers"] as $container) {
					$psqft["project_containers"]["".$containersById[$container]["volume"]] = $containersById[$container]["name"];
				}
				include("sqftcalculate.php");
			}
				
			$pf_html .= '</p>
						</div>
						<ul class="actions">
							<li><a href="/product/'.$tier->product.'/" class="link_out"><span class="valign">'.$GLOBALS["view_button_text"].'</span></a></li>
							<li><a href="https://concretesealersolutions.com/product/'.str_replace(" ","-",strtolower($page->data("products")->attr[$tier->product]["name"])) .'/" target="_blank" class="link_out"><span class="valign">'.$GLOBALS["buy_button_text"].'</span></a></li>
						</ul>
					</div>';

		} else {
		
			$pf_html .= '
					<h3>Project Square Footage</h3>
					<p>Optionally calculate quantity estimates* by providing the square footage (width &times; length) of your project.</p>
					<form method="GET" action="/product-finder/" data-query="'.$query.'">
						'.$form.'
						<input type="number" name="sqft" id="product_finder_sqft" />
						<input type="submit" name="pf" class="hex-button small" value="Submit" />
						<a href="/product-finder/?pf'.$query.'&sqft=0" class="hex-button small"><span class="valign">Skip</span></a>
					</form>
					<p class="legal">* Coverage may vary due to porosity and texture of substrate.</p>
					<p class="legal">Disclaimer: Recommendations are based on common applications, please call us at <a href="tel:8555738383">(855)&nbsp;573-8383</a> Monday - Friday betwen 9 AM and 5 PM <acronym title="Eastern Standard Time">EST</acronym> to discuss special application needs.</p>';

		}
	
	} else {
		
		if (isset($_REQUEST["q1"]) && strtolower($_REQUEST["q1"]) == "other") {
		
			// > 9am, < 5pm, M-F only
			if (date('H') > 9 && date('H') < 17 && date("N") < 6) {
				$pf_html .= '
					<h3>Have a special need?</h3>
					<div class="pf_info">
						<p>Our experts are happy to help you select the right sealer.<br />We\'re accessible until 5 PM <acronym title="Eastern Standard Time">EST</acronym>, please give us a call at <a href="tel:8555738383">(855) 573-8383</a>.</p>
					</div>';
			} else {
				$pf_html .= '
					<h3>Have a special need?</h3>
					<div class="pf_info">
						<p>Our experts will be happy to help you select the right sealer during our business hours.</p>
						<p>Please give us a call at <a href="tel:8555738383">(855) 573-8383</a> Monday - Friday betwen 9 AM and 5 PM <acronym title="Eastern Standard Time">EST</acronym>.</p>
					</div>';
			}
		
		} elseif (isset($tier->response)) {
			$pf_html .= '
					'.$tier->response;
		
		} else {
			if (!$included) header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
			$pf_html .= '
					<h3>Error!</h3>
					<div class="pf_info">
						<p>There has been an error. Please contact us.</p>
					</div>';
		}
	
	}

	if ($index >= 1) {
		
		$pf_html .= '
					<ul class="pf_navigation">';
		
		if (is_numeric($sqft)) {
			$pf_html .= '
						<li><a href="/product-finder/?pf'.preg_replace("/\&sqft\=[0-9\.]+/", "", $query).'" class="back">Re-enter Square Footage</a></li>';
		}
		// else ?
		if ($index > 1) $pf_html .= '
						<li><a href="/product-finder/?pf'.preg_replace("/\&q".($index-1)."\=.*/", "", $query).'" class="back">Return to Question #'.($index-1).'</a></li>';
	
		$pf_html .= '
						<li><a href="/product-finder/?pf-restart" class="restart">Start Over</a></li>
					</ul>';
		
	}

}

$pf_html .= '
				</div>
			</div>';

if (!$included) {

	if (!isset($_REQUEST["ajax"])) {
		
		$pf_html .= '
		</section>

		<div id="page_footer_before"></div>';
		
		$pf_html .= $page->footer();
	
	}

}

echo $pf_html;

?>
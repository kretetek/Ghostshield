<?php

require_once("lib/page.class.php");

$page = new page();
$page->setDescription("Approved regional applicators offering Ghostshield sealers with their service offerings.");

//require_once("inc/header.php");
echo $page->header("Applicators");

//$page->data("applicators", get_cache_data("applicators", ($page->dev?"dev":"pub")));

echo '
	<div id="banner">
		<div class="video_wrapper">
			<img src="//assets.ghostshield.com/img/bottle-bg/natural.jpg" />
		</div>
	</div>

	<section id="full_page_content" class="white">
		<div class="column full">
			<div class="column_interior">
				<h1 class="title">Ghostshield Applicators</h1>
				<ul class="applicators">';

foreach ($page->data("applicatorsSorted")->attr as $applicator) {
	//$applicator["longitude"]
	//$applicator["latitude"]
	echo '
					<li>
						<a href="'.($applicator["url"]?$applicator["url"]:"https://www.google.com/#safe=active&q=".$applicator["name"]."+".urlencode($applicator["phone"])).'" target="_blank" class="applicator">
							<div class="valign">
								'.(isset($applicator["logo"]["file"])?'<div class="applicator_logo"><span><img src="'.$applicator["logo"]["file"].'" /></span></div>':'').'
								<h3>'.$applicator["name"].'</h3>
								<p class="address">'.$applicator["address"].'</p>
						'.$applicator["phone"].'
								<!--div class="blurb">'.$applicator["blurb"].'</div-->
							</div>
						</a>
					</li>';
}

echo '
				</ul>
			</div>
		</div>
	</section>

	<section id="full_page_content" class="white">
		<div class="column full">
			<div class="column_interior">
				<h2 class="title">Join Our Family</h2>
				<form method="POST" class="contact">
					<label for="contact_email">Email</label>
					<input type="text" class="half_width contact_email inverse" placeholder="Email Address" name="contact_email" />
					<label for="contact_name">Company Name</label>
					<input type="text" class="half_width contact_name inverse" placeholder="Company Name" name="contact_name" />
					<label for="contact_address">Address</label>
					<input type="text" class="half_width contact_address inverse" placeholder="Company Address" name="contact_address" />
					<label for="contact_phone">Phone Number</label>
					<input type="text" class="half_width contact_phone inverse" placeholder="Phone" name="contact_phone" />
					<label for="contact_comment">Comments</label>
					<textarea name="contact_comment" class="contact_comment inverse" placeholder="'.$GLOBALS["comment_placeholder"].'"></textarea>
					<input type="hidden" name="contact_page" value="'.$page->location['filename'].(isset($_REQUEST["model"])?'/'.$_REQUEST["model"]:'').'" />
					<input type="submit" value="'.$GLOBALS["contact_button_text"].'" class="contact_submit" />
				</form>
			</div>
		</div>
	</section>

	<div id="page_footer_before"></div>';

//require_once("inc/footer.php");
echo $page->footer();

?>
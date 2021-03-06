<?php

require_once("lib/page.class.php");

$page = new page();
$page->setDescription("Regional and online distributors of Ghostshield concrete sealer products.");

//require_once("inc/header.php");
echo $page->header("Distributors");

//$distributors = get_cache_data("distributors", ($page->dev?"dev":"pub"));

echo '
	<div id="banner">
		<div class="video_wrapper">
			<img src="//assets.ghostshield.com/img/bottle-bg/natural.jpg" />
		</div>
	</div>

	<section id="distributor_list" class="white">
		<div class="column full">
			<div class="column_interior">
				<h1 class="title">Ghostshield Distributors</h1>
				<ul class="distributors">';

foreach ($page->data("distributorsSorted")->attr as $distributor) {
	//$distributor["longitude"]
	//$distributor["latitude"]
	echo '
					<li>
						<div class="distributor">
							<a href="'.$distributor["url"].'" target="_blank" class="distributor_logo">'.($distributor["logo"]["file"]?'<img src="'.$distributor["logo"]["file"].'" />':'').'</a>
							<h3><a href="'.$distributor["url"].'" target="_blank">'.$distributor["name"].'</a></h3>
							<p class="address">'.$distributor["address"].(isset($distributor["address2"])?'<br />'.$distributor["address2"]:"").'</p>
							<p class="phone"><a href="tel:'.$distributor["phone"].'">'.$distributor["phone"].'</a></p>
						</div>
						<div class="blurb">'.$distributor["blurb"].'</div>
						<div class="blurb_blur"></div>
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
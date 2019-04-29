<?php

require_once("inc/header.php");

?>

	<div id="banner">
		<div class="video_wrapper">
			<img src="assets/img/commercial/bridge8.jpg" />
		</div>
	</div>
	
	<section id="welcome">
		<div class="column full">
			<div class="column_interior">
				<h2 class="centered">Header</h2>
				<p id="intro">Content</p>
			</div>
		</div>
	</section>
	

<!--<form onsubmit="return executeQuery();" id="cse-search-box-form-id">-->
<form action="search_site.php" method="get">
  <!-- This is the input searc box -->
  <input type="text" id="cse-search-input-box-id" size="25" autocomplete="off" style="background-image: url(https://www.google.com/cse/static/en/google_custom_search_watermark.gif); background-color: rgb(255, 255, 255); background-position: 0% 50%; background-repeat: no-repeat no-repeat;">
  <!-- This is the search button -->
  <input type="submit" value="Search">
<!--<input name="siteurl" type="hidden" value="googlecustomsearch.appspot.com/elementv2/results-only_form_v2.html"><input name="ref" type="hidden" value=""><input name="ss" type="hidden" value="">-->
</form>

<?php

require_once("inc/footer.php");

?>
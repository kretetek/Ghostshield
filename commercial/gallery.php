<?php

//$admin_email = array("benjamin@ghostshield.com", "jeff@ghostshield.com");
//$show_dates_for_bydate_sort = false;

define("APP_PREFIX",	"gallery");
define("APP_PATH",		"/gallery/admin/");
define("LIB_DIR",		"../../lib/");

require_once("../lib/page.class.php");
require_once("../lib/parsedown.php");

$Parsedown = new Parsedown();

$page = new page("../../");
$page->setDescription("Pictures and videos of large-scale commercial products sealed with Ghostshield Iso-Tek concrete sealant.");

$page->addCSS("commercialPortfolio.css");
$page->addScript("videoModal.js");

require_once("../lib/gallery.class.php");

	echo $page->header("Commercial // Gallery", "commercial");
?>

<section id="full_page_content" class="white article">

<div class="section-headline">
	<h1>Commercial Portfolio</h1>
	<p>Recent projects with a specified Ghostshield concrete sealer</p>
	<div class="small-divider"></div>
</div>

<div class="projects">

<?php foreach ($page->data("commercialPortfolio")->attr as $project): ?>

<?php
	$vidId = $project['video'];
	$vidId = str_replace(array('https://vimeo.com/', '\\'), '', $vidId);
?>

	<div class="project-container">
		<div class="video-container">
			<div class="thumbnail" style="background-image: url('<?php echo $project['image']['file']; ?>');"></div>
		</div>
		<div class="info-container">
			<h2 class="project-title"><?php echo $project['title']; ?></h2>
			<p><?php echo $Parsedown->text($project['info']); ?></p>
			<?php if(sizeof($project['video']) > 0): ?>
				<div class="btn-container">
					<button vid-title="<?php echo $project['title']; ?>" vid-src="<?php echo $vidId; ?>" class="hex-button small modal_opener">Watch Video</button>
				</div>
			<?php endif; ?>
		</div>
	</div>

<?php endforeach; ?>

</div>

</section>

<!-- MODAL -->

<div class="modal" style="display: none">
  <div class="overlay"></div>
  <div class="modal_content">
    <!-- Dynamic Section -->
    <h2 id="video-title"></h2>
	<iframe id="video-embed" src="" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
    <!-- End of Dynamic Section -->
    <button title="Close" class="close_modal">
      X
    </button>
  </div>
</div>
</div>

<?php

echo '
	<div id="page_footer_before"></div>';

echo $page->footer(); //require_once("inc/footer.php");
?>
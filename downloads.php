<?php

require_once("lib/page.class.php");
$page = new page();
$page->addCSS("files.css");
$page->addCSS("linklist.css");
//$page->addCSS("sds.css?1");

echo $page->header("Brochure Downloads");

echo '

		<div id="banner">
			<div class="video_wrapper">
				<img src="//assets.'.$page->host.'/img/collage-dark.jpg" />
			</div>
		</div>
	
		<section id="tech_docs">
			<div class="column full">
				<div class="column_interior">
					<h1 class="sub">Brochure Downloads</h1>
					<p>Digital brochures are provided in Adobe Portable Document Format (PDF); please contact us to inquire about printed versions.</p>
					<p></p>
					<ul class="files file_list confined">';
	
/*
		foreach ($page->product["dataSheets"] as $datasheet) {

			$type = "Document";
			if ($datasheet["type"] == "application/msword" || $datasheet["type"] == "application/vnd.openxmlformats-officedocument.wordprocessingml.document") $type = "DOC";
			if ($datasheet["type"] == "application/vnd.ms-excel") $type = "XLS";
			if ($datasheet["type"] == "application/pdf") $type = "PDF";
			echo '
						<li><a href="'.$datasheet["file"].'" class="file '.strtolower($type).'"><span class="novalign"><span class="name">'.$datasheet["title"].'</span> <span class="details"><span class="parenthesis">(</span>'.$type.', '.number_format($datasheet["size"]/1000).'k<span class="parenthesis">)</span></span></span></a>';

		}
*/
	
echo '
						<li><a href="//ghostshield.com/identity/Ghostshield_Pro_Line_Brochure_2018.pdf" class="file pdf"><span class="novalign"><span class="name">Ghostshield Pro Line Brochure</span> <span class="details"><span class="parenthesis">(</span>PDF, 8.1mb<span class="parenthesis">)</span></span></span></a>
					<li><a href="//ghostshield.com/identity/Ghostshield_Commercial_Line_Brochure_2018.pdf" class="file pdf"><span class="novalign"><span class="name">Ghostshield Commercial Line Brochure</span> <span class="details"><span class="parenthesis">(</span>PDF, 4.1mb<span class="parenthesis">)</span></span></span></a>
					</ul>
				</div>
			</div>
		</section>
		<div id="page_footer_before"></div>';

echo $page->footer();

?>
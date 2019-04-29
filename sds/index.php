<?php

require_once("../lib/page.class.php");
$page = new page("../../");
$page->addCSS("files.css");
$page->addCSS("linklist.css");
$page->addCSS("sds.css?1");

echo $page->header("Technical Documents");

if (isset($page->model)) {

	//if (isset($page->product["dataSheets"]) && count($page->product["dataSheets"])>0) {

		echo '
		<section id="tech_docs">
			<div class="column full">
				<div class="column_interior">
					<h1 class="sub">'.$page->product["name"].' Technical Documents</h1>
					<ul class="files file_list confined">';

		foreach ($page->product["dataSheets"] as $datasheet) {

			$type = "Document";
			if ($datasheet["type"] == "application/msword" || $datasheet["type"] == "application/vnd.openxmlformats-officedocument.wordprocessingml.document") $type = "DOC";
			if ($datasheet["type"] == "application/vnd.ms-excel") $type = "XLS";
			if ($datasheet["type"] == "application/pdf") $type = "PDF";
			echo '
						<li><a href="'.$datasheet["file"].'" class="file '.strtolower($type).'"><span class="novalign"><span class="name">'.$datasheet["title"].'</span> <span class="details"><span class="parenthesis">(</span>'.$type.', '.number_format($datasheet["size"]/1000).'k<span class="parenthesis">)</span></span></span></a>';

		}
		
		if ($page->product["residential"] > 0) echo '
					<li><a href="//ghostshield.com/identity/Ghostshield_Pro_Line_Brochure_2018.pdf" class="file pdf"><span class="novalign"><span class="name">Ghostshield Pro Line Brochure</span> <span class="details"><span class="parenthesis">(</span>PDF, 8.1mb<span class="parenthesis">)</span></span></span></a>';
		if ($page->product["commercial"] > 0) echo '
					<li><a href="//ghostshield.com/identity/Ghostshield_Commercial_Line_Brochure_2018.pdf" class="file pdf"><span class="novalign"><span class="name">Ghostshield Commercial Line Brochure</span> <span class="details"><span class="parenthesis">(</span>PDF, 4.1mb<span class="parenthesis">)</span></span></span></a>';

		echo '
					</ul>
					<div class="clear"></div>
				</div>
			</div>
		</section>';

	//}

} else {

	echo '
		<section id="tech_docs">
			<div class="column full">
				<div class="column_interior link_list products">
					<h1 class="sub">'.strip_tags($page->data("pages")->attr["/sds"]["title"]).'</h1>
					<p>'.$page->data("pages")->attr["/sds"]["content"].'</p>
					<ul class="product_list confined">';
	
	foreach ($page->data("products")->attr as $p) {
		$count = count($p["dataSheets"]) + 1;
		/*if (count($p["dataSheets"]) > 0)*/ echo '
						<li><a href="/'.preg_replace("/[^a-z0-9]/","",strtolower($p["name"])).'" class="not_product_name nselected '.preg_replace("/[^a-z0-9\-]/","",str_replace(" ", "-", strtolower($p["name"]))).'">'.$p["name"].'<small>'.$count.' Document'.($count>1 ? "s" : "").'</small></a>';
	}
	
	echo '
					</ul>
				</div>
			</div>
		</section>';

}

echo '
	<div id="page_footer_before"></div>';

echo $page->footer();

?>
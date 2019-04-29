<?php

$cache=_db::_select('love_cache','*',array('name'=>conf::cause("directive")));
if(isset($cache[0]))$cache=$cache[0];

// more than 3 days old?
if(!isset($cache["_updated"]) || time()-(3*60*60*24)>strtotime($cache["_updated"])){

	try{
		$echo='
		<section data-src="'.$scrape["Source_URL"].'">';
		$data=data::get(conf::v("page"));
		foreach($data as $scrape){
			$source_url=$scrape["Source_URL"];
			$element_id=$scrape["Element_ID"];

			libxml_use_internal_errors(true);
			$file=file_get_contents($source_url);
			$xml=new DOMDocument();
			$xml->loadHTML('<meta http-equiv="content-type" content="text/html; charset=utf-8">'.$file);
			$content=$xml->getElementById($element_id);
			$content->setAttribute("id","importedContent");
			$echo.=preg_replace("/clear\:.?both\;?/","",$xml->saveXML($content)).'<div class="source_url">Source: <a href="'.$scrape["Source_URL"].'">'.$scrape["Source_URL"].'</a></div>';
		}
		$echo.='
		</section>';
	
		$cache["value"]=$echo.'<div class="generated">'.date("F j Y").'</div>';
		
		$result=_db::_update("love_cache",array("name"=>conf::cause("directive")),array("value"=>$cache["value"]));
	
	}catch(Exception $e){
		$cache["value"]="Sorry, there was an error loading data for this page: ".$e;
	}
}//else var_dump($cache);

echo $cache["value"];
?>
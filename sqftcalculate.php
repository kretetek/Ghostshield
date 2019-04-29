<?php

//$product = isset($_REQUEST["project_product"]) ? unserialize(urldecode($_REQUEST["project_product"])) : null;

// tolerance, in square feet, of coverage calculation
$tolerance_in_sqft = 50;

// inclusion check
if (!isset($psqft["project_sqft"]) && isset($_REQUEST["project_sqft"])) $psqft["project_sqft"] = $_REQUEST["project_sqft"];
if (!isset($psqft["project_coats"]) && isset($_REQUEST["project_coats"])) $psqft["project_coats"] = $_REQUEST["project_coats"];
if (!isset($psqft["project_coverage"]) && isset($_REQUEST["project_coverage"])) $psqft["project_coverage"] = $_REQUEST["project_coverage"];
if (!isset($psqft["project_model"]) && isset($_REQUEST["project_model"])) $psqft["project_model"] = $_REQUEST["project_model"];
if (!isset($psqft["project_containers"]) && isset($_REQUEST["project_containers"])) $psqft["project_containers"] = unserialize(urldecode($_REQUEST["project_containers"]));

$containers = isset($psqft["project_containers"]) ? $psqft["project_containers"] : null;

if (is_array($containers)) arsort($containers);

$model = isset($psqft["project_model"]) ? $psqft["project_model"] : null;

//var_dump($containers);

if (isset($psqft["project_sqft"]) && is_numeric($psqft["project_sqft"]) && $psqft["project_sqft"] > 0) {

	$required_gallons = /*ceil*/($psqft["project_sqft"] * $psqft["project_coats"] / $psqft["project_coverage"]);
	//echo $_REQUEST["project_sqft"] ."*". $_REQUEST["project_coats"] ."/". $_REQUEST["project_coverage"];

	$reqgal = $required_gallons;
	
	$string = "";
	
	$counter = 0;
	foreach ($containers as $volume => $name) {

		$counter++;
		
		// possible tolerance check prior to committal on a container?
		//$tolerance_check = ($reqgal - floor(round($reqgal) / $volume) * $volume) * $_REQUEST["project_coverage"] > $tolerance_in_sqft;

		$container_count = 0;
		// if the actual required gallons >= container volume, or this is the smallest container
		if ((ceil($reqgal) >= $volume && ceil($reqgal) / $volume >= 0) || ($reqgal > 0 && $counter == count($containers))/* || !$remainder_exceeds_tolerance*/) {

			// how many of these containers do we need to get closer to the required
			// material volume?
			$container_count = floor($reqgal / $volume);
			if ($container_count == 0 /*&& !$remainder_exceeds_tolerance*/) $container_count = 1;
			
			// update requirements
			$reqgal -= $container_count * $volume;
			if ($reqgal < 0) $reqgal = 0;
			
			// check to see if the remaining requirements exceed the tolerance
// no longer needed?			$remainder_exceeds_tolerance = $reqgal * /*(*/$_REQUEST["project_coverage"]/* / $_REQUEST["project_coats"])*/ > $tolerance_in_sqft;
			
			// if this is the smallest container and there's still required material,
			// +1 required container
			if ($reqgal > 0 && $counter == count($containers) /*&& $remainder_exceeds_tolerance*/) $container_count++;

			// in english, please...
			if ($container_count > 0) {
				$string .= "<tr>".($string > "" ? '<td>+' : '<td>');
				$string .= '<td class="quantity">'.$container_count.'x<td class="container">'.preg_replace("/\ \(.*\)/","",$name).($container_count>1?'s':'');
			}
		}
	
	}

	echo '<table><thead><tr><th colspan="3">You&apos;ll need '.round($required_gallons,2).' total gallon'.($required_gallons==1?'':'s').':</thead><tbody>'.$string.'</tbody><!--tfoot><tr><td class="legal" colspan="3">Coverage may vary</tfoot--></table>';
	//echo $required_bottles . " Bottle".($required_bottles>1?"s":"")." Required";

}

?>
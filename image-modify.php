<?php
/*
 *
 * IMAGE MODIFY PHP
 * version 2.4.independent with caching
 * copyright jeff silvestris (2005-2017)
 * http://x-industries.com
 *
 * parameters: image=[src_path], save=[dest_path], nodisplay=[true]
 * modifiers: width=[int], height=[int], mode=[scale/crop/scalecrop], 
 * rotate=[#/cw/ccw/flip], watermark=[png], string=[text]
 * effect=[invert/grayscale/pixelate], type=[jpg/png/gif], cache=[bool]
 *
 * when including, usage: createImg(array("var"=>"val"));
 *
 */

ini_set('memory_limit','1000M');

/*class Pixel{
	function Pixel($r,$g,$b){
		$this->r=($r>255)?255:(($r<0)?0:(int)($r));
		$this->g=($g>255)?255:(($g<0)?0:(int)($g));
		$this->b=($b>255)?255:(($b<0)?0:(int)($b));
	}
}*/


$test=get_included_files();
if(sizeof($test)<2){
	//newImg($_REQUEST);
	$img = new image_modify();
//var_dump($_REQUEST);
}

class image_modify {

	public $param;
	public $image_name;
	
	private $cname;
	private $cfile;
	private $cache_dir;
	private $error_image;
	private $film_image;
	private $mask_image;
	private $font;
	private $root_absolute_directory;
	private $bmp2png;

	function __construct($param = false) {
		if (!$param || !is_array($param)) $param = $_REQUEST;
		$this->param = $param;
		$this->cache_dir = "uploads/cache/";
		$this->error_image = "img/error.png";
		$this->film_image = "assets/img/film.png";
		$this->mask_image = 'http://assets.ghostshield.com/img/clip/hexagon-msie2.png';
		$this->font = "./assets/fonts/3098F4_1_0";
		$this->bmp2png = "/usr/local/bin/bmp2png";
		$this->root_absolute_directory = "";
		
		if ($this->param["height"]>15 &&
			$this->param["width"]>15 &&
			$this->param["width"]<2000 &&
			$this->param["height"]<1250 &&
			$this->param["image"]){
			if(!$this->param["mode"]) $this->param["mode"] = "scalecrop";
		
			// ghostshield specific -- watermark
			if (strpos($this->param["image"], "uploads") > -1 && $this->param["width"] > 300 && $this->param["height"] > 200 ) $this->param["watermark"] = "assets/img/watermark/7.png";
		
			//$mod["cache"] = false;
			if(!$this->param["cache"]){
				$this->create_image();

			}else{
				if(!file_exists($this->cache_dir.$this->cache_name())){
					$this->param["filename"] = $this->param["image"];
					$this->param["photoname"] = $this->file_name();
					$this->param["save"] = $this->cache_dir.$this->cache_name();
					$this->create_image();
					exit();
				}else{
					header("Content-type: image/".$this->param["type"]);
					header("HTTP/1.1 301 Moved Permanently");
					header("Location: /".$this->cache_dir.$this->cache_name());
					exit();
				}
			}
		}
		
	}
	
	function file_name() {
		if (!isset($this->cfile)) $this->cfile = str_replace(":","_",str_replace("/","_",$this->param["image"]));
		return $this->cfile;
	}

	function cache_name() {

		if (isset($this->cname) && $this->cname > "") return $this->cname;

		// check cache for existing resized image, if not, create it

		$dir2 = $this->param["width"]."x".$this->param["height"]."-".$this->param["mode"];
		if ($this->param["effect"]) $dir2 .= "-".$this->param["effect"];
		if ($this->param["rotate"]) $dir2 .= "-rotate".$this->param["rotate"];

		if (!is_dir($this->cache_dir.$dir2)) mkdir($this->cache_dir.$dir2);

		$newImageFileName = $dir2."/".$this->file_name();

		if (!$this->param["type"]) {
			$this->param["type"] = "png";
			if ($this->param["width"]*$this->param["height"]>300000) $this->param["type"] = "jpg"; //640x480+
		}

		$newImageFileName = str_ireplace(".png",  ".".$this->param["type"], $newImageFileName);
		$newImageFileName = str_ireplace(".jpg",  ".".$this->param["type"], $newImageFileName);
		$newImageFileName = str_ireplace(".jpeg", ".".$this->param["type"], $newImageFileName);
		$newImageFileName = str_ireplace(".gif",  ".".$this->param["type"], $newImageFileName);
		$newImageFileName = str_ireplace(".bmp",  ".".$this->param["type"], $newImageFileName);

		$this->cname = $newImageFileName;
		return $this->cname;

	}

	function create_image() {
	
		if (strstr($this->param["image"], "cmswm:") > -1) $this->param["watermark"] = "assets/img/watermark/7.png";
		$this->param["image"] = str_replace("external:", "http://", $this->param["image"]);
		$this->param["image"] = str_replace("cms:", "http://images.ctfassets.net/", $this->param["image"]);
		$this->param["image"] = str_replace("cmswm:", "http://images.ctfassets.net/", $this->param["image"]);
	
		if (str_replace("http://","",$this->param["image"]) == $this->param["image"]) {
			$this->param["image"] = $this->root_absolute_directory.$this->param["image"];
		}
		// check filetype
		list($x,$y,$type) = getimagesize($this->param["image"]);
	
		if ($this->param["debug"]) echo "source image x: ".$x.", image y: ".$y;
	
		if (strpos(strtolower($this->param["image"]),".png")) {
			$im = imagecreatefromPNG($this->param["image"]);
			if (!$this->param["type"]) $this->param["type"] = "png";
			$oldType = ".png";
		} elseif (strpos(strtolower($this->param["image"]),".gif")) {
			$im = imagecreatefromGIF($this->param["image"]);
			if (!$this->param["type"]) $this->param["type"] = "gif";
			$oldType = ".gif";
		} elseif (strpos(strtolower($this->param["image"]),".bmp") && file_exists($this->bmp2png)) {
			exec($this->bmp2png." -O ".str_ireplace("bmp","png",$this->param["image"])." ".$this->param["image"]);
			$this->param["image"] = str_ireplace("bmp","png",$this->param["image"]);
			$im = imagecreatefromPNG($this->param["image"]);
			if (!$this->param["type"]) $this->param["type"] = "png";
			$oldType = ".png";
		} elseif ((strpos(strtolower($this->param["image"]),".mp4") || strpos(strtolower($this->param["image"]),".m4v") || strpos(strtolower($this->param["image"]),".mov")) && file_exists($this->film_image)) {
			$im = imagecreatefromPNG($this->film_image);
			$this->param["type"] = "png";
			$oldType = ".mp4";
		} else {
			$im = imagecreatefromJPEG($this->param["image"]);
			if (!$this->param["type"]) $this->param["type"] = "jpg";
			$oldType = ".jpg";
		}
	
		if (!$im && file_exists($this->error_image)) {
			$this->param["image"] = $this->error_image;
			$im = imagecreatefromPNG($this->param["image"]);
			if (!$this->param["type"]) $this->param["type"] = "png";
			$oldType = ".png";
		}
	
		if (!$this->param["debug"]) {
			if (!$this->param["nodisplay"]) header("Content-type: image/".$this->param["type"]);
		} else {
			foreach (gd_info() as $k=>$test) { print $k.":".$test."\r\n<br />"; }
			echo "PHP v".phpversion()."\r\n<br />";
			echo "path:".$path."\r\n<br />";
			echo '<pre>'; var_dump($this->param); echo '</pre>';
		}
	
		if (!$im) {
			// error image
			$im = imagecreate($this->param["width"],$this->param["height"]);
			$tc = imagecolorallocate($im, 255,255,255);
			imagefill($im, 0,0,$tc);
			$tc = imagecolorallocate($im, 0,0,0);
			imagestring($im, 5,5,5, "Error!", $tc);
			if (!file_exists($this->param["image"])) {
				imagestring($im, 5,5,34, "\n\n\n\nFile not found:", $tc);
				imagestring($im, 5,5,53, $this->param["image"], $tc);
			}
			$tmpimage = $im;
			error_log("Error generating image: ".var_export($this->param,true));

		} else {
			$x=imagesx($im); $y=imagesy($im);
		
			// check mode
			if ($this->param["mode"]=="crop") { $sx = $this->param["width"]; }
			else $mx = $this->param["width"];
			$sy = $this->param["height"];
		
			// size for proportions
			if(!$sx){
				$sx = ($sy/$y)*$x; if ($sx>$mx && $mx>0) { $sx=$mx; $sy = ($mx/$x)*$y; }
			}elseif(!$sy){
				$sy = ($sx/$x)*$y; if ($sy>$my && $my>0){ $sy=$my; $sx = ($my/$y)*$x; }
			}
		
			// create buffer
			if ($this->param["mode"] == "scalecrop" && $this->param["width"] && $this->param["height"]) { 
				$sx = $this->param["width"];
				$sy = $this->param["height"];
			}
		
			$tmpimage = imagecreatetruecolor($sx,$sy);
			if (($x/$sx)>($y/$sy)) { $srcx=($x-(($y/$sy)*$sx))/2; $srcy='0'; $nw=$x-($srcx*2); $nh=$y; }
			else { $srcx='0'; $srcy=($y-(($x/$sx)*$sy))/2; $nw=$x; $nh=$y-($srcy*2); }
			imagecopyresampled($tmpimage,$im,'0','0',$srcx,$srcy,$sx,$sy,$nw,$nh);
		
			// rotation
			if (isset($this->param["rotate"]) && $this->param["rotate"] !== 0) {
				if ($this->param["rotate"] == "cw" || $this->param["rotate"] == "90") {
					$tmpimage = imagerotate($tmpimage, 270, 0);
					$syold = $sy; $sy = $sx; $sx = $syold;
				} elseif ($this->param["rotate"] == "ccw" || $this->param["rotate"] == "270") {
					$tmpimage = imagerotate($tmpimage, 90, 0);
					$syold = $sy; $sy = $sx; $sx = $syold;
				} elseif ($this->param["rotate"] == "180" || $this->param["rotate"] == "flip") {
					$tmpimage = imagerotate($tmpimage, 180, 0);
				}
			}
		
			if ($oldType == ".mp4") {
				$black = imagecolorallocate($tmpimage, 0,0,0);
			
				$string = ($this->param["string"] ? $this->param["string"] : $this->param["image"]);
				$string_y = ($this->param["height"]+6) / 2;
				if (function_exists(imagefttext) && file_exists($this->font.".ttf")) {
					$string_x = ($this->param["width"] - (strlen($string) * 8)) / 2;
					imagefttext($tmpimage, 10, 0, $string_x, $string_y, $black, $this->font, $string);
				} else {
					$string_x = ($this->param["width"] - (strlen($string) * 8)) / 2;
					imagestring($tmpimage, 4, $string_x, $string_y, $string, $black);
				}
			}
		}
	
		// apply effects/filters
		// invert effect, a.k.a. "negative" - first of many to come
		if (isset($this->param["effect"]) && function_exists('imagefilter')) {
			if ($this->param["effect"] == "invert") {
				/*$transparency=40;
				$tmpimage=imagecreatetruecolor($sx,$sy);
				$black=imagecolorallocate($tmpimage,0,0,0);
				imagecolortransparent($tmpimage,$black);
				for ($tmpx=0; $tmpx<imagesx($tmpimage); $tmpx++) {
					for ($tmpy=0; $tmpy<imagesy($tmpimage); $tmpy++) {
						$rgb=imagecolorat($tmpimage,$tmpx,$tmpy);
						$r=($rgb>>16)&0xFF; $g=($rgb>>8)&0xFF; $b=$rgb&0xFF;
						$pixel=new Pixel(255-$g,255-$r,255-$b);
						$color=imagecolorallocatealpha($tmpimage,$pixel->r,$pixel->g,$pixel->b,$transparency);
						imagealphablending($tmpimage,false); 
						imagesavealpha($tmpimage,true); 
						imagesetpixel($tmpimage,$tmpx,$tmpy,$color);
					}
				}*/
				imagefilter($tmpimage, IMG_FILTER_NEGATE);
			} elseif ($this->param["effect"] == "grayscale") {
				imagefilter($tmpimage, IMG_FILTER_GRAYSCALE);
			} elseif ($this->param["effect"] == "pixelate") {
				imagefilter($tmpimage, IMG_FILTER_PIXELATE, 3, true);
			} elseif ($this->param["effect"] == "mask") {
				$this->param["type"] = "png";
				$mask = imagecreatefrompng( $this->mask_image );
				// Apply mask to source
				//imagegammacorrect($tmpimage, 1.0, 0.9);

				$this->imagealphamask( $tmpimage, $mask );
			}
		}
	
		// ghostshield specific -- watermark
		if (isset($this->param["watermark"])) {
			$wm = imagecreatefromPNG($this->param["watermark"]);
		
			$maxwidth = $sx; $maxheight = $sy;
			$ogwidth = 2500; $ogheight = 2500;
		
			$ogratio = $ogwidth/$ogheight;
			if ($maxwidth/$maxheight > $ogratio) { $maxwidth = $maxheight*$ogratio; }
			else { $maxheight = $maxwidth/$ogratio; }
		
			imagecopyresampled($tmpimage, $wm, ($sx - $maxwidth)/2, ($sy - $maxheight)/2, 0, 0, $maxwidth, $maxheight, $ogwidth, $ogheight);
			// x/y dest coord, x/ source coord, w/h dest, w/h source
		}
	
		// make the image
		if (!strpos(strtolower($this->param["save"]),$this->param["type"]) && !strpos(strtolower($this->param["save"]),$this->param["type"])){
			$this->param["save"] .= ".".$this->param["type"];
		}
	
		if ($this->param["type"] == "png") {
			if (isset($this->param["save"])) imagePNG($tmpimage, $this->param["save"]);
			if (!$this->param["nodisplay"]&&!$this->param["debug"]) imagePNG($tmpimage);
		} elseif ($this->param["type"] == "gif") {
			if (isset($this->param["save"])) imageGIF($tmpimage, str_ireplace($oldType,".gif",$this->param["save"]));
			if (!$this->param["nodisplay"]&&!$this->param["debug"]) imageGIF($tmpimage);
		} elseif ($this->param["type"]=="jpg" || $this->param["type"]=="jpeg") {
			if (isset($this->param["save"])) imageJPEG($tmpimage, str_ireplace($oldType,".jpg",$this->param["save"]),100);
			if (!$this->param["nodisplay"]&&!$this->param["debug"]) imageJPEG($tmpimage);
		}
	
		if ($tmpimage) @imagedestroy($tmpimage);
		if ($im) @imagedestroy($im);
	
		/*try{
			if ($tmpimage) @imagedestroy($tmpimage);
			if($im)imagedestroy($im);
		}catch(Exception $e){}*/
	}


	function imagealphamask( &$picture, $mask ) {
		// Get sizes and set up new picture
		$xSize = imagesx( $picture );
		$ySize = imagesy( $picture );
		$newPicture = imagecreatetruecolor( $xSize, $ySize );
		imagesavealpha( $newPicture, true );
		imagefill( $newPicture, 0, 0, imagecolorallocatealpha( $newPicture, 0, 0, 0, 127 ) );

		// Resize mask if necessary
		if( $xSize != imagesx( $mask ) || $ySize != imagesy( $mask ) ) {
			$tempPic = imagecreatetruecolor( $xSize, $ySize );
			imagecopyresampled( $tempPic, $mask, 0, 0, 0, 0, $xSize, $ySize, imagesx( $mask ), imagesy( $mask ) );
			imagedestroy( $mask );
			$mask = $tempPic;
		}

		// Perform pixel-based alpha map application
		for( $x = 0; $x < $xSize; $x++ ) {
			for( $y = 0; $y < $ySize; $y++ ) {
				$alpha = imagecolorsforindex( $mask, imagecolorat( $mask, $x, $y ) );
				$alpha = 127 - floor( $alpha[ 'red' ] / 2 );
				$rgb = imagecolorat( $picture, $x, $y );
				$color = imagecolorsforindex( $picture, $rgb );

				$saturationPercentage = 105;
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;
				//$alpha = ($rgb & 0x7F000000) >> 24;
				list($h, $s, $l) = $this->rgb2hsl($r, $g, $b);
				$s = $s * (100 + $saturationPercentage ) /100;
	//			if($s > 1) $s = 1;
				list($r, $g, $b) = $this->hsl2rgb($h, $s, $l);

				imagesetpixel( $newPicture, $x, $y, imagecolorallocatealpha( $newPicture, $r, $g, $b, $alpha ) );
			}
		}

		// Copy back to original picture
		imagedestroy( $picture );
		$picture = $newPicture;
	}

	// nichabi.com

	function rgb2hsl ($r, $g, $b) {
		$r /= 255;
		$g /= 255;
		$b /= 255;
		$max = max($r, $g, $b);
		$min = min($r, $g, $b);
		$l = ($max + $min) / 2;
		if ($max == $min) {
			$h = $s = 0;
		} else {
			$d = $max - $min;
			$s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
			switch ($max) {
				case $r:
					$h = ($g - $b) / $d + ($g < $b ? 6 : 0);
					break;
				case $g:
					$h = ($b - $r) / $d + 2;
					break;
				case $b:
					$h = ($r - $g) / $d + 4;
					break;
			}
			$h /= 6;
		}
		$h = floor($h * 360);
		$s = floor($s * 100);
		$l = floor($l * 100);
		return [$h, $s, $l];
	}

	function hsl2rgb ($h, $s, $l) {

		$h /= 60;
		if ($h < 0) $h = 6 - fmod(-$h, 6);
		$h = fmod($h, 6);

		$s = max(0, min(1, $s / 100));
		$l = max(0, min(1, $l / 100));

		$c = (1 - abs((2 * $l) - 1)) * $s;
		$x = $c * (1 - abs(fmod($h, 2) - 1));

		if ($h < 1) {
			$r = $c;
			$g = $x;
			$b = 0;
		} elseif ($h < 2) {
			$r = $x;
			$g = $c;
			$b = 0;
		} elseif ($h < 3) {
			$r = 0;
			$g = $c;
			$b = $x;
		} elseif ($h < 4) {
			$r = 0;
			$g = $x;
			$b = $c;
		} elseif ($h < 5) {
			$r = $x;
			$g = 0;
			$b = $c;
		} else {
			$r = $c;
			$g = 0;
			$b = $x;
		}

		$m = $l - $c / 2;
		$r = round(($r + $m) * 255);
		$g = round(($g + $m) * 255);
		$b = round(($b + $m) * 255);

		return [$r, $g, $b];

	}

}



?>


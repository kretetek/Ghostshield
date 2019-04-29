<?php

require_once(__DIR__."/../../lib/sys/log.php");
require_once(__DIR__."/../../lib/usr/user.php");
require_once(__DIR__."/../../lib/usr/users.php");

class gallery {

	private $gallery_count;
	private $dev;
	private $log;
	private $products;
	private $admin;
	private $path;
	
	function __construct($path = "") {

		$this->gallery_count = 1;
		$this->dev = strpos($_SERVER["HTTP_HOST"], "dev.") > -1 ? true : false;
		$this->admin = false;
		$this->path = $path;

	}
	
	public function admin() {

		$this->admin = true;

	}
	
	private function log($action, $string) {

		if (!isset($this->log)) $this->log = new _log("gallery");
		$this->log->$action($string);

	}

	function get($var) {
	
		if ($var == "approved") {
			return db::_query('SELECT * FROM `gallery'.($this->dev?"-dev":"").'` WHERE `_approved`=1 ORDER BY `_created` DESC');
		} elseif ($var == "unapproved" || $var == "pending") {
			return db::_query('SELECT * FROM `gallery'.($this->dev?"-dev":"").'` WHERE `_approved`=0 ORDER BY `_created` DESC');
		} elseif ($var == "denied") {
			return db::_query('SELECT * FROM `gallery'.($this->dev?"-dev":"").'` WHERE `_approved`=2 ORDER BY `_created` DESC');
		} elseif ($var == "trash" || $var == "junk") {
			return db::_query('SELECT * FROM `gallery'.($this->dev?"-dev":"").'` WHERE `_approved`=3 ORDER BY `_created` DESC');
		}
	
	}
	
	function changeExtension($file, $ext) {
		$file = str_replace("png", $ext, $file);
		$file = str_replace("jpg", $ext, $file);
		$file = str_replace("gif", $ext, $file);
		$file = str_replace("bmp", $ext, $file);
		$file = str_replace("jpeg", $ext, $file);
		return $file;
	}
	
	function create($gallery, $name, $type = "residential", $crop = false, $new = true) {

		global $page;
		$name = "gallery" . preg_replace("/[^A-z0-9]/", "", $name) . "-" . sizeof($gallery);
	
		$html = "";
		
		$admin_cache_button = "";
		if (!$this->admin) $admin_cache_button .= '<form method="POST">';
		$admin_cache_button .= '<input type="submit" value="Refresh This Gallery" class="xxsmall hex-button reset_cache" name="clear_cache"  />';
		if (!$this->admin) $admin_cache_button .= '</form>';
		
		if (isset($_POST["clear_cache"]) && users::me("admin")) {
			$check_cache = null;
			$html .= "<p>Clearing cached gallery data and recreating gallery...</p>";
		} else if (!$this->admin) $check_cache = db::_query('SELECT * FROM `cache` WHERE `var` = "'.db::_cleanse($name).'" AND `site` = "'.($this->dev?"dev":"pub").'" AND `type` = "gallery-'.$type.'" AND `_created` > NOW() - INTERVAL 3 DAY ORDER BY `_created` DESC LIMIT 1');
		//echo 'SELECT * FROM `cache` WHERE `var` = "'.db::_cleanse($name).'" AND `site` = "'.($this->dev?"dev":"pub").'" AND `type` = "gallery-'.$type.'" AND `_created` > NOW() - INTERVAL 3 DAY ORDER BY `_created` DESC LIMIT 1';

		if (!$this->admin && sizeof($check_cache) > 0 && !isset($_REQUEST["update"])) {
			return $html.(users::me("admin")?$admin_cache_button:'').$check_cache[0]["data"];
	
		} else {
			if ($new) $html .= '
			<div class="cpgallery" itemscope itemtype="http://schema.org/ImageGallery">';
			foreach ($gallery as $item) {
			
				if (!($type == "commercial" && $item["comm_exclude"])) {
			
	//			$grayscale = ($products[$item["product"]]["commercial"] && !$products[$item["product"]]["residential"]) || $type=="commercial" ? true : false;

				if ($item["type"] == "image") {
					//error_log(var_export($item,true));
					$modImageName = str_replace(":", "_", str_replace("/", "_", "uploads/".$item["product"]."/".$item["md5"].".".$item["format"]));
					$mod1 = "1920x1080-scale" .($item["rotate"]?"-rotate".$item["rotate"]:'') ."/".$this->changeExtension($modImageName, "jpg");
					$mod2 = "200x200-scale" .($crop?"crop":"") .($type=="commercial"?"-grayscale":"") .($item["rotate"]?"-rotate".$item["rotate"]:'') ."/".$this->changeExtension($modImageName, "png");

					if (!file_exists("uploads/cache/".$mod1)) {
						$file = "//".$_SERVER["HTTP_HOST"]."/images/1920x1080-scale"./*($grayscale?"-grayscale":"").*/"/uploads/".$item["product"]."/".$item["md5"].".".$item["format"].($item["rotate"]!=0?"?rotate=".$item["rotate"]:"");
						list($create) = getimagesize("http:".$file);
					}
					$file = "//".$_SERVER["HTTP_HOST"]."/uploads/cache/".$mod1;
					
					if (!file_exists("uploads/cache/".$mod2)) {
						$thumb = "//".$_SERVER["HTTP_HOST"]."/images/200x200-scale".($crop?"crop":"").($type=="commercial"?"-grayscale":"")."/uploads/".$item["product"]."/".$item["md5"].".".$item["format"].($item["rotate"]!=0?"?rotate=".$item["rotate"]:"");
						list($create) = getimagesize("http:".$thumb);
					}
					$thumb = "//".$_SERVER["HTTP_HOST"]."/uploads/cache/".$mod2;

				} elseif ($item["type"] == "video") {
					//$thumb = ($item["youtube"] > "" ? 'https://img.youtube.com/vi/'.$item["youtube"].'/mqdefault.jpg' : "http://".$_SERVER["HTTP_HOST"]."/assets/img/film.png");
					$thumb = $item["youtube"] ? '//img.youtube.com/vi/'.$item["youtube"].'/mqdefault.jpg' : '//assets.ghostshield.com/img/bg/mqdefault.jpg';
					$file = '//www.youtube.com/embed/'.$item["youtube"];

				}

	/*

	this is the formula to resize an image proportionally.
	instead of loading the image size i need to calculate it from the original width and height, which i need to save to the db
	and extract here and figure it out. should speed things up quite a bit.

			$maxwidth = $sx; $maxheight = $sy;
			$ogwidth = 2500; $ogheight = 2500;
		
			$ogratio = $ogwidth/$ogheight;

			if ($maxwidth/$maxheight > $ogratio) { $maxwidth = $maxheight*$ogratio; }
			else { $maxheight = $maxwidth/$ogratio; }
		
			imagecopyresampled($tmpimage, $wm, ($sx - $maxwidth)/2, ($sy - $maxheight)/2, 0, 0, $maxwidth, $maxheight, $ogwidth, $ogheight);

	*/


				list($width, $height) = getimagesize("https:".$thumb);
				if (!$this->admin) list($widthfull, $heightfull) = getimagesize("https:".$file);
		
				$html .= '
				<figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
					<a href="'.($this->admin?"/gallery/admin/review?id=".$item["id"]."&md5=".$item["md5"]:$file.'" target="_new" itemprop="contentUrl" data-size="'.($item["type"] == "image" ? $widthfull.'x'.$heightfull : '560x315" class="video" data-videoplaceholder="'.str_replace("mqdefault", "hqdefault", $thumb))).'">
						<img src="'./*($item["type"] == "image" ? "/" : "").*/$thumb.'" width="100" height="100" itemprop="thumbnail" alt="Ghostshield Concrete Sealers - '.str_replace('"', '&quot;', $page->data("products")->attr[$item["product"]]["name"].($item["product2"] ? " &amp; ".$page->data("products")->attr[$item["product2"]]["name"] : "")." Concrete ".($page->data("products")->attr[$item["product"]]["model"] > 1000 ? "Sealer" : "Coating")." Customer Upload".(strlen($item["caption"]) > 1 ? ": ".$item["caption"] : "")).'"'.($height<$width?' style="margin-top:'.(50-(100*($height/$width)/2)).'px"':'').' class="'.($type=="commercial"&&$item["youtube"]?'commercial':'').'" />';
				
				if ($this->admin) {
					$commercial = false;
					if (!$item["comm_exclude"]) {
						if ($page->data("products")->attr[$item["product"]]["commercial"]) {
							$commercial = true;
						} elseif ($item["product2"] && $page->data("products")->attr[$item["product2"]]["commercial"]) $commercial = true;
					}
					$html .= '
					<label class="checkbox"><input type="checkbox" name="item[]" value="'.$item["id"].'" /></label>
					<div class="info">
						<table>
							<tr><td>Prod&nbsp;1:<td>'.$item["product"].'
							<tr><td>Prod&nbsp;2:<td>'.(isset($item["product2"])?$item["product2"]:"-").'
							<tr><td>Comm:<td>'.($commercial ? '<span class="true">&#x2713;</span>' : '<span class="false">&#x2717;</span>').'
							<tr><td>Date:<td>'.date("n/j/y", strtotime($item["_created"])).'
							<tr><td>Format:<td>'.strtoupper($item["format"]).'
							<tr><td colspan="2"><span class="link">Click to Edit</span>
						</table>
					</div>';
				} else $html .= '
						<span class="number">'.$this->gallery_count.'</span>';

				$html .= '
					</a>';
				
				if (!$this->admin) {
					$html .= '
					<figcaption itemprop="caption description">'.$page->data("products")->attr[$item["product"]]["name"].($item["product2"] ? " &amp; ".$page->data("products")->attr[$item["product2"]]["name"] : "")." <span>Concrete Sealer</span> ".($item["caption"] > "" ? "<i>//</i> ".$item["caption"] : '');

					if ($item["credit"] > "" || $item["location"]) {
						$html .= ' <i>//</i> '.($item["credit"] > "" ? "Credit: " : '').$item["credit"];
						if ($item["credit"] > "" && $item["location"]) $html .= ', ';
						$html .= $item["location"];
					}

					$html .= '</figcaption>';
				}

				$html .= '
				</figure>';
				
				$this->gallery_count++;
				
				}

			}
			if ($new) $html .= '
			</div>';
	
			if (!$this->admin) {
				db::_query("DELETE FROM cache WHERE `_created` < NOW() - INTERVAL 7 DAY AND `site` = '".($this->dev?"dev":"pub")."' AND `type` = 'gallery-".$type."'");
				db::_insert("cache", ["var" => $name, "site" => ($this->dev?"dev":"pub"), "type" => "gallery-".$type, "data" => $html]);
			}
	
			return $html.(users::me("admin")?$admin_cache_button:'');
		}
	}
	
	function overlay() {
		return '
	<!-- Root element of PhotoSwipe. Must have class pswp. -->
	<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">

		<!-- Background of PhotoSwipe. 
			 It\'s a separate element as animating opacity is faster than rgba(). -->
		<div class="pswp__bg"></div>

		<!-- Slides wrapper with overflow:hidden. -->
		<div class="pswp__scroll-wrap">

			<!-- Container that holds slides. 
				PhotoSwipe keeps only 3 of them in the DOM to save memory.
				Don\'t modify these 3 pswp__item elements, data is added later on. -->
			<div class="pswp__container">
				<div class="pswp__item"></div>
				<div class="pswp__item"></div>
				<div class="pswp__item"></div>
			</div>

			<!-- Default (PhotoSwipeUI_Default) interface on top of sliding area. Can be changed. -->
			<div class="pswp__ui pswp__ui--hidden">

				<div class="pswp__top-bar">

					<!--  Controls are self-explanatory. Order can be changed. -->
					<div class="pswp__counter"></div>
					<button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
					<button class="pswp__button pswp__button--share" title="Share"></button>
					<button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
					<button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>

					<!-- Preloader demo http://codepen.io/dimsemenov/pen/yyBWoR -->
					<!-- element will get class pswp__preloader--active when preloader is running -->
					<div class="pswp__preloader">
						<div class="pswp__preloader__icn">
						  <div class="pswp__preloader__cut">
							<div class="pswp__preloader__donut"></div>
						  </div>
						</div>
					</div>
				</div>

				<div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
					<div class="pswp__share-tooltip"></div> 
				</div>

				<button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>
				<button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>
				<div class="pswp__caption"><div class="pswp__caption__center"></div></div>

			</div>

		</div>

	</div>';
	}

}

class galleryItem {

	public $display;
	public $current;
	public $request;
	public $edit;
	public $tags;
	
	private $g_client;
	private $youtube;
	private $dev;
	private $old;
	private $old_tags;
	private $new_tags;
	private $path;
	
	private $secretHash;
	private $admin_email;

	function __construct($request, $path = "") {
		
		$this->secretHash = "25c6c7ff35b6979b151f2136cd13b0ff";
		$this->admin_email = array("benjamin@ghostshield.com", "jeff@ghostshield.com");
		
		$this->dev = strpos($_SERVER["HTTP_HOST"], "dev.") > -1 ? true : false;
		$this->display = true;
		$this->path = $path;
		
		if ($request) {
			/*
		
			notes:
			$request = array(
				"id" => number,
				"md5" => string,
				"action" => string,
			
				"old-credit",
				"old-caption",
				"old-location",
			
				"confirm" => occurs once
			);
		
			*/
			$this->request = $request;
			
			if (isset($request["tags"])) {
			
				$new_tags = explode(",", $request["tags"]);
				foreach ($new_tags as $tag) {
					$this->new_tags[] = trim($tag);
				}
			
			}
		
			/*
		
			validate if request->id is numeric, etc.
		
			*/
		
			if (isset($request["id"]) && is_numeric($request["id"])) $upload = db::_select("gallery".($this->dev?"-dev":""), $this->request["id"]);
	
			if (isset($upload) && is_array($upload) && sizeof($upload) > 0) {

				$this->current = [
					"product"		=> $upload[0]["product"],
					"product2"		=> $upload[0]["product2"],
					"md5"			=> $upload[0]["md5"],
					"format"		=> $upload[0]["format"],
					"email"			=> $upload[0]["email"],
					"credit"		=> $upload[0]["credit"],
					"location"		=> $upload[0]["location"],
					"caption"		=> $upload[0]["caption"],
					"rotate"		=> $upload[0]["rotate"],
					"status"		=> $upload[0]["_approved"],
					"youtube"		=> $upload[0]["youtube"],
					"comm_exclude"	=> $upload[0]["comm_exclude"],
					"filetype"		=> $upload[0]["type"],
					"change"		=> false
				];
		
				$this->edit = [
					"credit"		=> $upload[0]["credit_edit"],
					"location"		=> $upload[0]["location_edit"],
					"caption"		=> $upload[0]["caption_edit"]
				];
				
				$tags = db::_select("gallery_tags".($this->dev?"-dev":""), $this->request["id"]);
				
				$this->tags = array();
				
				if (sizeof($tags) > 0) {
					
					foreach ($tags as $record) {
					
						$this->tags[] = $record["tag"];
					
					}
				
				}

			}
			
		}
		
	}
	
	private function log($action, $string) {

		if (!isset($this->log)) $this->log = new _log("gallery");
		$this->log->$action($string);

	}
	
	function upload() {

		if (isset($_FILES['file']) && isset($_REQUEST["model"]) && is_numeric($_REQUEST["model"])) {

			//error_log(var_export($_FILES,true));
			$response = "";

			$semi_rand = md5(time());
			$fileatt_type = mime_content_type($_FILES['file']['tmp_name']);
			$extension = str_replace("quicktime", "mov",
						 str_replace("jpeg", "jpg",
						 str_replace("+xml", "",
						 preg_replace("/.+?\//", "",
						 strtolower($fileatt_type) ) ) ) );

			if (!is_dir($this->path.'uploads/'.$_REQUEST["model"])) mkdir($this->path.'uploads/'.$_REQUEST["model"]);
			$uploaddir = 'uploads/'.$_REQUEST["model"].'/';

			//$uploadfile = $uploaddir . basename($_FILES['file']['name']);
			$uploadfile = $uploaddir . $semi_rand . "." . $extension;

			if (move_uploaded_file($_FILES['file']['tmp_name'], $this->path.$uploadfile)) {

				if (!isset($_REQUEST["dropzone"])) $response .=  '
		<form method="post" action="'. $uri .'">';
		
				$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
				$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	
				$email = openssl_encrypt($_POST["email"], "AES-256-CBC", $this->secretHash, 0, $iv);
				$md5 = openssl_encrypt($semi_rand, "AES-256-CBC", $this->secretHash, 0, $iv);

				$file_description = strpos($fileatt_type, "video") > -1 ? "Video" : "Photo";

				$form_el = '
				<div class="gallery_caption_input" id="gallery_captions-'.$semi_rand.'">
					<div><label for="upload_credit-'.$semi_rand.'">'.$file_description.' Credit:</label>	<input type="text" id="upload_credit-'.$semi_rand.'" name="upload_credit-'.$semi_rand.'" value="" placeholder="'.$file_description.' Credit" /></div>
					<div><label for="upload_location-'.$semi_rand.'">'.$file_description.' Location:</label>	<input type="text" id="upload_location-'.$semi_rand.'" name="upload_location-'.$semi_rand.'" value="" placeholder="'.$file_description.' Location" /></div>
					<div><label for="upload_caption-'.$semi_rand.'">'.$file_description.' Caption:</label>	<textarea id="upload_caption-'.$semi_rand.'" name="upload_caption-'.$semi_rand.'" placeholder="'.$file_description.' Caption"></textarea></div>
					<input type="hidden" name="upload_rotate-'.$semi_rand.'" id="upload_rotate-'.$semi_rand.'" value="0" />
					<input type="hidden" name="upload_email" value="'.$_POST["email"].'" />
				</div>';
	
				$response .= '
			<div class="gallery_success gallery_toggle_open" id="gallery_success-'.$semi_rand.'">
				<div class="gallery_toggle noclickevent" data-id="'.$semi_rand.'"><div class="gallery_toggle_inner" id="gallery_toggle_button-'.$semi_rand.'">-</div><span><img src="/images/36x36/'.$uploadfile.'?string=%20" width="36" height="36" /> '.$_FILES['file']['name'].'</span></div>
				<div class="gallery_uploaded">
					<img src="/images/400x200/'.$uploadfile.'?string='.$_FILES['file']['name'].'" width="400" height="200" />
					<a href="/gallery/edit/?e='.urlencode($iv).urlencode($email).'&k='.urlencode($md5).'&remove" class="remove_link remove_a hex-button xsmall">Delete</a>
				</div>'.$form_el.'
			</div>';
			
		
				if (!isset($_REQUEST["dropzone"])) $response .=  '
			<input type="submit" value="Save Information" class="hex-button xsmall" id="gallery_save_captions" />
		</form>';

				if ($extension == "png" || $extension == "jpg" || $extension == "bmp" || $extension == "gif") {
					$filetype = "image";
				} elseif ($extension == "mp4" || $extension == "m4v" || $extension == "mov") {
					$filetype = "video";
				} else $filetype = "unknown";

				$w = 0; $h = 0;
				list($w, $h) = getimagesize($this->path.$uploadfile);
		
				$upload_id = db::_insert("gallery".($this->dev?"-dev":""), [
					"product"	=> $_REQUEST["model"],
					"md5"		=> $semi_rand,
					"format"	=> $extension,
					"type"		=> $filetype,
					"original"	=> $_FILES['file']['name'],
					"width"		=> $w,
					"height"	=> $h,
					"email"		=> $_POST['email'],
					"ip"		=> $_SERVER['REMOTE_ADDR']
				]);

				$mime_boundary = "MIME_Boundary_{$semi_rand}";

				$headers = 'From: Ghostshield <'.$_SERVER["SERVER_ADMIN"].'>'.
					"\nReply-To: ".$_POST["email"].
					"\nMessage-ID: <".$semi_rand."-admin@".$_SERVER["SERVER_NAME"].">".
					//"\nReferences: <".$semi_rand."-admin@".$_SERVER["SERVER_NAME"].">".
					"\nMIME-Version: 1.0".
					"\n"."Content-Type: multipart/mixed; ". "boundary=\"{$mime_boundary}\"";
		
				$email2 = "\n\n";

				if (strpos($fileatt_type, "video") > -1) {
					$noun = "video";
					$image = file_get_contents('http://'.$_SERVER["SERVER_NAME"].'/images/400x200-scalecrop/'.$uploadfile.'?string='.urlencode($_FILES["file"]["name"]), true);
					$email2 .= "--{$mime_boundary}\n".
						"Content-Type:image/png; ".
						"name=\"".$_FILES['file']['name'].".png\"\n";
					$message = quoted_printable_encode('<p>A '.$_REQUEST["model"].' video titled "<a href="https://'.$_SERVER["SERVER_NAME"].'/'.$uploadfile.'">'.$_FILES['file']['name'].'</a>" was uploaded on '.date("F j, Y").' at '.date("H:i").'.</p><p><a href="https://'.$_SERVER["SERVER_NAME"].'/gallery/admin/review?id='.$upload_id.'&md5='.$semi_rand.'">Review this submission.</a></p>');
				} else {
					$noun = "image";
					$image = file_get_contents('http://'.$_SERVER["SERVER_NAME"].'/images/400x400-scale/'.$uploadfile, true);
					$email2 .= "--{$mime_boundary}\n".
						"Content-Type:{$fileatt_type}; ".
						"name=\"".$_FILES['file']['name']."\"\n";
					$message = quoted_printable_encode('<p>The following '.$_REQUEST["model"].' image was uploaded on '.date("F j, Y").' at '.date("H:i").'.</p><p><a href="https://'.$_SERVER["SERVER_NAME"].'/gallery/admin/approve?id='.$upload_id.'&md5='.$semi_rand.'">Approve</a> or <a href="https://'.$_SERVER["SERVER_NAME"].'/gallery/admin/deny?id='.$upload_id.'&md5='.$semi_rand.'">Deny</a></p>');
				}

				$email0 = "This is a multi-part message in MIME format.\n\n";

				$email1 = "--{$mime_boundary}\n".
					"Content-Type:text/html;charset=\"iso-8859-1\"\n".
					"Content-Transfer-Encoding:quoted-printable\n\n";

				//foreach ($images as $name=>$image) {
				$data = chunk_split(base64_encode($image));
				$email2 .= "Content-Location: CID:".$semi_rand.
					"\nContent-ID: <".$semi_rand.">".
					"\nContent-Transfer-Encoding: base64".
					"\nContent-Disposition: attachment".
					"\n\n".$data."\n\n";
				//}

				$email2 .= "--{$mime_boundary}--\n";

				if (is_array($this->admin_email)) {
					foreach($this->admin_email as $ae) {
						mail( $ae, "WWW Ghostshield Gallery Upload", $email0.$email1.$message.$email2, $headers );
					}
				} else mail( $this->admin_email, "WWW Ghostshield Gallery Upload", $email0.$email1.$message.$email2, $headers );

				$encryptedKey = openssl_encrypt($semi_rand, "AES-256-CBC", $this->secretHash, 0, $iv);
				$encryptedEmail = openssl_encrypt($_POST["email"], "AES-256-CBC", $this->secretHash, 0, $iv);

				$headers = 'From: '.$_SERVER["SERVER_ADMIN"].
					"\nReply-To: support@ghostshield.com".
					"\nMessage-ID: <".$semi_rand."@".$_SERVER["SERVER_NAME"].">".
					//"\nReferences: <".$semi_rand."@".$_SERVER["SERVER_NAME"].">".
					"\nMIME-Version: 1.0".
					"\nContent-Type: multipart/related; ". "boundary=\"{$mime_boundary}\"";

				$quoted_printable = quoted_printable_encode( "Thank you for sharing your Ghostshield project! \n\n". 'Your email was used to share '.($noun == "image" ? "an image" : "a video").' on ghostshield.com on '.date("F j, Y").". \n\n". 'https://'.$_SERVER["SERVER_NAME"].'/gallery/edit?e='.urlencode($iv).urlencode($encryptedEmail).'&k='.urlencode($encryptedKey)." \n\n". "Please save this email or bookmark this link to manage this upload in the future.");
		
				$message_user = quoted_printable_encode('
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="-webkit-text-size-adjust:none;">
<head>
<style media="all" type="text/css">
@font-face { font-family:\'NexaThin\'; src: url(\'https://assets.ghostshield.com/fonts/308C1E_3_0.eot\'); src: url(\'https://assets.ghostshield.com/fonts/308C1E_3_0.eot?#iefix\') format(\'embedded-opentype\'), url(\'https://assets.ghostshield.com/fonts/308C1E_3_0.woff2\') format(\'woff2\'), url(\'https://assets.ghostshield.com/fonts/308C1E_3_0.woff\') format(\'woff\'), url(\'https://assets.ghostshield.com/fonts/308C1E_3_0.ttf\') format(\'truetype\'); }
@font-face { font-family:\'NexaBold\'; src: url(\'https://assets.ghostshield.com/fonts/308C1E_0_0.eot\'); src: url(\'https://assets.ghostshield.com/fonts/308C1E_0_0.eot?#iefix\') format(\'embedded-opentype\'), url(\'https://assets.ghostshield.com/fonts/308C1E_0_0.woff2\') format(\'woff2\'), url(\'https://assets.ghostshield.com/fonts/308C1E_0_0.woff\') format(\'woff\'), url(\'https://assets.ghostshield.com/fonts/308C1E_0_0.ttf\') format(\'truetype\'); }
h2 { display:block; padding:40px 30px 20px 30px; color:#fff; background:#ff611d; text-align:center; font-size:16pt; font-family:\'NexaThin\',sans-serif; font-weight:normal; letter-spacing:6px; text-transform:uppercase; }
p { color:#555; font-size:12pt; text-align:center; }
p.small { color:#888; font-size:11pt; }
p a { color:#ff611d; }
.button { display:inline-block; text-align:center; text-transform:uppercase; line-height:14px; font-size:14px; padding:10px 18px 8px 22px; letter-spacing:2px; font-family:"NexaBold",sans-serif; min-width:100px; text-decoration:none; z-index:1; border:1px solid transparent; border-bottom:1px solid rgba(0,0,0,0.2); border-right:1px solid rgba(0,0,0,0.1); border-radius:2px; text-shadow:-1px -1px 0 rgba(0,0,0,0.1); outline:0; background:#ff611d; color:#fff; cursor:pointer; -webkit-transition:background 200ms; transition:background 200ms; }
.button:hover { background-color:#F7B430 !important; cursor:pointer; border-top-color:rgba(255,255,255,0.2); border-left-color:rgba(255,255,255,0.1); -webkit-transition:background 200ms; transition:background 200ms; }
.button:active { background-color:#ff611d !important; border-color:transparent; border-bottom:0; -webkit-transition:background 200ms; transition:background 200ms; }
</style>
</head>
<body style="background:#ededed;">
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="20">
	<tr>
		<td>
			<div style="max-width:760px; margin:10px auto; background:#fff;">
				<h2>Thank You For Sharing Your Ghostshield&reg; Project</h2>
				<div style="padding:2px 40px 40px 40px;">
					<p>Your email was used to share '.($noun == "image" ? "an image" : "a video").' on <a href="https://ghostshield.com">ghostshield.com</a> on '.date("F j, Y").'.</p>
					<p><a href="https://'.$_SERVER["SERVER_NAME"].'/gallery/edit?e='.urlencode($iv).urlencode($encryptedEmail).'&k='.urlencode($encryptedKey).'" class="button">Edit '.ucwords($noun).' Information</a></p>
				<div style="text-align:center;"><img src="cid:'.$semi_rand.'" width="80%" alt="image attachment" /></div>
				<p class="small">Please save this email or bookmark this link to manage this upload in the future.</p>
			</div>
		</td>
	</tr>
</table>
</body>
</html>');

				$message_text = "--{$mime_boundary}".
					"\nContent-Type: multipart/alternative; boundary=\"{$mime_boundary}ALT\"\n".
					"\n--{$mime_boundary}ALT".
					"\nContent-Type: text/plain; charset=utf-8".
					"\nContent-Transfer-Encoding: quoted-printable".
					"\n\n".$quoted_printable."\n".
					"\n--{$mime_boundary}ALT".
					"\nContent-Type:text/html;charset=\"iso-8859-1\"".
					"\nContent-Transfer-Encoding:quoted-printable".
					"\n\n".$message_user.
					"\n--{$mime_boundary}ALT--\n";

				@mail( $_POST["email"], "Ghostshield.com Gallery Upload", $email0.$message_text.$email2, $headers );
		
				//$decryptedMessage = openssl_decrypt($encryptedMessage, "AES-256-CBC", $this->secretHash);

				if (isset($_REQUEST["dropzone"])) {

					$reply = array(
						"html"	=> $response,
						"form"	=> $form_el,
						"id"	=> $semi_rand,
						"email" => $email,
						"key"	=> $md5,
						"type"	=> $file_description
					);

					echo json_encode($reply);
		
				} else echo $response;

				$this->log("notice", "Upload processed for ".$file_description." with ID ".$semi_rand);
		
			} else {
	
				echo "<p>Sorry, an error has occurred. Please try again.</p>";
	
			}

		//	echo 'Here is some more debugging info:';
		//	print_r($_FILES);

		} else if (isset($_REQUEST["gallery_captions"])) {

			if (isset($_POST["upload_email"])) {
				echo $this->editCaptions($_POST["upload_email"]);
			}

		}
	
	}
	
	function editCaptions($email) {
	
		$post = array();
		$return = array();
		$json = array();

		foreach ($_POST as $k => $v) {
			if (strpos($k, "upload_") > -1 && strpos($k, "-") > -1) {
				$md5 = preg_replace("/[a-z\_]+\-/", "", $k);
				$akey = str_replace("upload_", "", str_replace("-".$md5, "", $k));
				if ($akey == "rotate") {
					$post[$md5][$akey] = strip_tags($v);
				} else $post[$md5][$akey."_edit"] = strip_tags($v);
				$return[$md5][$akey] = strip_tags($v);
			}
		}

		foreach ($post as $md5 => $values) {

			$headers = 'From: './*$_SERVER["SERVER_ADMIN"]*/$email.
				"\nMessage-ID: <".$md5."-admin-".time()."@".$_SERVER["SERVER_NAME"].">".
				"\nIn-Reply-To: <".$md5."-admin@".$_SERVER["SERVER_NAME"].">".
				"\nReferences: <".$md5."-admin@".$_SERVER["SERVER_NAME"].">".
				"\nMIME-Version: 1.0".
				"\nContent-Type:text/html;charset=\"iso-8859-1\"";

			if (is_array($values) && count($values) > 0) {
		
				$record = db::_select("gallery".($this->dev?"-dev":""), ["where" => ["md5" => $md5, "email" => $email]]);

				if ($record[0]["format"] == "png" || $record[0]["format"] == "jpg" || $record[0]["format"] == "bmp" || $record[0]["format"] == "gif") {
					$filetype = "image";
				} elseif ($record[0]["format"] == "mp4" || $record[0]["format"] == "m4v" || $record[0]["format"] == "mov") {
					$filetype = "video";
				}
		
				if (db::_update("gallery".($this->dev?"-dev":""), ["md5" => $md5, "email" => $email], $values)) {

					$json[$md5] = $return[$md5];

					$message = "\n".'<p>The following captions were submit on '.date("F j, Y").' at '.date("H:i").' by '.$email.'.</p>'."\n".'<p><a href="https://'.$_SERVER["SERVER_NAME"].'/gallery/admin/approve-captions?id='.$record[0]["id"].'&md5='.$md5.'">Approve</a> or <a href="https://'.$_SERVER["SERVER_NAME"].'/gallery/admin/deny-captions?id='.$record[0]["id"].'&md5='.$md5.'">Deny</a></p>';

					if ($filetype == "video") {
						$message .= '<img src="https://'.$_SERVER["SERVER_NAME"].'/images/400x200-scalecrop/uploads/'.$record[0]["product"].'/'.$record[0]["md5"].'.'.$record[0]["format"].'?string='.urlencode($record[0]["original"]).'" style="float:left; margin-right:20px; width:200px; height:100px;" alt="image preview" />';

					} else {
						$message .= '<img src="https://'.$_SERVER["SERVER_NAME"].'/images/150x150-scale/uploads/'.$record[0]["product"].'/'.$record[0]["md5"].'.'.$record[0]["format"].'" style="float:left; margin-right:20px;" alt="image preview" />';

					}
					$message .= "\n".'<table border="0">'."\n".'<tr>'."\n".'<th style="text-align:left" width="120">Record'."\n".'<th style="text-align:left">Value';

					foreach ($values as $k => $v) {
						$message .= "\n".'<tr style="vertical-align:top">'."\n".'<td>'.ucwords(str_replace("_edit","",$k))."\n".'<td>'.$v;
					}

					$message .= "\n".'</table>'."\n".'<div style="clear:both; padding-top:20px;"></div>';
		
					if ($record[0]["_approved"] == 1) {
						$message .= '<p><em>This '.$filetype.' has already been <b>approved</b></em>.</p>';
					} elseif ($record[0]["_approved"] == 2) {
						$message .= '<p><em>This '.$filetype.' has already been <b>denied</b></em>.</p>';
					} else $message .= '<p>This '.$filetype.' has not been approved or denied. Would you like to <a href="https://'.$_SERVER["SERVER_NAME"].'/gallery/admin/approve?id='.$record[0]["id"].'&md5='.$record[0]["md5"].'">approve it</a>?</p>';

					if ($record[0]["credit_edit"] != $values["credit_edit"] ||
						$record[0]["location_edit"] != $values["location_edit"] ||
						$record[0]["caption_edit"] != $values["caption_edit"]) {

						if (is_array($this->admin_email)) {
							foreach($this->admin_email as $ae) {
								mail( $ae, "Re: WWW Ghostshield Gallery Upload", $message, $headers );
							}
						} else mail( $this->admin_email, "Re: WWW Ghostshield Gallery Upload", $message, $headers );
			
					}
				}
			}
		}

		return json_encode($json);
	
	}
	
	
	function validate() {
	
		if (!isset($this->request["action"])) return false;
		if (!isset($this->request["md5"])) return false;
		if (!isset($this->current) || sizeof($this->current) < 1) return false;
		if ($this->request["md5"] != $this->current["md5"]) return false;
		
		return true;
	
	}
	
	function products($model, $attr = null) {
		if (!isset($this->products) || sizeof($this->products) < 1) $this->products = $this->getCacheData("products");
		if ($attr) return $this->products[$model][$attr];
		return $this->products[$model];
	}
	
	public function getCacheData($type) {
		$cache = db::_select("cache", ["where" => ["site" => $this->dev, "var" => $type], "order_by" => "_updated", "order_direction" => "DESC", "limit" => 1]);
		if (isset($cache[0]) && isset($cache[0]["data"])) {
			return unserialize($cache[0]["data"]);
		} else return null;
	}
	
	function approve() {
	
		db::_update("gallery".($this->dev?"-dev":""), $this->request["id"], ["_approved" => 1]);
		
		$this->current["status"] = 1;

		$this->log("notice", "Upload approved with ID ".$this->current["md5"]);

		return '
			<h2>Submission Approved</h2>
			<p>You just approved the following '.$this->current["filetype"].'. Did you mean to <a href="?id='.$this->request["id"].'&md5='.$this->current["md5"].'&action=deny">deny this submission</a>?</p>';

	}
	
	function deny() {
	
		db::_update("gallery".($this->dev?"-dev":""), $this->request["id"], ["_approved" => 2]);
		
		$this->current["status"] = 2;

		$this->log("notice", "Upload denied with ID ".$this->current["md5"]);
		
		return '
			<h2>Submission Denied</h2>
			<p>You just denied the following '.$this->current["filetype"].'. Did you mean to <a href="?id='.$this->request["id"].'&md5='.$this->current["md5"].'&action=approve">approve this submission</a>?</p>';
	
	}
	
	function approveCaptions() {
	
		if ($this->current["youtube"] > "") {

			if ($this->g_client->getAccessToken()) {
	
				try {
		
					// Define an object that will be used to make all API requests.
					$this->youtube = new Google_Service_YouTube($this->g_client);
			
					//$deleteRequest = $youtube->videos->delete($upload[0]["youtube"]);
					// Call the API's videos.list method to retrieve the video resource.
					$listResponse = $this->youtube->videos->listVideos("snippet",
						array('id' => $this->current["youtube"]));

					if (empty($listResponse)) {
						
						return '
			<h2>Error</h2>
			'.sprintf('<p>YouTube can\'t find a video with video ID <code>%s</code>. Until this error is resolved these captions may not be approved.</p>', $this->current["youtube"]);
						
					} else {
		
						$video_description = "Via Ghostshield.com\n\n";
						if ($this->edit["credit"]) $video_description .= "Credit: ".$this->edit["credit"]."\n";
						if ($this->edit["location"]) $video_description .= "Location: ".$this->edit["location"]."\n";
						if ($this->edit["credit"]) $video_description .= $this->edit["caption"]."\n";
		
						$video = $listResponse[0];
						$video["snippet"]["description"] = $video_description;
						
						$tags = $video["snippet"]["tags"];
						// Preserve any tags already associated with the video. If the video does
						// not have any tags, create a new list. Replace the values "tag1" and
						// "tag2" with the new tags you want to associate with the video.
						if (is_null($tags)) {
							$videoSnippet['tags'] = $this->tags;
						} else {
							$videoSnippet['tags'] = array_unique(array_merge($tags, $this->tags));
						}
						
						// Update the video resource by calling the videos.update() method.
						$updateResponse = $this->youtube->videos->update("snippet", $video);

						//$responseTags = $updateResponse['snippet']['tags'];

						$this->log("notice", "Captions and YouTube captions approved with ID ".$this->current["md5"]);
						return $this->updateCaptions($updateResponse);
					
					}
	
				} catch (Google_Service_Exception $e) {
					return $this->googleAuthorize();
					/*$result = '
					<h2>YouTube Upload Failed</h2>
					'.sprintf('<p>A service error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));*/
		
				} catch (Google_Exception $e) {
					$this->log("error", "YouTube caption update failed for upload with ID ".$this->current["md5"].": ".htmlspecialchars($e->getMessage()));
					return '
			<h2>YouTube Edit Failed</h2>
			'.sprintf('<p>A client error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));
	
				}

			} else return $this->googleAuthorize();
		
		} else { // Needs to be reiterated because of dumb Oauth. Sad!
		
			$this->log("notice", "Captions approved with ID ".$this->current["md5"]);
			return $this->updateCaptions();
		
		}
	
	}
	
	function updateCaptions($youtube = false) {
	
		db::_update("gallery".($this->dev?"-dev":""), $this->request["id"], ["caption" => $this->edit["caption"], "location" => $this->edit["location"], "credit" => $this->edit["credit"]]);

		$this->old = $this->current;
		$this->current["credit"] = $this->edit["credit"];
		$this->current["location"] = $this->edit["location"];
		$this->current["caption"] = $this->edit["caption"];
		$this->current["change"] = true;

		$this->log("notice", "Captions updated with ID ".$this->current["md5"]);

		return '
			<h2>Captions Approved</h2>
			<p>You just approved the following captions. Did you mean to <a href="?id='.$this->request["id"].'&md5='.$this->current["md5"].'&action=deny-captions&old-credit='.urlencode($this->old["credit"]).'&old-location='.urlencode($this->old["location"]).'&old-caption='.urlencode($this->old["caption"]).'">deny these captions</a>?</p>';
	
	}
	
	function denyCaptions() {
		
		if (isset($this->request["old-caption"]) || isset($this->request["old-credit"]) || isset($this->request["old-location"])) db::_update("gallery".($this->dev?"-dev":""), $this->request["id"], ["caption" => $this->request["old-caption"], "location" => $this->request["old-location"], "credit" => $this->request["old-credit"]]);
		
		$this->current["credit"] = $this->request["old-credit"];
		$this->current["location"] = $this->request["old-location"];
		$this->current["caption"] = $this->request["old-caption"];
		$this->current["change"] = true;

		$this->log("notice", "Captions denied with ID ".$this->current["md5"]);
		
		return '
			<h2>Captions Denied</h2>
			<p>You just denied the following captions. Did you mean to <a href="?id='.$this->request["id"].'&md5='.$this->current["md5"].'&action=approve-captions">approve these captions</a>?</p>';
	
	}
	
	function updateTags() {

		db::_delete("gallery_tags".($this->dev?"-dev":""), ["id" => $this->request["id"]]);
		foreach ($this->new_tags as $tag) {
			db::_insert("gallery_tags".($this->dev?"-dev":""), ["id" => $this->request["id"], "tag" => $tag]);
		}
		
		$this->old_tags = $this->tags;
		$this->tags = $this->new_tags;

		$this->log("notice", "Tags modified with ID ".$this->current["md5"]);
		
		return '
			<h2>Tags Updated</h2>
			<p>You just updated the tags for this upload. <a href="?id='.$this->request["id"].'&md5='.$this->current["md5"].'&action=tags&tags='.urlencode(implode(",", $this->old_tags)).'">Undo this action</a>?</p>';
	
	}
	
	function product2() {

		db::_update("gallery".($this->dev?"-dev":""), $this->request["id"], ["product2" => $this->request["product2"]]);
		
		$old = $this->current["product2"];
		$this->current["product2"] = $this->request["product2"];

		$this->log("notice", "Product 2 changed for post with ID ".$this->current["md5"]);
		
		return '
			<h2>Product #2 Updated</h2>
			<p>You just updated the second product association for this upload. <a href="?id='.$this->request["id"].'&md5='.$this->current["md5"].'&action=product2&product2='.$old.'">Undo this action</a>?</p>';
	
	}
	
	function commExclude() {
		$this->request["comm_exclude"] = isset($this->request["comm_exclude"]) && $this->request["comm_exclude"] == "on" ? 1 : 0;

		db::_update("gallery".($this->dev?"-dev":""), $this->request["id"], ["comm_exclude" => $this->request["comm_exclude"]]);
		
		$this->current["comm_exclude"] = $this->request["comm_exclude"];

		$this->log("notice", "Commercial Gallery Exclusion changed for post with ID ".$this->current["md5"]);
		
		return '
			<h2>Commercial Exclusion Updated</h2>
			<p>You just '.($this->request["comm_exclude"] == 1 ? 'excluded' : 'included').' this upload from display in the commercial photo gallery. <a href="?id='.$this->request["id"].'&md5='.$this->current["md5"].'&action=comm_exclude&comm_exclude='.($this->request["comm_exclude"] == 1 ? 'off' : 'on').'">Undo this action</a>?</p>';
	
	}
	
	function remove() {

		db::_update("gallery".($this->dev?"-dev":""), $this->request["id"], ["_approved" => 3]);
		
		$this->current["status"] = 0;

		$this->log("notice", "Upload soft-removed with ID ".$this->current["md5"]);
		
		return '
			<h2>Submission Removed</h2>
			<p>You just marked the following '.$this->current["filetype"].' for removal. To undo this action, you may <a href="?id='.$this->request["id"].'&md5='.$this->current["md5"].'&action=approve">approve this submission</a>.</p>';

	}
	
	function delete() {

		$unlink = @unlink("uploads/".$this->current["product"]."/".$this->current["md5"].".".$this->current["format"]);

		db::_delete("gallery".($this->dev?"-dev":""), $this->request["id"]);

		$this->log("warning", "Upload permanently deleted with ID ".$this->current["md5"]);
		
		$this->current = [];
		$this->tags = [];
		$this->edit = [];

		return '
			<h2>Submission Deleted</h2>
			<p>This action can not be undone.</p>';

	}
	
	function rotate() {

		$rotate = $this->request["rotate"];
		if ($rotate == 360 || $rotate > 360) $rotate -= 360;

		db::_update("gallery".($this->dev?"-dev":""), $this->request["id"], ["rotate" => $rotate]);
		
		$oldrotate = $this->current["rotate"];
		$this->current["rotate"] = $rotate;

		$this->log("notice", "Upload rotated with ID ".$this->current["md5"]);
		
		return '
			<h2>Image Rotated</h2>
			<p>You just rotated the image 90 degrees. Would you like to <a href="?id='.$this->request["id"].'&md5='.$this->current["md5"].'&action=rotate&rotate='.$oldrotate.'">derotate the image</a>?</p>';

	}

	function googleAuthorize() {
	
		// If the user hasn't authorized the app, initiate the OAuth flow
		$state = mt_rand();
		$this->g_client->setState($state);
		$_SESSION['state'] = $state;
		$_SESSION['request'] = serialize($this->request);
		$_SESSION['request_time'] = date("U");

		$authUrl = $this->g_client->createAuthUrl();

		$this->display = false;
	
		return '
		<h2>Authorization Required</h2>
		<p>You need to <a href="'.$authUrl.'">authorize access</a> before proceeding.<p>';
	
	}
	
	function youtubeConnect() {
	
		$ghostshield_youtube_uid = "9X3Cl8uRVG5X9XlXfmUryw";
		$ghostshield_youtube_channel = "UC9X3Cl8uRVG5X9XlXfmUryw";
		$ghostshield_youtube_apikey = "AIzaSyDahV8UH_hijvc4KzAEcceY6VEWXgQzgIs";
		
		/*
		 * You can acquire an OAuth 2.0 client ID and client secret from the
		 * {{ Google Cloud Console }} <{{ https://cloud.google.com/console }}>
		 * For more information about using OAuth 2.0 to access Google APIs, please see:
		 * <https://developers.google.com/youtube/v3/guides/authentication>
		 * Please ensure that you have enabled the YouTube Data API for your project.
		 */
		$OAUTH2_CLIENT_ID = '690143441417-uhur2ana7h69bbe4lb41g825530rne7l.apps.googleusercontent.com';
		$OAUTH2_CLIENT_SECRET = 'Xr_7P2jyhx3K4S4TGf_3msOG';

		$this->g_client = new Google_Client();
		$this->g_client->setClientId($OAUTH2_CLIENT_ID);
		$this->g_client->setClientSecret($OAUTH2_CLIENT_SECRET);
		$this->g_client->setScopes('https://www.googleapis.com/auth/youtube');
		$redirect = filter_var('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'], FILTER_SANITIZE_URL);
		$this->g_client->setRedirectUri($redirect);

		// Define an object that will be used to make all API requests.
		$this->youtube = new Google_Service_YouTube($this->g_client);

		// Check if an auth token exists for the required scopes
		$tokenSessionKey = 'token-' . $this->g_client->prepareScopes();
		if (isset($_GET['code'])) {
			if (strval($_SESSION['state']) !== strval($_GET['state'])) {
				die('The session state did not match.');
			}

			$this->g_client->authenticate($_GET['code']);
			$_SESSION[$tokenSessionKey] = $this->g_client->getAccessToken();
			header('Location: ' . $redirect);
			exit(); // Added this, maybe put it back?
		}

		if (isset($_SESSION[$tokenSessionKey])) {
			$this->g_client->setAccessToken($_SESSION[$tokenSessionKey]);
		}

	}
	
	function youtubeUpload() {
	
		// Check to ensure that the access token was successfully acquired.
		if ($this->g_client->getAccessToken()) {
		
			try {
		
				$video_title = "Ghostshield Customer Upload - ".$this->products($this->current["product"], "name");
				
				$video_description = "Via Ghostshield.com\n\n";
				if ($this->current["credit"]) $video_description .= "Credit: ".$this->current["credit"]."\n";
				if ($this->current["location"]) $video_description .= "Location: ".$this->current["location"]."\n";
				if ($this->current["credit"]) $video_description .= $this->current["caption"]."\n";
		
				//$g_client = new Google_Client();
				//$g_client->setDeveloperKey($ghostshield_youtube_apikey);
				//$g_service = new Google_Service_Drive($g_client);
		
			//	$yt_file = new Google_Service_Drive_DriveFile();
			//	$yt_file->setTitle("Hello World!");
			/*	$yt_result = $g_service->files->insert($yt_file, array(
				  'data' => file_get_contents('uploads/'.$upload[0]["product"].'/'.$upload[0]["md5"].'.'.$upload[0]["format"]),
				  'mimeType' => 'application/octet-stream',
				  'uploadType' => 'multipart'
				)); */
			
				// Define an object that will be used to make all API requests.
				$this->youtube = new Google_Service_YouTube($this->g_client);
				$videoPath = 'uploads/'.$this->current["product"].'/'.$this->current["md5"].'.'.$this->current["format"];
			
				// Create a snippet with title, description, tags and category ID
				// Create an asset resource and set its snippet metadata and type.
				// This example sets the video's title, description, keyword tags, and
				// video category.
				$snippet = new Google_Service_YouTube_VideoSnippet();
				$snippet->setTitle($video_title);
				$snippet->setDescription($video_description);
				$snippet->setTags(array("customer video", "ghostshield", "concrete sealer", "kretetek"));

				// Numeric video category. See
				// https://developers.google.com/youtube/v3/docs/videoCategories/list
				$snippet->setCategoryId("26");

				// Set the video's status to "public". Valid statuses are "public",
				// "private" and "unlisted".
				$status = new Google_Service_YouTube_VideoStatus();
				$status->privacyStatus = "public";

				// Associate the snippet and status objects with a new video resource.
				$video = new Google_Service_YouTube_Video();
				$video->setSnippet($snippet);
				$video->setStatus($status);

				// Specify the size of each chunk of data, in bytes. Set a higher value for
				// reliable connection as fewer chunks lead to faster uploads. Set a lower
				// value for better recovery on less reliable connections.
				$chunkSizeBytes = 1 * 1024 * 1024;

				// Setting the defer flag to true tells the client to return a request which can be called
				// with ->execute(); instead of making the API call immediately.
				$this->g_client->setDefer(true);

				// Create a request for the API's videos.insert method to create and upload the video.
				$insertRequest = $this->youtube->videos->insert("status,snippet", $video);

				// Create a MediaFileUpload object for resumable uploads.
				$media = new Google_Http_MediaFileUpload(
					$this->g_client,
					$insertRequest,
					'video/*',
					null,
					true,
					$chunkSizeBytes
				);
				$media->setFileSize(filesize($videoPath));

				// Read the media file and upload it chunk by chunk.
				$status = false;
				$handle = fopen($videoPath, "rb");
				while (!$status && !feof($handle)) {
					$chunk = fread($handle, $chunkSizeBytes);
					$status = $media->nextChunk($chunk);
				}
				fclose($handle);

				// If you want to make other calls after the file upload, set setDefer back to false
				$this->g_client->setDefer(false);

				db::_update("gallery".($this->dev?"-dev":""), $this->request["id"], ["youtube" => $status["id"]]);
				
				$this->current["youtube"] = $status["id"];

				$this->log("notice", "YouTube upload complete for upload with ID ".$this->current["md5"]);
				
				return '
				<h2>YouTube Upload</h2>
				<p>This video is on YouTube with the ID '.$status["id"].'</p>'; //$status['snippet']['title']
		
			} catch (Google_Service_Exception $e) {
				
				return $this->googleAuthorize();
				/*$result = '
				<h2>YouTube Upload Failed</h2>
				<p>Would you like to try <a href="?id='.$id.'&md5='.$md5.'&action=session">resetting your session</a>?</p>';//.sprintf('<p>A service error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));*/
			
			} catch (Google_Exception $e) {
				$this->log("error", "YouTube upload failed for upload with ID ".$this->current["md5"].": ".htmlspecialchars($e->getMessage()));
				return '
				<h2>YouTube Upload Failed</h2>
				'.sprintf('<p>A client error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));
		
			}

		} else return $this->googleAuthorize();
	
	}
	
	function youtubeRemove() {
	
		if (isset($this->request["confirm"]) && $this->request["confirm"] == $this->current["youtube"]) {
	
			if ($this->g_client->getAccessToken()) {
		
				try {
			
					// Define an object that will be used to make all API requests.
					$this->youtube = new Google_Service_YouTube($this->g_client);
				
					$deleteRequest = $this->youtube->videos->delete($this->current["youtube"]);
					
					db::_update("gallery".($this->dev?"-dev":""), $this->request["id"], ["youtube" => NULL]);
					
					$this->current["youtube"] = NULL;

					$this->log("warning", "YouTube upload removed for upload with ID ".$this->current["md5"]);

					return '
			<h2>Removed From YouTube</h2>
			<p>The video has been removed from YouTube.</p>';
		
				} catch (Google_Service_Exception $e) {
					return $this->googleAuthorize();
			
				} catch (Google_Exception $e) {
					$this->log("error", "YouTube upload failed for upload with ID ".$this->current["md5"].": ".htmlspecialchars($e->getMessage()));
					return '
			<h2>YouTube Removal Failed</h2>
			'.sprintf('<p>A client error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));
		
				}

			} else return $this->googleAuthorize();
		
		} else {

			return '
			<h2>Confirm YouTube Removal</h2>
			<p>Please confirm intentions to <a href="?id='.$this->request["id"].'&md5='.$this->current["md5"].'&action=youtube_remove&confirm='.$this->current["youtube"].'">remove this video from YouTube</a>.</p>';
		
		}
	
	}

	function resetSession() {
		$_SESSION["state"] = null;
		unset($_SESSION["state"]);
		$_SESSION["token-https://www.googleapis.com/auth/youtube"] = null;
		unset($_SESSION["token-https://www.googleapis.com/auth/youtube"]);
		//unset($_SESSION);
	}

}

?>
<?php

class page {

	public $dev;
	public $host;
	public $data;
	public $location;
	public $product;
	public $model;
	public $number;
	public $env;
	public $fresh;
	public $mobile;
	private $page_title;
	private $banner;
	private $scripts;
	private $css;
	private $styles;
	private $body_class;
	private $url_parts;
	private $keywords;
	private $description;
	private $meta_tags;
	private $body_attr;
	private $version;
	private $user_agent;

	function __construct($path = "../") {

		$this->dev = strpos($_SERVER["HTTP_HOST"], "dev.") > -1 ? true : false;
		

		$this->mobile = false;
		$this->mobile = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');
		if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($_SERVER['HTTP_USER_AGENT'], 0, 4))) $this->mobile = true;
	
		$this->version = date("y.m.d")."e".($this->dev?'.'.rand():'');

		if (strpos($_SERVER["HTTP_HOST"], "concretesealersolutions.com")) header("Location: http://ghostshield.com/");
		
		$this->user_agent = $_SERVER['HTTP_USER_AGENT'];

		// crappy old code - can i find all instances and delete it?
		$check = basename(str_replace("/index.php", "", $_SERVER["REQUEST_URI"]));
		$this->location = pathinfo($_SERVER["REQUEST_URI"]);
		if ($this->location['filename'] == "ghostshield" ||
			$this->location['filename'] == "ghostshield_dev" ||
			$this->location['filename'] == "") $this->location['filename'] = "index";
		if ($this->location['filename'] == "index" &&
			$check != "ghostshield" &&
			$check != "ghostshield_dev") $this->location['filename'] = $check;
		// end crappy old code
		
		$script_uri = preg_replace("/\?.*/", "", $_SERVER["REQUEST_URI"]);
		if ($script_uri == "/") $script_uri = "/index.php";
		$this->url_parts = explode("/", str_replace(".php", "", $script_uri));
		$this->body_class = implode(" ", $this->url_parts);
		$this->env = $this->dev ? "dev" : "pub";
		$this->host = $this->dev ? "dev.ghostshield.com" : "ghostshield.com";
		$this->fresh = isset($_SERVER["HTTP_REFERER"]) && strstr($_SERVER["HTTP_REFERER"], $this->host) ? false : true;

		if (!isset($this->path)) $this->path = $path;

		$errors = "";

		if ($this->dev) {
			error_reporting(E_ALL);
			ini_set('display_errors',"On");
			ini_set('display_startup_errors',"On");
		}

		$this->number = isset($_REQUEST["page"]) ? $_REQUEST["page"] : 1;
		$this->meta_tags = '
	<meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />
	<meta name="google-site-verification" content="V7nug8xxwfylHRa4MMiYXovVQ8cECnkNR_aVrJQZa24" />';
		$this->body_attr = "";

		$this->keywords = "ghostshield, ghost shield, water concrete, concrete water, oil concrete";
		$this->description = "Ghostshield is a manufacturer of water, oil and road salt resistant concrete sealer, densifiers and epoxy coatings.";

		require_once($this->path."lib/sys/syntax.php");
		require_once($this->path."lib/sys/db.php");
		db::credentials("concrfe5_gs", "concrfe5_gs", "4EwgifF8aeP!");

		$global_vars = $this->getCacheData("globals", $this->env);
		foreach ($global_vars as $v) { $GLOBALS[$v["key"]] = $v["text"]; }

		if (isset($_REQUEST["model"]) && is_numeric($_REQUEST["model"]) && $_REQUEST["model"] > "") {
			$this->model = $_REQUEST["model"];
			if (!isset($this->data("products")->attr[$this->model])) {
				$this->model = null;
				header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
			} else $this->product = $this->data("products")->attr[$this->model];
			
			// fix containers (maybe a better place to do this... update.php?)
			$containers = $this->product["containers"];
			$this->product["containers"] = array();
			$containersById = $this->getCacheData("containers", $this->env);
			foreach ($containers as $container) {
				$this->product["containers"]["".$containersById[$container]["volume"]] = $containersById[$container]["name"];
			}
		}
		if (!isset($this->product["waterproofs"])) $this->product["waterproofs"] = false;

		/* tracking */
		session_start();
		if (!$this->dev) {
			if (isset($_SESSION["gsa-id"]) && isset($_SESSION["gsa-time"])) {
				db::_update("analytics", $_SESSION["gsa-id"], [
					"duration" => (time()-$_SESSION["gsa-time"])
				]);
			}
			$order_id = (strstr($_SERVER["HTTP_REFERER"], "https://concretesealersolutions.com/checkout/order-received/") > -1) ? preg_replace("/\/?\?key\=wc_order_[a-f0-9]+/", "", str_replace("&utm_nooverride=1", "", str_replace("https://concretesealersolutions.com/checkout/order-received/", "", $_SERVER["HTTP_REFERER"]))) : 0;
			$sid = db::_insert("analytics", [
				"ip"		=> $_SERVER["REMOTE_ADDR"],
				"url"		=> $_SERVER["REQUEST_URI"],
				"agent"		=> $_SERVER["HTTP_USER_AGENT"],
				"referrer"	=> $_SERVER["HTTP_REFERER"],
				"order_id"	=> $order_id,
				"location"	=> gethostbyaddr($_SERVER["REMOTE_ADDR"]),
				"origin"	=> isset($_SESSION["gsa-id"]) ? $_SESSION["gsa-id"] : "null",
				"bot"		=> stristr($_SERVER["HTTP_USER_AGENT"], "bot") > -1 ? 1 : 0
			]);
			if (!isset($_SESSION["gsa-id"])) $_SESSION["gsa-id"] = $sid;
			$_SESSION["gsa-time"] = time();
		}

		$GLOBALS["loaded"] = true;

	}

	public function getCacheData($type, $site = "pub") {
		$cache = db::_select("cache", ["where" => ["site" => $site, "var" => $type], "order_by" => "_updated", "order_direction" => "DESC", "limit" => 1]);
		if (isset($cache[0]) && isset($cache[0]["data"])) {
			return unserialize($cache[0]["data"]);
		} else return array();
	}

	public function data($type, $array = false) {
		if ($array) $this->data[$type] = $array;
		
		if (!isset($this->data[$type])) $this->data($type, $this->getCacheData($type, $this->env));
		
		return (object) ["attr" => $this->data[$type]];
	}

	public function articles($limit = null, $article_ids = null, $search = null) {
		$criteria = ["order_by" => "_created", "order_direction" => "DESC"];
		if ($search) {
			$criteria["what"] = "*, MATCH(`title`,`article`) AGAINST ('".db::_cleanse(str_replace(" ", "* ", $search))."' IN NATURAL LANGUAGE MODE) AS score";
			$criteria["order_by"] = "score";
		}
		
		if (is_array($article_ids)) {
			$criteria["when"] = "";
			foreach ($article_ids as $id) {
				if ($criteria["when"] > "") $criteria["when"] .= ' OR ';
				$criteria["when"] .= '`id`="'.$id["post_id"].'"';
			}
		}
		$criteria["when"] = '`site`="'.$this->env.'"'.(isset($criteria["when"]) ? ' AND ('.$criteria["when"].')' : '');
		if ($limit) {
			$criteria["limit"] = $limit;
			$criteria["offset"] = $limit * ($this->number - 1);
		}
		return db::_select("blog", $criteria);
	}

	public function article($url) {
		if (!isset($this->data["article"][$url])) $this->data["article"][$url] = db::_select("blog", ["where" => ["site" => $this->env, "url" => $url], "limit" => 1]);
		return $this->data["article"][$url][0];
	}

	function pageTitle($str = false) {
		if ($str) {
			$this->page_title = $str;
		} else return $this->page_title;
	}
	
	public function setDescription($str) {
		$this->description = $str;
	}
	
	public function setBanner($html) {
		$this->banner = $html;
	}
	
	public function getBanner() {
		return $this->banner;
	}

	function keywords($str) {
		$this->keywords .= ", ".$str;
	}

	function model($num = false) {
		if ($num && is_numeric($num)) {
			$this->model = $num;
		} else return $this->model;
	}

	function addScript($filename) {
		$this->scripts[] = $filename;
	}

	function addStyles($str) {
		$this->styles .= $str;
	}

	function addCSS($filename) {
		$this->css[] = $filename;
	}
	
	function addBodyAttr($str) {
		$this->body_attr .= " ".$str;
	}
	
	function meta($property, $content, $schema = false) {
		$meta = '<meta property="'.(strpos($property, ":") > 0 ? $property : "og:".$property).'" name="'.$property.'"'.($schema ? ' itemprop="'.$property.'"' : '').' content="'.str_replace('"', '&quot;', $content).'" />';
		$this->meta_tags .= "\n\t".$meta;
		return $meta;
	}

	function header($page_title = false, $type = null) {
		if ($page_title) $this->pageTitle($page_title);

		if (!isset($this->page_title)) $this->page_title = 'Water, Oil &amp; Salt Repellent Concrete Sealers';
		if ($this->number > 1) $this->page_title .= " // Page ".$this->number;

		$html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
	<title>'.$this->page_title.' // Ghostshield&reg;</title>

	<meta property="og:site_name" name="site_name" content="Ghostshield&reg; Concrete Sealers" />
	<meta property="og:title" name="title" content="'.str_replace('"', '&quot;', $this->page_title).'" />
	<meta property="description" name="description" content="'.str_replace('"', '&quot;', $this->description).'" />
	<meta property="og:keywords" name="keywords" content="'.str_replace('"', '&quot;', $this->keywords).'"/>
	<meta property="og:url" name="url" content="https://ghostshield.com/'.$_SERVER["REQUEST_URI"].'" />
	<meta http-equiv="content-language" content="en" />'.
		$this->meta_tags;
		if (isset($this->model) && isset($this->data("products")->attr[$this->model]["name"])) $html .= '
	<meta property="og:image" name="image" content="//assets.'.$this->host.'/img/bottle/'.$this->model.'.png" />';

		$html .= "\n".'
	<link rel="apple-touch-icon" sizes="60x60" href="//assets.ghostshield.com/img/touch/icon-ipad-76x76.png" />
	<link rel="apple-touch-icon" sizes="114x114" href="//assets.ghostshield.com/img/touch/icon-iphone-retina-120x120.png" />
	<link rel="apple-touch-icon" sizes="144x144" href="//assets.ghostshield.com/img/touch/icon-ipad-retina-152x152.png" />
	<link rel="apple-touch-icon" href="//assets.ghostshield.com/img/touch/icon-iphone-60x60.png" />
	<link rel="canonical" href="https://ghostshield.com/" />
	<link rel="publisher" href="https://plus.google.com/+Ghostshield" />
	<link async href="//assets.'.$this->host.'/img/logo/ghostshield-g-orange.svg" rel="mask-icon" color="#000" />
	<link rel="stylesheet" href="https://use.typekit.net/jcf0qmg.css" />
	<style type="text/css" media="screen">'.
		file_get_contents(__DIR__.'/../assets/css/buttons-flat.css').
		file_get_contents(__DIR__.'/../assets/css/nav.css').
		file_get_contents(__DIR__.'/../assets/css/global.css').
		file_get_contents(__DIR__.'/../assets/css/graph.css').
		file_get_contents(__DIR__.'/../assets/css/dropzone.css').
		file_get_contents(__DIR__.'/../assets/css/photoswipe.css').
		file_get_contents(__DIR__.'/../assets/css/new_header.css').
		file_get_contents(__DIR__.'/../assets/css/new_footer.css').
		file_get_contents(__DIR__.'/../assets/css/photoswipe-default-skin/default-skin.css');
		
		foreach ($this->url_parts as $part) {
			if (file_exists(__DIR__.'/../assets/css/'.$part.'.css')) $html .= file_get_contents(__DIR__.'/../assets/css/'.$part.'.css');
		}
		
		if (isset($this->css)) foreach ($this->css as $css) {
			if (file_exists(__DIR__.'/../assets/css/'.$css)) $html .= file_get_contents(__DIR__.'/../assets/css/'.$css);
		}
	
		$os = $this->getOS(true);
		if ($os == "Windows" && file_exists(__DIR__.'/../assets/css/microsoft.css')) $html .= file_get_contents(__DIR__.'/../assets/css/microsoft.css');

		if (isset($this->styles)) $html .= $this->styles;
		$html .= '</style>';


/*
		$html .= '
    <noscript id="deferred-styles">';
		foreach ($this->url_parts as $part) {
			if (file_exists(__DIR__.'/../assets/css/'.$part.'.css')) $html .= '
		<link href="//assets.'.$this->host.'/css/'.$part.'.css?v='.$this->version.'" rel="stylesheet" />';
		}
		$html .= '
		<link href="//assets.'.$this->host.'/css/graph.css?v='.$this->version.'" rel="stylesheet" />
		<link href="//assets.'.$this->host.'/css/dropzone.css?v='.$this->version.'" rel="stylesheet" />
		<link href="//assets.'.$this->host.'/css/photoswipe.css?v='.$this->version.'" rel="stylesheet" />
		<link href="//assets.'.$this->host.'/css/photoswipe-default-skin/default-skin.css" rel="stylesheet" />';

		if (isset($this->css)) foreach ($this->css as $css) {
			$html .= '
		<link href="//assets.'.$this->host.'/css/'.$css.'?v='.$this->version.'" rel="stylesheet" />';
		}
	
		$os = $this->getOS(true);
		if ($os == "Windows") $html .= '
		<link href="//assets.'.$this->host.'/css/microsoft.css" rel="stylesheet" />';
	
		$html .= '
    </noscript>
    <script>
      var loadDeferredStyles = function() {
        var addStylesNode = document.getElementById("deferred-styles");
        var replacement = document.createElement("div");
        replacement.innerHTML = addStylesNode.textContent;
        document.body.appendChild(replacement)
        addStylesNode.parentElement.removeChild(addStylesNode);
      };
      var raf = window.requestAnimationFrame || window.mozRequestAnimationFrame ||
          window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;
      if (raf) raf(function() { window.setTimeout(loadDeferredStyles, 0); });
      else window.addEventListener("load", loadDeferredStyles);
    </script>';

		if (isset($this->styles)) $html .= '
	<style type="text/css" media="screen">'.$this->styles.'
	</style>';
*/

		$html .= "\n".'
<body class="'.($this->mobile?'mobile ':'desktop ').$this->body_class.(isset($_REQUEST["model"])?' product'.$_REQUEST["model"]:'').(isset($this->product["color"])?' color_'.strtolower($this->product["color"]):'').'"'.$this->body_attr.'>
	<a name="top" id="top_anchor"></a>

	<div id="container" class="'.$this->location['filename'].'">
		<div id="logo">
			<a href="/">
				<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" preserveAspectRatio="xMinYMid" viewBox="0 0 262.9 47.5" style="enable-background:new 0 0 262.9 47.5;" xml:space="preserve" alt="Ghostshield Logo">
					<path class="st0" d="M9,27.8L9,27.8c0.1,0,0.2-0.1,0.2-0.1l4.4-11.9c0.5-1.1,1-2,1.7-2.4c0.7-0.5,1.7-0.7,2.9-0.7h21.6c0.1,0,0.2-0.1,0.2-0.2l0.2-1.7c0.6-4.5,0.6-7.5,0.6-7.5c0-0.1-0.1-0.2-0.2-0.2c-2.4-0.5-5.1-0.7-7.6-0.7C18.8,2.3,7.5,9,7.4,9c-0.1,0-0.1,0.2-0.1,0.2C6.6,15.9,7,22.1,8.7,27.6C8.7,27.6,8.9,27.8,9,27.8"/>
					<path class="st0" d="M21.4,29.9c0.1,0,0.3,0,0.6-0.3l1.4-3.7c0-0.1,0-0.1,0-0.1c0-0.1-0.1-0.2-0.2-0.2h0h-2.6c0.3-0.9,0.9-1.6,1.5-2c0.7-0.5,1.6-0.7,2.8-0.7h6.1l-2.3,6.7c-0.3,1.1-1.1,1.8-1.9,2.3c-0.7,0.5-1.7,0.7-2.9,0.7H3.3c-0.1,0-0.2,0.1-0.2,0.2c0,0.1,0.1,0.2,0.2,0.2l8.6,1.8c4.1,7.4,9.5,10.5,9.5,10.5l0.1,0.1l0.1-0.1C32.9,39.2,37.3,26.1,39.3,16c0-0.2-0.1-0.2-0.2-0.2c0,0,0,0-0.1,0H20.5c-0.2,0-0.5,0.1-0.6,0.3l-4.8,13.3c0,0-0.1,0.2,0,0.3c0.1,0.1,0.1,0.1,0.3,0.1H21.4z"/>
					<polygon class="st0 st-hide" points="172.2,33.2 179.1,33.2 186.1,12.8 179.2,12.8"/>
					<polygon class="st0 st-hide" points="57.4,12.8 54.5,21.3 47.9,21.3 50.8,12.8 43.9,12.8 36.9,33.2 43.8,33.2 46.7,24.9 53.2,24.9 50.4,33.2 57.3,33.2 64.3,12.8"/>
					<path class="st0 st-hide" d="M83.9,12.8H70.4c-1.9,0-4,1.6-4.6,3.4l-4.6,13.5c-0.7,1.9,0.4,3.4,2.3,3.4h13.5c1.9,0,4-1.6,4.6-3.4l4.6-13.5C86.8,14.4,85.8,12.8,83.9,12.8 M75,28.7c-0.2,0.6-0.9,1.1-1.5,1.1h-4.3c-0.6,0-1-0.5-0.7-1.1l3.9-11.3c0.2-0.6,0.9-1.1,1.5-1.1h4.3c0.6,0,1,0.5,0.7,1.1L75,28.7z"/>
					<path class="st0 st-hide" d="M192,24.8h11.1l1.2-3.6h-18l-3,8.8c-0.1,0.3-0.2,0.7-0.2,1c0,0.8,0.2,1.4,0.8,1.7c0.6,0.3,1.1,0.5,1.8,0.5h13.3c1.3,0,2.2-0.2,3-0.7c0.8-0.5,1.5-1.7,1.9-2.9h-13.6L192,24.8z"/>
					<path class="st0 st-hide" d="M211.4,16.4l-5.8,16.7l14.2,0c1.3,0,2.2-0.3,3-0.7c0.8-0.5,1.4-1.6,1.7-2.9l-10.8,0l5.8-16.7l-26.7,0c-1.3,0-2.3,0.3-3.1,0.8c-0.8,0.5-1.4,1.6-1.8,2.8L211.4,16.4z"/>
					<path class="st0 st-hide" d="M249.4,12.8c1.9,0,2.9,1.6,2.3,3.4l-4.6,13.5c-0.7,1.9-2.7,3.4-4.6,3.4h-16.9c0,0,3.1-8.9,5.1-14.9h6.9l-4,11.5h5.4c0.6,0,1.3-0.5,1.5-1.1l3.9-11.4c0.2-0.6-0.1-1.1-0.7-1.1h-16c0.7-1.9,2.7-3.4,4.6-3.4L249.4,12.8z"/>
					<path class="st0 st-hide" d="M259.7,14.3c0,0.4-0.1,0.8-0.2,1.2c-0.2,0.4-0.4,0.7-0.7,0.9c-0.3,0.3-0.6,0.5-1,0.6c-0.4,0.2-0.7,0.2-1.2,0.2c-0.4,0-0.8-0.1-1.2-0.2c-0.4-0.2-0.7-0.4-0.9-0.6c-0.3-0.3-0.5-0.6-0.6-0.9c-0.2-0.4-0.2-0.7-0.2-1.2c0-0.4,0.1-0.8,0.2-1.2c0.2-0.4,0.4-0.7,0.6-0.9c0.3-0.3,0.6-0.5,0.9-0.6c0.4-0.2,0.7-0.2,1.2-0.2c0.4,0,0.8,0.1,1.2,0.2c0.4,0.2,0.7,0.4,1,0.6c0.3,0.3,0.5,0.6,0.7,0.9C259.6,13.5,259.7,13.9,259.7,14.3 M254.8,15.7l0.8-2.3c0,0,0,0,0,0h-0.7c0.1-0.2,0.1-0.3,0.3-0.4c0.1-0.1,0.3-0.1,0.4-0.1h2.7c0.1,0,0.1,0,0.2,0c0,0,0.1,0,0.1,0.1c0,0,0.1,0.1,0.1,0.1c0,0,0,0.1,0,0.1c0,0,0,0.1,0,0.1l-0.2,0.6c0,0.1-0.1,0.3-0.2,0.3c-0.1,0.1-0.2,0.1-0.4,0.1c0.1,0,0.1,0.1,0.2,0.1c0.1,0.1,0.1,0.1,0.1,0.2c0,0,0,0.1,0,0.1l-0.3,1h-1l0.4-1.2c0,0,0,0,0,0h-0.9c0,0,0,0,0,0l-0.4,1.2h-0.9L255,16c0.2,0.2,0.5,0.4,0.8,0.5c0.3,0.1,0.6,0.2,0.9,0.2c0.3,0,0.6-0.1,0.9-0.2c0.3-0.1,0.6-0.3,0.8-0.5c0.2-0.2,0.4-0.5,0.5-0.8c0.1-0.3,0.2-0.6,0.2-0.9c0-0.3-0.1-0.6-0.2-0.9c-0.1-0.3-0.3-0.6-0.5-0.8c-0.2-0.2-0.5-0.4-0.8-0.5c-0.3-0.1-0.6-0.2-0.9-0.2c-0.3,0-0.7,0.1-0.9,0.2c-0.3,0.1-0.5,0.3-0.8,0.5c-0.2,0.2-0.4,0.5-0.5,0.8c-0.1,0.3-0.2,0.6-0.2,0.9c0,0.3,0.1,0.7,0.2,0.9C254.6,15.4,254.7,15.6,254.8,15.7L254.8,15.7z M257.5,13.3h-0.8c0,0,0,0,0,0l-0.2,0.7h0.9c0.1,0,0.1,0,0.1-0.1L257.5,13.3C257.6,13.4,257.5,13.3,257.5,13.3"/>
					<path class="st0 st-hide" d="M169,12.8l-2.9,8.5h-6.6l2.9-8.5h-27c-1.9,0-4,1.5-4.6,3.4l-1.7,5c-0.7,1.9,0.4,3.4,2.3,3.4l8.9,0c0.6,0,1,0.5,0.7,1.1l-1,2.9c-0.2,0.6-0.9,1.1-1.5,1.1l-12.3,0l0,0h0c-0.1,0.4-0.1,0.7-0.1,1.1c0,0.8,0.2,1.4,0.8,1.8c0.6,0.4,1.2,0.5,1.9,0.5l0,0l0,0h13.1c1.9,0,4-1.6,4.6-3.4l1.7-5c0.7-1.9-0.4-3.4-2.3-3.4l-8.9,0c-0.6,0-1-0.5-0.7-1.1l1-2.8c0.2-0.6,0.9-1.1,1.5-1.1l15.5,0l-5.9,17h6.9l2.9-8.3h6.6l-2.9,8.3h6.9l7-20.4H169z"/>
					<path class="st0 st-hide" d="M93.1,12.8c-1.9,0-4,1.5-4.6,3.4l-1.7,5c-0.7,1.9,0.4,3.4,2.3,3.4l8.9,0c0.6,0,1,0.5,0.7,1.1l-1,2.9c-0.2,0.6-0.9,1.1-1.5,1.1l-12.3,0l0,0h0c-0.1,0.4-0.1,0.7-0.1,1.1c0,0.8,0.2,1.4,0.8,1.8c0.6,0.4,1.2,0.5,1.9,0.5h13.1c1.9,0,4-1.6,4.6-3.4l1.7-5c0.7-1.9-0.4-3.4-2.3-3.4l-8.9,0c-0.6,0-1-0.5-0.7-1.1l1-2.8c0.2-0.6,0.9-1.1,1.5-1.1l18.7,0l-5.8,17h6.9l5.8-17h5.5c2.8,0,3.6-3.4,3.6-3.4L93.1,12.8z"/>
				</svg>
			</a>
		</div>
		<div id="main_nav_container">
			<div id="site_controls">
				<div id="main_nav_open">
					<h3 id="main_nav_button"><span>Navigation</span></h3>
				</div>
				<a id="mobile_home_link" href="//'.$this->host.'/"></a>
				<ul>
					<li id="link-tel">
						<a href="tel:855-573-8383">
							<span class="link-icon">
								<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 38.2 38" style="enable-background:new 0 0 38.2 38;" xml:space="preserve">
									 <path class="path" d="M34.9,31.3c-0.4,1-1.4,1.8-2.9,2.5c-1.5,0.7-2.9,1.1-4.1,1.1c-0.3,0-0.7,0-1.1-0.1c-0.4-0.1-0.7-0.1-0.9-0.2c-0.3-0.1-0.6-0.1-1-0.3c-0.4-0.1-0.7-0.2-0.9-0.3c-0.2-0.1-0.5-0.2-1-0.4c-0.5-0.2-0.8-0.3-0.9-0.3c-3.4-1.2-6.7-3.5-10-6.7c-3.2-3.2-5.5-6.6-6.7-10c0-0.1-0.2-0.4-0.3-0.9c-0.2-0.5-0.3-0.8-0.4-1s-0.2-0.5-0.3-0.9c-0.1-0.4-0.2-0.8-0.3-1c-0.1-0.3-0.1-0.6-0.2-0.9c-0.1-0.4-0.1-0.7-0.1-1.1c0-1.2,0.4-2.5,1.1-4.1s1.5-2.5,2.5-2.9C8.4,3.2,9.4,3,10.4,3c0.2,0,0.4,0,0.5,0.1c0.1,0,0.3,0.2,0.5,0.6c0.2,0.3,0.5,0.8,0.8,1.3c0.3,0.5,0.6,1,0.8,1.5c0.3,0.5,0.5,0.9,0.8,1.4c0.2,0.5,0.4,0.8,0.5,0.9c0.1,0.1,0.2,0.3,0.4,0.6c0.2,0.3,0.4,0.5,0.5,0.8c0.1,0.2,0.2,0.4,0.2,0.7c0,0.3-0.2,0.7-0.6,1.1c-0.4,0.4-0.9,0.9-1.4,1.2c-0.5,0.4-1,0.8-1.4,1.2c-0.4,0.4-0.6,0.8-0.6,1c0,0.1,0,0.3,0.1,0.5c0.1,0.2,0.1,0.4,0.2,0.5s0.2,0.3,0.3,0.5c0.1,0.2,0.2,0.4,0.3,0.4c1.1,2.1,2.5,3.8,3.9,5.3c1.5,1.5,3.3,2.8,5.3,3.9c0.1,0,0.2,0.1,0.4,0.3c0.2,0.1,0.4,0.2,0.5,0.3c0.1,0.1,0.3,0.1,0.5,0.2c0.2,0.1,0.4,0.1,0.5,0.1c0.3,0,0.8-0.3,1.4-1c0.6-0.7,1.2-1.4,1.8-2c0.6-0.7,1.1-1,1.5-1c0.2,0,0.4,0.1,0.7,0.2c0.2,0.1,0.5,0.3,0.8,0.5c0.3,0.2,0.5,0.3,0.6,0.4l1.6,0.9c1.1,0.6,2,1.1,2.8,1.5c0.7,0.4,1.1,0.8,1.2,0.9c0,0.1,0.1,0.3,0.1,0.5C35.6,29.2,35.4,30.2,34.9,31.3z"/>
								</svg>
							</span>
							<span class="link-text">(855) 573-8383</span>
						</a>
					</li>
					<li id="link-menu">
						<a href="#menu">
							<span class="link-icon">
								<!--svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"  viewBox="0 0 17.5 21.5" style="enable-background:new 0 0 17.5 21.5;" xml:space="preserve">
									<path class="path" d="M7.7,8.6l-1.4,4.1c-0.1,0.2,0,0.4,0.3,0.4h1.6c0.2,0,0.5-0.2,0.6-0.4l0.5-1.4H8.3L8.8,10h3.1l-1.5,4.4H1.6c0,0-0.1,0-0.1,0.1s0,0.1,0.1,0.1l3.1,0.7c0.9,1.5,2,2.6,3.3,3.5c0,0,4.7-1.7,6.4-10.5H8.3C8.1,8.2,7.8,8.4,7.7,8.6z"/>
									<path class="path" d="M2.8,5.5c0,0-0.5,3.9,0.5,6.8c0.1,0.3,0.2,0.6,0.3,0.9l0,0l1.7-4.9C5.6,7.5,6.3,7,7,7h7.6 c0.1-0.8,0.3-2.1,0.3-3.6C15,3.4,9.8,1.8,2.8,5.5z"/>
								</svg-->
								<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 7.8 7" style="enable-background:new 0 0 7.8 7;" xml:space="preserve">
									<rect class="path" width="8" height="1"/>
									<rect y="3" class="path" width="8" height="1"/>
									<rect y="6" class="path" width="8" height="1"/>
								</svg>
							</span>
							<span class="link-text">Menu</span>
						</a>
					</li>
				</ul>
			</div>
			
			<nav id="fs_nav">
				<div class="gs_wrapper">
					<div class="gs_wrapper2">
					<section class="gs_custom_section" id="gs_logo_section"><a href="//'.$this->host.'/"><img src="http://assets.ghostshield.com/img/logo/ghostshield-logo-black-nt.svg" /></a></section>
					<section class="gs_custom_section" id="gs_search_section"><form class="ignore" method="POST" action="/search/"><input id="search_input" type="search" name="search" /><input type="submit" /></form></section>
					<section class="gs_custom_section" id="gs_actions_section">
					</section>
					<section class="gs_custom_section" id="prod-section">
						<h3 class="gs_section_headline mobile-only"><a href="//'.$this->host.'/">Home</a></h3>
						<h3 class="gs_section_headline"><a href="//'.$this->host.'/browse">Products</a><span class="expand-menu-item">+</span></h3>
						<div class="product-gs_list-container collapsed">
							<div class="gs_subsection_container product-gs_list">
								<ul class="residential gs_list">';
		foreach ($this->data("products")->attr as $m => $p) {
			if ($p["residential"] == "yes" && $p["availability"] != 0) $html .= '
									<li><a class="'.strtolower($p["color"]).'" href="//'.$this->host.'/product/'.preg_replace("/[^a-z0-9\-]+/", "-", strtolower($p["name"])).'/" data-model="'.$m.'">'.$p["name"].' <small>'.str_replace("&", "<span>&amp;</span>", $p["simple"]).'</small></a></li>';
		}
		$html .= '
								</ul>
								<ul class="commercial gs_list">';
		foreach ($this->data("products")->attr as $m => $p) {
			if ($p["commercial"] == "yes" && $p["residential"] != "yes" && $p["availability"] != 0) $html .= '
									<li><a class="gray" href="//'.$this->host.'/product/'.preg_replace("/[^a-z0-9\-]+/", "-", strtolower($p["name"])).'/" data-model="'.$m.'">'.$p["name"].' <small>'.str_replace("&", "<span>&amp;</span>", $p["simple"]).'</small></a></li>';
		}
		$html .= '
								</ul>
							</div>
						</div>
					</section>
					<section class="gs_custom_section" id="rec-section">
						<h3 class="gs_section_headline"><a href="//'.$this->host.'/browse">Recommendations</a><span class="expand-menu-item">+</span></h3>
						<div class="gs_rec_gs_list-container collapsed gs_subsection_container">
							<div class="gs_rec_gs_list">
								<div class="application-section">
									<h5 class="gs_subsection_headline">By Application</h5>
									<div class="gs_subsection_container2">
										<ul class="gs_list">';
		foreach($this->data("productCategories")->attr["Applications"] as $c){
			$html .= '
											<li><a href="//'.$this->host.'/browse/applications/'.str_replace(" ","+",strtolower($c["category"])).'/">'.($c["shortname"] > "" ? $c["shortname"] : $c["category"]).'</a></li>';
		}
		foreach($this->data("productCategories")->attr["Commercial Applications"] as $c){
			$html .= '
											<li><a href="//'.$this->host.'/browse/commercial+applications/'.str_replace(" ","+",strtolower($c["category"])).'/">'.($c["shortname"] > "" ? $c["shortname"] : $c["category"]).'</a></li>';
		}
		$html .= '
										</ul>
									</div>
								</div>
								<div class="type-section">
									<h5 class="gs_subsection_headline">By Type</h5>
									<div class="gs_subsection_container2">
										<ul class="gs_list">';
		foreach($this->data("productCategories")->attr["Type"] as $c){
			$html .= '
											<li><a href="//'.$this->host.'/browse/type/'.str_replace(" ","+",strtolower($c["category"])).'/">'.($c["shortname"] > "" ? $c["shortname"] : $c["category"]).'</a></li>';
		}
		$html .= '
										</ul>
									</div>
								</div>
								<div class="substrate-section">
									<h5 class="gs_subsection_headline">By Substrate</h5>
									<div class="gs_subsection_container2">
										<ul class="gs_list">';
		foreach($this->data("productCategories")->attr["Substrates"] as $c){
			$html .= '
											<li><a href="//'.$this->host.'/browse/substrates/'.str_replace(" ","+",strtolower($c["category"])).'/">'.($c["shortname"] > "" ? $c["shortname"] : $c["category"]).'</a></li>';
		}
		$html .= '
										</ul>
									</div>
								</div>
							</div>
						</div>
					</section>
					<section class="gs_custom_section" id="wtb-section">
						<h3 class="gs_section_headline desktop-only"><a href="//'.$this->host.'/">Home</a></h3>
						<div class="menu_chunk">
							<h3 class="gs_section_headline"><a href="//'.$this->host.'/product-finder">Product Finder</a></h3>
							<h3 class="gs_section_headline"><a href="https://sds.ghostshield.com">Technical Documents</a></h3>
							<h3 class="gs_section_headline"><a href="//'.$this->host.'/nanotechnology">Technology</a></h3>
							<h3 class="gs_section_headline"><a href="//'.$this->host.'/articles">Articles</a></h3>
						</div>
						<!--div class="menu_chunk">
							<h3 class="gs_section_headline"><a href="#">Where To Buy</a></h3>
							<div class="gs_subsection_container wtb-gs_list-container">
								<div class="wtb-gs_list">
									<ul class="gs_list_wide">
										<li><a href="#"><span class="">Home Depot</span></a></li>
										<li><a href="#"><span class="">Concrete Sealer Supply</span></a></li>
										<li><a href="#"><span class="">Lowe\'s</span></a></li>
										<li><a href="#"><span class="">Garage Flooring, LLC</span></a></li>
										<li><a href="#"><span class="">Redi-Mix Colors</span></a></li>
										<li><a href="#"><span class="">DIY Home Center</span></a></li>
									</ul>
									<ul class="gs_list_wide">
										<li><a href="#"><span class="">Professional Applicators</span></a></li>
									</ul>
								</div>
							</div>
						</div-->
						<div class="menu_chunk">
							<h3 class="gs_section_headline"><a href="//'.$this->host.'/gallery">Customer Photos</a></h3>
							<h3 class="gs_section_headline"><a href="//'.$this->host.'/commercial/gallery">Commercial Portfolio</a></h3>
							<h3 class="gs_section_headline"><a href="//'.$this->host.'/videos">Videos</a></h3>
						</div>
						<div class="menu_chunk">
							<h3 class="gs_section_headline"><a href="//'.$this->host.'/distributors">Distributors</a></h3>
							<h3 class="gs_section_headline"><a href="//'.$this->host.'/applicators">Applicators</a></h3>
						</div>
					</div>
					<!--a href="#" title="Close (Esc)" type="button" class="close">&times;</a-->
				</div>
			</nav>
		</div>

		<div class="page_content">';
	/*

		<header>
			<div id="logo"><a href="//'.$this->host.'/">GhostShield &reg; <small>'.str_replace("Repellent Concrete Sealer", "Repellent Concrete Sealers", $this->page_title).'</small></a></div>';

		if ($type == "commercial") {
			//echo '<!--span class="part1 part">Commercial flavor text</span> <span class="part2 part">something specific about industrial use.</span-->';
		} else if ($type == "browse") {
			$html .= '
			<div id="tagline">
				<span>Explore the Many Uses of Ghostshield</span>
				<a href="#product_finder" class="btn hex-button tall">Find Your Product</a>
			</div>'.$this->banner;
		}


		$html .= '
		</header>'."\n";*/

		return $html;

	}

	function footer() {

		$html = '
		
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

<section id="page_footer">
	<div class="footer-container">
	<h2 class="sub centered">AVAILABLE AT</h2>';

	if (isset($this->product["distributors"])) {
		$html .= '

	<ul class="distributors" style="grid-template-columns: repeat('.count($this->product["distributors"]).', 1fr);" class="width'.(count($this->product["distributors"])).'">';

	foreach ($this->product["distributors"] as $id) {
			$html .= '
					<li class="distributor">
					<a info="'.$this->data("onlineDistributor")->attr[$id]["logoWhite"]["file"].'" href="'.str_replace("%s", 'ghostshield+'.$this->model, $this->data("onlineDistributor")->attr[$id]["search"]).'" class="'.str_replace(" ", "", strtolower($this->data("onlineDistributor")->attr[$id]["name"])).'" target="_blank">';
					$html .= file_get_contents('http:'.$this->data("onlineDistributor")->attr[$id]["logoWhite"]["file"]).'</a>
					</li>';

		}

		$html .= '
		</ul>';
	} else {
		$html .= '<ul class="distributors">
		<li class="distributor">
			<a href="https://www.homedepot.com/s/ghostshield?NCNI-5">
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 176 176" style="enable-background:new 0 0 176 176;" xml:space="preserve">
<style type="text/css">
	.st0{fill:#222;}
</style>
<g>
	<path class="st0" d="M31.8,171.1L4.6,143.9c-1.9-1.9-2.2-2.3-4.6-1c0,0,0,0-0.1,0v32.8h32.8c0,0,0-0.1,0-0.1   C34.1,173.3,33.7,173,31.8,171.1z"></path>
	<path class="st0" d="M168.9,172.7c2.1,0,3.8-1.8,3.8-4.1c0-2.3-1.7-4.1-3.8-4.1c-2.1,0-3.8,1.8-3.8,4.1   C165.1,171,166.7,172.7,168.9,172.7z M169.1,165.8c1.3,0,1.9,0.5,1.9,1.6c0,1-0.6,1.5-1.4,1.6l1.5,2.5h-0.9l-1.4-2.5h-0.9v2.5h-0.8   l0-5.7H169.1z"></path>
	<path class="st0" d="M168.9,168.3L168.9,168.3c0.7,0,1.3-0.1,1.3-0.9c0-0.7-0.6-0.8-1.2-0.8h-1.1l0,1.8H168.9z"></path>
	<path class="st0" d="M169.8,24l6,6V0H145l6.5,6.5c0.3,0.3,1.2,1.3-0.4,2.8c-0.9,0.9-1.7,0.6-2.7,0.1c-5.5-2.9-10.7-2.8-16,1.1l-3-3   l7.5-7.5H0v136.9l12.5-12.5c0.2-0.2,2-1.8,3.5-0.3c0.7,0.7,0.7,1.7,0.3,2.5c-1.2,2.4-0.9,2.6,1,4.6l27.2,27.2   c1.9,1.9,2.2,2.3,4.6,1c0.9-0.5,1.8-0.5,2.5,0.3c1.5,1.5-0.1,3.3-0.3,3.5l-12.5,12.5h50c0.1-0.2,0.2-0.4,0.3-0.6   c0.4-0.7-0.5-1.5-0.9-1.9l-15.3-15.3c-0.4-0.4-1.2-1.3-1.9-0.9c-0.8,0.4-1.4,0.8-2.2,0.1c-0.8-0.8-0.1-1.6,0.5-2.2l7.5-7.5   l21.8,21.8l-6.4,6.4h83.6V92.2l-7,7c-0.1,0.1-1.1,1-2,0.2c-0.4-0.4-0.4-0.9-0.1-1.4c0.7-1.3,0.5-1.5-0.6-2.6l-18.6-18.6l7.1-7.1   l18.6,18.6c1.1,1.1,1.2,1.3,2.6,0.6v-50l-7.5,7.5l-3-3c3.7-4.7,4-10.8,1.3-16.3c-0.4-1.1-0.9-2.3,0-3.2   C168.2,22.1,169.6,23.8,169.8,24z M145.8,13.3c1.6-1.6,3.2,0.5,3.6,0.9l11.2,11.2c0.4,0.4,2.5,2,0.9,3.6c-1.1,1.1-2.2,0.6-3.4-0.1   c-2.4-1.5-5.4-2.5-8.2-0.9l-3.2-3.2c1.6-2.7,0.6-5.8-0.9-8.2C145.2,15.5,144.7,14.3,145.8,13.3z M107.7,29.2c0.2-0.2,2-1.8,3.5-0.3   c0.7,0.7,0.7,1.7,0.3,2.5c-1.2,2.4-0.9,2.6,1,4.6l27.2,27.2c1.9,1.9,2.2,2.3,4.6,1c0.9-0.5,1.8-0.5,2.5,0.3   c1.5,1.5-0.1,3.3-0.3,3.5l-14.7,14.7c-0.2,0.2-2,1.8-3.5,0.3c-0.7-0.7-0.7-1.7-0.3-2.5c1.2-2.4,0.9-2.6-1-4.6l-24.1-24.1l21,38.8   l-1.5,1.5L79.5,70.8c-1-0.5-2.1-1-3.2-1.3c-1.2-0.3-2.1-0.7-3,0.1c-0.7,0.6-2.2,1.9-3.4,0.6c-1.6-1.6,0-3.2,0.6-3.8l13.1-13.1   l21.5,10.4l0.1-0.1L94,42.9L107.7,29.2z M107.2,131.5c-0.4-0.6-0.6-1.3,0-1.9c0.9-0.9,1.8,0.3,2,0.5l6.3,6.3c0.2,0.2,1.4,1.1,0.5,2   c-0.6,0.6-1.3,0.3-1.9,0c-1.4-0.8-3.1-1.4-4.6-0.5l-1.8-1.8C108.6,134.5,108,132.8,107.2,131.5z M110.2,127.3   c-0.5,0.5-0.9,0.4-1.5,0.1c-3.1-1.6-6-1.5-9,0.6l-1.7-1.7l6.5-6.5l5.9,5.9C110.6,125.9,111.1,126.5,110.2,127.3z M88.5,114.1   L61.6,87.2c-1.3-1.3-3.1-2.6-6.4-1.1l-3-3c12-10,26-6.2,36.3,4.1c10.3,10.3,14.1,24.3,4.1,36.3l-3-3   C91.2,117.2,89.8,115.4,88.5,114.1z M103.3,94.9L85.8,77.4l0.1-0.1L97.3,83l8.9,8.9c7.3,5.7,8.6,0.3,10.8,2.6   c1.6,1.6-0.6,3.6-1.2,4.2l-5.9,5.9c-0.6,0.6-2.6,2.8-4.2,1.2C103.6,103.5,108.9,102.2,103.3,94.9z M83.6,33.9   c-0.2-0.6-0.5-1.2,0-1.7c0.9-0.9,1.6,0,1.7,0.1l5.5,5.5l-6.3,6.3l-1.6-1.6C84.8,40.1,85,36.8,83.6,33.9z M81,35   c-0.6,0.6-1.2,0.3-1.8,0c-1.3-0.8-2.9-1.3-4.3-0.5l-1.7-1.7c0.8-1.5,0.3-3-0.5-4.3c-0.3-0.6-0.6-1.2,0-1.8c0.9-0.9,1.7,0.3,1.9,0.5   l5.9,5.9C80.7,33.3,81.8,34.2,81,35z M70.1,17.5l5.6,5.6c0.1,0.1,0.6,0.7-0.2,1.5c-0.5,0.5-0.9,0.3-1.4,0.1   c-2.9-1.5-5.7-1.5-8.5,0.6L64,23.6L70.1,17.5z M55.5,32.1l7.2-7.2l20.5,20.5L76,52.6c-0.1,0.1-1.1,1-1.9,0.2   c-0.4-0.4-0.4-0.9-0.1-1.3c0.7-1.3,0.5-1.4-0.5-2.4L59.1,34.7c-1-1-1.2-1.2-2.4-0.5c-0.5,0.2-1,0.2-1.3-0.1   C54.5,33.2,55.4,32.2,55.5,32.1z M45.2,42.4l7.8-7.8c0.1-0.1,1.1-1,1.9-0.2c0.4,0.4,0.4,0.9,0.1,1.3c-0.7,1.3-0.5,1.4,0.5,2.4   l14.4,14.4c1,1,1.2,1.2,2.4,0.5c0.5-0.2,1-0.2,1.3,0.1c0.8,0.8-0.1,1.8-0.2,1.9L65.7,63c-0.1,0.1-1.1,1-1.9,0.2   c-0.4-0.4-0.4-0.9-0.1-1.3c0.7-1.3,0.5-1.4-0.5-2.4l-6.6-6.6l-2.4,2.4l-1.6-1.6l2.4-2.4L48.8,45c-1-1-1.2-1.2-2.4-0.5   c-0.5,0.2-1,0.2-1.3-0.1C44.2,43.5,45.1,42.6,45.2,42.4z M34.8,52.8l7.8-7.8c0.1-0.1,1.1-1,1.9-0.2c0.4,0.4,0.4,0.9,0.1,1.3   c-0.7,1.3-0.5,1.4,0.5,2.4L59.5,63c1,1,1.2,1.2,2.4,0.5c0.5-0.2,1-0.2,1.3,0.1c0.8,0.8-0.1,1.8-0.2,1.9l-7.8,7.8   c-0.1,0.1-1.1,1-1.9,0.2c-0.4-0.4-0.4-0.9-0.1-1.3c0.7-1.3,0.5-1.4-0.5-2.4L38.4,55.4c-1-1-1.2-1.2-2.4-0.5c-0.5,0.2-1,0.2-1.3-0.1   C33.8,53.9,34.7,52.9,34.8,52.8z M33,54.6l6.4,6.4c0.1,0.1,0.7,0.9-0.1,1.7c-0.8,0.8-1.6,0.2-2.3-0.3c-1.7-1.2-3.9-2.6-6.3-2.2   L29,58.6L33,54.6z M27.8,59.8l17.4,17.4c1,1,1.2,1.2,2.4,0.5c0.5-0.2,1-0.2,1.3,0.1c0.8,0.8-0.1,1.8-0.2,1.9L41,87.6   c-0.1,0.1-1.1,1-1.9,0.2c-0.4-0.4-0.4-0.9-0.1-1.3c0.7-1.3,0.5-1.4-0.5-2.4L21.1,66.5L27.8,59.8z M15.9,71.7l3.9-3.9l1.6,1.6   c-0.4,2.4,1,4.6,2.2,6.3c0.5,0.7,1.1,1.5,0.3,2.3c-0.8,0.8-1.6,0.2-1.7,0.1L15.9,71.7z M70.9,143.7l-14.7,14.6   c-0.2,0.2-2,1.8-3.5,0.3c-0.7-0.7-0.7-1.7-0.3-2.5c1.2-2.4,0.9-2.6-1-4.6L39,139.1l-4.6,4.6l-3-3l4.6-4.6l-11.8-11.8   c-1.9-1.9-2.2-2.3-4.6-1c-0.9,0.5-1.8,0.5-2.5-0.3c-1.5-1.5,0.1-3.3,0.3-3.5l14.7-14.6c0.2-0.2,2-1.8,3.5-0.3   c0.7,0.7,0.7,1.7,0.3,2.5c-1.2,2.4-0.9,2.6,1,4.6l27.2,27.2c1.9,1.9,2.2,2.3,4.6,1c0.9-0.5,1.8-0.5,2.5,0.3   C72.7,141.7,71.1,143.5,70.9,143.7z M54,121.7c-10.3-10.3-14.1-24.3-4.1-36.3l3,3c-1.6,3.3-0.2,5.1,1.1,6.4l26.9,26.9   c1.3,1.3,3.1,2.6,6.4,1.1l3,3C78.3,135.8,64.3,132.1,54,121.7z M99.9,168l-1.7-1.7c1.5-2,0-3.8-1.1-4.9L84.7,149   c-1.1-1.1-2.9-2.7-4.9-1.1l-1.7-1.7c4.9-5,12.5-5.7,20.4,1.5C105.6,154.2,104.8,163.2,99.9,168z M110.8,157.2c-0.1,0.1-1.1,1-2,0.2   c-0.4-0.4-0.4-0.9-0.1-1.4c0.7-1.3,0.5-1.5-0.6-2.6L92.8,138c-1.1-1.1-1.2-1.3-2.6-0.6c-0.5,0.3-1,0.3-1.4-0.1   c-0.8-0.8,0.1-1.9,0.2-2l7.7-7.7l21.8,21.8L110.8,157.2z M119.8,148.1l-1.7-1.7c2.1-2.7,2.3-6.1,0.7-9.2c-0.2-0.6-0.5-1.3,0-1.8   c0.9-0.9,1.7,0,1.8,0.1l5.8,5.8L119.8,148.1z M136.8,131.1l-8.3,8.3c-0.1,0.1-0.9,0.8-1.8-0.1c-0.4-0.4-0.5-0.9-0.2-1.4   c0.7-1.3,0.3-1.3-0.7-2.4l-15.3-15.3c-1.1-1.1-1.2-1.3-2.6-0.6c-0.5,0.3-1,0.3-1.4-0.1c-0.8-0.8,0.1-1.9,0.2-2l7.7-7.7l18.6,18.6   c1.1,1.1,1.2,1.3,2.6,0.6c0.5-0.3,1-0.3,1.4,0.1C137.8,130,136.9,131,136.8,131.1z M134.8,111.3c-0.4-0.4-0.8-0.8-1.1-1.2   c0.6,1.5,0.7,3.2,0.2,5.1c-0.6,2.2-1.9,4.1-4.7,6.9l-1.7-1.7c1.4-2,0.3-3.3-1.1-4.8l-4.4-4.4c-1.3-1.3-2.8-2.2-4.6-0.9l-1.7-1.7   c2.8-2.8,4.7-4.1,6.9-4.7c3.4-0.9,6.1,0.2,8.5,2.5c-2.6-4.9-2.7-10.5,1.5-15.5l1.7,1.7c-0.9,1.9-0.1,2.9,0.6,3.6l15.1,15.1   c0.7,0.7,1.7,1.5,3.6,0.6l1.7,1.7C148.5,119.2,140.6,117.1,134.8,111.3z M168.9,163.7c2.6,0,4.7,2.1,4.7,4.9c0,2.9-2.2,5-4.7,5   c-2.6,0-4.8-2.1-4.8-5C164.1,165.8,166.3,163.7,168.9,163.7z M147.9,79.8c-0.4,2.6,1.1,4.9,2.4,6.7c0.5,0.7,1.1,1.6,0.4,2.4   c1.3,0.8,2.5,1.8,3.6,2.9c5.8,5.8,7.9,13.7,2.3,20.4l-1.7-1.7c0.9-1.9,0.1-2.9-0.6-3.6l-15.1-15.1c-0.7-0.7-1.7-1.5-3.6-0.6   l-1.7-1.7c4.1-3.4,8.6-4,12.8-2.6l-4.6-4.6l4.2-4.2L147.9,79.8z M166.8,72.8c-0.8,0.8-1.7,0.2-2.4-0.4c-1.8-1.3-4.1-2.8-6.7-2.4   l-1.7-1.7l4.2-4.2l6.8,6.8C167.1,71.1,167.7,71.9,166.8,72.8z M165.9,48.6l-13.7,13.7c-0.2,0.2-2,1.8-3.5,0.3   c-0.7-0.7-0.7-1.7-0.3-2.5c1.2-2.4,0.9-2.6-1-4.6l-27.2-27.2c-1.9-1.9-2.2-2.3-4.6-1c-0.9,0.5-1.8,0.5-2.5-0.3   c-1.5-1.5,0.1-3.3,0.3-3.5l13.7-13.7L165.9,48.6z"></path>
</g>
</svg>
			</a>
		</li>
		<li class="distributor">
			<a href="https://www.garageflooringllc.com/?s=ghostshield&post_type=product">
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="GLFFC-LOGO-SOURCE-social-media_inversed_xA0_Image_1_" x="0px" y="0px" viewBox="0 0 1205.9 300.9" style="enable-background:new 0 0 1205.9 300.9;" xml:space="preserve">
<style type="text/css">
	.st0{fill:#222;}
</style>
<g>
	</g><g>
		<path class="st0" d="M-2.2,123.3c0.5,0.2,0.7,0.3,0.9,0.3c1.8,0.4,3.7,0.9,5.5,1.3c10.6,2.2,21.3,3,32.1,2.9    c14.1-0.1,28-1.5,41.9-3.6c13.8-2.1,27.4-4.8,41-7.8c21.5-4.8,42.8-10.4,64-16.4c27.6-7.8,55.2-15.7,82.8-23.5    c35-9.9,70.1-19.2,105.7-26.8c16.2-3.5,32.4-6.5,48.8-9c11.5-1.7,23-3.3,34.5-4.3c9.3-0.8,18.6-1.5,27.9-1.9    c15.7-0.6,31.4-0.2,47.1,1.9c9.3,1.2,18.5,3,27.6,5.3c17,4.3,33.3,10.5,49.1,18.1c18.7,9,36.3,19.9,53.2,31.9    c4.3,3.1,8.6,6.3,12.9,9.4c0.5,0.4,1,0.8,1.7,0.7c4.1-0.3,8.1-0.6,12.2-0.9c7.5-0.5,14.9-1.1,22.4-1.6c6.3-0.5,12.7-0.9,19-1.3    c8.1-0.6,16.1-1.1,24.2-1.7c7.3-0.5,14.5-0.9,21.8-1.3c10-0.6,20-1.2,30-1.6c10.7-0.5,21.5-1,32.3-1.3c16.3-0.5,32.5-0.9,48.8-1.1    c28.5-0.3,57,0.1,85.5,2c11.1,0.7,22.2,1.6,33.3,2.7c8.8,0.8,17.6,1.9,26.4,3.1c10.6,1.4,21.2,3.2,31.7,5.2    c16.7,3.3,33.2,7.3,49.4,12.6c17.6,5.7,34.6,12.8,50.6,22.1c0.2,0.1,0.5,0.3,0.8,0.4c0-0.2,0-0.3,0-0.3c-0.3-0.4-0.6-0.8-1-1.1    c-4.9-5.5-10.5-10.4-16.5-14.7c-10.5-7.5-21.8-13.4-33.7-18.5c-17.6-7.5-35.9-13-54.5-17.6c-20.9-5.1-42.1-8.9-63.4-11.8    c-11.4-1.6-22.9-2.9-34.4-4.2c-10-1.1-19.9-1.9-29.9-2.7c-10.9-0.8-21.8-1.5-32.7-2.1c-12.4-0.6-24.9-1.1-37.3-1.5    c-20.5-0.6-41-0.9-61.4-0.6c-18.3,0.3-36.6,0.7-54.9,1.2c-5.3,0.1-10.6,0.4-15.9,0.6c-7.1,0.3-14.3,0.7-21.4,1.1    c-5.5,0.3-11.1,0.6-16.6,1c-1.4,0.1-2.4-0.2-3.5-1c-15-11.3-30.5-21.7-46.9-30.9c-18.4-10.3-37.6-18.7-57.8-24.6    c-14.1-4.1-28.5-6.9-43.1-8.6c-8.4-0.9-16.9-1.5-25.4-1.7c-13.2-0.4-26.5-0.2-39.7,0.6c-8.4,0.5-16.7,1.1-25.1,1.9    c-8.3,0.8-16.6,2-24.9,3.1c-11.9,1.6-23.8,3.9-35.5,6.4c-21.8,4.6-43.3,10.4-64.6,17c-24.5,7.6-48.6,16.2-72.5,25.2    c-23,8.7-46,17.6-69,26.2c-22.1,8.3-44.4,16.1-67.1,22.8c-26.1,7.7-52.5,13.8-79.6,16.6c-7.2,0.8-14.5,1.3-21.8,1.7    C7.8,123.1,3,123.1-2.2,123.3z M647.1,105.2c-0.2-0.3-0.3-0.4-0.3-0.5c-0.7-0.4-1.3-0.9-2-1.3c-17.8-11.1-36.4-20.7-55.7-29    c-23.3-10-47.2-18-72.3-22c-7.9-1.3-15.9-2-24-2.2c-7.5-0.2-15,0.2-22.4,1.2c-3.7,0.5-7.2,1.6-10.5,3.5c-3.8,2.2-7.1,5-10.1,8.2    c-7.2,7.7-12.6,16.5-16.3,26.4c-1.6,4.3-2.8,8.7-3.1,13.3c-0.1,2,0,4,0.7,5.9c1,2.8,3.1,4.3,6.1,4.4c2.1,0.1,4.3,0.2,6.4,0.2    c15.7,0.3,31.4,0.7,47.1,0.9c22.6,0.3,45.2-0.1,67.8-0.9c6.9-0.2,13.9-0.6,20.8-1c6.7-0.4,13.4-0.8,20-1.4c6-0.5,12.1-1,18.1-1.6    c6.8-0.7,13.7-1.7,20.5-2.6C640.9,106.4,643.9,105.8,647.1,105.2z M144,269.9c0,0.1-0.1,0.2-0.1,0.4    c11.8,12.9,26.1,21.9,42.9,26.7c21.2,6.1,42.1,5,62.6-3.3c13-5.2,24.2-13.2,33.8-23.8c-0.7,0-1.1-0.1-1.5-0.1    c-10.9,0-21.7,0-32.6,0c-0.8,0-1.5,0.2-2.3,0.6c-3.1,1.7-6.2,3-9.5,4.1c-9.5,3.2-19.3,4.2-29.3,3.4c-9.4-0.7-18.2-3.2-26.5-7.6    c-0.6-0.3-1.4-0.4-2.1-0.4c-11.3,0-22.6,0-33.9,0C145,269.9,144.5,269.9,144,269.9z M854.1,269.9c0,0.3,0,0.4,0.1,0.5    c0.4,0.4,0.7,0.8,1.1,1.2c18.1,18.8,40.2,28.4,66.2,29.2c11.3,0.4,22.3-1.4,33-5.1c14.6-5,27.2-13.2,37.8-24.5    c0.3-0.3,0.6-0.7,1-1.2c-0.5,0-0.7-0.1-0.8-0.1c-11.1,0-22.2,0-33.3,0c-0.6,0-1.2,0.2-1.8,0.5c-2.4,1.1-4.8,2.3-7.3,3.3    c-11.2,4.3-22.8,5.3-34.6,4c-8.1-0.9-15.8-3.3-23-7.1c-0.9-0.5-1.8-0.7-2.8-0.7c-11.3,0-22.5,0-33.8,0    C855.2,269.9,854.7,269.9,854.1,269.9z M262.7,126.7c0-0.1,0-0.2,0.1-0.3c-19.4-11.9-40.4-15.8-62.9-12.9    c-12,1.5-32.5,9.2-35.8,13.3C197,126.7,229.9,126.7,262.7,126.7z M972.9,126.7c0-0.1,0.1-0.2,0.1-0.2c-0.3-0.2-0.6-0.4-0.9-0.6    c-12.4-7.6-26-11.8-40.5-13c-8.3-0.7-16.5-0.3-24.7,1.1c-11.4,2-22,5.9-31.8,12c-0.3,0.2-0.6,0.4-1,0.8    C907.1,126.7,940,126.7,972.9,126.7z M1200.7,230.6c0.1-0.2,0.2-0.4,0.3-0.6c0.8-3.4,1.7-6.8,2.5-10.2c0.2-0.8,0.3-1.7,0.3-2.5    c0-2-0.9-3.3-2.8-3.9c-1.3-0.4-2.6-0.5-3.9-0.6c-3.6-0.1-7.1-0.1-10.7,0c-1.4,0-2.8,0.2-4.1,0.4c-1.3,0.2-2.2,0.9-2.7,2.1    c-0.4,1-0.9,2.1-1.2,3.2c-2.8,11.1-5.6,22.3-8.3,33.4c-0.3,1-0.4,2.2-0.4,3.2c0,1.2,0.6,1.9,1.7,2.1c0.8,0.2,1.7,0.3,2.5,0.3    c5,0,10,0,15,0c0.8,0,1.6-0.1,2.4-0.2c1.2-0.2,2.2-0.8,2.8-2c0.4-0.9,0.8-1.8,1.1-2.8c1-3.8,2-7.7,3-11.5c0.1-0.4,0.2-0.8,0.2-1.3    c-3.7,0-7.3,0-11,0c-0.8,3-1.6,5.9-2.4,8.8c-1,0-1.9,0-2.9,0c2.3-9,4.6-17.9,6.8-26.8c1,0,1.9,0,2.9,0c-0.7,3-1.3,5.8-2,8.7    C1193.5,230.6,1197.1,230.6,1200.7,230.6z M1154.9,212.7c-3.7,14.9-7.4,29.7-11.2,44.6c7.2,0,14.3,0,21.4,0    c0.7-2.9,1.4-5.8,2.2-8.8c-1.3,0-2.4,0-3.4,0c-1.1,0-2.2,0-3.3,0c-1.1,0-2.1,0.2-3.3-0.2c3-11.9,6.1-23.6,9.1-35.5    C1162.5,212.7,1158.7,212.7,1154.9,212.7z M1141.6,212.7c-4,0-7.7,0-11.5,0c-3.7,14.9-7.4,29.7-11.2,44.5    c1.9,0.3,20.3,0.3,21.4-0.1c0.7-2.8,1.4-5.6,2.2-8.7c-3.4,0-6.6,0-10,0C1135.5,236.5,1138.5,224.7,1141.6,212.7z"></path>
		<path class="st0" d="M-2.2,123.3c5.1-0.1,10-0.1,14.7-0.4c7.3-0.4,14.5-0.9,21.8-1.7c27.1-2.9,53.5-9,79.6-16.6    c22.7-6.7,45-14.5,67.1-22.8c23-8.7,46-17.5,69-26.2c24-9,48.1-17.7,72.5-25.2c21.3-6.6,42.8-12.4,64.6-17    c11.8-2.5,23.6-4.7,35.5-6.4c8.3-1.1,16.6-2.3,24.9-3.1c8.3-0.8,16.7-1.4,25.1-1.9c13.2-0.8,26.5-1,39.7-0.6    c8.5,0.3,16.9,0.8,25.4,1.7c14.6,1.6,29,4.4,43.1,8.6c20.3,5.9,39.4,14.4,57.8,24.6c16.4,9.1,31.9,19.6,46.9,30.9    c1.1,0.8,2.2,1.1,3.5,1c5.5-0.4,11.1-0.7,16.6-1c7.1-0.4,14.3-0.8,21.4-1.1c5.3-0.2,10.6-0.5,15.9-0.6c18.3-0.4,36.6-0.9,54.9-1.2    c20.5-0.3,41,0,61.4,0.6c12.4,0.4,24.9,0.9,37.3,1.5c10.9,0.5,21.8,1.3,32.7,2.1c10,0.8,20,1.6,29.9,2.7    c11.5,1.2,22.9,2.6,34.4,4.2c21.3,2.9,42.5,6.7,63.4,11.8c18.6,4.5,36.9,10,54.5,17.6c11.8,5.1,23.2,11,33.7,18.5    c6,4.3,11.6,9.2,16.5,14.7c0.3,0.4,0.6,0.8,1,1.1c0,0.1,0,0.2,0,0.3c-0.3-0.1-0.5-0.3-0.8-0.4c-16-9.3-33-16.3-50.6-22.1    c-16.2-5.3-32.7-9.3-49.4-12.6c-10.5-2-21.1-3.8-31.7-5.2c-8.8-1.2-17.6-2.2-26.4-3.1c-11.1-1.1-22.2-1.9-33.3-2.7    c-28.5-1.9-57-2.3-85.5-2c-16.3,0.2-32.5,0.6-48.8,1.1c-10.8,0.3-21.5,0.8-32.3,1.3c-10,0.5-20,1.1-30,1.6    c-7.3,0.4-14.6,0.8-21.8,1.3c-8.1,0.5-16.1,1.1-24.2,1.7c-6.3,0.4-12.7,0.9-19,1.3c-7.5,0.5-14.9,1.1-22.4,1.6    c-4.1,0.3-8.1,0.6-12.2,0.9c-0.7,0.1-1.2-0.3-1.7-0.7c-4.3-3.1-8.5-6.3-12.9-9.4c-16.9-12-34.6-22.8-53.2-31.9    c-15.8-7.6-32.1-13.8-49.1-18.1c-9.1-2.3-18.3-4-27.6-5.3c-15.6-2.1-31.3-2.6-47.1-1.9c-9.3,0.4-18.6,1.2-27.9,1.9    c-11.6,1-23.1,2.5-34.5,4.3c-16.4,2.5-32.6,5.5-48.8,9c-35.6,7.6-70.7,16.9-105.7,26.8c-27.6,7.8-55.2,15.7-82.8,23.5    c-21.2,6-42.5,11.5-64,16.4c-13.6,3-27.2,5.8-41,7.8c-13.9,2.1-27.8,3.5-41.9,3.6c-10.8,0.1-21.5-0.7-32.1-2.9    c-1.8-0.4-3.7-0.9-5.5-1.3C-1.4,123.6-1.7,123.5-2.2,123.3z"></path>
		<path class="st0" d="M647.1,105.2c-3.2,0.5-6.2,1.1-9.2,1.5c-6.8,0.9-13.7,1.8-20.5,2.6c-6,0.7-12,1.1-18.1,1.6    c-6.7,0.5-13.3,1-20,1.4c-6.9,0.4-13.9,0.7-20.8,1c-22.6,0.8-45.2,1.1-67.8,0.9c-15.7-0.2-31.4-0.6-47.1-0.9    c-2.1,0-4.3-0.1-6.4-0.2c-3-0.1-5-1.6-6.1-4.4c-0.7-1.9-0.8-3.9-0.7-5.9c0.2-4.6,1.4-9,3.1-13.3c3.7-9.8,9.1-18.7,16.3-26.4    c3-3.2,6.3-6,10.1-8.2c3.3-1.9,6.8-3,10.5-3.5c7.4-1,14.9-1.4,22.4-1.2c8,0.2,16,1,24,2.2c25.1,4,49,11.9,72.3,22    c19.3,8.3,37.9,18,55.7,29c0.7,0.4,1.3,0.9,2,1.3C646.9,104.8,646.9,104.9,647.1,105.2z"></path>
		<path class="st0" d="M144,269.9c0.5,0,1,0,1.5,0c11.3,0,22.6,0,33.9,0c0.7,0,1.5,0.1,2.1,0.4c8.3,4.4,17.2,6.9,26.5,7.6    c10,0.8,19.8-0.2,29.3-3.4c3.3-1.1,6.5-2.5,9.5-4.1c0.7-0.4,1.4-0.6,2.3-0.6c10.9,0,21.7,0,32.6,0c0.4,0,0.8,0,1.5,0.1    c-9.7,10.6-20.8,18.6-33.8,23.8c-20.5,8.3-41.4,9.3-62.6,3.3c-16.8-4.8-31.1-13.9-42.9-26.7C143.9,270.1,144,270,144,269.9z"></path>
		<path class="st0" d="M854.1,269.9c0.6,0,1.1,0,1.6,0c11.3,0,22.5,0,33.8,0c1,0,1.9,0.2,2.8,0.7c7.2,3.8,14.9,6.1,23,7.1    c11.9,1.4,23.4,0.3,34.6-4c2.5-1,4.9-2.2,7.3-3.3c0.6-0.3,1.2-0.5,1.8-0.5c11.1,0,22.2,0,33.3,0c0.2,0,0.4,0,0.8,0.1    c-0.4,0.5-0.7,0.9-1,1.2c-10.6,11.3-23.1,19.5-37.8,24.5c-10.7,3.7-21.7,5.5-33,5.1c-26-0.8-48.1-10.4-66.2-29.2    c-0.4-0.4-0.7-0.8-1.1-1.2C854.1,270.3,854.2,270.2,854.1,269.9z"></path>
		<path class="st0" d="M262.7,126.7c-32.8,0-65.7,0-98.6,0c3.2-4.1,23.7-11.7,35.8-13.3c22.4-2.9,43.5,1,62.9,12.9    C262.8,126.4,262.7,126.5,262.7,126.7z"></path>
		<path class="st0" d="M972.9,126.7c-32.9,0-65.7,0-98.9,0c0.4-0.4,0.7-0.6,1-0.8c9.8-6,20.4-10,31.8-12c8.2-1.4,16.4-1.8,24.7-1.1    c14.5,1.2,28.1,5.4,40.5,13c0.3,0.2,0.6,0.4,0.9,0.6C972.9,126.5,972.9,126.6,972.9,126.7z"></path>
		<path class="st0" d="M1200.7,230.6c-3.6,0-7.2,0-10.9,0c0.7-2.9,1.3-5.8,2-8.7c-1,0-1.9,0-2.9,0c-2.3,8.9-4.5,17.8-6.8,26.8    c1,0,1.9,0,2.9,0c0.8-2.9,1.6-5.8,2.4-8.8c3.6,0,7.2,0,11,0c-0.1,0.4-0.1,0.9-0.2,1.3c-1,3.8-2,7.7-3,11.5    c-0.3,0.9-0.7,1.9-1.1,2.8c-0.5,1.2-1.5,1.8-2.8,2c-0.8,0.1-1.6,0.2-2.4,0.2c-5,0-10,0.1-15,0c-0.8,0-1.7-0.1-2.5-0.3    c-1.1-0.2-1.7-0.9-1.7-2.1c0-1.1,0.1-2.2,0.4-3.2c2.7-11.2,5.5-22.3,8.3-33.4c0.3-1.1,0.7-2.2,1.2-3.2c0.5-1.2,1.4-1.9,2.7-2.1    c1.4-0.2,2.8-0.4,4.1-0.4c3.6-0.1,7.1-0.1,10.7,0c1.3,0,2.7,0.2,3.9,0.6c1.9,0.6,2.8,1.9,2.8,3.9c0,0.8-0.1,1.7-0.3,2.5    c-0.8,3.4-1.7,6.8-2.5,10.2C1201,230.2,1200.8,230.3,1200.7,230.6z"></path>
		<path class="st0" d="M1154.9,212.7c3.9,0,7.6,0,11.5,0c-3,11.9-6.1,23.7-9.1,35.5c1.1,0.4,2.2,0.2,3.3,0.2c1.1,0,2.2,0,3.3,0    c1.1,0,2.2,0,3.4,0c-0.8,3.1-1.5,5.9-2.2,8.8c-7.1,0-14.2,0-21.4,0C1147.4,242.4,1151.1,227.7,1154.9,212.7z"></path>
		<path class="st0" d="M1141.6,212.7c-3.1,12-6.1,23.7-9.2,35.8c3.4,0,6.6,0,10,0c-0.8,3.1-1.5,5.9-2.2,8.7    c-1.1,0.3-19.5,0.4-21.4,0.1c3.7-14.8,7.4-29.6,11.2-44.5C1133.9,212.7,1137.7,212.7,1141.6,212.7z"></path>
	</g>

<g>
	<path class="st0" d="M150.5,211.2c-4,16-8,32.1-12,48.4c-10,0.1-20-0.1-30.1-0.3c0.1-0.7,0.2-1.2,0.4-1.7   c4.6-17.9,9.2-35.8,13.8-53.7c4.2-16.4,8.4-32.7,12.6-49.1c1-3.8,2.6-7.4,4.5-10.8c1.8-3.1,4.4-4.8,7.8-5.4c2-0.4,4-0.7,6-0.7   c12.5,0,25,0,37.5,0.1c2,0,4,0.3,5.9,0.8c4.8,1.1,6.6,3.5,6.9,8.4c0.2,3-0.3,5.8-1,8.7c-2.4,9.8-4.9,19.6-7.4,29.4   c-6,24.1-12,48.2-18,72.4c-0.2,0.6-0.3,1.2-0.5,1.9c-10.2,0-20.2,0-30.5,0c4.2-16.1,8.4-32.2,12.6-48.2   C157.8,211,152.5,210.9,150.5,211.2z M162.9,162.2c-1,3.1-5.2,23.1-5.1,24.3c2.4,0,4.8,0,7.2,0c1.1-3,5.9-23.1,5.8-24.3   C168.2,162.2,165.6,162.2,162.9,162.2z"></path>
	<path class="st0" d="M311,211.2c-4,16.1-8,32.2-12,48.5c-10-0.1-20-0.2-30.2-0.3c0.5-2.2,1-4.2,1.6-6.2c8.4-32.7,16.8-65.3,25.2-98   c0.9-3.7,2.5-7.1,4.2-10.4c2.1-4,5.5-5.9,9.7-6.4c1.5-0.2,3-0.4,4.4-0.4c12.6,0,25.2,0,37.8,0.1c2.1,0,4.1,0.4,6.2,0.9   c4.2,1.1,6,3.4,6.3,7.7c0.3,3.3-0.3,6.5-1.1,9.7c-3,12.2-6.1,24.3-9.1,36.5c-5.4,21.7-10.8,43.4-16.3,65.1c-0.1,0.5-0.3,1-0.4,1.4   c-1.1,0.3-27.4,0.4-30.5,0.1c4.2-16.1,8.5-32.1,12.6-48.3C316.6,211.2,313.8,211.2,311,211.2z M325.6,186.5c2-8.1,4-16.1,6-24.2   c-0.5-0.1-0.9-0.1-1.2-0.2c-1.9,0-3.9,0-5.8,0c-1.2,0-1.2,0-1.5,1.3c-1.6,7.3-3.2,14.7-4.8,22c-0.1,0.3-0.1,0.6-0.1,1.1   C320.7,186.5,323.1,186.5,325.6,186.5z"></path>
	<path class="st0" d="M707.2,259.6c-6.6,0-13.2,0-19.8,0c-1.2,0-2.5-0.1-3.7-0.4c-5.1-1-7.2-5.1-7.9-8.5c-0.5-2.3-0.1-4.5,0.4-6.8   c6.6-25.9,13.3-51.7,19.9-77.6c0.9-3.7,1.9-7.4,2.9-11.1c1.1-4.1,3-7.9,5.7-11.2c2.8-3.4,6.3-5.3,10.7-5.8c1.4-0.2,2.8-0.2,4.1-0.2   c10.7,0,21.4,0,32.1,0c2,0,4.1,0.2,6,0.9c6.6,2.1,10.4,8,9.5,14.8c-0.5,3.4-1.4,6.7-2.2,10c-6.4,25.7-12.8,51.3-19.2,77   c-0.7,2.9-1.6,5.8-2.9,8.6c-0.8,1.5-1.7,3-2.6,4.5c-2.3,3.5-5.7,5.2-9.8,5.6c-1.6,0.2-3.3,0.2-4.9,0.3   C719.3,259.6,713.2,259.6,707.2,259.6C707.2,259.6,707.2,259.6,707.2,259.6z M709.3,235.3c2.7,0,5.1,0,7.5,0   c0.1-0.2,0.2-0.3,0.3-0.4c6.1-23.9,12.1-47.8,18.2-71.7c0.1-0.2,0.1-0.5,0.1-0.7c0,0,0-0.1-0.1-0.1c0,0-0.1-0.1-0.1-0.2   c-2.5,0-5,0-7.6,0C721.4,186.5,715.4,210.8,709.3,235.3z"></path>
	<path class="st0" d="M785.3,259.6c-6.6,0-13.2,0-19.8,0c-1.3,0-2.6-0.1-3.8-0.4c-4.4-1-7.6-4.9-7.9-9.5c-0.1-1.8,0-3.7,0.5-5.5   c4.9-19.2,9.9-38.5,14.8-57.7c2.6-10.2,5.2-20.4,7.9-30.6c0.9-3.6,2.4-6.9,4.5-10c3.5-5.3,8.5-7.8,14.8-7.8c8.6,0,17.2,0,25.7,0   c2.7,0,5.5,0,8.2,0c2,0,4.1,0.3,6,1c6.6,2.3,10.3,8.5,9,15.4c-0.8,4-1.9,8-2.8,12c-6.2,24.9-12.5,49.9-18.7,74.8   c-0.7,2.8-1.6,5.6-2.9,8.2c-0.8,1.6-1.7,3.1-2.8,4.6c-2.3,3.3-5.6,4.8-9.4,5.3c-1.7,0.2-3.4,0.3-5,0.3   C797.4,259.6,791.3,259.6,785.3,259.6C785.3,259.6,785.3,259.6,785.3,259.6z M795,235.3c6.2-24.4,12.3-48.7,18.5-73.1   c-0.7,0-1.2-0.1-1.6-0.1c-1.6,0-3.2,0-4.8,0c-1.6,0-1.6,0-2,1.6c-5.8,23.4-11.7,46.9-17.5,70.3c-0.1,0.4-0.1,0.8-0.2,1.3   C790.1,235.3,792.5,235.3,795,235.3z"></path>
	<path class="st0" d="M1019,259.6c-10.1,0-20.1,0-30.3,0c8.2-32.5,16.4-64.9,24.5-97.3c-1.4-0.4-2.7-0.2-3.9-0.2c-1.2,0-2.5,0-3.9,0   c-8.3,32.5-16.6,65-24.8,97.5c-9.9,0-19.7,0-29.4,0c0-0.1-0.1-0.1-0.1-0.2c0,0,0-0.1,0-0.1c0-0.3,0.1-0.6,0.1-0.9   c10-40.1,20-80.1,30-120.3c0.5,0,1-0.1,1.5-0.1c15.3,0,30.6,0,46,0c2.7,0,5.4,0.2,8.1,0.9c5.4,1.3,8.2,4,8.8,10.6   c0.4,4.3-0.5,8.4-1.5,12.5c-8.2,32-16.4,64.1-24.6,96.1C1019.3,258.5,1019.1,259,1019,259.6z"></path>
	<path class="st0" d="M261.6,259.5c-9.9,0-19.6,0-29.4,0c-1.8-12.6-3.7-25.1-5.5-37.6c-0.1,0-0.2,0-0.3,0c-1.1,4.1-2,8.3-3,12.4   c-1,4.2-2,8.4-3,12.6c-1,4.2-2,8.4-3,12.6c-10,0-19.9,0-30,0c10.1-40.5,20.2-81,30.3-121.5c0.6,0,1.1,0,1.6,0c14.6,0,29.2,0,43.7,0   c2.3,0,4.7,0.2,6.9,0.7c6.2,1.3,10,5.1,11.4,11.3c0.3,1.2,0.5,2.3,0.6,3.5c0.1,0.7,0,1.5-0.2,2.2c-3.6,13.4-7.3,26.9-10.9,40.3   c-1.2,4.6-4,8-7.9,10.6c-2.3,1.5-4.7,3-7.4,3.7c-0.6,0.2-1.2,0.5-1.8,0.8C256.2,227.3,258.9,243.3,261.6,259.5z M243.2,186.6   c0.8-2,5.8-23,5.7-24.2c-2.4,0-4.8,0-7.4,0c-2.2,8-4.4,16-6.7,24.2C237.9,186.6,240.6,186.6,243.2,186.6z"></path>
	<path class="st0" d="M868.3,221.9c-3,12.5-6,25.1-9,37.6c-10,0-19.9,0-30,0c10.1-40.5,20.1-80.9,30.3-121.5c0.6,0,1.1,0,1.6,0   c14.5,0,29.1,0,43.6,0c2.5,0,5,0.2,7.5,0.8c5.8,1.4,9.3,5,10.7,10.7c0.4,1.5,0.6,3.1,0.8,4.7c0.1,0.5-0.1,1.1-0.2,1.6   c-3.7,13.5-7.3,27.1-11,40.6c-1.2,4.5-4,7.8-7.9,10.3c-2.3,1.5-4.6,2.9-7.3,3.7c-0.6,0.2-1.2,0.5-1.9,0.8   c2.7,16.1,5.4,32.2,8.1,48.4c-9.8,0-19.5,0-29.4,0c-0.9-6.3-1.9-12.6-2.8-18.9c-0.9-6.2-1.8-12.5-2.8-18.7   C868.5,221.9,868.4,221.9,868.3,221.9z M883.5,162.4c-2.2,8.1-4.4,16-6.6,24.1c0.5,0.1,0.8,0.2,1.1,0.2c2,0,4.1,0,6.1,0   c1.1,0,1.2,0,1.5-1.2c1.8-7.2,3.5-14.4,5.3-21.6c0.1-0.4,0.1-0.9,0.2-1.4C888.5,162.4,886.1,162.4,883.5,162.4z"></path>
	<path class="st0" d="M1064.9,235.5c5.4,0,10.7,0,16.1,0c1-3.6,2-7.3,3.1-11.2c-3.3-0.2-6.4,0-9.7-0.1c2.3-8.5,4.6-16.8,6.8-25.1   c12.6,0,25.1,0,37.7,0c0,0.4-0.1,0.7-0.1,1c-3.7,14.7-7.3,29.3-11.1,43.9c-0.6,2.5-1.6,5-2.7,7.4c-2.4,5.4-6.9,7.9-12.6,8.2   c-2.6,0.1-5.2-0.1-7.7-0.1c-12.5,0-25.1,0-37.6,0c-2.6,0-5.3-0.1-7.8-0.7c-0.5-0.1-1-0.2-1.4-0.4c-3.9-1.3-5.6-3.5-5.9-7.6   c-0.3-4.2,0.3-8.2,1.3-12.2c6.6-25.7,13.1-51.4,19.7-77.2c0.9-3.6,1.7-7.1,2.8-10.6c0.6-2,1.4-3.9,2.3-5.8c1.4-2.9,3.8-4.6,6.9-5.5   c2.9-0.8,6-1.1,9-1.1c12.9,0,25.8,0,38.7,0c3,0,5.9,0.4,8.8,1.4c0.4,0.2,0.8,0.3,1.2,0.5c5,2.1,7.5,5.9,7.5,11.4   c0,2.9-0.6,5.6-1.2,8.4c-1.1,5.3-2.2,10.6-3.4,15.8c-0.1,0.4-0.2,0.8-0.3,1.2c-9.6,0-19.2,0-28.9,0c1.2-4.8,2.4-9.6,3.6-14.4   c-0.5-0.1-0.8-0.2-1.2-0.2c-4.8,0-9.6,0-14.4,0c-1.2,0-1.2,0-1.5,1.3c-2.8,11.1-5.5,22.1-8.3,33.2c-3,12.2-6.1,24.4-9.1,36.6   C1065.2,234.1,1065.1,234.7,1064.9,235.5z"></path>
	<path class="st0" d="M443.8,177.1c-9.7,0-19.2,0-28.9,0c1.2-4.9,2.4-9.6,3.6-14.6c-0.6-0.1-1.2-0.1-1.7-0.1c-4.5,0-9,0-13.5,0   c-1.7,0-1.7,0-2.1,1.6c-3.1,12.4-6.2,24.9-9.3,37.3c-2.6,10.7-5.3,21.3-7.9,32c-0.2,0.7-0.3,1.3-0.5,2.1c5.5,0,10.7,0,16.1,0   c1-3.7,2-7.3,3.1-11.2c-3.3,0-6.4,0-9.7,0c2.3-8.5,4.6-16.8,6.8-25.2c12.6,0,25.2,0,37.8,0c-0.1,0.5-0.1,0.9-0.2,1.3   c-3.6,14.5-7.2,29-11,43.5c-0.6,2.5-1.6,5-2.7,7.4c-2.4,5.5-6.9,7.9-12.7,8.2c-2.5,0.1-5-0.1-7.4-0.1c-12.5,0-25.1,0-37.6,0   c-2.7,0-5.5-0.1-8.1-0.8c-0.7-0.2-1.3-0.4-2-0.6c-3.2-1.1-4.9-3.4-5.2-6.9c-0.3-4.1,0.1-8,1.1-11.9c3.2-12.5,6.3-25,9.5-37.4   c4.2-16.5,8.4-32.9,12.6-49.4c0.5-1.9,1.1-3.7,1.8-5.5c1.8-4.9,5.5-7.5,10.6-8.1c2.5-0.3,4.9-0.5,7.4-0.5c12.2-0.1,24.5,0,36.7,0   c3,0,6.1,0.2,8.9,1.2c6.9,2.3,10.1,6.4,9.2,14.4c-0.4,4-1.5,8-2.3,12c-0.7,3.3-1.4,6.5-2.1,9.7C444,176.1,443.9,176.6,443.8,177.1z   "></path>
	<path class="st0" d="M119.6,177.1c-9.6,0-19.1,0-28.9,0c1.2-4.9,2.4-9.6,3.6-14.6c-0.6-0.1-1.1-0.1-1.5-0.1c-4.7,0-9.3,0-14,0   c-1.4,0-1.4,0-1.7,1.4c-5.8,23.4-11.6,46.7-17.4,70.1c-0.1,0.5-0.2,1-0.3,1.6c5.4,0,10.7,0,16.1,0c1-3.6,2-7.3,3.1-11.2   c-3.3,0-6.4,0-9.7,0c2.3-8.6,4.6-16.9,6.9-25.3c12.6,0,25.1,0,37.7,0c0,0.4,0,0.7-0.1,1c-3.7,14.6-7.3,29.2-11,43.8   c-0.6,2.5-1.6,5-2.7,7.4c-2.4,5.5-6.9,8-12.7,8.3c-2.6,0.1-5.3-0.1-7.9-0.1c-12.5,0-25,0-37.5,0c-3,0-6-0.1-8.8-1   c-0.8-0.3-1.6-0.6-2.3-0.9c-2.1-1.1-3.3-2.7-3.7-5.1c-0.5-3.2-0.5-6.4,0.2-9.5c1.2-5.3,2.5-10.6,3.8-15.9   c6.3-24.7,12.6-49.4,18.9-74.1c0.5-2.1,1.3-4.2,2-6.2c1.8-4.8,5.5-7.3,10.5-7.8c2.5-0.3,5-0.5,7.6-0.5c12.1-0.1,24.3,0,36.4,0   c3.3,0,6.5,0.3,9.7,1.4c1.1,0.4,2.2,0.9,3.2,1.5c3.6,2,5.2,5.1,5.5,9.1c0.2,3.1-0.3,6-0.9,9c-1.2,5.5-2.4,11-3.6,16.6   C119.8,176.3,119.7,176.7,119.6,177.1z"></path>
	<path class="st0" d="M502.6,210.9c-9.4,0-18.6,0-27.9,0c-2.2,8.1-4.3,16.2-6.5,24.5c4.9,0,9.5,0,14.2,0c4.6,0,9.3,0,14.2,0   c-2,8.2-3.9,16.1-5.9,24.1c-19.5,0-38.9,0-58.3,0c-0.1-1,30.1-119.7,30.9-121.6c19.4,0,38.9,0,58.6,0c-2.3,8.1-4.5,16-6.8,24.1   c-9.3,0-18.5,0-28,0c-2,8.2-4,16.2-6,24.5c9.3,0,18.3,0,27.6,0C506.6,194.7,504.7,202.7,502.6,210.9z"></path>
	<path class="st0" d="M619.2,162.1c-0.8,0-1.4,0-2,0c-7.7,0-15.5,0-23.2,0c-0.3,0-0.7,0-1,0c-1.2,0-1.2,0-1.5,1.2   c-0.6,2.3-1.2,4.7-1.9,7c-1.3,5-2.6,10-3.9,14.9c-0.1,0.4-0.2,0.9-0.3,1.5c1.9,0.1,3.8,0.1,5.6,0.1c1.8,0,3.7,0,5.5,0   c1.9,0,3.8,0,5.7,0c1.8,0,3.7,0,5.5,0c1.9,0,3.7,0,5.8,0c-2.2,8.2-4.3,16.2-6.5,24.3c-9.4,0-18.7,0-28.2,0   c-4.1,16.2-8.1,32.3-12.2,48.4c-10.1,0-20,0-30.1,0c10.3-40.5,20.6-80.8,31-121.2c19.6,0,39.2,0,58.9,0   C623.9,146.3,621.6,154.1,619.2,162.1z"></path>
	<path class="st0" d="M666,137.8c-8.3,32.5-16.6,64.9-25,97.5c9.2,0,18.2,0,27.4,0c-2,8.2-4,16.1-6,24.1c-19.3,0-38.6,0-58,0   c10.2-40.6,20.3-81,30.5-121.5C645.2,137.8,655.5,137.8,666,137.8z"></path>
	<path class="st0" d="M941.2,259.6c-10,0-19.8,0-29.7,0c-0.1-0.1-0.1-0.1-0.1-0.1c0,0,0-0.1,0-0.1c0-0.2,0.1-0.5,0.1-0.7   c10-40,20-79.9,30-119.9c0-0.2,0.1-0.4,0.2-0.6c10,0,20,0,30.2,0C961.6,178.7,951.4,219.1,941.2,259.6z"></path>
</g>
</svg>
			</a>
		</li>
		<li class="distributor">
			<a href="https://concretesealersupply.com/">
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 260 99" style="enable-background:new 0 0 260 99;" xml:space="preserve">
<style type="text/css">
	.st0{fill:#222;}
</style>
<g>
	<path class="st0" d="M43.6,25.1c-0.9,1.1-2,1.9-3.3,2.5c-0.3,0.2-0.7,0.3-1,0.5c-0.1,0.1-0.3,0.1-0.2,0.3c0,0.2,0.2,0.1,0.3,0.1   c1.1,0,2.1-0.3,3-1c0.6-0.5,1.1-1.1,1.4-1.9c0.1-0.2,0.1-0.4,0.1-0.7C43.7,25,43.6,25.1,43.6,25.1z"></path>
	<path class="st0" d="M37.6,11.7c-0.2,0.2-0.3,0.4-0.5,0.7c-1.1,1.5-2.1,3.1-3,4.8c-0.7,1.3-1.2,2.6-1.5,4.1c-0.2,1-0.2,2,0.1,3   c0.4,1.3,1.4,2,2.7,1.9c0.7,0,1.3-0.4,1.5-1.1c0.2-0.6,0.3-1.2,0.3-1.8c0-0.4-0.2-0.8-0.4-1.1c-0.1-0.1-0.2-0.2-0.3-0.4   c-0.1-0.1-0.2-0.3-0.3-0.5c-0.1-0.2-0.1-0.5-0.1-0.8c0-0.7,0.1-1.4,0.2-2.1c0.3-2.1,0.8-4.1,1.4-6.1c0.1-0.3,0.2-0.6,0.3-0.9   c0,0,0,0-0.1,0C37.8,11.5,37.7,11.6,37.6,11.7z"></path>
	<path class="st0" d="M93.6,14.9c-0.5-0.3-1.2-0.3-1.8-0.3h-2.2v3.9h2.2c1,0,2.1-0.1,2.5-1.2c0-0.1,0.1-0.4,0.1-0.7   c0-0.4-0.1-0.9-0.3-1.2C94,15.1,93.7,15,93.6,14.9z"></path>
	<path class="st0" d="M116.2,41.9h-6.7v9.6h6.8c0.2,0,1.3-0.1,2.1-0.3c1.1-0.4,2.7-1.5,2.7-4.5c0-0.4,0-1.5-0.6-2.6   C119.4,41.9,116.9,41.9,116.2,41.9z"></path>
	<polygon class="st0" points="194.8,15.9 192.9,21.8 196.6,21.8  "></polygon>
	<path class="st0" d="M243.8,17.3c0-0.1,0.1-0.4,0.1-0.7c0-0.4-0.1-0.9-0.3-1.2c-0.2-0.2-0.4-0.4-0.5-0.4c-0.5-0.3-1.2-0.3-1.8-0.3   h-2.2v3.9h2.2C242.3,18.5,243.4,18.4,243.8,17.3z"></path>
	<path class="st0" d="M0,0v88.9h74.5v-7.5h111.1v7.5H260V0H0z M233.7,11.1h9.1c0.5,0,1,0,1.5,0.1c0.7,0.1,2.9,0.3,4.1,2.3   c0.3,0.5,0.8,1.6,0.8,3c0,1.6-0.6,3.1-2,4c-0.4,0.2-0.7,0.4-1.5,0.7l4.1,6.8h-6l-3.3-5.9h-1.6v5.9h-5.4V11.1z M218.3,11.1h13.4v3.7   h-8v2.5h7.5v3.7h-7.5v3.3h8.7v3.7h-14.1V11.1z M205.3,11.1h5.3v12.6h6.5v4.3h-11.9V11.1z M191.8,11.1h6.1l6.3,16.9h-5.7l-0.7-2.4   h-5.9l-0.7,2.4h-5.7L191.8,11.1z M171,11.1h13.4v3.7h-8v2.5h7.5v3.7h-7.5v3.3h8.7v3.7H171V11.1z M163.9,22.6   c-0.1-0.2-0.3-0.5-0.8-0.6c-0.6-0.2-1.7-0.4-3.2-0.7c-2.3-0.5-3.6-1-4.4-1.7c-0.5-0.5-1.5-1.6-1.5-3.4c0-0.5,0-1.5,0.6-2.6   c1.7-3.2,6.2-3.1,6.8-3.1c0.9,0,3.9,0,6.3,1.8c0.5,0.4,0.8,0.7,1.3,1.2l-3.4,2.8c-0.2-0.3-0.3-0.4-0.5-0.5c-1-0.9-2.7-1.2-3.9-1.2   c-0.1,0-0.8,0-1.3,0.2c-0.1,0-0.7,0.3-0.7,0.9c0,0.3,0.2,0.6,0.4,0.8c0.4,0.3,1.4,0.5,1.9,0.5c0.6,0.1,2.2,0.3,3.8,0.9   c1.1,0.3,3.7,1.3,3.7,4.6c0,2.5-1.5,3.9-2.2,4.4c-2.2,1.6-5,1.6-5.7,1.6c-2.5,0-4.8-0.4-6.8-1.9c-0.8-0.6-1.2-1.1-1.8-1.8l4.1-2.6   c0.4,0.6,0.6,0.8,0.8,1c1,0.8,2.7,1.2,3.9,1.2c1.5,0,2.6-0.5,2.6-1.3C164,22.8,163.9,22.7,163.9,22.6z M130.6,11.1H144v3.7h-8v2.5   h7.5v3.7h-7.5v3.3h8.7v3.7h-14.1V11.1z M115.6,11.1h13.9v4.2h-4.3v12.7h-5.3V15.2h-4.3V11.1z M101.2,11.1h13.4v3.7h-8v2.5h7.5v3.7   h-7.5v3.3h8.7v3.7h-14.1V11.1z M67.7,13.9c2.3-3.3,5.5-3.5,7-3.5c3.8,0,6.1,2,7.1,3.1c0.5,0.6,0.7,1,1.1,1.6l-4.5,2.2   c-0.1-0.2-0.2-0.3-0.2-0.5c-0.3-0.6-1.1-2.3-3.3-2.3c-1,0-1.6,0.4-1.9,0.6c-1.7,1.2-1.6,3.7-1.6,4.1c0,2.3,0.7,3.6,1.6,4.2   c0.8,0.6,1.6,0.7,2,0.7c1.8,0,2.7-1.2,3-1.8c0.1-0.2,0.2-0.5,0.4-1l4.6,2.2c-0.6,1.1-0.9,1.5-1.3,2c-1,1.1-3.2,2.9-6.9,2.9   c-2.2,0-4.1-0.5-5.7-1.9c-2.8-2.4-3-5.7-3-6.9C66,18.6,66.1,16.2,67.7,13.9z M14,13.9c2.3-3.3,5.5-3.5,7-3.5c3.8,0,6.1,2,7.1,3.1   c0.5,0.6,0.7,1,1.1,1.6l-4.5,2.2c-0.1-0.2-0.2-0.3-0.2-0.5c-0.3-0.6-1.1-2.3-3.3-2.3c-1,0-1.6,0.4-1.9,0.6   c-1.7,1.2-1.6,3.7-1.6,4.1c0,2.3,0.7,3.6,1.6,4.2c0.8,0.6,1.6,0.7,2,0.7c1.8,0,2.7-1.2,3-1.8c0.1-0.2,0.2-0.5,0.4-1l4.6,2.2   c-0.6,1.1-0.9,1.5-1.3,2c-1,1.1-3.2,2.9-6.9,2.9c-2.2,0-4.1-0.5-5.7-1.9c-2.8-2.4-3-5.7-3-6.9C12.3,18.6,12.4,16.2,14,13.9z    M40.5,69.3c-4.9,3.6-11.2,3.5-12.8,3.5c-5.5,0-10.7-0.9-15.2-4.3c-1.7-1.3-2.7-2.4-4-4.1l9.1-5.8c0.8,1.3,1.3,1.9,1.8,2.3   c2.1,1.9,6.1,2.7,8.7,2.7c3.4,0,5.7-1,5.7-2.9c0-0.4-0.2-0.7-0.3-0.9c-0.3-0.5-0.7-1-1.8-1.4c-1.3-0.5-3.7-0.8-7.1-1.5   c-5.2-1-7.9-2.3-9.8-3.9c-1.2-1-3.3-3.5-3.3-7.6c0-1,0.1-3.3,1.4-5.7c3.8-7,13.8-6.9,15.3-6.9c1.9,0,8.7,0.1,14.1,4.1   c1.2,0.9,1.9,1.6,2.9,2.6l-7.5,6.2c-0.5-0.6-0.7-1-1.1-1.2c-2.2-2-6-2.6-8.8-2.6c-0.3,0-1.7,0-2.8,0.5c-0.2,0.1-1.5,0.7-1.5,2   c0,0.7,0.4,1.3,1,1.7c1,0.6,3.2,1,4.2,1.2c1.3,0.2,4.9,0.7,8.6,1.9c2.3,0.7,8.2,2.8,8.2,10.3C45.4,65,42.1,68.1,40.5,69.3z    M38.5,30.3C38.5,30.3,38.5,30.3,38.5,30.3c-7.2,0-9-5.1-7.6-10.3c1.3-4.8,6.7-13.2,7.5-14.3l0-0.1l0,0.1c0.8,1.2,6.2,9.5,7.5,14.3   C47.3,25.2,45.6,30.3,38.5,30.3z M48,27.9V11.1h5.7l5.7,9.8l-0.1-9.8h5v16.9h-5.8l-5.6-9.8l0,9.8H48z M88.2,56.5   c0,2.4-0.3,4.4-1.2,6.7c-2.6,6.8-9.4,9.4-16.2,9.4c-1.4,0-5.1-0.1-8.9-1.8c-1.6-0.7-5.4-2.6-7.2-7.2c-0.7-1.9-1-3.4-1-7.4V34h11.9   v23.5c0,0.4-0.1,1.7,0.4,2.9c1,2.9,3.9,3,4.8,3c0.8,0,3.8-0.2,4.9-3c0.4-1,0.4-1.8,0.4-2.9V34h12V56.5z M91.2,22h-1.6v5.9h-5.4   V11.1h9.1c0.5,0,1,0,1.5,0.1c0.7,0.1,2.9,0.3,4.1,2.3c0.3,0.5,0.8,1.6,0.8,3c0,1.6-0.6,3.1-2,4c-0.4,0.2-0.7,0.4-1.5,0.7l4.1,6.8   h-6L91.2,22z M132.1,51.6c-0.5,1.3-2.4,5.2-6.6,6.8c-2.2,0.9-4.5,1-6.8,1h-9.2v12.3H97.6V34h21.5c3.7,0,7.1,0.3,10,2.8   c3.8,3.3,3.9,7.8,3.9,9.4C133,47,132.9,49.4,132.1,51.6z M175.2,51.6c-0.5,1.3-2.4,5.2-6.6,6.8c-2.2,0.9-4.5,1-6.8,1h-9.2v12.3   h-11.9V34h21.5c3.7,0,7.1,0.3,10,2.8c3.8,3.3,3.9,7.8,3.9,9.4C176.1,47,176,49.4,175.2,51.6z M209.9,71.6h-26.5V34h11.9v28.2h14.6   V71.6z M236.4,55.8v15.8h-11.9V55.8L209.4,34h13.8l7.4,12.5l7.3-12.5h13.8L236.4,55.8z"></path>
	<path class="st0" d="M159.3,41.9h-6.7v9.6h6.8c0.2,0,1.3-0.1,2.1-0.3c1.1-0.4,2.7-1.5,2.7-4.5c0-0.4,0-1.5-0.6-2.6   C162.5,41.9,160,41.9,159.3,41.9z"></path>
</g>
<g>
	<path class="st0" d="M81.4,87.4v1.5h3.8v1.8h-3.8V94h-2.7v-8.5h6.7v1.9H81.4z"></path>
	<path class="st0" d="M92,94l-1.6-3h-0.8v3h-2.7v-8.5h4.6c0.2,0,0.5,0,0.7,0c0.4,0,1.5,0.1,2.1,1.2c0.1,0.3,0.4,0.8,0.4,1.5   c0,0.8-0.3,1.5-1,2c-0.2,0.1-0.3,0.2-0.7,0.3L95,94H92z M91.8,87.7c-0.1-0.1-0.2-0.2-0.3-0.2c-0.3-0.2-0.6-0.2-0.9-0.2h-1.1v2h1.1   c0.5,0,1-0.1,1.2-0.6c0,0,0.1-0.2,0.1-0.4C92,88.1,91.9,87.9,91.8,87.7z"></path>
	<path class="st0" d="M96,94v-8.5h6.7v1.9h-4v1.3h3.8v1.8h-3.8v1.7h4.4V94H96z"></path>
	<path class="st0" d="M104.3,94v-8.5h6.7v1.9h-4v1.3h3.8v1.8H107v1.7h4.4V94H104.3z"></path>
	<path class="st0" d="M123.8,93.5c-1.1,0.8-2.5,0.8-2.9,0.8c-1.2,0-2.4-0.2-3.4-1c-0.4-0.3-0.6-0.5-0.9-0.9l2.1-1.3   c0.2,0.3,0.3,0.4,0.4,0.5c0.5,0.4,1.4,0.6,2,0.6c0.8,0,1.3-0.2,1.3-0.6c0-0.1,0-0.2-0.1-0.2c-0.1-0.1-0.2-0.2-0.4-0.3   c-0.3-0.1-0.8-0.2-1.6-0.3c-1.2-0.2-1.8-0.5-2.2-0.9c-0.3-0.2-0.7-0.8-0.7-1.7c0-0.2,0-0.7,0.3-1.3c0.9-1.6,3.1-1.6,3.4-1.6   c0.4,0,2,0,3.2,0.9c0.3,0.2,0.4,0.4,0.6,0.6l-1.7,1.4c-0.1-0.1-0.2-0.2-0.2-0.3c-0.5-0.5-1.3-0.6-2-0.6c-0.1,0-0.4,0-0.6,0.1   c0,0-0.3,0.2-0.3,0.4c0,0.2,0.1,0.3,0.2,0.4c0.2,0.1,0.7,0.2,0.9,0.3c0.3,0,1.1,0.2,1.9,0.4c0.5,0.2,1.8,0.6,1.8,2.3   C124.9,92.5,124.1,93.2,123.8,93.5z"></path>
	<path class="st0" d="M131.6,94v-3.3H129V94h-2.7v-8.5h2.7v3.1h2.6v-3.1h2.7V94H131.6z"></path>
	<path class="st0" d="M136,94v-8.5h2.7V94H136z"></path>
	<path class="st0" d="M148.1,89.5c-0.1,0.3-0.5,1.2-1.5,1.5c-0.5,0.2-1,0.2-1.5,0.2H143V94h-2.7v-8.5h4.8c0.8,0,1.6,0.1,2.3,0.6   c0.9,0.7,0.9,1.8,0.9,2.1C148.3,88.5,148.3,89,148.1,89.5z M145.5,87.8c-0.3-0.5-0.8-0.5-1-0.5H143v2.2h1.5c0,0,0.3,0,0.5-0.1   c0.2-0.1,0.6-0.3,0.6-1C145.6,88.3,145.6,88,145.5,87.8z"></path>
	<path class="st0" d="M157.2,89.5c-0.1,0.3-0.5,1.2-1.5,1.5c-0.5,0.2-1,0.2-1.5,0.2h-2.1V94h-2.7v-8.5h4.8c0.8,0,1.6,0.1,2.3,0.6   c0.9,0.7,0.9,1.8,0.9,2.1C157.4,88.5,157.4,89,157.2,89.5z M154.6,87.8c-0.3-0.5-0.8-0.5-1-0.5h-1.5v2.2h1.5c0,0,0.3,0,0.5-0.1   c0.2-0.1,0.6-0.3,0.6-1C154.7,88.3,154.7,88,154.6,87.8z"></path>
	<path class="st0" d="M158.5,94v-8.5h2.7V94H158.5z"></path>
	<path class="st0" d="M168.2,94l-2.8-4.9l0,4.9h-2.5v-8.5h2.8l2.9,4.9l0-4.9h2.5V94H168.2z"></path>
	<path class="st0" d="M178.9,88.5c0-0.2-0.1-0.4-0.2-0.6c-0.4-0.6-1.3-0.7-1.4-0.7c-0.2,0-0.6,0-1,0.3c-1,0.6-1,2-1,2.2   c0,0.3,0,0.7,0.1,1c0.2,0.6,0.6,1.2,1.3,1.4c0.2,0,0.3,0.1,0.7,0.1c0.4,0,0.8-0.1,1.2-0.4c0.1-0.1,0.2-0.2,0.3-0.3   c0.1-0.2,0.1-0.5,0.2-0.5h-1.6v-1.7h3.9V94h-2v-0.6c-0.3,0.2-0.4,0.3-0.6,0.4c-0.8,0.4-1.7,0.5-2,0.5c-0.3,0-1,0-1.8-0.4   c-0.8-0.4-2.4-1.6-2.4-4c0-0.5,0.1-2,1.1-3.2c0.9-1,2.1-1.5,3.4-1.5c1.4,0,2.9,0.5,3.8,1.6c0.3,0.4,0.4,0.8,0.5,1L178.9,88.5z"></path>
</g>
</svg>
			</a>
		</li>
		<li class="distributor">
			<a href="http://redimixcolors.com/product-category/ghostshield/">
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 209 52" style="enable-background:new 0 0 209 52;" xml:space="preserve">
<style type="text/css">
	.st0{fill-rule:evenodd;clip-rule:evenodd;fill:#222;}
	.st1{fill:#222;}
</style>
<g>
	<rect x="58.1" y="1.5" class="st0" width="0.7" height="49.3"></rect>
	</g><g>
		</g><g>
			<path class="st1" d="M84.8,28.7L80,23.5h-7.1v-2.9h7.9c1.2,0,2.1-0.3,2.7-1c0.6-0.6,0.9-1.6,0.9-2.8s-0.3-2.2-1-2.8     c-0.6-0.6-1.5-0.9-2.7-0.9H70.5v15.7h-3.3v-19h13.6c1.1,0,2,0.2,2.9,0.5c0.9,0.3,1.6,0.8,2.2,1.4c0.6,0.6,1,1.3,1.4,2.2     s0.5,1.8,0.5,2.9c0,1.6-0.3,2.9-1,4C86,21.7,85,22.5,83.8,23l5.7,5.8H84.8z"></path>
			<path class="st1" d="M91.2,28.7v-19h17v3.3H94.5v12.4h13.7v3.3H91.2z M96.9,20.5v-2.8h10.5v2.8H96.9z"></path>
			<path class="st1" d="M111,28.7v-19h11c1.3,0,2.6,0.2,3.7,0.7c1.1,0.5,2.1,1.1,2.9,1.9c0.8,0.8,1.5,1.8,1.9,2.9     c0.5,1.1,0.7,2.4,0.7,3.8c0,1.4-0.2,2.7-0.7,3.8c-0.5,1.2-1.1,2.2-1.9,3.1c-0.8,0.9-1.8,1.5-2.9,2c-1.1,0.5-2.4,0.7-3.7,0.7H111z      M122.1,25.4c0.9,0,1.6-0.2,2.4-0.5c0.7-0.3,1.3-0.7,1.9-1.3c0.5-0.5,0.9-1.2,1.2-2c0.3-0.8,0.4-1.6,0.4-2.5s-0.1-1.7-0.4-2.5     c-0.3-0.8-0.7-1.4-1.2-1.9c-0.5-0.5-1.2-1-1.9-1.3s-1.5-0.5-2.4-0.5h-7.7v12.4H122.1z"></path>
			<path class="st1" d="M134.2,28.7v-19h3.3v19H134.2z"></path>
			<path class="st1" d="M140.1,20.2V18h6.3v2.2H140.1z"></path>
			<path class="st1" d="M171.5,28.7l-3.6-13.4l-5.4,12.4c-0.2,0.4-0.4,0.7-0.7,0.9c-0.3,0.2-0.6,0.3-1,0.3c-0.8,0-1.3-0.4-1.7-1.3     l-5.4-12.4l-3.6,13.4h-3.3l4.9-17.9c0.2-0.9,0.8-1.4,1.6-1.4c0.3,0,0.7,0.1,1,0.3c0.3,0.2,0.5,0.5,0.7,0.8l5.9,13.4l5.9-13.4     c0.3-0.7,0.8-1.1,1.6-1.1c0.8,0,1.3,0.5,1.6,1.4l4.9,17.9H171.5z"></path>
			<path class="st1" d="M177.2,28.7v-19h3.3v19H177.2z"></path>
			<path class="st1" d="M203.1,28.7l-8-7.8l-7.9,7.8h-4.3l10.2-9.9l-9.5-9.1h4.6l7.2,7l7.1-7h4.3l-9.5,9.1l10.3,9.9H203.1z"></path>
		</g>
		<g>
			<path class="st1" d="M77.5,39.6l1.5,0.3c-0.5,0.9-1.1,1.5-1.7,2c-1.1,0.7-2.4,1-4,1c-1.9,0-3.4-0.5-4.5-1.5     c-1-0.9-1.5-2.2-1.5-3.7c0-1.7,0.6-3,1.8-3.9c1.1-0.8,2.4-1.2,4.1-1.2c1.5,0,2.8,0.3,3.7,0.9c0.7,0.4,1.3,1.1,1.8,1.9l-1.5,0.3     c-0.1-0.3-0.3-0.5-0.4-0.6c-0.2-0.2-0.4-0.4-0.7-0.6c-0.8-0.5-1.7-0.7-2.9-0.7c-1.3,0-2.3,0.3-3.1,0.9c-0.9,0.7-1.4,1.7-1.4,3     c0,1.1,0.3,2.1,1,2.8c0.8,0.8,2,1.2,3.6,1.3c1.3,0,2.4-0.3,3.1-0.9C76.9,40.4,77.3,40,77.5,39.6z"></path>
			<path class="st1" d="M99.9,32.5c1.7,0,3.2,0.5,4.3,1.4c1.1,1,1.7,2.3,1.7,3.9c0,1.6-0.6,2.9-1.8,3.8c-1.1,0.8-2.5,1.3-4.1,1.3     c-1.7,0-3-0.4-4-1.1c-1.3-0.9-2-2.3-2-4s0.6-3,1.9-4C96.9,32.9,98.3,32.5,99.9,32.5z M99.8,33.7c-1.2,0-2.2,0.3-3,0.9     c-0.9,0.7-1.4,1.7-1.4,3.1c0,1,0.3,1.8,0.9,2.5c0.8,0.9,2.1,1.4,3.8,1.4c1.6,0,2.8-0.5,3.6-1.4c0.6-0.7,0.9-1.6,0.9-2.6     c0-1.3-0.5-2.3-1.4-3C102.3,34,101.2,33.7,99.8,33.7z"></path>
			<path class="st1" d="M121.8,42.6v-9.8h1.4v8.6h6.5v1.2H121.8z"></path>
			<path class="st1" d="M150.1,32.5c1.7,0,3.2,0.5,4.3,1.4c1.1,1,1.7,2.3,1.7,3.9c0,1.6-0.6,2.9-1.8,3.8c-1.1,0.8-2.5,1.3-4.1,1.3     c-1.7,0-3-0.4-4-1.1c-1.3-0.9-2-2.3-2-4s0.6-3,1.9-4C147.2,32.9,148.5,32.5,150.1,32.5z M150,33.7c-1.2,0-2.2,0.3-3,0.9     c-0.9,0.7-1.4,1.7-1.4,3.1c0,1,0.3,1.8,0.9,2.5c0.8,0.9,2.1,1.4,3.8,1.4c1.6,0,2.8-0.5,3.6-1.4c0.6-0.7,0.9-1.6,0.9-2.6     c0-1.3-0.5-2.3-1.4-3C152.5,34,151.4,33.7,150,33.7z"></path>
			<path class="st1" d="M172,42.6v-9.8h6.7c1.2,0,2.2,0.3,2.8,0.8c0.6,0.5,0.9,1.2,0.9,2.1c0,1.6-0.9,2.5-2.6,2.8l3.6,4.2h-1.8     l-3.3-3.9h-4.8v3.9H172z M178.8,33.9h-5.4v3.6h4.9c1.1,0,1.9-0.3,2.3-0.8c0.2-0.3,0.3-0.6,0.3-1c0-0.4-0.1-0.7-0.3-1.1     c-0.2-0.3-0.5-0.5-0.8-0.6C179.5,34,179.2,33.9,178.8,33.9z"></path>
			<path class="st1" d="M197.5,40.6l1.3-0.5c0.2,0.2,0.4,0.4,0.7,0.6c0.9,0.6,2.2,1,3.7,1c1.1,0,1.9-0.2,2.5-0.5     c0.6-0.4,0.9-0.8,0.9-1.5c0-0.6-0.3-1-1-1.3c-0.4-0.1-1.2-0.3-2.5-0.4c-1.4-0.1-2.3-0.2-2.7-0.3c-0.6-0.1-1.1-0.3-1.4-0.7     c-0.5-0.4-0.7-1-0.7-1.6c0-1.2,0.7-2,2-2.5c0.7-0.3,1.6-0.4,2.6-0.4c1.6,0,2.9,0.3,3.9,1c0.4,0.2,0.7,0.6,1,1l-1.3,0.5     c-0.1-0.1-0.3-0.3-0.6-0.5c-0.8-0.5-1.8-0.8-3-0.8c-1,0-1.8,0.1-2.4,0.4c-0.6,0.3-0.9,0.7-0.9,1.2c0,0.3,0.1,0.5,0.3,0.8     c0.3,0.3,0.8,0.5,1.8,0.6c0.6,0.1,1.5,0.1,2.7,0.3c0.5,0.1,1,0.1,1.3,0.2c1.5,0.4,2.3,1.2,2.3,2.5c0,0.6-0.2,1.2-0.5,1.6     c-0.7,1-2.2,1.6-4.3,1.6c-0.9,0-1.8-0.1-2.5-0.3C199.2,42.2,198.1,41.5,197.5,40.6z"></path>
		</g>
	
	<g>
		<rect x="0.9" y="1.5" class="st0" width="14.6" height="14.6"></rect>
		<rect x="0.9" y="18.9" class="st0" width="14.6" height="14.6"></rect>
		<rect x="0.9" y="36.3" class="st0" width="14.6" height="14.6"></rect>
		<polygon class="st0" points="32.9,50.8 18.3,36.3 32.9,36.3   "></polygon>
		<path class="st0" d="M35.6,1.5c8,0,14.6,6.5,14.6,14.6H35.6V1.5z"></path>
		<path class="st0" d="M50.1,18.9c0,8-6.5,14.6-14.6,14.6V18.9H50.1z"></path>
		<rect x="18.3" y="1.5" class="st0" width="14.6" height="14.6"></rect>
		<rect x="18.3" y="18.9" class="st0" width="14.6" height="14.6"></rect>
		<polygon class="st0" points="35.6,36.3 50.1,50.8 35.6,50.8   "></polygon>
	</g>

</svg>
			</a>
		</li>
		<li class="distributor">
			<a href="https://www.lowes.com/search?searchTerm=ghostshield">
				<svg xmlns:cc="http://creativecommons.org/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="svg5890" x="0px" y="0px" viewBox="0 0 91.2 43" style="enable-background:new 0 0 91.2 43;" xml:space="preserve">
<style type="text/css">
	.st0{fill:#222;}
</style>
<g>
	<path class="st0" d="M82.6,21.6c0-0.2-0.2-0.2-0.3-0.2H82v0.4h0.3v0C82.4,21.9,82.6,21.9,82.6,21.6z"></path>
	<path class="st0" d="M83,14.9v-4H68.7L45.6,1l-23,9.9H8.2v4H1V42h89.2V14.9H83z M21.5,36.5H11.2V18.1h4.1V33h6.3V36.5z M35,34.6   c0,1.1-0.9,1.9-1.9,1.9h-7.9c-1.1,0-1.9-0.9-1.9-1.9V23.3c0-1.1,0.9-1.9,1.9-1.9H33c1.1,0,1.9,0.9,1.9,1.9V34.6z M54,34.6   c0,1.1-0.9,1.9-1.9,1.9h-4.8c-0.8,0-1.6-0.6-1.6-1.4c0,0.8-0.8,1.4-1.6,1.4h-4.8c-1.1,0-1.9-0.9-1.9-1.9V21.3h4.1V33h2.5V21.3h3.6   V33h2.5V21.3H54V34.6z M66,24.9h-5.6v2.3H66v3.4h-5.6V33H66v3.6h-9.7V21.4v0H66V24.9z M66.9,18.1L66.9,18.1l3.4,0l-0.6,4.4l-2.8,0   L66.9,18.1z M79,36.3c-0.7,0.6-1.8,0.9-3.3,0.9h0c-2.7,0-5.7-1-5.8-1l-0.1,0v-3.6l0.1,0c0,0,3.2,1.2,4.7,1.4l0,0c0.1,0,0.2,0,0.3,0   c0.8,0,1.4-0.4,1.6-0.9c0.1-0.5-0.2-1.1-0.9-1.5l0,0c-0.6-0.3-1.2-0.6-1.8-0.9c-1.5-0.7-3-1.3-3.9-3c-0.7-1.4-0.6-3.4,0.2-4.6   c1-1.5,2.6-2.2,4.7-2.2c0.2,0,0.5,0,0.8,0c2.3,0.3,4.5,1.7,4.5,1.7l0,0v3.3l-0.1,0c0,0-1-0.4-2.2-0.9c-0.6-0.2-1.6-0.6-2.6-0.6   c-0.6,0-1,0.2-1.3,0.5c-0.3,0.2-0.4,0.6-0.3,0.9c0.2,0.9,1.6,1.4,2,1.6l0.1,0c2.3,0.9,3.8,2,4.4,3.3l0,0c0,0,0,0.1,0,0.1l0,0.1l0,0   l0,0c0.3,0.6,0.4,1.3,0.4,1.9C80.7,34.9,79,36.3,79,36.3z M82.2,23.2c-0.7,0-1.3-0.5-1.3-1.2c0-0.7,0.6-1.2,1.3-1.2   c0.7,0,1.3,0.5,1.3,1.2C83.5,22.7,82.9,23.2,82.2,23.2z"></path>
	<rect x="27.3" y="24.9" class="st0" width="3.6" height="8.1"></rect>
	<path class="st0" d="M82.2,20.9c-0.6,0-1,0.4-1,1c0,0.6,0.4,1,1,1c0.6,0,1-0.4,1-1C83.2,21.4,82.8,20.9,82.2,20.9z M82.6,22.7   L82.2,22H82v0.6h-0.2v-1.4h0.5c0.3,0,0.5,0.1,0.5,0.4c0,0.3-0.2,0.4-0.4,0.4l0.4,0.6H82.6z"></path>
</g>
</svg>
			</a>
		</li>
		<li class="distributor">
			<a href="https://www.diyhomecenter.com/search?keywords=ghostshield">
				<svg xmlns:cc="http://creativecommons.org/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="svg5890" x="0px" y="0px" viewBox="0 0 274.5 57.2" style="enable-background:new 0 0 274.5 57.2;" xml:space="preserve">
<style type="text/css">
	.st0{fill:#222;}
</style>
<polygon class="st0" points="19.2,38.6 19.2,18.8 23.6,16.4 42.2,6.5 60.8,16.4 71.1,16.4 47.3,3.7 42.2,1 37,3.7 19.2,13.3   19.2,7.8 14.4,7.8 14.4,15.8 7,19.8 9.3,24.1 14.3,21.4 14.3,46.1 14.3,50.9 19.2,50.9 69.7,50.9 69.7,46.1 19.2,46.1 "></polygon>
<path class="st0" d="M30.1,25.4h3.1c0.9,0,1.6,0.1,2.3,0.4s1.2,0.6,1.7,1.1c0.4,0.5,0.8,1,1,1.7c0.2,0.6,0.3,1.3,0.3,2.1  c0,0.7-0.1,1.4-0.4,2c-0.2,0.6-0.6,1.2-1,1.7c-0.5,0.5-1,0.9-1.7,1.2C34.8,35.8,34,36,33.2,36h-3.1V25.4L30.1,25.4z M33.2,40.3  c1.5,0,2.9-0.2,4.2-0.7c1.3-0.4,2.3-1.1,3.3-1.9c0.9-0.8,1.6-1.8,2.2-3c0.5-1.2,0.8-2.5,0.8-4s-0.3-2.8-0.8-4s-1.2-2.2-2.2-3  c-0.9-0.8-2-1.5-3.3-1.9s-2.7-0.7-4.2-0.7h-8.1v19.2C25.1,40.3,33.2,40.3,33.2,40.3z"></path>
<rect x="45.6" y="21.1" class="st0" width="4.9" height="19.2"></rect>
<polygon class="st0" points="51.8,21.1 59.1,32.6 59.1,40.3 64,40.3 64,32.6 71.3,21.1 65.7,21.1 61.6,28.2 57.4,21.1 "></polygon>
<polygon class="st0" points="88.4,21.1 88.4,28.3 80.4,28.3 80.4,21.1 75.4,21.1 75.4,40.3 80.4,40.3 80.4,32.6 88.4,32.6   88.4,40.3 93.3,40.3 93.3,21.1 "></polygon>
<path class="st0" d="M98.7,23.6c-0.9,0.9-1.6,1.9-2.2,3.1c-0.5,1.2-0.8,2.6-0.8,4c0,1.5,0.3,2.8,0.8,4s1.2,2.3,2.2,3.1  c0.9,0.9,2,1.6,3.3,2c1.2,0.5,2.6,0.7,4.1,0.7s2.8-0.2,4.1-0.7c1.2-0.5,2.3-1.2,3.3-2s1.6-1.9,2.1-3.1c0.5-1.2,0.8-2.6,0.8-4  c0-1.5-0.3-2.8-0.8-4s-1.2-2.3-2.1-3.1c-0.9-0.9-2-1.6-3.3-2c-1.2-0.5-2.6-0.7-4.1-0.7s-2.8,0.2-4.1,0.7  C100.7,22,99.7,22.7,98.7,23.6L98.7,23.6z M106.1,25.2c0.8,0,1.5,0.1,2.2,0.4c0.6,0.3,1.2,0.7,1.6,1.2c0.5,0.5,0.8,1.1,1,1.8  c0.2,0.7,0.4,1.4,0.4,2.2c0,0.8-0.1,1.5-0.4,2.2c-0.2,0.7-0.6,1.3-1,1.8c-0.5,0.5-1,0.9-1.6,1.2s-1.4,0.4-2.2,0.4  c-0.8,0-1.5-0.1-2.2-0.4c-0.6-0.3-1.2-0.7-1.7-1.2s-0.8-1.1-1.1-1.8c-0.2-0.7-0.4-1.4-0.4-2.2c0-0.8,0.1-1.5,0.4-2.2  c0.2-0.7,0.6-1.3,1.1-1.8s1-0.9,1.7-1.2C104.6,25.3,105.3,25.2,106.1,25.2z"></path>
<polygon class="st0" points="118.8,21.1 118.8,40.3 123.8,40.3 123.8,27.6 128.7,40.3 130.9,40.3 135.7,27.6 135.7,40.3 140.7,40.3   140.7,21.1 133.8,21.1 129.8,31.7 125.7,21.1 "></polygon>
<polygon class="st0" points="144.1,21.1 144.1,40.3 158.2,40.3 158.2,36 149.1,36 149.1,32.8 158,32.8 158,28.4 149.1,28.4   149.1,25.4 158.2,25.4 158.2,21.1 "></polygon>
<path class="st0" d="M171.9,22.9c0.7,0,1.4,0.1,2.1,0.4c0.7,0.2,1.3,0.6,1.8,1s1,0.9,1.4,1.5l2-1.1c-0.5-0.7-1.1-1.4-1.8-2  s-1.5-1-2.4-1.3s-1.9-0.5-3.1-0.5c-1.1,0-2.2,0.2-3.2,0.5s-1.9,0.8-2.7,1.3c-0.8,0.6-1.5,1.3-2.1,2.1c-0.6,0.8-1.1,1.7-1.4,2.7  c-0.3,1-0.5,2.1-0.5,3.3c0,1.2,0.2,2.3,0.5,3.3s0.8,1.9,1.4,2.7c0.6,0.8,1.3,1.5,2.1,2.1s1.7,1,2.7,1.3s2,0.5,3.2,0.5  c1.1,0,2.1-0.2,3-0.5s1.7-0.8,2.4-1.4c0.7-0.6,1.3-1.2,1.9-2l-2-1.1c-0.4,0.5-0.8,1-1.4,1.4c-0.5,0.4-1.2,0.7-1.8,1  c-0.7,0.2-1.4,0.4-2.1,0.4c-1.1,0-2-0.2-2.9-0.6c-0.9-0.4-1.7-0.9-2.4-1.6c-0.7-0.7-1.2-1.5-1.6-2.5c-0.4-1-0.6-2-0.6-3.1  c0-1.2,0.2-2.2,0.6-3.2c0.4-1,0.9-1.8,1.6-2.5c0.7-0.7,1.5-1.2,2.4-1.6C169.9,23.1,170.9,22.9,171.9,22.9z"></path>
<polygon class="st0" points="181.8,21.1 181.8,40.3 194.4,40.3 194.4,38.2 184.2,38.2 184.2,31.6 194.1,31.6 194.1,29.4 184.2,29.4   184.2,23.2 194.4,23.2 194.4,21.1 "></polygon>
<polygon class="st0" points="197.7,21.1 197.7,40.3 200.1,40.3 200.1,25 211.3,40.3 213.6,40.3 213.6,21.1 211.2,21.1 211.2,36.1   200.1,21.1 "></polygon>
<polygon class="st0" points="216.8,23.2 222.9,23.2 222.9,40.3 225.3,40.3 225.3,23.2 231.4,23.2 231.4,21.1 216.8,21.1 "></polygon>
<polygon class="st0" points="234.6,21.1 234.6,40.3 247.2,40.3 247.2,38.2 237,38.2 237,31.6 247,31.6 247,29.4 237,29.4 237,23.2   247.2,23.2 247.2,21.1 "></polygon>
<path class="st0" d="M250.6,21.1v19.2h2.4v-7.6h3.9l4.9,7.6h2.8l-5.2-7.8c0.7-0.1,1.3-0.2,1.9-0.5s1.1-0.6,1.6-1.1s0.8-1,1.1-1.7  c0.3-0.7,0.4-1.4,0.4-2.3s-0.2-1.7-0.5-2.4c-0.3-0.7-0.7-1.3-1.3-1.8c-0.5-0.5-1.2-0.9-1.9-1.2c-0.7-0.3-1.5-0.4-2.4-0.4L250.6,21.1  L250.6,21.1z M253,30.6v-7.3h5c0.8,0,1.4,0.2,2,0.5s1,0.7,1.4,1.3c0.3,0.5,0.5,1.2,0.5,1.9c0,0.7-0.2,1.4-0.5,1.9  c-0.3,0.6-0.8,1-1.4,1.3s-1.2,0.5-2,0.5L253,30.6L253,30.6z"></path>
<g>
	<polygon class="st0" points="260.6,45.1 260.6,51.1 264.8,51.1 264.8,50.2 261.7,50.2 261.7,48.5 264.7,48.5 264.7,47.6    261.7,47.6 261.7,46 264.8,46 264.8,45.1  "></polygon>
	<polygon class="st0" points="78.3,51.1 78.3,46 80.1,46 80.1,45.1 75.4,45.1 75.4,46 77.2,46 77.2,51.1  "></polygon>
	<polygon class="st0" points="81,45.1 81,51.1 82.1,51.1 82.1,48.5 85.3,48.5 85.3,51.1 86.3,51.1 86.3,45.1 85.3,45.1 85.3,47.5    82.1,47.5 82.1,45.1  "></polygon>
	<polygon class="st0" points="87.6,45.1 87.6,51.1 91.8,51.1 91.8,50.2 88.7,50.2 88.7,48.5 91.7,48.5 91.7,47.6 88.7,47.6 88.7,46    91.8,46 91.8,45.1  "></polygon>
	<path class="st0" d="M95.2,45.1v6.1h3.1c0.3,0,0.5,0,0.8-0.1c0.2-0.1,0.4-0.2,0.6-0.3c0.1-0.1,0.3-0.3,0.3-0.5   c0.1-0.2,0.1-0.4,0.1-0.7c0-0.2,0-0.4-0.1-0.5c-0.1-0.2-0.1-0.3-0.3-0.5c-0.1-0.1-0.2-0.2-0.4-0.3c-0.1-0.1-0.3-0.1-0.5-0.2   c0.1,0,0.3-0.1,0.4-0.2c0.1-0.1,0.2-0.2,0.3-0.3c0.1-0.1,0.2-0.3,0.2-0.4c0.1-0.2,0.1-0.3,0.1-0.5s0-0.4-0.1-0.6   c-0.1-0.2-0.2-0.4-0.3-0.5c-0.1-0.1-0.3-0.3-0.5-0.3c-0.2-0.1-0.5-0.1-0.7-0.1h-3V45.1z M96.3,47.6V46H98c0.2,0,0.3,0,0.5,0.1   c0.1,0.1,0.2,0.2,0.3,0.3c0.1,0.1,0.1,0.3,0.1,0.4c0,0.2,0,0.3-0.1,0.4c-0.1,0.1-0.2,0.2-0.3,0.3c-0.1,0.1-0.3,0.1-0.5,0.1H96.3   L96.3,47.6z M96.3,50.2v-1.7h1.8c0.2,0,0.4,0,0.5,0.1s0.2,0.2,0.3,0.3C99,49,99,49.2,99,49.3c0,0.3-0.1,0.5-0.2,0.6   c-0.2,0.1-0.4,0.2-0.7,0.2h-1.8V50.2z"></path>
	<polygon class="st0" points="101.1,45.1 101.1,51.1 105.3,51.1 105.3,50.2 102.2,50.2 102.2,48.5 105.2,48.5 105.2,47.6    102.2,47.6 102.2,46 105.3,46 105.3,45.1  "></polygon>
	<path class="st0" d="M106.2,46.7c0,0.3,0.1,0.5,0.2,0.7s0.2,0.4,0.4,0.5c0.2,0.1,0.4,0.2,0.6,0.3s0.4,0.2,0.6,0.2l0.7,0.2   c0.2,0.1,0.4,0.1,0.6,0.2c0.2,0.1,0.3,0.2,0.4,0.3s0.2,0.2,0.2,0.4c0,0.1,0,0.2-0.1,0.3c0,0.1-0.1,0.2-0.2,0.3s-0.2,0.1-0.4,0.2   c-0.2,0-0.3,0.1-0.6,0.1c-0.3,0-0.5,0-0.8-0.1c-0.2-0.1-0.5-0.2-0.6-0.3c-0.2-0.1-0.4-0.3-0.5-0.4l-0.6,0.8   c0.2,0.2,0.4,0.4,0.6,0.5s0.5,0.3,0.8,0.3c0.3,0.1,0.6,0.1,1,0.1s0.8-0.1,1.1-0.2c0.3-0.1,0.5-0.2,0.7-0.4s0.3-0.4,0.4-0.6   c0.1-0.2,0.1-0.5,0.1-0.7c0-0.3-0.1-0.5-0.2-0.7s-0.2-0.4-0.4-0.5c-0.2-0.1-0.4-0.2-0.6-0.3s-0.4-0.2-0.6-0.2   c-0.2-0.1-0.4-0.1-0.6-0.2c-0.2-0.1-0.4-0.1-0.6-0.2c-0.2-0.1-0.3-0.1-0.4-0.2c-0.1-0.1-0.2-0.2-0.2-0.3c0-0.2,0-0.3,0.1-0.4   c0.1-0.1,0.2-0.2,0.4-0.3c0.2-0.1,0.3-0.1,0.5-0.1s0.4,0,0.6,0.1s0.4,0.1,0.6,0.2c0.2,0.1,0.4,0.2,0.5,0.4l0.6-0.8   c-0.3-0.3-0.6-0.5-1-0.6s-0.8-0.2-1.3-0.2c-0.3,0-0.6,0-0.9,0.1s-0.5,0.2-0.7,0.4s-0.3,0.4-0.4,0.6   C106.3,46.3,106.2,46.5,106.2,46.7z"></path>
	<polygon class="st0" points="113.3,46 113.3,51.1 114.3,51.1 114.3,46 116.2,46 116.2,45.1 111.4,45.1 111.4,46  "></polygon>
	<path class="st0" d="M119.4,45.1v6.1h3.1c0.3,0,0.5,0,0.8-0.1c0.2-0.1,0.4-0.2,0.6-0.3c0.1-0.1,0.3-0.3,0.3-0.5   c0.1-0.2,0.1-0.4,0.1-0.7c0-0.2,0-0.4-0.1-0.5c-0.1-0.2-0.1-0.3-0.3-0.5c-0.1-0.1-0.2-0.2-0.4-0.3c-0.1-0.1-0.3-0.1-0.5-0.2   c0.1,0,0.3-0.1,0.4-0.2c0.1-0.1,0.2-0.2,0.3-0.3c0.1-0.1,0.2-0.3,0.2-0.4c0.1-0.2,0.1-0.3,0.1-0.5s0-0.4-0.1-0.6   c-0.1-0.2-0.2-0.4-0.3-0.5c-0.1-0.1-0.3-0.3-0.5-0.3c-0.2-0.1-0.5-0.1-0.7-0.1h-3V45.1z M120.5,47.6V46h1.7c0.2,0,0.3,0,0.5,0.1   c0.1,0.1,0.2,0.2,0.3,0.3c0.1,0.1,0.1,0.3,0.1,0.4c0,0.2,0,0.3-0.1,0.4c-0.1,0.1-0.2,0.2-0.3,0.3c-0.1,0.1-0.3,0.1-0.5,0.1H120.5   L120.5,47.6z M120.5,48.5h1.8c0.2,0,0.4,0,0.5,0.1s0.2,0.2,0.3,0.3c0.1,0.1,0.1,0.3,0.1,0.4c0,0.3-0.1,0.5-0.2,0.6   c-0.2,0.1-0.4,0.2-0.7,0.2h-1.8C120.5,50.1,120.5,48.5,120.5,48.5z"></path>
	<path class="st0" d="M125.3,45.1v6.1h1.1v-2.3h1.1l1.3,2.3h1.2l-1.5-2.4c0.2,0,0.4-0.1,0.5-0.2c0.2-0.1,0.3-0.2,0.5-0.4   c0.1-0.1,0.2-0.3,0.3-0.5s0.1-0.4,0.1-0.7s0-0.5-0.1-0.8c-0.1-0.2-0.2-0.4-0.4-0.6c-0.2-0.2-0.4-0.3-0.6-0.4   c-0.2-0.1-0.5-0.1-0.8-0.1L125.3,45.1L125.3,45.1z M126.4,47.9V46h1.5c0.2,0,0.4,0,0.5,0.1c0.2,0.1,0.3,0.2,0.4,0.3   c0.1,0.1,0.1,0.3,0.1,0.5s0,0.4-0.1,0.5s-0.2,0.3-0.4,0.3c-0.2,0.1-0.3,0.1-0.5,0.1h-1.5V47.9z"></path>
	<path class="st0" d="M132.8,45.1l-2.4,6.1h1.2L132,50h2.8l0.4,1.2h1.2l-2.4-6.1H132.8L132.8,45.1z M132.4,49l1.1-2.9l1.1,2.9H132.4   z"></path>
	<polygon class="st0" points="137.2,45.1 137.2,51.1 138.3,51.1 138.3,46.8 141.5,51.1 142.5,51.1 142.5,45.1 141.4,45.1    141.4,49.3 138.3,45.1  "></polygon>
	<path class="st0" d="M143.8,45.1v6.1h2.3c0.5,0,0.9-0.1,1.3-0.2c0.4-0.1,0.7-0.4,1-0.6c0.3-0.3,0.5-0.6,0.6-1   c0.2-0.4,0.2-0.8,0.2-1.2s-0.1-0.9-0.2-1.2c-0.2-0.4-0.4-0.7-0.6-1c-0.3-0.3-0.6-0.5-1-0.6s-0.8-0.2-1.3-0.2L143.8,45.1L143.8,45.1   z M144.8,50.2V46h1.2c0.3,0,0.6,0.1,0.9,0.2c0.3,0.1,0.5,0.3,0.7,0.4c0.2,0.2,0.3,0.4,0.4,0.7c0.1,0.3,0.1,0.5,0.1,0.8   s0,0.6-0.1,0.8c-0.1,0.3-0.2,0.5-0.4,0.7s-0.4,0.3-0.7,0.5c-0.3,0.1-0.6,0.2-0.9,0.2h-1.2V50.2z"></path>
	<path class="st0" d="M150.1,46.7c0,0.3,0.1,0.5,0.2,0.7c0.1,0.2,0.2,0.4,0.4,0.5s0.4,0.2,0.6,0.3c0.2,0.1,0.4,0.2,0.6,0.2l0.7,0.2   c0.2,0.1,0.4,0.1,0.6,0.2c0.2,0.1,0.3,0.2,0.4,0.3c0.1,0.1,0.2,0.2,0.2,0.4c0,0.1,0,0.2-0.1,0.3c0,0.1-0.1,0.2-0.2,0.3   c-0.1,0.1-0.2,0.1-0.4,0.2c-0.2,0-0.3,0.1-0.6,0.1s-0.5,0-0.8-0.1c-0.2-0.1-0.5-0.2-0.6-0.3c-0.2-0.1-0.4-0.3-0.5-0.4l-0.6,0.8   c0.2,0.2,0.4,0.4,0.6,0.5c0.2,0.1,0.5,0.3,0.8,0.3c0.3,0.1,0.6,0.1,1,0.1s0.8-0.1,1.1-0.2c0.3-0.1,0.5-0.2,0.7-0.4s0.3-0.4,0.4-0.6   c0.1-0.2,0.1-0.5,0.1-0.7c0-0.3-0.1-0.5-0.2-0.7c-0.1-0.2-0.2-0.4-0.4-0.5s-0.4-0.2-0.6-0.3c-0.2-0.1-0.4-0.2-0.6-0.2   c-0.2-0.1-0.4-0.1-0.6-0.2c-0.2-0.1-0.4-0.1-0.6-0.2c-0.2-0.1-0.3-0.1-0.4-0.2c-0.1-0.1-0.2-0.2-0.2-0.3c0-0.2,0-0.3,0.1-0.4   c0.1-0.1,0.2-0.2,0.4-0.3s0.3-0.1,0.5-0.1s0.4,0,0.6,0.1c0.2,0.1,0.4,0.1,0.6,0.2c0.2,0.1,0.4,0.2,0.5,0.4l0.6-0.8   c-0.3-0.3-0.6-0.5-1-0.6s-0.8-0.2-1.3-0.2c-0.3,0-0.6,0-0.9,0.1c-0.3,0.1-0.5,0.2-0.7,0.4s-0.3,0.4-0.4,0.6   C150.1,46.3,150.1,46.5,150.1,46.7z"></path>
	<polygon class="st0" points="158,45.1 158,51.1 159.1,51.1 159.1,48.5 162.1,48.5 162.1,47.6 159.1,47.6 159.1,46 162.2,46    162.2,45.1  "></polygon>
	<path class="st0" d="M166.8,50.1c-0.2,0.1-0.5,0.2-0.8,0.2s-0.6-0.1-0.8-0.2c-0.2-0.1-0.5-0.3-0.6-0.5c-0.2-0.2-0.3-0.4-0.4-0.7   c-0.1-0.3-0.1-0.6-0.1-0.9c0-0.3,0-0.6,0.1-0.9c0.1-0.3,0.2-0.5,0.4-0.7s0.4-0.4,0.6-0.5c0.2-0.1,0.5-0.2,0.8-0.2s0.6,0.1,0.8,0.2   c0.2,0.1,0.5,0.3,0.6,0.5c0.2,0.2,0.3,0.4,0.4,0.7c0.1,0.3,0.1,0.6,0.1,0.9c0,0.3,0,0.6-0.1,0.9c-0.1,0.3-0.2,0.5-0.4,0.7   C167.3,49.9,167.1,50,166.8,50.1L166.8,50.1z M162.9,48.1c0,0.5,0.1,0.9,0.2,1.3c0.2,0.4,0.4,0.7,0.6,1c0.3,0.3,0.6,0.5,1,0.7   s0.8,0.2,1.3,0.2s0.9-0.1,1.3-0.2c0.4-0.2,0.7-0.4,1-0.7s0.5-0.6,0.6-1c0.2-0.4,0.2-0.8,0.2-1.3s-0.1-0.9-0.2-1.3   c-0.2-0.4-0.4-0.7-0.6-1c-0.3-0.3-0.6-0.5-1-0.7s-0.8-0.2-1.3-0.2s-0.9,0.1-1.3,0.2c-0.4,0.2-0.7,0.4-1,0.7s-0.5,0.6-0.6,1   C163,47.2,162.9,47.6,162.9,48.1z"></path>
	<path class="st0" d="M170.1,45.1v6.1h1.1v-2.3h1.1l1.3,2.3h1.2l-1.5-2.4c0.2,0,0.4-0.1,0.5-0.2c0.2-0.1,0.3-0.2,0.5-0.4   c0.1-0.1,0.2-0.3,0.3-0.5s0.1-0.4,0.1-0.7s0-0.5-0.1-0.8c-0.1-0.2-0.2-0.4-0.4-0.6c-0.2-0.2-0.4-0.3-0.6-0.4   c-0.2-0.1-0.5-0.1-0.8-0.1L170.1,45.1L170.1,45.1z M171.2,47.9V46h1.5c0.2,0,0.4,0,0.5,0.1c0.2,0.1,0.3,0.2,0.4,0.3   c0.1,0.1,0.1,0.3,0.1,0.5s0,0.4-0.1,0.5s-0.2,0.3-0.4,0.3c-0.2,0.1-0.3,0.1-0.5,0.1h-1.5V47.9z"></path>
	<polygon class="st0" points="179.9,48.6 179.9,51.1 181,51.1 181,48.6 183.4,45.1 182.1,45.1 180.5,47.7 178.8,45.1 177.6,45.1     "></polygon>
	<path class="st0" d="M183.6,49.4c0.2,0.4,0.4,0.7,0.6,1c0.3,0.3,0.6,0.5,1,0.7s0.8,0.2,1.3,0.2s0.9-0.1,1.3-0.2   c0.4-0.2,0.7-0.4,1-0.7s0.5-0.6,0.6-1c0.2-0.4,0.2-0.8,0.2-1.3s-0.1-0.9-0.2-1.3c-0.2-0.4-0.4-0.7-0.6-1c-0.3-0.3-0.6-0.5-1-0.7   s-0.8-0.2-1.3-0.2s-0.9,0.1-1.3,0.2c-0.4,0.2-0.7,0.4-1,0.7s-0.5,0.6-0.6,1c-0.2,0.4-0.2,0.8-0.2,1.3   C183.4,48.6,183.5,49,183.6,49.4L183.6,49.4z M186.5,50.3c-0.3,0-0.6-0.1-0.8-0.2c-0.2-0.1-0.5-0.3-0.6-0.5   c-0.2-0.2-0.3-0.4-0.4-0.7c-0.1-0.3-0.1-0.6-0.1-0.9c0-0.3,0-0.6,0.1-0.9c0.1-0.3,0.2-0.5,0.4-0.7s0.4-0.4,0.6-0.5   c0.2-0.1,0.5-0.2,0.8-0.2s0.6,0.1,0.8,0.2c0.2,0.1,0.5,0.3,0.6,0.5c0.2,0.2,0.3,0.4,0.4,0.7c0.1,0.3,0.1,0.6,0.1,0.9   c0,0.3,0,0.6-0.1,0.9c-0.1,0.3-0.2,0.5-0.4,0.7s-0.4,0.4-0.6,0.5C187.1,50.2,186.8,50.3,186.5,50.3z"></path>
	<path class="st0" d="M190.8,49.8c0.1,0.3,0.3,0.6,0.5,0.8s0.5,0.4,0.8,0.5s0.7,0.2,1.2,0.2c0.4,0,0.8-0.1,1.2-0.2   c0.3-0.1,0.6-0.3,0.8-0.5s0.4-0.5,0.5-0.8s0.2-0.7,0.2-1v-3.7h-1.1v3.6c0,0.3-0.1,0.6-0.2,0.8c-0.1,0.2-0.3,0.4-0.5,0.6   c-0.2,0.1-0.5,0.2-0.9,0.2c-0.3,0-0.6-0.1-0.9-0.2c-0.2-0.1-0.4-0.3-0.5-0.6c-0.1-0.2-0.2-0.5-0.2-0.8v-3.6h-1.1v3.7   C190.6,49.1,190.7,49.4,190.8,49.8z"></path>
	<path class="st0" d="M197.2,45.1v6.1h1.1v-2.3h1.1l1.3,2.3h1.2l-1.5-2.4c0.2,0,0.4-0.1,0.5-0.2c0.2-0.1,0.3-0.2,0.5-0.4   c0.1-0.1,0.2-0.3,0.3-0.5s0.1-0.4,0.1-0.7s0-0.5-0.1-0.8c-0.1-0.2-0.2-0.4-0.4-0.6c-0.2-0.2-0.4-0.3-0.6-0.4   c-0.2-0.1-0.5-0.1-0.8-0.1L197.2,45.1L197.2,45.1z M198.3,47.9V46h1.5c0.2,0,0.4,0,0.5,0.1c0.2,0.1,0.3,0.2,0.4,0.3   c0.1,0.1,0.1,0.3,0.1,0.5s0,0.4-0.1,0.5s-0.2,0.3-0.4,0.3c-0.2,0.1-0.3,0.1-0.5,0.1h-1.5V47.9z"></path>
	<path class="st0" d="M205.3,45.1v6.1h2.3c0.5,0,0.9-0.1,1.3-0.2c0.4-0.1,0.7-0.4,1-0.6c0.3-0.3,0.5-0.6,0.6-1   c0.2-0.4,0.2-0.8,0.2-1.2s-0.1-0.9-0.2-1.2c-0.2-0.4-0.4-0.7-0.6-1c-0.3-0.3-0.6-0.5-1-0.6s-0.8-0.2-1.3-0.2L205.3,45.1L205.3,45.1   z M206.3,50.2V46h1.2c0.3,0,0.6,0.1,0.9,0.2c0.3,0.1,0.5,0.3,0.7,0.4c0.2,0.2,0.3,0.4,0.4,0.7c0.1,0.3,0.1,0.5,0.1,0.8   s0,0.6-0.1,0.8c-0.1,0.3-0.2,0.5-0.4,0.7s-0.4,0.3-0.7,0.5c-0.3,0.1-0.6,0.2-0.9,0.2h-1.2V50.2z"></path>
	<polygon class="st0" points="211.7,45.1 211.7,51.1 215.9,51.1 215.9,50.2 212.8,50.2 212.8,48.5 215.8,48.5 215.8,47.6    212.8,47.6 212.8,46 215.9,46 215.9,45.1  "></polygon>
	<path class="st0" d="M216.9,49.1c0.1,0.3,0.3,0.6,0.4,0.9c0.2,0.3,0.4,0.5,0.7,0.7s0.6,0.3,0.9,0.4c0.3,0.1,0.7,0.1,1,0.1   s0.6,0,0.8-0.1s0.5-0.2,0.7-0.3s0.4-0.3,0.5-0.4s0.3-0.3,0.4-0.5l-0.9-0.5c-0.1,0.2-0.2,0.3-0.4,0.5c-0.2,0.1-0.3,0.2-0.5,0.3   s-0.4,0.1-0.6,0.1c-0.3,0-0.6-0.1-0.8-0.2c-0.3-0.1-0.5-0.3-0.7-0.5s-0.3-0.4-0.4-0.7c-0.1-0.3-0.2-0.6-0.2-0.9   c0-0.3,0.1-0.6,0.2-0.9c0.1-0.3,0.3-0.5,0.4-0.7c0.2-0.2,0.4-0.3,0.7-0.5c0.3-0.1,0.5-0.2,0.8-0.2c0.2,0,0.4,0,0.6,0.1   c0.2,0.1,0.4,0.2,0.5,0.3c0.2,0.1,0.3,0.3,0.4,0.5l0.9-0.5c-0.1-0.2-0.2-0.4-0.4-0.5c-0.2-0.2-0.3-0.3-0.5-0.4   c-0.2-0.1-0.4-0.2-0.7-0.3c-0.2-0.1-0.5-0.1-0.8-0.1c-0.4,0-0.7,0.1-1,0.1c-0.3,0.1-0.6,0.2-0.9,0.4c-0.3,0.2-0.5,0.4-0.7,0.7   s-0.3,0.5-0.4,0.9c-0.1,0.3-0.2,0.7-0.2,1C216.7,48.5,216.8,48.8,216.9,49.1z"></path>
	<polygon class="st0" points="223.2,45.1 223.2,51.1 224.3,51.1 224.3,49.2 224.8,48.6 226.9,51.1 228.2,51.1 225.5,47.9 228,45.1    226.7,45.1 224.3,47.9 224.3,45.1  "></polygon>
	<path class="st0" d="M231.7,48c-0.2,0.1-0.3,0.2-0.4,0.4c-0.1,0.1-0.2,0.3-0.3,0.5s-0.1,0.4-0.1,0.6c0,0.3,0.1,0.5,0.2,0.8   c0.1,0.2,0.2,0.4,0.4,0.5s0.4,0.3,0.6,0.3c0.2,0.1,0.5,0.1,0.8,0.1c0.2,0,0.4,0,0.6-0.1c0.2-0.1,0.4-0.1,0.6-0.2   c0.2-0.1,0.3-0.2,0.5-0.3c0.1,0.1,0.2,0.2,0.3,0.3s0.2,0.2,0.2,0.2h1.3c-0.2-0.2-0.4-0.4-0.6-0.6c-0.2-0.2-0.4-0.4-0.6-0.6   c0.2-0.2,0.3-0.5,0.5-0.8c0.1-0.3,0.3-0.5,0.4-0.8l-0.8-0.4c0,0.1-0.1,0.3-0.1,0.4c-0.1,0.1-0.1,0.3-0.2,0.4   c-0.1,0.2-0.2,0.3-0.3,0.4c-0.1-0.1-0.2-0.2-0.3-0.3l-0.3-0.4c-0.1-0.1-0.2-0.2-0.3-0.3s-0.2-0.3-0.3-0.4c0.2-0.1,0.4-0.2,0.6-0.3   c0.2-0.1,0.3-0.2,0.5-0.3c0.1-0.1,0.3-0.3,0.3-0.5c0.1-0.2,0.1-0.4,0.1-0.6c0-0.2,0-0.4-0.1-0.5c-0.1-0.2-0.2-0.3-0.3-0.4   c-0.1-0.1-0.3-0.2-0.5-0.2c-0.2-0.1-0.4-0.1-0.6-0.1c-0.2,0-0.5,0-0.7,0.1s-0.4,0.2-0.5,0.3s-0.3,0.3-0.3,0.5   c-0.1,0.2-0.1,0.4-0.1,0.6c0,0.2,0,0.4,0.1,0.7c0.1,0.2,0.2,0.5,0.3,0.7C232,47.8,231.8,47.9,231.7,48L231.7,48z M232.9,48.8   c0.1,0.1,0.2,0.3,0.3,0.4c0.1,0.1,0.2,0.2,0.3,0.4c0.1,0.1,0.3,0.3,0.4,0.5c-0.2,0.1-0.3,0.2-0.5,0.3s-0.4,0.1-0.6,0.1   c-0.1,0-0.3,0-0.4-0.1c-0.1,0-0.2-0.1-0.3-0.2c-0.1-0.1-0.2-0.2-0.2-0.3c-0.1-0.1-0.1-0.3-0.1-0.4c0-0.2,0-0.3,0.1-0.5   c0.1-0.1,0.1-0.3,0.2-0.4c0.1-0.1,0.2-0.2,0.4-0.3C232.7,48.5,232.8,48.6,232.9,48.8L232.9,48.8z M233,47.3   c-0.1-0.2-0.2-0.3-0.2-0.5c-0.1-0.2-0.1-0.3-0.1-0.4c0-0.2,0-0.3,0.1-0.4c0.1-0.1,0.2-0.2,0.3-0.3s0.2-0.1,0.4-0.1   c0.1,0,0.2,0,0.3,0.1c0.1,0,0.2,0.1,0.2,0.2c0.1,0.1,0.1,0.2,0.1,0.3c0,0.2,0,0.3-0.1,0.4c-0.1,0.1-0.2,0.2-0.4,0.3   C233.4,47.1,233.2,47.2,233,47.3z"></path>
	<polygon class="st0" points="239.5,45.1 239.5,51.1 240.5,51.1 240.5,48.5 243.7,48.5 243.7,51.1 244.8,51.1 244.8,45.1    243.7,45.1 243.7,47.5 240.5,47.5 240.5,45.1  "></polygon>
	<path class="st0" d="M245.8,48.1c0,0.5,0.1,0.9,0.2,1.3c0.2,0.4,0.4,0.7,0.6,1c0.3,0.3,0.6,0.5,1,0.7s0.8,0.2,1.3,0.2   s0.9-0.1,1.3-0.2c0.4-0.2,0.7-0.4,1-0.7s0.5-0.6,0.6-1c0.2-0.4,0.2-0.8,0.2-1.3s-0.1-0.9-0.2-1.3c-0.2-0.4-0.4-0.7-0.6-1   c-0.3-0.3-0.6-0.5-1-0.7s-0.8-0.2-1.3-0.2s-0.9,0.1-1.3,0.2c-0.4,0.2-0.7,0.4-1,0.7s-0.5,0.6-0.6,1S245.8,47.6,245.8,48.1   L245.8,48.1z M247,49c-0.1-0.3-0.1-0.6-0.1-0.9c0-0.3,0-0.6,0.1-0.9c0.1-0.3,0.2-0.5,0.4-0.7s0.4-0.4,0.6-0.5   c0.2-0.1,0.5-0.2,0.8-0.2s0.6,0.1,0.8,0.2c0.2,0.1,0.5,0.3,0.6,0.5c0.2,0.2,0.3,0.4,0.4,0.7c0.1,0.3,0.1,0.6,0.1,0.9   c0,0.3,0,0.6-0.1,0.9c-0.1,0.3-0.2,0.5-0.4,0.7s-0.4,0.4-0.6,0.5c-0.2,0.1-0.5,0.2-0.8,0.2s-0.6-0.1-0.8-0.2   c-0.2-0.1-0.5-0.3-0.6-0.5C247.3,49.5,247.1,49.2,247,49z"></path>
	<polygon class="st0" points="253,45.1 253,51.1 254.1,51.1 254.1,46.4 256,51.1 256.4,51.1 258.3,46.4 258.3,51.1 259.3,51.1    259.3,45.1 257.8,45.1 256.2,49.2 254.5,45.1  "></polygon>
</g>
</svg>
			</a>
		</li>
	</ul>';
	}


	$html .= '<div class="form_container"><div><h4>Contact a Pro</h4><form method="POST" class="contact">
		<label for="fcontact_email">Email</label>
		<input type="text" class="half_width contact_email" placeholder="Email Address" name="contact_email" id="fcontact_email" />
		<label for="fcontact_name">Name</label>
		<input type="text" class="half_width contact_name" placeholder="Name" name="contact_name" id="fcontact_name" />
		<label for="fcontact_comment">Comments</label>
		<textarea name="contact_comment" class="contact_comment" placeholder="'.$GLOBALS["comment_placeholder"].'" id="fcontact_comment"></textarea>
		<input type="submit" value="'.$GLOBALS["contact_button_text"].'" class="contact_submit" />
	</form></div>
	<div class="info_container">
	<div>
	<h4>Phone:</h4>
	<a href="tel:18555738383">855.573.8383</a>
</div>
<div>
	<h4>Address:</h4>
	<span>66 RIVER ROAD</span>
	<span>HUDSON, NH 03051</span>
</div>
<div>
	<h4>Hours:</h4>
	<span>M-F 9:00AM EST - 5:00PM EST</span>
</div>
</div>
</div>
	<hr>
	<div>
		<div class="contact_info">
				<ul class="footer_menu">
				<li class="footer_menu_item">
					<a href="//'.$this->host.'/about">About Ghostshield</a>
				</li>
				<li class="footer_menu_item">
					<a href="//'.$this->host.'/privacy">Privacy Policy</a>
				</li>
				<li class="footer_menu_item">
					<a href="//'.$this->host.'/warranty">Warranty</a>
				</li>';
				if ($this->host == "dev.ghostshield.com") $html .= '
								<li class="footer_menu_item"><a href="//'.$this->host.'/update/" class="item">Update &amp; Preview</a></li>
								<li class="footer_menu_item"><a href="//'.$this->host.'/publish/" class="item">Publish</a></li>';
		$html .='</ul>
				<div class="social_link_container"><ul class="footer_social_links">
					<li><a href="https://www.instagram.com/ghostshield_sealers/"><i class="fab fa-instagram"></i></a></li>
					<li><a href="https://www.facebook.com/GhostshieldSealers/"><i class="fab fa-facebook-f"></i></a></li>
					<li><a href="https://twitter.com/kretetek"><i class="fab fa-twitter"></i></a></li>
					<li><a href="https://www.youtube.com/channel/UC9X3Cl8uRVG5X9XlXfmUryw"><i class="fab fa-youtube"></i></a></li>
					<li><a href="https://vimeo.com/ghostshieldsealers"><i class="fab fa-vimeo-v"></i></a></li>
				</ul>
				<span id="copy_info">© '.date("Y").'. KRETETEK INDUSTRIES INC. ALL RIGHTS RESERVED.</span>
				</div>
		</div>
	</div>
</div>
	<div class="footer_image_container"></div>
</section>
		
		</div> <!-- /page_content -->

	</div> <!-- /container -->

	<div id="mobile-check"></div>
	<iframe width="1" height="1" border="0" id="analytics-iframe" src="//ghostshield.com/analytics/locate"></iframe>';


		if (file_exists(__DIR__.'/../assets/script/jquery.js')) {
			$html .= '<script type="text/javascript">'.file_get_contents(__DIR__.'/../assets/script/jquery.js').'</script>';
		} else $html .= '<script async src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>';
	
		$html .= '
	<script async src="//assets.'.$this->host.'/script/fastclick.js"></script>
	<script async src="//assets.'.$this->host.'/script/dropzone.js?v='.$this->version.'"></script>
	<script async src="//assets.'.$this->host.'/script/ghostshield-nav.min.js?v='.$this->version.'"></script>
	<script async src="//assets.'.$this->host.'/script/ghostshield.js?v='.$this->version.'"></script>
	<script async src="//assets.'.$this->host.'/script/ghostshield-calculator.js?v='.$this->version.'"></script>
	<script async src="//assets.'.$this->host.'/script/photoswipe.min.js"></script>
	<script async src="//assets.'.$this->host.'/script/accordion-menu.js"></script>
	<script async src="//assets.'.$this->host.'/script/photoswipe-ui-default.min.js"></script>';

		foreach ($this->url_parts as $part) {
			if (file_exists(__DIR__.'/../assets/script/'.$part.'.js')) {
				$html .= '
	<script async src="//assets.'.$this->host.'/script/'.$part.'.js?v='.$this->version.'"></script>';
			}
		}

		if (isset($this->scripts)) foreach ($this->scripts as $script) {
			$html .= '
	<script async src="//assets.'.$this->host.'/script/'.$script.'?v='.$this->version.'"></script>';
		}

		/*if(!isset($_SESSION["notice_seen"]))$html.= '
	<style>#gs_notice_close{background:#222;}#gs_notice_close:hover{background:#666;}#gs_notice{bottom:-220px;}#gs_notice.present {bottom:100px;transition:bottom 2s ease-in-out;}</style>
	<div id="gs_notice" style="background:#ff611d;color:#fff;border:2px solid #fff;max-width:80vw;color:#fff;position:fixed;text-align:left; border-radius:2px;padding:32px 66px;left:50%;transform:translateX(-50%);z-index:2000;font-size:18px;transition:bottom 2s ease-in-out;transition-delay:2s;">
		<a id="gs_notice_close" style="position:absolute;top:-10px;left:-10px;width:28px;height:28px;border-radius:15px;border:2px solid white;drop-shadow:1px 1px 3px #000;font-size:30px;line-height:26px;text-align:center;cursor:pointer;font-weight:bold;">&times;</a>
		We are pleased to announce <a href="https://ghostshieldconcretesealers.com" style="color:white;font-weight:bold">GhostshieldConcreteSealers.com</a>, a new experimental site where you can buy direct. Drop in and let us know if you enjoy the experience!
	</div>
	<script> setTimeout(function(){$("#gs_notice").addClass("present");},1000); $("#gs_notice_close").click(function(){ $.ajax({ url: "/gs_notice.php" }).done(function() { $("#gs_notice").removeClass("present"); }); }); </script>';*/

		$html .= '
	<link href="//hello.myfonts.net/count/308c1e" rel="stylesheet" />
</html>';

		return $html;

	}

	public function pagination($pages, $words = true){
		$pages = ceil($pages);
		$page_url = preg_replace("/\&?page\=([0-9]+)/", "", $_SERVER["REQUEST_URI"]);

		if (!strpos($page_url, "?")>0) {
			$page_url .= "?";
		} elseif (strpos($page_url, "?") != strlen($page_url)-1) $page_url .= "&";

		$x = '<div class="pagination">';

		$delimiter = '<span class="pagination_delimiter"> &nbsp; </span>';

		if($this->number>1){
			$x.= '<a href="'.$page_url.(($this->number-1!=0)?'page='.($this->number-1):'').'" class="pagination_page first_child" id="pagination_back" title="Previous Page">';
		}else $x.= '<span class="pagination_page pagination_disabled first_child">';

		$x.= '&#8592;';

		if($this->number>1){
			$x.= '</a>'.$delimiter;
		}else $x.= '</span>'.$delimiter;

		if($pages>$this->number){
			$x.= '<a href="'.$page_url.'page='.($this->number+1).'" class="pagination_page" id="pagination_next" title="Next Page">';
		}else $x.= '<span class="pagination_page pagination_disabled">';

		$x.= '&#8594;';

		if($pages>$this->number){
			$x.= '</a>'.$delimiter;
		}else $x.= '</span>'.$delimiter;

		$pagstart=($this->number>5)?ceil($this->number-4):1;
		$pagend=($this->number<$pages-5)?ceil($this->number+4):$pages;

		if($pagstart!=1)$x.= '<a href="'.$page_url.'page=1" class="pagination_page">1</a>'.$delimiter;
		if($pagstart>2)$x.= '<span class="pagination_page">...</span>'.$delimiter;

		for($i=$pagstart;$i<$pagend+1;$i++){
			if($this->number!=$i){
				$x.= '<a href="'.$page_url.'page='.$i.'" class="'.($pagend+1>=$pages&&$i==$pagend?'last_child ':'').($this->number==$i+1?'pagination_before_on ':($this->number==$i-1?'pagination_after_on ':'')).'pagination_page">';
			}else $x.= '<span class="'.($pagend+1>=$pages&&$i==$pagend?'last_child ':'').'pagination_page pagination_on">'.($words?"Page".' ':'');

			$x.= $i;

			if($this->number!=$i){
				$x.= '</a>'.$delimiter;
			}else $x.= '</span>'.$delimiter;
		}
		if($pagend+1<$pages)$x.= '<span class="pagination_page">...</span>'.$delimiter.'<a href="'.$page_url.'page='.$pages.'" class="pagination_page last_child">'.$pages.'</a>';

		$x .= '</div>';

		return $x;
	}

	public function pagination_buttons($pages, $words=true){
		$x="";
		if($words){
			if($this->number>1){
				$x.= '<button type="submit" value="'.($this->number-1).'"" name="index">&#9668;</button>';
			}else{ $x.= '<button type="submit" value="'.($this->number-1).'"" name="index" disabled="disabled">&#9668;</button>'; }

			if($pages>$this->number){
				$x.= '<button type="submit" value="'.($this->number+1).'"" name="index">&#9658;</button>';
			}else{ $x.= '<button type="submit" value="'.($this->number+1).'"" name="index" disabled="disabled">&#9658;</button>'; }
		}

		$pagstart=($this->number>5)?$this->number-4:1;
		$pagend=($this->number<$pages-5)?$this->number+4:$pages;

		if($pagstart!=1)$x.='<input type="submit"'.($i+1==$this->number?' disabled="disabled"':'').' name="index" value="1" />';
		if($pagstart>2)$x.= '<div>...</div>';
		for($i=$pagstart;$i<$pagend+1;$i++){
			$x.='<input type="submit"'.($i==$this->number?' disabled="disabled"':'').' name="index" value="'.$i.'" />';
		}
		if($pagend+1<$pages)$x.= '<div>...</div><input type="submit" name="index" value="'.$pages.'" />';
		return $x;
	}
	
	public function getOS($simple = false) { 

		$os_platform  = "Unknown OS";

		if ($simple) {

			$os_array     = array(
				'/windows nt 10/i'      =>  'Windows',
				'/windows nt 6.3/i'     =>  'Windows',
				'/windows nt 6.2/i'     =>  'Windows',
				'/windows nt 6.1/i'     =>  'Windows',
				'/windows nt 6.0/i'     =>  'Windows',
				'/windows nt 5.2/i'     =>  'Windows',
				'/windows nt 5.1/i'     =>  'Windows',
				'/windows xp/i'         =>  'Windows',
				'/windows nt 5.0/i'     =>  'Windows',
				'/windows me/i'         =>  'Windows',
				'/win98/i'              =>  'Windows',
				'/win95/i'              =>  'Windows',
				'/win16/i'              =>  'Windows',
				'/macintosh|mac os x/i' =>  'Mac',
				'/mac_powerpc/i'        =>  'Mac',
				'/linux/i'              =>  'Linux',
				'/ubuntu/i'             =>  'Linux',
				'/iphone/i'             =>  'Mobile',
				'/ipod/i'               =>  'Mobile',
				'/ipad/i'               =>  'Tablet',
				'/android/i'            =>  'Mobile',
				'/blackberry/i'         =>  'Mobile',
				'/webos/i'              =>  'Mobile'
				);

			foreach ($os_array as $regex => $value)
				if (preg_match($regex, $this->user_agent))
					$os_platform = $value;
		
		} else {

			$os_array     = array(
				'/windows nt 10/i'      =>  'Windows 10',
				'/windows nt 6.3/i'     =>  'Windows 8.1',
				'/windows nt 6.2/i'     =>  'Windows 8',
				'/windows nt 6.1/i'     =>  'Windows 7',
				'/windows nt 6.0/i'     =>  'Windows Vista',
				'/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
				'/windows nt 5.1/i'     =>  'Windows XP',
				'/windows xp/i'         =>  'Windows XP',
				'/windows nt 5.0/i'     =>  'Windows 2000',
				'/windows me/i'         =>  'Windows ME',
				'/win98/i'              =>  'Windows 98',
				'/win95/i'              =>  'Windows 95',
				'/win16/i'              =>  'Windows 3.11',
				'/macintosh|mac os x/i' =>  'Mac OS X',
				'/mac_powerpc/i'        =>  'Mac OS 9',
				'/linux/i'              =>  'Linux',
				'/ubuntu/i'             =>  'Ubuntu',
				'/iphone/i'             =>  'iPhone',
				'/ipod/i'               =>  'iPod',
				'/ipad/i'               =>  'iPad',
				'/android/i'            =>  'Android',
				'/blackberry/i'         =>  'BlackBerry',
				'/webos/i'              =>  'Mobile'
				);

			foreach ($os_array as $regex => $value)
				if (preg_match($regex, $this-user_agent))
					$os_platform = $value;
		
		}

		return $os_platform;

	}

}

?>
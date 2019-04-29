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
	
		$this->version = date("y.m.d")."c".($this->dev?'.'.rand():'');

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

		$this->dev = strpos($_SERVER["HTTP_HOST"], "dev.") > -1 ? true : false;
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
		$this->description = "Ghostshield is a manufacturer of water, oil and road salt resistant concrete sealer, densifiers and coatings.";

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
	<link rel="publisher" href="https://plus.google.com/+Ghostshield"/>
	<link async href="//assets.'.$this->host.'/img/logo/ghostshield-g-orange.svg" rel="mask-icon" color="#000" />
	<style type="text/css" media="screen">'.
		file_get_contents(__DIR__.'/../assets/css/buttons-flat.css').
		file_get_contents(__DIR__.'/../assets/css/nav.css').
		file_get_contents(__DIR__.'/../assets/css/global.css').
		file_get_contents(__DIR__.'/../assets/css/graph.css').
		file_get_contents(__DIR__.'/../assets/css/dropzone.css').
		file_get_contents(__DIR__.'/../assets/css/photoswipe.css').
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
<body class="'.$this->body_class.(isset($_REQUEST["model"])?' product'.$_REQUEST["model"]:'').(isset($this->product["color"])?' color_'.strtolower($this->product["color"]):'').'"'.$this->body_attr.'>
	<a name="top" id="top_anchor"></a>

	<div id="container" class="'.$this->location['filename'].'">

		<div id="main_nav_container" class="nav_hover not_nav_chevrons not_nav_wide">
			<nav class="main_nav" id="main_nav">
				<div id="main_nav_open">
					<h3 id="main_nav_button"><span>Navigation</span></h3>
				</div>
				<div id="dropDownMenuBG">
					<div id="ddmbg_top" class="white"></div>
					<div id="ddmbg_mid" class="gray"></div>
					<div id="ddmbg_bot" class="black"></div>
				</div>
				<a id="mobile_home_link" href="//'.$this->host.'/"></a>
				<ul>
					<li class="no_submenu '.( $this->location['filename'] == "index" ? 'active' : '' ).'">
						<a href="//'.$this->host.'/" class="hex-container color2">
							<!--span class="hex1 button-home small">
								<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"  width="19px" height="23px" x="0px" y="0px" viewBox="0 0 17.5 21.5" style="enable-background:new 0 0 17.5 21.5;" xml:space="preserve">
									<path class="st0" d="M7.7,8.6l-1.4,4.1c-0.1,0.2,0,0.4,0.3,0.4h1.6c0.2,0,0.5-0.2,0.6-0.4l0.5-1.4H8.3L8.8,10h3.1l-1.5,4.4H1.6c0,0-0.1,0-0.1,0.1s0,0.1,0.1,0.1l3.1,0.7c0.9,1.5,2,2.6,3.3,3.5c0,0,4.7-1.7,6.4-10.5H8.3C8.1,8.2,7.8,8.4,7.7,8.6z"/>
									<path class="st0" d="M2.8,5.5c0,0-0.5,3.9,0.5,6.8c0.1,0.3,0.2,0.6,0.3,0.9l0,0l1.7-4.9C5.6,7.5,6.3,7,7,7h7.6 c0.1-0.8,0.3-2.1,0.3-3.6C15,3.4,9.8,1.8,2.8,5.5z"/>
								</svg>
							</span-->
							<span class="hex1 button-home">
								<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 262.9 47.5" style="enable-background:new 0 0 262.9 47.5;" xml:space="preserve">
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
							</span>
							<span class="button-text no-display">Home</span>
						</a>
					</li>
					<li id="nav_products" class="has_submenu">
						<div class="a hex-container color2" style="cursor:pointer">
							<span class="hex1 button-product">
								<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="19px" height="23px" x="0px" y="0px" viewBox="0 0 27.6 30.8" style="enable-background:new 0 0 27.6 30.8;" xml:space="preserve">
									<polygon class="st0" points="13.4,3.6 8.9,3.6 8.8,5.1 13.6,5.1 	"/>
									<path class="st0" d="M22.8,22.3L22.8,22.3c0.1-0.1,0.1-0.3,0-2.1s0-2.9,0-2.9c0.3-5.5-1.4-11.8-1.4-11.8c0.4-0.9,0-1.2,0-1.2c-1.1-0.9-1.8-0.8-2.6-0.7s-3.7,1.8-4.2,2c-0.5,0.2-0.9,0.2-0.9,0.2c-0.1-0.1-0.1-0.2-0.1-0.2l-4.9,0v0.1c0,1.1-0.3,1.5-0.3,1.5s-2.8,5.9-3.1,6.7s-0.3,1.7-0.3,1.7v0.6l0.1,0.1v0.5l-0.1,0.1v1.4l0,0.1v0.4l0,0.1v1.4l0.1,0.1v0.4l-0.1,0.1v1.5l0.1,0.1v0.4l-0.1,0.1v1.5l0.1,0.1l0,0.4l-0.1,0.1l0,1.4c0,1.5,1.4,1.6,1.4,1.6c8.8,0.1,12.8,0,14.5,0s1.9-1.5,1.9-1.5v-1.6l-0.1,0v-0.5l0.1,0v-1.5l-0.1,0V22.3zM19.7,15.1c-0.8,0-0.9-0.6-0.9-0.6c-0.8-3.5-1.6-5.4-1.7-6c-0.2-0.6,0.2-1,0.2-1l1.4-1c0.3-0.1,0.5,0.2,0.5,0.2c1.3,2.9,1.4,7,1.4,7.4S20.5,15,19.7,15.1z"/>
								</svg>
							</span>
							<span class="button-text">Products</span>
						</div>
						<div id="nav_products_menu" class="submenu nav-menu-products submenu1" data-num="1">
							<div id="scrollable1" class="scrollable1 menu_container">
							<div class="valign2">
								<div class="nav_list_main nav_menu_sub0">
									<ul>
										<li>
											<a href="//'.$this->host.'/product-finder/" class="orange">
												<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 51.9 58.1" style="enable-background:new 0 0 51.9 58.1;" xml:space="preserve">
													<polygon class="st0" points="49.6,42.6 49.6,15.4 26,1.8 2.5,15.4 2.5,42.6 26,56.2"/>
													<circle class="st1" cx="24.1" cy="26.1" r="13.2"/>
													<path class="st2" d="M21.2,40.2c-7.8-1.6-13-9.3-11.3-17.2s9.3-13,17.2-11.3S40,21,38.4,28.9C36.8,36.7,29.1,41.8,21.2,40.2zM26.6,13.6c-6.8-1.4-13.5,3-14.9,9.8s3,13.5,9.8,14.9s13.5-3,14.9-9.8S33.5,15,26.6,13.6z"/>
													<polygon class="st2" points="37.2,42.3 33.4,44.8 37.1,50.4 40.8,47.8"/>
													<path class="st2" d="M49,13.5L28,1.4c-1.3-0.7-2.8-0.7-4.1,0L3,13.4c-1.3,0.7-2,2.1-2,3.5V41c0,1.5,0.8,2.8,2,3.5l21,12.1c1.2,0.8,2.8,0.8,4,0l21-12c1.3-0.7,2-2.1,2-3.5V17C51,15.5,50.2,14.2,49,13.5zM48,40c0,1.2-0.6,2.2-1.6,2.8L27.6,53.6c-1,0.5-2.2,0.5-3.2,0L5.6,42.8C4.6,42.2,4,41.2,4,40V18.3c0-1.2,0.6-2.2,1.6-2.8L24.4,4.6c1-0.6,2.2-0.6,3.2,0l18.8,10.9c1,0.6,1.6,1.6,1.6,2.8V40z"/>
													<path class="st2" d="M21,16.8c0,0,8.7-3.5,12.7,6.3C33.7,23.1,31.3,18.2,21,16.8z"/>
													<path class="st2" d="M35.2,39.2l-1.1-1.6c-0.7-1-2.1-1.3-3.2-0.6c-1,0.7-1.3,2.1-0.6,3.2l1.1,1.6L35.2,39.2z"/>
													<rect x="32" y="41.2" transform="matrix(0.8345 -0.551 0.551 0.8345 -17.4817 25.8412)" class="st2" width="4.5" height="1.7"/>
												</svg>
												<em>Product Finder</em>
												<span>Find the right product for your project</span>
											</a>
										</li>
										<li>
											<a href="//'.$this->host.'/browse/" class="red">
												<svg version="1.1" id="Layer_3" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 51.9 58.1" style="enable-background:new 0 0 51.9 58.1;" xml:space="preserve">
													<polygon class="st0" points="49.6,42.7 49.6,15.4 26,1.8 2.5,15.4 2.5,42.7 26,56.3 "/>
													<path class="st1" d="M37.6,24.7H14.3c-2.1,0-3.8-1.7-3.8-3.8v0c0-2.1,1.7-3.8,3.8-3.8h23.3c2.1,0,3.8,1.7,3.8,3.8v0C41.4,23,39.7,24.7,37.6,24.7z"/>
													<path class="st1" d="M37.6,32.9H14.3c-2.1,0-3.8-1.7-3.8-3.8v0c0-2.1,1.7-3.8,3.8-3.8h23.3c2.1,0,3.8,1.7,3.8,3.8v0C41.4,31.2,39.7,32.9,37.6,32.9z"/>
													<path class="st1" d="M37.6,41.1H14.3c-2.1,0-3.8-1.7-3.8-3.8v0c0-2.1,1.7-3.8,3.8-3.8h23.3c2.1,0,3.8,1.7,3.8,3.8v0C41.4,39.4,39.7,41.1,37.6,41.1z"/>
													<path class="st2" d="M14.6,18.6c-1.3,0-2.3,1-2.3,2.3c0,1.3,1,2.3,2.3,2.3c1.3,0,2.3-1,2.3-2.3C16.9,19.6,15.8,18.6,14.6,18.6z"/>
													<path class="st2" d="M38.1,19.5H20.7c-0.8,0-1.5,0.7-1.5,1.5c0,0.8,0.7,1.5,1.5,1.5h17.4c0.8,0,1.5-0.7,1.5-1.5C39.5,20.2,38.9,19.5,38.1,19.5z"/>
													<path class="st2" d="M14.6,26.8c-1.3,0-2.3,1-2.3,2.3c0,1.3,1,2.3,2.3,2.3c1.3,0,2.3-1,2.3-2.3C16.9,27.8,15.8,26.8,14.6,26.8z"/>
													<path class="st2" d="M38.1,27.6H20.7c-0.8,0-1.5,0.7-1.5,1.5s0.7,1.5,1.5,1.5h17.4c0.8,0,1.5-0.7,1.5-1.5S38.9,27.6,38.1,27.6z"/>
													<path class="st2" d="M14.6,35c-1.3,0-2.3,1-2.3,2.3c0,1.3,1,2.3,2.3,2.3c1.3,0,2.3-1,2.3-2.3C16.9,36,15.8,35,14.6,35z"/>
													<path class="st2" d="M38.1,35.7H20.7c-0.8,0-1.5,0.7-1.5,1.5c0,0.8,0.7,1.5,1.5,1.5h17.4c0.8,0,1.5-0.7,1.5-1.5C39.5,36.3,38.9,35.7,38.1,35.7z"/>
													<path class="st2" d="M49,13.5L28,1.4c-1.3-0.7-2.8-0.7-4.1,0L3,13.4c-1.3,0.7-2,2.1-2,3.5V41c0,1.5,0.8,2.8,2,3.5l21,12.1c1.2,0.8,2.8,0.8,4,0l21-12c1.3-0.7,2-2.1,2-3.5V17C51,15.5,50.2,14.2,49,13.5zM48,40c0,1.2-0.6,2.2-1.6,2.8L27.6,53.6c-1,0.5-2.2,0.5-3.2,0L5.6,42.8C4.6,42.2,4,41.2,4,40V18.3c0-1.2,0.6-2.2,1.6-2.8L24.4,4.6c1-0.6,2.2-0.6,3.2,0l18.8,10.9c1,0.6,1.6,1.6,1.6,2.8V40z"/>
												</svg>

												<em>Browse All</em>
												<span>View the Pro Line by application or substrate</span>
											</a>
										</li>
										<li>
											<a href="//'.$this->host.'/gallery/" class="blue">
												<svg version="1.1" id="Layer_2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 51.9 58.1" style="enable-background:new 0 0 51.9 58.1;" xml:space="preserve">
													<polygon class="st0" points="49.6,43.1 49.6,15.9 26,2.2 2.5,15.9 2.5,43.1 26,56.7 "/>
													<rect x="12.1" y="18.5" transform="matrix(0.9977 -6.813821e-02 6.813821e-02 0.9977 -1.6819 1.6252)" class="st1" width="21.8" height="14"/>	
													<rect x="10.8" y="17.3" transform="matrix(0.9977 -6.815504e-02 6.815504e-02 0.9977 -1.6816 1.6128)" class="st1" width="24.1" height="16.2"/>
													<path class="st2" d="M10.3,35.5L9,17.2l26.2-1.8l1.3,18.4L10.3,35.5zM11.4,19.2l1,13.9l21.8-1.5l-1-13.9L11.4,19.2z"/>
													<circle class="st1" cx="27.2" cy="26.9" r="0.4"/>
													<rect x="17.5" y="24.6" transform="matrix(0.9977 6.743087e-02 -6.743087e-02 0.9977 2.2696 -1.9163)" class="st1" width="24.1" height="16.2"/>
													<path class="st2" d="M42,42.7L15.8,41L17,22.6l26.3,1.8L42,42.7z M18.2,38.9l21.8,1.5l0.9-13.9L19.1,25L18.2,38.9z"/>
													<path class="st2" d="M49,13.5L28,1.4c-1.3-0.7-2.8-0.7-4.1,0L3,13.4c-1.3,0.7-2,2.1-2,3.5V41c0,1.5,0.8,2.8,2,3.5l21,12.1c1.2,0.8,2.8,0.8,4,0l21-12c1.3-0.7,2-2.1,2-3.5V17C51,15.5,50.2,14.2,49,13.5zM48,40c0,1.2-0.6,2.2-1.6,2.8L27.6,53.6c-1,0.5-2.2,0.5-3.2,0L5.6,42.8C4.6,42.2,4,41.2,4,40V18.3c0-1.2,0.6-2.2,1.6-2.8L24.4,4.6c1-0.6,2.2-0.6,3.2,0l18.8,10.9c1,0.6,1.6,1.6,1.6,2.8V40z"/>
												</svg>

												<em>Customer Gallery</em>
												<span>Project images and videos uploaded by customers</span>
											</a>
										</li>
									</ul>
								</div>
								<div id="nav_product_list">
									<div class="subset nav_menu_sub1">
										<ul class="residential nav_list" class="">';
		foreach ($this->data("products")->attr as $m => $p) {
			if ($p["residential"] == "yes" && $p["availability"] != 0) $html .= '
											<li><a class="'.strtolower($p["color"]).'" href="//'.$this->host.'/product/'.preg_replace("/[^a-z0-9\-]+/", "-", strtolower($p["name"])).'/" data-model="'.$m.'">'./*preg_replace("/([0-9]+)/", "<span>\\1</span>", */$p["name"]/*)*/.' <small>'.str_replace("&", "<span>&amp;</span>", $p["simple"]).'</small></a></li>';
		}
		$html .= '
										</ul>
									</div>
									<div class="subset nav_menu_sub2">
										<ul class="commercial nav_list" class="">';
		foreach ($this->data("products")->attr as $m => $p) {
			if ($p["commercial"] == "yes" && $p["residential"] != "yes" && $p["availability"] != 0) $html .= '
											<li><a class="gray" href="//'.$this->host.'/product/'.preg_replace("/[^a-z0-9\-]+/", "-", strtolower($p["name"])).'/" data-model="'.$m.'">'./*preg_replace("/([0-9]+)/", "<span>\\1</span>", */$p["name"]/*)*/.' <small>'.str_replace("&", "<span>&amp;</span>", $p["simple"]).'</small></a></li>';
		}
//											<li class="commercial"><a class="choice" href="//'.$this->host.'/product/iso-tek-8501/" data-model="8501" data-finish="Natural">Iso-Tek <span>8501</span> <small>'.$this->data("products")->attr[8501]["simple"].'</small></a></li>
		$html .= '
										</ul>
										<div id="nav_product_error"></div>
									</div>
								</div>
							</div>

							</div>';

/*	foreach ($this->data("products")->attr as $product) {
		echo '
							<li><a href="product.php?model='.$product["model"].'">'.preg_replace("/([0-9]+)/","<span>\\1</span>",$product["name"]).'</a></li>';
	}*/

		$html .= '
						</div>
					</li>';
/*
		$html .= '
					<!--li id="nav_commercial" class="has_submenu">
						<div class="a hex-container color2">
							<span class="hex1">
								<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="19px" height="23px" x="0px" y="0px" viewBox="0 0 27.6 30.8" style="enable-background:new 0 0 27.6 30.8;" xml:space="preserve">
									<ellipse class="st0" cx="14" cy="5.4" rx="8.8" ry="2.2"/>
									<path class="st0" d="M5.2,22.5c0,1.2,3.9,3.2,8.8,3.2s8.8-2,8.8-3.2v-7c0,1.2-3.9,2.7-8.8,2.7s-8.8-1.5-8.8-2.7V22.5z"/>
									<path class="st0" d="M5.2,24.4c0,1.2,3.9,3.2,8.8,3.2s8.8-2,8.8-3.2v-1c0,1.2-3.9,3.2-8.8,3.2s-8.8-2-8.8-3.2V24.4z"/>
									<path class="st0" d="M5.2,6.4c0,1.2,3.9,2.2,8.8,2.2s8.8-1,8.8-2.2v-1c0,1.2-3.9,2.2-8.8,2.2s-8.8-1-8.8-2.2V6.4z"/>
									<path class="st0" d="M5.2,14.5c0,1.2,3.9,2.7,8.8,2.7s8.8-1.5,8.8-2.7V7.3c0,1.2-3.9,2.2-8.8,2.2s-8.8-1-8.8-2.2V14.5z"/>
								</svg>
							</span>
							<span class="button-text">Commercial</span>
						</div>
						<div class="submenu anchor-mid-right nav-menu-commercial">
							<div class="submenu-right-narrow no-padding">
								<div id="nav-menu-commercial-link2" class="nav-menu-commercial-link">
									<a href="//'.$this->host.'/commercial/"><!--span>Iso-Tek Commercial Line Sealers</span--></a>
								</div>
							</div>
							<div class="submenu-left-wide no-padding commercial-list">
								<div id="nav_commercial_product_list">
									<div class="subset nav_commercial_half_width">
										<h4>Info</h4>
										<ul class="commercial nav_list">
											<li><a class="choice" href="//'.$this->host.'/commercial">Iso-Tek Overview</a>
											<li><a class="choice" href="//'.$this->host.'/commercial/gallery">Gallery</a>
											<li><a class="choice" href="//'.$this->host.'/applicators">Applicators</a>
										</ul>
									</div>
									<div class="subset">
										<h4>Solutions</h4>
										<ul class="commercial nav_list">';

		foreach ($this->data("products")->attr as $m => $p) {
			if ($p["commercial"] == "yes") $html .= '
											<li><a class="choice" href="//'.$this->host.'/product/'.preg_replace("/[^a-z0-9\-]+/", "-", strtolower($p["name"])).'/" data-model="'.$m.'">'.preg_replace("/([0-9]+)/", "<span>\\1</span>", $p["name"]).' <small>'.$p["simple"].'</small></a></li>';
		}

		$html .= '
										</ul>
									</div>
								</div>
							</div>
						</div>
					</li -->';*/
		$html .= '
					<li id="nav_distributors" class="has_submenu'.( $this->location['filename'] == "interior" ? ' active' : '' ).'">
						<div class="a hex-container color2">
							<span class="hex1 button-distributors">
								<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="19px" height="23px" x="0px" y="0px" viewBox="0 0 140.4 156.7" style="enable-background:new 0 0 140.4 156.7;" xml:space="preserve">
									<path class="st0" d="M30.6,41.6c7.8-12.7,18.8-22,29.1-25.7C42.4,18.4,27.4,28,17.5,41.6H30.6z"/>
									<path class="st0" d="M19.7,77c0.3-11.3,3.5-21.9,8.4-31H14.6c-5.5,9.1-8.8,19.7-9.1,31H19.7z"/>
									<path class="st0" d="M30.7,115.8H17.4c9.9,13.8,25.2,23.5,42.7,26C49.8,138,38.7,128.6,30.7,115.8z"/>
									<path class="st0" d="M28.1,111.4c-4.8-8.9-8-19.1-8.4-30H5.5c0.4,10.9,3.7,21.2,9,30H28.1z"/>
									<path class="st0" d="M52.5,41.6c4.2-13.3,9.8-22.9,13.9-26.2c-10.6,1.5-23,11.5-31.5,26.2H52.5z"/>
									<path class="st0" d="M46.7,77c0.2-11.3,1.9-21.9,4.4-31H32.5c-4.5,9.1-7.5,19.7-7.8,31H46.7z"/>
									<path class="st0" d="M50.9,111.4c-2.3-8.9-3.9-19.1-4.2-30h-22c0.4,10.9,3.4,21.2,7.8,30H50.9z"/>
									<path class="st0" d="M52.1,115.8H34.9c8.5,14.6,20.9,24.6,31.1,26.3C61.6,138.7,56.1,129,52.1,115.8z"/>
									<path class="st0" d="M81.3,41.6c-4-16-9.7-26.4-12.3-26.4c-2.9,0-8.4,10.5-12.4,26.4H81.3z"/>
									<path class="st0" d="M85.9,77c-0.1-11.3-1.6-21.9-3.6-31H55.6c-2,9.1-3.3,19.7-3.5,31H85.9z"/>
									<path class="st0" d="M82.8,111.4c1.8-8.9,3-19.1,3.1-30H52.2c0.2,10.9,1.4,21.2,3.3,30H82.8z"/>
									<path class="st0" d="M56.4,115.8c3.8,16.1,9.3,26.6,12.6,26.6c3.7,0,9.1-10.5,12.8-26.6H56.4z"/>
									<path class="st0" d="M85.5,41.6h17.6c-8.7-14.7-21.2-24.8-31.5-26.2C75.5,18.7,81.2,28.3,85.5,41.6z"/>
									<path class="st0" d="M113.4,77c-0.3-11.3-3.4-21.9-7.9-31H86.8c2.6,9.1,4.4,19.7,4.6,31H113.4z"/>
									<path class="st0" d="M105.6,111.4c4.4-8.9,7.4-19.1,7.8-30h-22c-0.2,10.9-1.8,21.1-4,30H105.6z"/>
									<path class="st0" d="M86.2,115.8c-3.9,13.2-9.3,22.9-13.8,26.3c10.3-1.8,22.5-11.8,30.9-26.3H86.2z"/>
									<path class="st0" d="M107.3,41.6h13.3C110.6,27.9,95.4,18.3,78,15.8C88.2,19.5,99.3,28.9,107.3,41.6z"/>
									<path class="st0" d="M132.6,77c-0.3-11.3-3.6-21.9-9.1-31h-13.6c4.9,9.1,8.2,19.7,8.5,31H132.6z"/>
									<path class="st0" d="M123.6,111.4c5.3-8.9,8.6-19.1,9-30h-14.2c-0.4,10.9-3.6,21.1-8.3,30H123.6z"/>
									<path class="st0" d="M107.5,115.8c-8,12.8-19.1,22.2-29.3,25.9c17.4-2.5,32.6-12.2,42.5-25.9H107.5z"/>
								</svg>
							</span>
							<span class="button-text">Where to Buy</span>
						</div>
						<div id="nav_distributors_menu" class="submenu nav-menu-distributors submenu2" data-num="2">
							<div id="distributor-info" class="nav_menu_sub0">
								<div class="img">
									<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="127px" height="138px" x="0px" y="0px" viewBox="0 0 111 103.1" style="enable-background:new 0 0 111 103.1;" xml:space="preserve" preserveAspectRatio="xMidYMax">
										<path class="st0" d="M24.7,60.1L24.7,60.1c0.3,0,0.5-0.3,0.5-0.3l9.6-26.2c1-2.5,2.3-4.3,3.8-5.3c1.5-1,3.8-1.5,6.3-1.5h47.4c0.3,0,0.5-0.3,0.5-0.5l0.5-3.8c1.3-9.8,1.3-16.4,1.3-16.4c0-0.3-0.3-0.5-0.5-0.5c-5.3-1-11.1-1.5-16.6-1.5c-31,0-55.7,14.6-56,14.9c-0.3,0-0.3,0.3-0.3,0.5c-1.5,14.6-0.5,28.3,3.3,40.4C24.2,59.9,24.4,60.1,24.7,60.1"/>
										<path class="st0" d="M91,34.4c0,0-0.3-0.3-0.5-0.3H49.7c-0.5,0-1,0.3-1.3,0.8c0,0.3,0,0-0.8,2l-2,5.5c-0.8,2.3-1.8,4.5-2.5,7.1c-1,2.5-1.8,4.8-2.5,7.1c-0.8,2.3-1.8,4.8-2.8,7.6c0,0,0,0,0,0.3s0.3,0.8,0.5,0.8c0.3,0,0.3,0,0.3,0h13.1c0.3,0,0.8,0,1.3-0.8c0.3-0.5,0.5-1.3,0.8-2c0.3-0.8,0.5-1.3,0.8-1.8l1.5-4.3c0-0.3,0-0.3,0-0.5s-0.3-0.3-0.5-0.3h-5.8c0.8-2,2-3.5,3.3-4.3c1.5-1,3.5-1.5,6.1-1.5h13.4l-5.3,14.6c-0.8,2.5-2,4-3.8,5c-1.5,1-3.8,1.5-6.3,1.5H11.8c-0.3,0-0.5,0.3-0.5,0.5c0,0.3,0.3,0.5,0.5,0.5l18.9,4c9.1,16.1,20.9,22.7,20.9,23h0.3h0.3c24.5-13.4,34.3-42.1,38.6-64.3C91.3,34.7,91.3,34.4,91,34.4"/>
									</svg>
								</div>
								<h4 class="name">Ghostshield Pro Line</h4>
								<div class="phone"></div>
								<div class="text">is available at...</div>
							</div>
							<div class="no-padding distributor-list nav_menu_sub1">
								<!--h4>Online</h4-->
								<ul id="online-distributors">
									<li><a href="http://www.homedepot.com/s/Ghostshield" target="_blank" class="choice external" data-logo="//assets.ghostshield.com/img/logo/homedepot.svg" data-phone="800-466-3337" data-text="Ships Nationwide">Home Depot</a></li>
									<li><a href="http://concretesealersolutions.com" target="_blank" class="choice external" data-logo="//assets.ghostshield.com/img/logo/concretesealersupply.svg" data-phone="888-230-5569" data-text="Ships Nationwide">Concrete Sealer Supply</a></li>
									<li><a href="https://www.lowes.com/search?searchTerm=Ghostshield" target="_blank" class="choice external" data-logo="//assets.ghostshield.com/img/logo/lowes.svg" data-phone="877-465-6937" data-text="Ships Nationwide">Lowe\'s</a></li>
									<li><a href="http://www.garageflooringllc.com/?s=Ghostshield&post_type=product" target="_blank" class="choice external" data-logo="//assets.ghostshield.com/img/logo/garage-flooring-llc.svg" data-phone="800-956-4301" data-text="Ships Nationwide">Garage Flooring, LLC</a></li>
									<li><a href="https://www.redimixcolors.com" target="_blank" class="choice external" data-logo="//assets.ghostshield.com/img/logo/redimix-colors.svg" data-phone="508-823-0771" data-text="Ships Nationwide">Redi-Mix Colors</a></li>
									<li><a href="http://www.diyhomecenter.com/search?keywords=Ghostshield" target="_blank" class="choice external" data-logo="//assets.ghostshield.com/img/logo/diyhomecenter.svg" data-phone="888-349-4660" data-text="Ships Nationwide">DIY Home Center</a></li>
								</ul>
								<h4>Canada</h4>
								<ul id="regional-distributors">
									<li><a href="http://www.daycampbell.com" target="_blank" class="choice external" data-logo="//images.contentful.com/muyees5bu8n0/3p5LX8JZJSgGoSGUeWuOw2/72f2d0b81a6964afef9f1e17f105e127/Day-Campbell-Logo.png" data-phone="800-263-6787" data-text="Ships Nationwide">Day &amp; Campbell</a></li>
								</ul>
							</div>
							<div class="all-link nav_menu_sub2">
								<a href="//'.$this->host.'/distributors/" class="choice">View All Distributors</a>
								<a href="//'.$this->host.'/applicators/" class="choice">View Applicators</a>
							</div>
						</div>
					</li>
					<li id="nav_help" class="has_submenu">
						<div class="a hex-container color2">
							<span class="hex1 button-help">
								<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="19px" height="23px" x="0px" y="0px" viewBox="0 0 27.6 30.8" style="enable-background:new 0 0 27.6 30.8;" xml:space="preserve">
									<path class="st0" d="M26.7,14.4c0-5.5-5.8-10-12.8-10S1.2,8.8,1.2,14.4c0,5.3,5.3,9.7,12,10H13c0,0,1.8-0.2,0.6,2.4s9.3-5.3,9.3-5.3l0,0C25.2,19.7,26.7,17.1,26.7,14.4zM12.3,19.5c0-1.9,2.8-1.9,2.8,0C15,21.4,12.3,21.4,12.3,19.5zM15.5,15.6c-0.4,0.3-0.7,0.8-0.7,1.7v0.1h-2.3v-0.1c0-2.4,1-3,1.9-3.4c0.9-0.4,2-0.7,2-1.7c0-1.3-1.2-1.7-2.3-1.7s-2.3,0.7-2.3,1.8H9.5 c0-2.6,2.2-3.8,4.7-3.8s4.8,1.1,4.7,3.7C18.8,14.7,16.5,14.9,15.5,15.6z"/>
								</svg>
							</span>
							<span class="button-text">Resources</span>
						</div>
						<div id="nav_help_menu" class="submenu nav-menu-help submenu3" data-num="3">
							<div class="nav_list_main nav_menu_sub0">
								<ul>
									<li>
										<a href="//'.$this->host.'/articles/" class="orange">
											<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 51.9 58.1" style="enable-background:new 0 0 51.9 58.1;" xml:space="preserve">
												<polygon class="st0" points="49.6,42.7 49.6,15.5 26,1.8 2.4,15.5 2.4,42.7 26,56.3"/>
												<path class="st2" d="M49,13.5L28,1.4c-1.3-0.7-2.8-0.7-4.1,0L3,13.4c-1.3,0.7-2,2.1-2,3.5V41c0,1.5,0.8,2.8,2,3.5l21,12.1c1.2,0.8,2.8,0.8,4,0l21-12c1.3-0.7,2-2.1,2-3.5V17C51,15.5,50.2,14.2,49,13.5zM48,40c0,1.2-0.6,2.2-1.6,2.8L27.6,53.6c-1,0.5-2.2,0.5-3.2,0L5.6,42.8C4.6,42.2,4,41.2,4,40V18.3c0-1.2,0.6-2.2,1.6-2.8L24.4,4.6c1-0.6,2.2-0.6,3.2,0l18.8,10.9c1,0.6,1.6,1.6,1.6,2.8V40z"/>
												<circle class="st1" cx="26" cy="29.1" r="14"/>
												<path class="st2" d="M23.8,36.6c0-0.2,0-0.3,0-0.5c0-0.2,0-0.3,0-0.5c0-1.2,0.2-2.3,0.5-3.2c0.3-0.9,0.7-1.8,1.2-2.6c0.5-0.8,1-1.5,1.6-2.2c0.6-0.7,1.1-1.4,1.6-2.1c0.5-0.7,0.9-1.4,1.2-2.2s0.5-1.7,0.5-2.6c0-1.4-0.4-2.6-1.3-3.5s-2.1-1.4-3.8-1.4c-1,0-1.9,0.1-2.6,0.2c-0.7,0.2-1.3,0.3-1.7,0.6s-0.8,0.5-1.1,0.8c-0.3,0.3-0.5,0.6-0.7,0.9c0.7,0,1.4,0.3,1.9,0.9c0.5,0.5,0.8,1.2,0.8,2.1c0,0.9-0.3,1.7-0.8,2.4c-0.5,0.7-1.4,1-2.6,1c-1,0-1.8-0.3-2.4-1c-0.6-0.7-0.9-1.6-0.9-2.6c0-0.9,0.2-1.8,0.5-2.7c0.4-1,1-1.8,1.8-2.6c0.9-0.8,2-1.5,3.5-2c1.4-0.5,3.3-0.8,5.5-0.8c1.5,0,3,0.1,4.3,0.3c1.3,0.2,2.5,0.6,3.5,1.2c1,0.6,1.8,1.4,2.4,2.4c0.6,1,0.9,2.3,0.9,3.8c0,1.4-0.2,2.6-0.7,3.5c-0.5,1-1.1,1.8-1.9,2.6c-0.7,0.8-1.6,1.4-2.4,2.1c-0.9,0.6-1.7,1.3-2.5,2c-0.8,0.7-1.5,1.5-2,2.5c-0.6,0.9-0.9,2-1,3.3H23.8zM28.6,42.8c0,0.9-0.3,1.6-0.9,2.2c-0.6,0.6-1.3,0.9-2.2,0.9c-0.8,0-1.6-0.3-2.2-0.9c-0.6-0.6-0.9-1.3-0.9-2.2c0-0.8,0.3-1.6,0.9-2.2c0.6-0.6,1.3-0.9,2.2-0.9c0.9,0,1.6,0.3,2.2,0.9C28.3,41.2,28.6,42,28.6,42.8z"/>
											</svg>
											<em>Articles</em>
											<span>Helpful information about all things concrete</span>
										</a>
									</li>
									<li>
										<a href="//sds.ghostshield.com/" class="blue">
											<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 51.9 58.1" style="enable-background:new 0 0 51.9 58.1;" xml:space="preserve">
												<polygon class="st0" points="49.6,42.6 49.6,15.4 26,1.8 2.4,15.4 2.4,42.6 26,56.2 "/>
												<path class="st2" d="M49,13.5L28,1.4c-1.3-0.7-2.8-0.7-4.1,0L3,13.4c-1.3,0.7-2,2.1-2,3.5V41c0,1.5,0.8,2.8,2,3.5l21,12.1c1.2,0.8,2.8,0.8,4,0l21-12c1.3-0.7,2-2.1,2-3.5V17C51,15.5,50.2,14.2,49,13.5zM48,40c0,1.2-0.6,2.2-1.6,2.8L27.6,53.6c-1,0.5-2.2,0.5-3.2,0L5.6,42.8C4.6,42.2,4,41.2,4,40V18.3c0-1.2,0.6-2.2,1.6-2.8L24.4,4.6c1-0.6,2.2-0.6,3.2,0l18.8,10.9c1,0.6,1.6,1.6,1.6,2.8V40z"/>
												<polygon class="st1" points="41.3,39.5 21.8,39.5 21.8,12.4 33.2,12.4 41.3,20.5"/>
												<path class="st2" d="M33.7,11.4h-13v29.1h21.7V20.1L33.7,11.4z M34.4,15.2l4.3,4.3h-4.3V15.2z M22.9,38.3V13.6h9.2v8.1h8.1v16.6H22.9z"/>
												<polygon class="st1" points="31.7,43.3 12.2,43.3 12.2,16.3 23.7,16.3 31.7,24.3"/>
												<path class="st2" d="M24.1,15.3h-13v29.1h21.7V24.1L24.1,15.3z M24.8,19.1l4.3,4.3h-4.3V19.1z M13.4,42.2V17.6h9.2v8.1h8.1v16.6H13.4z"/>
											</svg>
											<em>Technical Documents</em>
											<span>Safety Data Sheets, brochures, and more</span>
										</a>
									</li>
									<li>
										<a href="//'.$this->host.'/videos" class="violet">
											<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 51.9 58.1" style="enable-background:new 0 0 51.9 58.1;" xml:space="preserve">
												<polygon class="st0" points="49.6,42.7 49.6,15.4 26,1.8 2.5,15.4 2.5,42.7 26,56.3"/>
												<path class="st1" d="M49,13.5L28,1.4c-1.3-0.7-2.8-0.7-4.1,0L3,13.4c-1.3,0.7-2,2.1-2,3.5V41c0,1.5,0.8,2.8,2,3.5l21,12.1c1.2,0.8,2.8,0.8,4,0l21-12c1.3-0.7,2-2.1,2-3.5V17C51,15.5,50.2,14.2,49,13.5zM48,40c0,1.2-0.6,2.2-1.6,2.8L27.6,53.6c-1,0.5-2.2,0.5-3.2,0L5.6,42.8C4.6,42.2,4,41.2,4,40V18.3c0-1.2,0.6-2.2,1.6-2.8L24.4,4.6c1-0.6,2.2-0.6,3.2,0l18.8,10.9c1,0.6,1.6,1.6,1.6,2.8V40z"/>
												<path class="st2" d="M38.8,41.6H13.2c-2.9,0-5.3-2.4-5.3-5.3V22.1c0-2.9,2.4-5.3,5.3-5.3h25.6c2.9,0,5.3,2.4,5.3,5.3v14.1C44.2,39.2,41.8,41.6,38.8,41.6z"/>
												<path class="st1" d="M20.4,36.1l12.9-6.3c0.5-0.3,0.5-1,0-1.2l-12.9-6.3c-0.5-0.2-1,0.1-1,0.6v12.5C19.4,36,20,36.3,20.4,36.1z"/>
											</svg>
											<em>Instructional Videos</em>
											<span>"How-To" videos demonstrate common applications</span>
										</a>
									</li>
								<!--/ul>
							</div>
							<div class="nav_list_main nav_menu_sub2">
								<ul-->
									<li>
										<a href="//'.$this->host.'/commercial/" class="black">
											<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 51.9 58.1" style="enable-background:new 0 0 51.9 58.1;" xml:space="preserve">
												<path class="st0" d="M31.4,35.2c0,0-0.1,0-0.1,0c-0.1-0.1-0.2-0.2-0.2-0.4l5.6-14.7c0.1-0.1,0.2-0.2,0.4-0.2c0.1,0.1,0.2,0.2,0.2,0.4L31.7,35C31.6,35.1,31.5,35.2,31.4,35.2z"/>
												<path class="st0" d="M8.6,35.2c0,0-0.1,0-0.1,0c-0.1-0.1-0.2-0.2-0.2-0.4l5.6-14.7c0.1-0.1,0.2-0.2,0.4-0.2c0.1,0.1,0.2,0.2,0.2,0.4L8.9,35C8.8,35.1,8.7,35.2,8.6,35.2z"/>
												<path class="st0" d="M20,35.2c-0.1,0-0.2-0.1-0.3-0.2L15,23.5l2.4,11.2c0,0.2-0.1,0.3-0.2,0.3c-0.2,0-0.3-0.1-0.3-0.2l-3.1-14.5c0-0.1,0.1-0.3,0.2-0.3s0.3,0,0.3,0.2l5.9,14.6c0.1,0.1,0,0.3-0.2,0.4C20,35.2,20,35.2,20,35.2z"/>
												<path class="st0" d="M11.4,35.2C11.4,35.2,11.4,35.2,11.4,35.2c-0.2,0-0.3-0.2-0.3-0.3l2.9-14.7c0-0.2,0.2-0.3,0.3-0.2c0.2,0,0.3,0.2,0.2,0.3L11.7,35C11.7,35.1,11.5,35.2,11.4,35.2z"/>
												<path class="st1" d="M14.2,35.9c-0.3,0-0.5-0.2-0.5-0.5V19.7c0-0.3,0.2-0.5,0.5-0.5s0.5,0.2,0.5,0.5v15.7C14.7,35.7,14.5,35.9,14.2,35.9z"/>
												<path class="st0" d="M42.6,35.2c-0.1,0-0.2-0.1-0.3-0.2l-4.5-10.6l2.3,10.3c0,0.2-0.1,0.3-0.2,0.3c-0.2,0-0.3-0.1-0.3-0.2l-2.9-13.6c0-0.1,0.1-0.3,0.2-0.3c0.1,0,0.3,0,0.3,0.2l5.7,13.7c0.1,0.1,0,0.3-0.2,0.4C42.6,35.2,42.6,35.2,42.6,35.2z"/>
												<path class="st0" d="M34,35.2C34,35.2,34,35.2,34,35.2c-0.2,0-0.3-0.2-0.3-0.3l2.9-14.7c0-0.2,0.2-0.3,0.3-0.2c0.2,0,0.3,0.2,0.2,0.3L34.3,35C34.3,35.1,34.2,35.2,34,35.2z"/>
												<path class="st1" d="M36.9,35.9c-0.3,0-0.5-0.2-0.5-0.5V19.7c0-0.3,0.2-0.5,0.5-0.5s0.5,0.2,0.5,0.5v15.7C37.4,35.7,37.2,35.9,36.9,35.9z"/>
												<path class="st0" d="M3.8,35.2c-0.1,0-0.1,0-0.2-0.1c-0.1-0.1-0.1-0.3,0-0.4l7.7-9.1c0.1-0.1,0.3-0.1,0.4,0c0.1,0.1,0.1,0.3,0,0.4L4,35.1C4,35.2,3.9,35.2,3.8,35.2z"/>
												<path class="st0" d="M26,35.7c-0.1,0-0.1,0-0.2,0L25,35.2l-8.3-8.8c-0.1-0.1-0.1-0.3,0-0.4c0.1-0.1,0.3-0.1,0.4,0l8.2,8.7l0.5,0.3l8.5-9.7c0.1-0.1,0.3-0.1,0.4,0c0.1,0.1,0.1,0.3,0,0.4l-8.7,9.9C26.2,35.7,26.1,35.7,26,35.7z"/>
												<path class="st0" d="M47.1,35.2c-0.1,0-0.2,0-0.2-0.1l-7.7-8.9c-0.1-0.1-0.1-0.3,0-0.5c0.1-0.1,0.3-0.1,0.4,0l7.7,8.9c0.1,0.1,0.1,0.3,0,0.5C47.2,35.2,47.1,35.2,47.1,35.2z"/>
												<rect x="3.6" y="38.3" class="st1" width="44.8" height="0.6"/>
												<path class="st1" d="M48.5,30.5c-1.9,0-9.6-0.6-11.6-9.5c-0.8,2.9-3.6,9.4-11.3,9.4c-7.9,0-10.6-6.8-11.4-9.6c-3,10.2-11.9,9.6-12,9.6c-0.2,0-0.3-0.2-0.3-0.3c0-0.2,0.2-0.3,0.3-0.3c0,0,2.3,0.2,4.9-1c3.4-1.6,5.7-4.7,6.8-9.3c0-0.1,0.1-0.2,0.3-0.2c0.1,0,0.3,0.1,0.3,0.2c0.1,0.4,1.7,10.3,11.1,10.3s11-9.8,11.1-10.3c0-0.1,0.1-0.2,0.3-0.2l0,0c0.1,0,0.3,0.1,0.3,0.2c1.7,11,11.6,10.3,11.7,10.3c0.2,0,0.3,0.1,0.3,0.3c0,0.2-0.1,0.3-0.3,0.3C49,30.5,48.8,30.5,48.5,30.5z"/>
												<rect x="3.1" y="34.4" class="st1" width="45.9" height="2"/>
												<rect x="35.5" y="38.9" class="st1" width="3.1" height="11.2"/>
												<rect x="12.5" y="38.9" class="st1" width="3.1" height="11.2"/>
												<path class="st2" d="M4.6,43.4l21.2,12.2l22.5-12.2c0,0-3.2-0.4-4.9-1.2c0,0-5.6,3-13,0.2c0,0-5.2,2.9-11.2,0.1c0,0-6.1,2.6-11.4-0.1C7.8,42.4,7.2,43.2,4.6,43.4z"/>
												<path class="st1" d="M49,13.5L28,1.4c-1.3-0.7-2.8-0.7-4.1,0L3,13.4c-1.3,0.7-2,2.1-2,3.5V41c0,1.5,0.8,2.8,2,3.5l21,12.1c1.2,0.8,2.8,0.8,4,0l21-12c1.3-0.7,2-2.1,2-3.5V17C51,15.5,50.2,14.2,49,13.5z M48,40c0,1.2-0.6,2.2-1.6,2.8L27.6,53.6c-1,0.5-2.2,0.5-3.2,0L5.6,42.8C4.6,42.2,4,41.2,4,40V18.3c0-1.2,0.6-2.2,1.6-2.8L24.4,4.6c1-0.6,2.2-0.6,3.2,0l18.8,10.9c1,0.6,1.6,1.6,1.6,2.8V40z"/>
											</svg>
											<em>Commercial Portfolio</em>
											<span>Iso-Tek specialty sealers</span>
										</a>
									</li>
								</ul>
							</div>
							<div class="nav_list_main nav_menu_sub1">
								<div class="nav_search_container">
									<script>
										(function() {
											var cx = "000281191848178557010:0kgtdtkdffg";
											var gcse = document.createElement("script");
											gcse.type = "text/javascript";
											gcse.async = true;
											gcse.src = "https://cse.google.com/cse.js?cx=" + cx;
											var s = document.getElementsByTagName("script")[0];
											s.parentNode.insertBefore(gcse, s);
										})();
									</script>
									<gcse:searchbox-only></gcse:searchbox-only>
								</div>
							</div>
							<!--ul>
								<li><a href="//'.$this->host.'/downloads/" class="choice">Brochure Downloads</a></li>
								<li class="break"><a href="//'.$this->host.'/gallery/" class="choice">Project Gallery</a></li>
								<li><a href="//'.$this->host.'/commercial/gallery" class="choice">Commercial Gallery</a></li>
								<li class="break"><a href="//'.$this->host.'/product-finder" class="choice">Product Finder</a></li>
								<li><a href="//'.$this->host.'/about/" class="choice">About KreteTek</a></li>
							</ul-->
						</div>
					</li>
					<!--li id="nav_search" class="no_has_submenu">
						<script>
							(function() {
								var cx = "000281191848178557010:0kgtdtkdffg";
								var gcse = document.createElement("script");
								gcse.type = "text/javascript";
								gcse.async = true;
    gcse.src = "https://cse.google.com/cse.js?cx=" + cx;
								var s = document.getElementsByTagName("script")[0];
								s.parentNode.insertBefore(gcse, s);
							})();
						</script>
						<gcse:searchbox-only></gcse:searchbox-only>';
/*
						<!--a href="#" class="hex-container color2">
					
							<span class="hex1 button-help">
								<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="19px" height="23px" x="0px" y="0px" viewBox="0 0 27.6 30.8" style="enable-background:new 0 0 27.6 30.8;" xml:space="preserve">
									<path class="st0" d="M16.1,6.7C13.6,3.5,9.1,3,6,5.4c-3.1,2.4-3.7,6.9-1.2,10c2.4,3.1,6.9,3.7,10,1.2C17.9,14.3,18.5,9.8,16.1,6.7z M13.7,15.2c-2.3,1.8-5.6,1.4-7.4-0.9C4.5,12,4.9,8.7,7.2,6.9c2.3-1.8,5.6-1.4,7.4,0.9C16.4,10.1,16,13.4,13.7,15.2z"/>
									<path class="st0" d="M22.9,25.4l-1.6,1.2c-0.4,0.3-0.9,0.2-1.2-0.2l-5.4-6.9c-0.3-0.4-0.2-0.9,0.2-1.2l1.6-1.2 c0.4-0.3,0.9-0.2,1.2,0.2l5.4,6.9C23.4,24.6,23.3,25.1,22.9,25.4z"/>
									<rect class="st0" x="15" y="15.8" transform="matrix(0.7885 -0.6151 0.6151 0.7885 -7.8027 13.6939)" class="st0" width="1.9" height="4.8"/>
								</svg>
							</span>
							<!--span class="button-text no-display">Search</spa-->
						</a-->
						<!--div class="submenu anchor-right nav-menu-search">
	<gcse:searchresults></gcse:searchresults>
							<div id="scrollable2" class="scrollable2">
								<div class="search" id="search">
									<script>
										(function() {
											var cx = "000281191848178557010:0kgtdtkdffg";
											var gcse = document.createElement("script");
											gcse.type = "text/javascript";
											gcse.async = true;
											gcse.src = "https://cse.google.com/cse.js?cx=" + cx;
											var s = document.getElementsByTagName("script")[0];
											s.parentNode.insertBefore(gcse, s);
										})();
									</script>
									<gcse:search></gcse:search>
								</div>
							</div>
						</div-->*/
		$html .= '
					</li-->
					<!--li id="nav_contact" class="no_has_submenu">
						<a href="//'.$this->host.'/contact" class="hex-container color2">
							<span class="hex1 button-help">
								<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="19px" height="23px" x="0px" y="0px" viewBox="0 0 27.6 30.8" style="enable-background:new 0 0 27.6 30.8;" xml:space="preserve">
									<path class="st0" d="M26.7,14.4c0-5.5-5.8-10-12.8-10S1.2,8.8,1.2,14.4c0,5.3,5.3,9.7,12,10H13c0,0,1.8-0.2,0.6,2.4s9.3-5.3,9.3-5.3 l0,0C25.2,19.7,26.7,17.1,26.7,14.4z M12.3,19.5c0-1.9,2.8-1.9,2.8,0C15,21.4,12.3,21.4,12.3,19.5zM15.5,15.6c-0.4,0.3-0.7,0.8-0.7,1.7v0.1h-2.3v-0.1c0-2.4,1-3,1.9-3.4c0.9-0.4,2-0.7,2-1.7c0-1.3-1.2-1.7-2.3-1.7s-2.3,0.7-2.3,1.8H9.5 c0-2.6,2.2-3.8,4.7-3.8s4.8,1.1,4.7,3.7C18.8,14.7,16.5,14.9,15.5,15.6z"/>
								</svg>
							</span>
							<span class="button-text">Contact</span>
						</a>
					</li-->
					<li id="phone" class="has_submenu">
						<a href="tel:855-573-8383" class="hex-container color2">
							<span class="hex1 button-help">
								<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 38.2 38" style="enable-background:new 0 0 38.2 38;" xml:space="preserve">
									 <path class="st0" d="M34.9,31.3c-0.4,1-1.4,1.8-2.9,2.5c-1.5,0.7-2.9,1.1-4.1,1.1c-0.3,0-0.7,0-1.1-0.1c-0.4-0.1-0.7-0.1-0.9-0.2c-0.3-0.1-0.6-0.1-1-0.3c-0.4-0.1-0.7-0.2-0.9-0.3c-0.2-0.1-0.5-0.2-1-0.4c-0.5-0.2-0.8-0.3-0.9-0.3c-3.4-1.2-6.7-3.5-10-6.7c-3.2-3.2-5.5-6.6-6.7-10c0-0.1-0.2-0.4-0.3-0.9c-0.2-0.5-0.3-0.8-0.4-1s-0.2-0.5-0.3-0.9c-0.1-0.4-0.2-0.8-0.3-1c-0.1-0.3-0.1-0.6-0.2-0.9c-0.1-0.4-0.1-0.7-0.1-1.1c0-1.2,0.4-2.5,1.1-4.1s1.5-2.5,2.5-2.9C8.4,3.2,9.4,3,10.4,3c0.2,0,0.4,0,0.5,0.1c0.1,0,0.3,0.2,0.5,0.6c0.2,0.3,0.5,0.8,0.8,1.3c0.3,0.5,0.6,1,0.8,1.5c0.3,0.5,0.5,0.9,0.8,1.4c0.2,0.5,0.4,0.8,0.5,0.9c0.1,0.1,0.2,0.3,0.4,0.6c0.2,0.3,0.4,0.5,0.5,0.8c0.1,0.2,0.2,0.4,0.2,0.7c0,0.3-0.2,0.7-0.6,1.1c-0.4,0.4-0.9,0.9-1.4,1.2c-0.5,0.4-1,0.8-1.4,1.2c-0.4,0.4-0.6,0.8-0.6,1c0,0.1,0,0.3,0.1,0.5c0.1,0.2,0.1,0.4,0.2,0.5s0.2,0.3,0.3,0.5c0.1,0.2,0.2,0.4,0.3,0.4c1.1,2.1,2.5,3.8,3.9,5.3c1.5,1.5,3.3,2.8,5.3,3.9c0.1,0,0.2,0.1,0.4,0.3c0.2,0.1,0.4,0.2,0.5,0.3c0.1,0.1,0.3,0.1,0.5,0.2c0.2,0.1,0.4,0.1,0.5,0.1c0.3,0,0.8-0.3,1.4-1c0.6-0.7,1.2-1.4,1.8-2c0.6-0.7,1.1-1,1.5-1c0.2,0,0.4,0.1,0.7,0.2c0.2,0.1,0.5,0.3,0.8,0.5c0.3,0.2,0.5,0.3,0.6,0.4l1.6,0.9c1.1,0.6,2,1.1,2.8,1.5c0.7,0.4,1.1,0.8,1.2,0.9c0,0.1,0.1,0.3,0.1,0.5C35.6,29.2,35.4,30.2,34.9,31.3z"/>
								</svg>
							</span>
							<span class="button-text">(855) 573-8383</span>
						</a>
						<div id="phone_menu" class="submenu nav-menu-contact submenu4" data-num="4">
							<form method="POST" class="contact">
								<div class="submenu-contact-form nav_menu_sub0">
									<h4>Expert Technical Advice</h4>
									<label for="contact_email">Email</label>
									<input type="text" class="half_width contact_email" placeholder="Email Address" name="contact_email" />
									<label for="contact_name">Name</label>
									<input type="text" class="half_width contact_name" placeholder="Name" name="contact_name" />
									<label for="contact_comment">Comments</label>
									<textarea name="contact_comment" class="contact_comment" placeholder="'.$GLOBALS["comment_placeholder"].'"></textarea>
								</div>
								<div class="submenu-contact-form nav_menu_sub1">
									<input type="submit" value="'.$GLOBALS["contact_button_text"].'" class="inverse contact_submit" />
								</div>
							</form>
						</div>
					</li>
				</ul>
			</nav>
		</div>

		<div class="page_content">
<gcse:searchresults></gcse:searchresults>

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

		/* old home icon
		<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="19px" height="23px" x="0px" y="0px" viewBox="0 0 27.6 30.8" style="enable-background:new 0 0 27.6 30.8;" xml:space="preserve"><polygon class="st0" points="26,15.6 13.9,3.8 1.8,15.6 5.2,15.6 5.2,27.3 11.7,27.3 11.7,21.3 16.1,21.3 16.1,27.3 22.7,27.3 22.7,15.6 "/></svg>
		*/

		$html .= '
		</header>'."\n";

		if ($type == "index") {
			$html .= '
			<div id="tagline">
				<h3>Industrial-grade concrete protection. Impressive results.</h3>';
			if (isset($this->data("pages")->attr["/home/section3"])) $html .= '
				<h1>'.str_replace('<em>concrete sealer</em>', '<em class="plural">concrete sealer</em>', $this->data("pages")->attr["/home/welcome"]["title"]).'</h1>';
			$html .= '
				<div id="calltoaction"><a href="#product_finder" class="btn hex-button tall">Find Your Product</a></div>';
			if (isset($this->data("pages")->attr["/home/section3"])) $html .= '<div class="blurb">'.preg_replace("/(\<\/?)em\>/", "$1span>", $this->data("pages")->attr["/home/welcome"]["content"]).'</div>';
			$html .= '
				<a href="/browse/" class="arrow link">
					<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 17.3 25.7" style="enable-background:new 0 0 17.3 25.7;" xml:space="preserve">
						<polygon class="st0" points="0,24.6 16,12.9 0,1.1"/>
					</svg>
					Browse the Many Uses
				</a>
			</div>
			'.$this->banner;
		}
		return $html;

	}

	function footer() {

		$html = '
			<section id="contact_footer" class="black">
				<div class="column full">
					<!--h2 class="centered">Contact Us</h2>
					<p class="centered">'.$GLOBALS["contact_paragraph"].'</p-->
					<div class="column_interior">
						<div class="contact_left">
							<form method="POST" class="contact">
								<label for="fcontact_email">Email</label>
								<input type="text" class="half_width contact_email" placeholder="Email Address" name="contact_email" id="fcontact_email" />
								<label for="fcontact_name">Name</label>
								<input type="text" class="half_width contact_name" placeholder="Name" name="contact_name" id="fcontact_name" />
								<label for="fcontact_comment">Comments</label>
								<textarea name="contact_comment" class="contact_comment" placeholder="'.$GLOBALS["comment_placeholder"].'" id="fcontact_comment"></textarea>
								<input type="submit" value="'.$GLOBALS["contact_button_text"].'" class="contact_submit" />
							</form>
						</div>
						<div class="contact_right" itemscope itemprop="organization" itemtype="http://schema.org/Organization">
							<h4 itemprop="name">KreteTek Industries</h4>
							<address itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
								<span itemprop="streetAddress">'.$GLOBALS["address_street"].'</span><br />
								<span itemprop="addressLocality">'.$GLOBALS["address_city"].', '.$GLOBALS["address_state"].'</span> <span itemprop="postalCode">'.$GLOBALS["address_zip"].'</span>
							</address>
							<div class="phone">
								<a href="tel:'.preg_replace("/[^0-9]/", "", $GLOBALS["phone"]).'" itemprop="telephone">'.$GLOBALS["phone"].'</a>
							</div>
							<meta itemprop="url" content="https://ghostshield.com/" />
							<meta itemprop="brand" content="Ghostshield" />
							<meta itemprop="duns" content="080519532" />
							<meta itemprop="email" content="support@ghostshield.com" />
							<span itemprop="logo" itemscope="" itemtype="https://schema.org/ImageObject">
								<meta itemprop="url" content="https://assets.ghostshield.com/img/logo/ghostshield-g-orange.png" />
								<meta itemprop="width" content="420" />
								<meta itemprop="height" content="420" />
							</span>
							<div class="hours">
								Monday - Friday<br />
								'.$GLOBALS["office_hours_est"].' <acronym title="Eastern Standard Time">EST</acronym>
							</div>
						</div>
					</div>
				</div>
			</section>

			<footer>
				<div class="column full">
					<div class="column_interior">
						<ul>
							<li><a href="//'.$this->host.'/sitemap/" class="item">Site Map</a></li>
							<li><a href="//'.$this->host.'/about/" class="item">About KreteTek</a></li>
							<li><a href="//'.$this->host.'/privacy/" class="item">Privacy Policy</a></li>
							<li><a href="//'.$this->host.'/warranty/" class="item">Warranty</a></li>';
		if ($this->host == "dev.ghostshield.com") $html .= '
							<li><a href="//'.$this->host.'/update/" class="item">Update &amp; Preview</a></li>
							<li><a href="//'.$this->host.'/publish/" class="item">Publish</a></li>';
		$html .= '
							<li class="footer_menu_right"><a href="https://www.youtube.com/channel/UC9X3Cl8uRVG5X9XlXfmUryw" class="twitter" target="_blank">YouTube</a></li>
							<li class="footer_menu_right"><a href="https://twitter.com/kretetek" class="twitter" target="_blank">Twitter</a></li>
							<li class="footer_menu_right"><a href="https://www.pinterest.com/concretesealers/" class="instagram" target="_blank">Pinterest</a></li>
							<li class="footer_menu_right"><a href="https://www.instagram.com/ghostshield_sealers/" class="instagram" target="_blank">Instagram</a></li>
							<li class="footer_menu_right"><a href="http://facebook.com/GhostshieldSealers/" class="facebook" target="_blank">Facebook</a></li>
						</ul>
						<p>&copy; KreteTek Industries '.date("Y").'</p>
					</div>
				</div>
			</footer>

		</div> <!-- /page_content -->

	</div> <!-- /container -->

	<div id="mobile-check"></div>
	<iframe width="1" height="1" border="0" id="analytics-iframe" src="//ghostshield.com/analytics/locate"></iframe>

	
	';
	if (file_exists(__DIR__.'/../assets/script/jquery.js')) {
		$html .= '<script type="text/javascript">'.file_get_contents(__DIR__.'/../assets/script/jquery.js').'</script>';
	} else $html .= '<script async src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>';
	
	$html .= '
	<script async src="//assets.'.$this->host.'/script/fastclick.js"></script>
	<script async src="//assets.'.$this->host.'/script/jquery.nicescroll.min.js"></script>
	<script async src="//assets.'.$this->host.'/script/dropzone.js?v='.$this->version.'"></script>
	<script async src="//assets.'.$this->host.'/script/ghostshield-nav.min.js?v='.$this->version.'"></script>
	<script async src="//assets.'.$this->host.'/script/ghostshield.js?v='.$this->version.'"></script>
	<script async src="//assets.'.$this->host.'/script/ghostshield-calculator.js?v='.$this->version.'"></script>
	<script async src="//assets.'.$this->host.'/script/photoswipe.min.js"></script>
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
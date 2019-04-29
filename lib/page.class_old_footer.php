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
								<!--svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"  viewBox="0 0 17.5 21.5" style="enable-background:new 0 0 17.5 21.5;" xml:space="preserve">
									<path class="path" d="M7.7,8.6l-1.4,4.1c-0.1,0.2,0,0.4,0.3,0.4h1.6c0.2,0,0.5-0.2,0.6-0.4l0.5-1.4H8.3L8.8,10h3.1l-1.5,4.4H1.6c0,0-0.1,0-0.1,0.1s0,0.1,0.1,0.1l3.1,0.7c0.9,1.5,2,2.6,3.3,3.5c0,0,4.7-1.7,6.4-10.5H8.3C8.1,8.2,7.8,8.4,7.7,8.6z"/>
									<path class="path" d="M2.8,5.5c0,0-0.5,3.9,0.5,6.8c0.1,0.3,0.2,0.6,0.3,0.9l0,0l1.7-4.9C5.6,7.5,6.3,7,7,7h7.6 c0.1-0.8,0.3-2.1,0.3-3.6C15,3.4,9.8,1.8,2.8,5.5z"/>
								</svg-->
								<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 7.8 7" style="enable-background:new 0 0 7.8 7;" xml:space="preserve">
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
						<h3 class="gs_section_headline"><a href="//'.$this->host.'/browse">Products</a></h3>
						<div class="product-gs_list-container">
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
						<h3 class="gs_section_headline"><a href="//'.$this->host.'/browse">Recommendations</a></h3>
						<div class="gs_rec_gs_list-container gs_subsection_container">
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
						<h3 class="gs_section_headline"><a href="//'.$this->host.'/">Home</a></h3>
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
			<section id="contact_footer" class="black">
					<!--h2 class="centered">Contact Us</h2>
					<p class="centered">'.$GLOBALS["contact_paragraph"].'</p-->
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
							<h4 itemprop="name">Ghostshield <span>Concrete Sealers</span></h4>
							<address itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
								<span itemprop="streetAddress">'.$GLOBALS["address_street"].'</span><br class="info_sep" />
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
								Monday - Friday<br class="info_sep" />
								'.$GLOBALS["office_hours_est"].' <acronym title="Eastern Standard Time">EST</acronym>
							</div>
						</div>
			</section>

			<footer>
				<div class="column full">
					<div class="column_interior">
						<ul>
							<li><a href="//'.$this->host.'/sitemap/" class="item">Site Map</a></li>
							<li><a href="//'.$this->host.'/about/" class="item">About Ghostshield</a></li>
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
						<p>&copy; KreteTek Industries '.date("Y").', Ghostshield</p>
					</div>
				</div>
			</footer>

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
<?php
/*

Copyright 2014 MagicToolbox (email : support@magictoolbox.com)

*/

$error_message = false;

function WordPress_Magic360_activate () {

    if(!function_exists('file_put_contents')) {
        function file_put_contents($filename, $data) {
            $fp = fopen($filename, 'w+');
            if ($fp) {
                fwrite($fp, $data);
                fclose($fp);
            }
        }
    }


    //fix url's in css files
    $fileContents = file_get_contents(dirname(__FILE__) . '/core/magic360.css');
    $cssPath = preg_replace('/https?:\/\/[^\/]*/is', '', get_option("siteurl"));

    $cssPath .= '/wp-content/'.preg_replace('/^.*?\/(plugins\/.*?)$/is', '$1', str_replace("\\","/",dirname(__FILE__))).'/core';

    $pattern = '/url\(\s*(?:\'|")?(?!'.preg_quote($cssPath, '/').')\/?([^\)\s]+?)(?:\'|")?\s*\)/is';
    $replace = 'url(' . $cssPath . '/$1)';
    $fixedFileContents = preg_replace($pattern, $replace, $fileContents);
    if($fixedFileContents != $fileContents) {
        file_put_contents(dirname(__FILE__) . '/core/magic360.css', $fixedFileContents);
    }
    magictoolbox_WordPress_Magic360_init() ;

    WordPress_Magic360_send_stat('install');

}

function WordPress_Magic360_deactivate () {

    //delete_option("WordPressMagic360CoreSettings");
    WordPress_Magic360_send_stat('uninstall');
}

function WordPress_Magic360_send_stat($action = '') {

    //NOTE: don't send from working copy
    if('working' == 'v5.12.31' || 'working' == 'v4.5.20') {
        return;
    }

    $hostname = 'www.magictoolbox.com';

    $url = preg_replace('/^https?:\/\//is', '', get_option("siteurl"));
    $url = urlencode(urldecode($url));

    global $wp_version;
    $platformVersion = isset($wp_version) ? $wp_version : '';
    
    

    $path = "api/stat/?action={$action}&tool_name=magic360&license=trial&tool_version=v4.5.20&module_version=v5.12.31&platform_name=wordpress&platform_version={$platformVersion}&url={$url}";
    $handle = @fsockopen($hostname, 80, $errno, $errstr, 30);
    if($handle) {
        $headers  = "GET /{$path} HTTP/1.1\r\n";
        $headers .= "Host: {$hostname}\r\n";
        $headers .= "Connection: Close\r\n\r\n";
        fwrite($handle, $headers);
        fclose($handle);
    }

}

function showMessage_WordPress_Magic360($message, $errormsg = false) {
    if ($errormsg) {
        echo '<div id="message" class="error">';
    } else {
        echo '<div id="message" class="updated fade">';
    }
    echo "<p><strong>$message</strong></p></div>";
}    


function showAdminMessages_WordPress_Magic360(){
    global $error_message;
    if (current_user_can('manage_options')) {
       showMessage_WordPress_Magic360($error_message,true);
    }
}


function plugin_get_version_WordPress_Magic360() {
    $plugin_data = get_plugin_data(str_replace('/plugin.php','.php',__FILE__));
    $plugin_version = $plugin_data['Version'];
    return $plugin_version;
}

function update_plugin_message_WordPress_Magic360() {
    $ver = json_decode(@file_get_contents('http://www.magictoolbox.com/api/platform/wordpress/version/'));
    if (empty($ver)) return false;
    $ver = str_replace('v','',$ver->version);
    $oldVer = plugin_get_version_WordPress_Magic360();
    if (version_compare($oldVer, $ver, '<')) {
        echo '<div id="message" class="updated fade">
                  <p>New version available! We recommend that you download the latest version of the plugin <a href="http://magictoolbox.com/magic360/modules/wordpress/">here</a>. </p>
              </div>';
    }
}

function Magic360_remove_update_nag($value) {
    if (isset($value->response)) {
        unset($value->response[ str_replace('/plugin','',plugin_basename(__FILE__)) ]);
    }
    return $value;
}

function  magictoolbox_WordPress_Magic360_init() {

    global $error_message;
    
    $tool_lower = 'magic360';
    switch ($tool_lower) {
	case 'magiczoom': 	$priority = '90'; break;
	case 'magiczoomplus': 	$priority = '100'; break;
	case 'magicthumb': 	$priority = '110'; break;
	case 'magicscroll': 	$priority = '120'; break;
	case 'magicslideshow':	$priority = '130'; break;
	case 'magic360': 	$priority = '140'; break;
	case 'magictouch': 	$priority = '150'; break;
	default :		$priority = '90'; break;
    }
    
    /* add filters and actions into WordPress */
    add_action("admin_menu", "magictoolbox_WordPress_Magic360_config_page_menu");

    
    add_action("wp_head", "magictoolbox_WordPress_Magic360_styles",$priority); //load scripts and styles

    add_filter("the_content", "magictoolbox_WordPress_Magic360_create", 13); //filter content


    
    
    
    add_filter('site_transient_update_plugins', 'Magic360_remove_update_nag');
    add_filter( 'plugin_action_links', 'magictoolbox_WordPress_Magic360_links', 10, 2 );

    if (!file_exists(dirname(__FILE__) . '/core/magic360.js')) {
        $jsContents = file_get_contents('http://www.magictoolbox.com/static/magic360/trial/magic360.js');
        if (!empty($jsContents) && preg_match('/\/\*.*?\\\*/is',$jsContents)){
            if ( !is_writable(dirname(__FILE__) . '/core/')) {
                $error_message = 'The '.substr(dirname(__FILE__),strpos(dirname(__FILE__),'wp-content')).'/core/magic360.js file is missing. Please re-uplaod it.';
            }
            file_put_contents(dirname(__FILE__) . '/core/magic360.js', $jsContents);
            chmod(dirname(__FILE__) . '/core/magic360.js', 0777);
        } else {
            $error_message = 'The '.substr(dirname(__FILE__),strpos(dirname(__FILE__),'wp-content')).'/core/magic360.js file is missing. Please re-uplaod it.';
        }
    }
    if ($error_message) add_action('admin_notices', 'showAdminMessages_WordPress_Magic360');

    //add_filter("shopp_catalog", "magictoolbox_create", 1); //filter content for SHOPP plugin

    if(!isset($GLOBALS['magictoolbox']['WordPressMagic360'])) {
        require_once(dirname(__FILE__) . '/core/magic360.module.core.class.php');
        $coreClassName = "Magic360ModuleCoreClass";
        $GLOBALS['magictoolbox']['WordPressMagic360'] = new $coreClassName;
        $coreClass = &$GLOBALS['magictoolbox']['WordPressMagic360'];
    }
    $coreClass = &$GLOBALS['magictoolbox']['WordPressMagic360'];
    /* get current settings */
    $settings = get_option("WordPressMagic360CoreSettings");
    if($settings !== false && is_array($settings) && !isset($_GET['reset_settings'])) {
        $coreClass->params->appendArray($settings);
    } else {
        update_option("WordPressMagic360CoreSettings", $coreClass->params->getArray());
    }
}


function WordPressMagic360_config_page() {
     magictoolbox_WordPress_Magic360_config_page('WordPressMagic360');
}

function magictoolbox_WordPress_Magic360_links( $links, $file ) {
    if ( $file == plugin_basename( dirname(__FILE__).'.php' ) ) {
        $settings_link = '<a href="plugins.php?page=WordPressMagic360-config-page">'.__('Settings').'</a>';
        array_unshift( $links, $settings_link );
    }
    return $links;
}

function magictoolbox_WordPress_Magic360_config_page_menu() {
    if(function_exists("add_menu_page")) {
        //$page = add_submenu_page("plugins.php", __("Magic 360 Plugin Configuration"), __("Magic 360 Configuration"), "manage_options", "WordPressMagic360-config-page", "WordPressMagic360_config_page");
        $page = add_menu_page( __("Magic 360"), __("Magic 360"), "manage_options", "WordPressMagic360-config-page", "WordPressMagic360_config_page", plugin_dir_url( __FILE__ )."/core/admin_graphics/icon.png");
    }
}

function  magictoolbox_WordPress_Magic360_config_page($id) {
    update_plugin_message_WordPress_Magic360();
    $settings = $GLOBALS['magictoolbox'][$id]->params->getArray();
    if(isset($_POST["submit"])) {
        /* save settings */
        foreach($settings as $name => $s) {
            if(isset($_POST["magic360settings".ucwords(strtolower($name))])) {
                $v = $_POST["magic360settings".ucwords(strtolower($name))];
                switch($s["type"]) {
                    case "num": $v = intval($v); break;
                    case "array": 
                        $v = trim($v);
                        if(!in_array($v,$s["values"])) $v = $s["default"];
                        break;
                    case "text":
                    default: $v = trim($v);
                }
                $s["value"] = $v;
                $settings[$name] = $s;                
            }
        }
        update_option($id . "CoreSettings", $settings);
        $GLOBALS['magictoolbox'][$id]->params->appendArray($settings);
    }
    
    $toolAbr = '';
    $abr = explode(" ", strtolower("Magic 360"));
    foreach ($abr as $word) $toolAbr .= $word{0};
    
     $corePath = preg_replace('/https?:\/\/[^\/]*/is', '', get_option("siteurl"));
     $corePath .= '/wp-content/'.preg_replace('/^.*?\/(plugins\/.*?)$/is', '$1', str_replace("\\","/",dirname(__FILE__))).'/core';
    ?>
	<style>
        .<?php echo $toolAbr; ?>params { margin:20px 0; width:90%; border:1px solid #dfdfdf; }
        .<?php echo $toolAbr; ?>params .params { margin:0; width:100%;}
        .<?php echo $toolAbr; ?>params .params th { <? /*white-space:nowrap; */ ?> vertical-align:middle; border-bottom:1px solid #dfdfdf; padding:15px 5px; font-weight:bold; background:#fff; text-align:left; padding:0 20px; }
        .<?php echo $toolAbr; ?>params .params td { vertical-align:middle; border-bottom:1px solid #dfdfdf; padding:10px 5px; background:#fff; width:100%; }
        .<?php echo $toolAbr; ?>params .params tr.back th, .<?php echo $toolAbr; ?>params .params tr.back td { background:#f9f9f9; }
        .<?php echo $toolAbr; ?>params .params tr.last th, .<?php echo $toolAbr; ?>params .params tr.last td { border:none; }
        .afterText {font-size:10px;font-style:normal;font-weight:normal;}
        .settingsTitle {font-size: 1.5em;font-weight: normal;margin: 1.7em 0 1em 0;}
        input[type="checkbox"],input[type="radio"] {margin:5px;vertical-align:middle !important;}
        td img {vertical-align:middle !important; margin-right:10px;}
        td span {vertical-align:middle !important; margin-right:10px;}
		#footer , #wpfooter {position:relative;}
    </style>
    
    <div class="icon32" id="icon-options-general"><br></div>
    <h2>Magic 360 Settings</h2><br/>
    <p style="font-size:15px;">Learn about all the <a href="http://www.magictoolbox.com/magic360/integration/" target="_blank">Magic 360&trade; settings and examples too!</a>&nbsp;|&nbsp;<a href="http://www.magictoolbox.com/contact/">Get support</a></p>
    <p style="font-size:15px;">To set up a 360 spin, <a href="http://www.magictoolbox.com/magic360/modules/wordpress/">follow the instructions</a> or <a target="_blank" href="http://www.youtube.com/watch?v=R9omDz1JT-M">watch the video</a>.</p>
    <p><h3>Default settings</h3></p>
    <p>Below are the default settings for Magic 360. You can override these defaults for a particular spin by using the rel tag as <a target="_blank" href="http://www.youtube.com/watch?v=R9omDz1JT-M">described in the video</a>.</p>
    <form action="" method="post" id="magic360-config-form">
            <?php
                $groups = array();
                $imgArray = array('zoom & expand','zoom&expand','yes','zoom','expand','swap images only','original','expanded','no','left','top left','top','top right', 'right', 'bottom right', 'bottom', 'bottom left'); //array for the images ordering

                foreach($settings as $name => $s) { 
                
		    $s['value'] = $GLOBALS['magictoolbox'][$id]->params->getValue($name);
		    
                    if (strtolower($s['id']) == 'disable-expand' || strtolower($s['id']) == 'disable-zoom') continue;
                    if (strtolower($s['id']) == 'direction') continue;
                    if (strtolower($s['id']) == 'class') continue;
		    if (strtolower($s['id']) == 'enabled-effect' || strtolower($s['id']) == 'class' || strtolower($s['id']) == 'nextgen-gallery'  ) {
			$s['group'] = 'top';
                    }
                    
                    
                    if (!isset($groups[$s['group']])) {
                        $groups[$s['group']] = array();
                    }

                    //$s['value'] = $GLOBALS['magictoolbox'][$id]->params->getValue($name);

                    if (strpos($s["label"],'(')) {
                        $before = substr($s["label"],0,strpos($s["label"],'('));
                        $after = ' '.str_replace(')','',substr($s["label"],strpos($s["label"],'(')+1));
                    } else {
                        $before = $s["label"];
                        $after = '';
                    }
                    if (strpos($after,'%')) $after = ' %';
                    if (strpos($after,'in pixels')) $after = ' pixels';
                    if (strpos($after,'milliseconds')) $after = ' milliseconds';

                    $html  .= '<tr>';
                    $html  .= '<th width="50%">';
                    $html  .= '<label for="magic360settings'. ucwords(strtolower($name)).'">'.$before.'</label>';

                    if(($s['type'] != 'array') && isset($s['values'])) $html .= '<br/> <span class="afterText">' . implode(', ',$s['values']).'</span>';

                    $html .= '</th>';
                    $html .= '<td width="50%">';

                    switch($s["type"]) {
                        case "array": 
                                $rButtons = array();
                                foreach($s["values"] as $p) {
                                    $rButtons[strtolower($p)] = '<label><input type="radio" value="'.$p.'"'. ($s["value"]==$p?"checked=\"checked\"":"").' name="magic360settings'.ucwords(strtolower($name)).'" id="magic360settings'. ucwords(strtolower($name)).$p.'">';
                                    $pName = ucwords($p);
                                    if(strtolower($p) == "yes")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/yes.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "no")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/no.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "left")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/left.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "right")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/right.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "top")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/top.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "bottom")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/bottom.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "bottom left")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/bottom-left.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "bottom right")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/bottom-right.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "top left")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/top-left.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    elseif(strtolower($p) == "top right")
                                        $rButtons[strtolower($p)] .= '<img src="'.$corePath.'/admin_graphics/top-right.gif" alt="'.$pName.'" title="'.$pName.'" /></label>';
                                    else {
                                        if (strtolower($p) == 'load,hover') $p = 'Load & hover';
                                        if (strtolower($p) == 'load,click') $p = 'Load & click';
                                        $rButtons[strtolower($p)] .= '<span>'.ucwords($p).'</span></label>';
                                    }
                                }
                                foreach ($imgArray as $img){
                                    if (isset($rButtons[$img])) {
                                        $html .= $rButtons[$img];
                                        unset($rButtons[$img]);
                                    }
                                }
                                $html .= implode('',$rButtons);
                            break;
                        case "num": 
                        case "text": 
                        default:
                            if (strtolower($name) == 'message') { $width = 'style="width:95%;"';} else {$width = '';}
                            $html .= '<input '.$width.' type="text" name="magic360settings'.ucwords(strtolower($name)).'" id="magic360settings'. ucwords(strtolower($name)).'" value="'.$s["value"].'" />';
                            break;
                    }
                    $html .= '<span class="afterText">'.$after.'</span>';
                    $html .= '</td>';
                    $html .= '</tr>';
                    $groups[$s['group']][] = $html;
                    $html = '';
                }
            echo '<div class="'.$toolAbr.'params">
                  <table class="params" cellspacing="0">';
            $i = 0;
            $keys = array_keys($groups);
            
            $top = $groups['top'];
            unset($groups['top']);
            array_unshift($groups, $top);

            foreach ($groups as $name => $group) {
		if ($name == '0') {
		    $name = '';
		    $group = preg_replace('/(^.*)(Class\sName)(.*?<span>)(All)(<\/span>.*?<span>)(Magic360)(<\/span>.*)/is','$1Apply effect to all image links$3Yes$5No$7',$group);
		    
		}
                if ($name == $keys[count($keys)-1]) {
                $group[count($group)-1] = str_replace('<tr','<tr class="last"',$group[count($group)-1]); //set "last" class
                }
                if (is_array($group)) {
		    foreach ($group as $g) {
			if (++$i%2==0) { //set stripes
			    if (strpos($g,'class="last"')) {
				$g = str_replace('class="last"','class="back last"',$g);
			    } else {
				$g = str_replace('<tr','<tr class="back"',$g);
			    }
			}
			echo $g;
		    }
                }
            }
                echo '</table> </div>';
            ?>
            
            <p><input type="submit" name="submit" class="button-primary" value="Save settings" />&nbsp;<a href="plugins.php?page=WordPressMagic360-config-page&reset_settings=true">Reset to defaults</a></p>
        </form>

   
    </div>
    <div style="font-size:12px;margin:5px auto;text-align:center;">Learn more about the <a href="http://www.magictoolbox.com/magic360_integration/" target="_blank">customisation options</a></div>
    <?php
}



function  magictoolbox_WordPress_Magic360_styles() {
    if(!defined('MAGICTOOLBOX_MAGIC360_HEADERS_LOADED')) {
        $plugin = $GLOBALS['magictoolbox']['WordPressMagic360'];
		if (function_exists('plugins_url')) {
			$core_url = plugins_url();
		} else {
			$core_url = get_option("siteurl").'/wp-content/plugins';
		}


        $path = preg_replace('/^.*?\/plugins\/(.*?)$/is', '$1', str_replace("\\","/",dirname(__FILE__)));
        
        $headers = $plugin->headers($core_url."/{$path}/core");

        echo $headers;
        define('MAGICTOOLBOX_MAGIC360_HEADERS_LOADED', true);
    }
}



function  magictoolbox_WordPress_Magic360_create($content) {


    $plugin = $GLOBALS['magictoolbox']['WordPressMagic360'];
    /*$pattern = "<img([^>]*)(?:>)(?:[^<]*<\/img>)?";
    $pattern = "(?:<a([^>]*)>.*?){$pattern}(.*?)(?:<\/a>)";*/
    $pattern = "(?:<a([^>]*)>)[^<]*<img([^>]*)(?:>)(?:[^<]*<\/img>)?(.*?)[^<]*?<\/a>";


    $oldContent = $content;
        $content = preg_replace_callback("/{$pattern}/is","magictoolbox_WordPress_Magic360_callback",$content);
        if ($content == $oldContent) return $content;


    if (!$plugin->params->checkValue('template','original') && $plugin->type == 'standard' && isset($GLOBALS['magictoolbox']['Magic360']['main'])) {
        // template helper class
        require_once(dirname(__FILE__) . '/core/magictoolbox.templatehelper.class.php');
        MagicToolboxTemplateHelperClass::setPath(dirname(__FILE__).'/core/templates');
        MagicToolboxTemplateHelperClass::setOptions($plugin->params);
        if (!WordPress_Magic360_page_check('WordPress')) { //do not render thumbs on category pages
            $thumbs = WordPress_Magic360_get_prepared_selectors();
        } else {
            $thumbs = array();
        }
        
        if (is_array($GLOBALS['MAGICTOOLBOX_'.strtoupper('Magic360').'_SELECTORS'])) {
	    $thumbs = array_merge($thumbs,$GLOBALS['MAGICTOOLBOX_'.strtoupper('Magic360').'_SELECTORS']);
        }
        
        $html = MagicToolboxTemplateHelperClass::render(array(
            'main' => $GLOBALS['magictoolbox']['Magic360']['main'],
            'thumbs' => (count($thumbs) >= 1) ? $thumbs : array(),
            'pid' => $GLOBALS['magictoolbox']['prods_info']['product_id'],
        ));

        $content = str_replace('MAGICTOOLBOX_PLACEHOLDER', $html, $content);
    } else if ($plugin->params->checkValue('template','original') || $plugin->type != 'standard') {
        $html = $GLOBALS['magictoolbox']['Magic360']['main'];
        $content = str_replace('MAGICTOOLBOX_PLACEHOLDER', $html, $content);
    }


    return $content;
}
function  magictoolbox_WordPress_Magic360_callback($matches) {
    $plugin = $GLOBALS['magictoolbox']['WordPressMagic360'];


    $alignclass = preg_replace('/^.*?align(left|right|center|none).*$/is', '$1', $matches[2]);
    if($alignclass != $matches[2]) {
        $alignclass = ' align'.$alignclass;
    } else {
        $alignclass='';
        $float = preg_replace('/^.*?float:\s*(left|right|none).*$/is', '$1', $matches[2]);
        if($float == $matches[2]) {
            $float = '';
        } else {
            $float = ' float: ' . $float . ';';
        }
    }
    

    // get needed attributes 
    global $wp_query;
    $alt = preg_replace("/^.*?alt\s*=\s*[\"\'](.*?)[\"\'].*$/is","$1",$matches[2]);
    if (isset($matches[1]) && !empty($matches[1])) { // thecartpress fix
	$img = preg_replace("/^.*?href\s*=\s*[\"\'](.*?)[\"\'].*$/is","$1",$matches[1]);
	$thumb = preg_replace("/^.*?src\s*=\s*[\"\'](.*?)[\"\'].*$/is","$1",$matches[2]);
    } else {
	$thumb = $img = preg_replace("/^.*?href\s*=\s*[\"\'](.*?)[\"\'].*$/is","$1",$matches[2]); // only thecartpress
    }
    
      
    if($plugin->params->checkValue('show-message', 'Yes')) {
        $message = '<div class="MagicToolboxMessage">' . $plugin->params->getValue('message') . '</div>';
    } else $message = '';
    $result = $matches[0].$message;
      //restore after the rel was generated



    
    $result = "<div style=\"{$float}\" class=\"MagicToolboxContainer {$alignclass}\">{$result}</div>";


    return $result;
    //return $matches[0];
}


function WordPress_Magic360_get_post_attachments()  {
    $args = array(
            'post_type' => 'attachment',
            'numberposts' => '-1',
            'post_status' => null,
            'post_parent' => $post_id
        );

    $attachments = get_posts($args);
    return $attachments;
}



?>

<?php

if(!defined('Magic360ModuleCoreClassLoaded')) {

    define('Magic360ModuleCoreClassLoaded', true);

    require_once(dirname(__FILE__) . '/magictoolbox.params.class.php');

    class Magic360ModuleCoreClass {

        public $params;
        public $general;//initial parameters
        public $type = 'circle';// set module type
        public $optionsClassEnable = false;

        function Magic360ModuleCoreClass() {

            // init params
            $this->params = new MagicToolboxParamsClass();
            $this->general = new MagicToolboxParamsClass();
            // load default params
            $this->_paramDefaults();
            if(defined('MagicToolboxOptionsLoaded')) {
                $this->optionsClassEnable = true;
            }

        }

        function headers($jsPath = '', $cssPath = null, $notCheck = false) {

            //to prevent multiple displaying of headers
            if(!defined('Magic360ModuleHeaders')) {
                define('Magic360ModuleHeaders', true);
            } else {
                return '';
            }
            if($cssPath == null) {
                $cssPath = $jsPath;
            }
            $headers = array();
            // add module version
            $headers[] = '<!-- Magic 360 WordPress module version v5.12.31 [v1.4.21:v4.5.20] -->';
            // add style link
            $headers[] = '<link type="text/css" href="' . $cssPath . '/magic360.css" rel="stylesheet" media="screen" />';
            // add script link
            $headers[] = '<script type="text/javascript" src="' . $jsPath . '/magic360.js"></script>';
            // add options
            $headers[] = '<script type="text/javascript">Magic360.options = {' . "\n\t" . implode(",\n\t", $this->options()) . "\n" . '}</script>';
            $headers[] = '<script type="text/javascript">Magic360.lang = {' . "\n\t" . implode(",\n\t", $this->langOptions()) . "\n" . '}</script>';
            return implode("\r\n", $headers);

        }

        function options() {

            return array(
                '\'spin\': \'' . $this->params->getValue('spin') . '\'',
                '\'autospin-direction\': \'' . $this->params->getValue('autospin-direction') . '\'',
                '\'speed\': ' . $this->params->getValue('speed'),
                '\'smoothing\': ' . ($this->params->checkValue('smoothing', 'Yes') ? 'true' : 'false'),
                '\'autospin\': \'' . $this->params->getValue('autospin') . '\'',
                '\'autospin-start\': \'' . $this->params->getValue('autospin-start') . '\'',
                '\'autospin-stop\': \'' . $this->params->getValue('autospin-stop') . '\'',
                '\'initialize-on\': \'' . $this->params->getValue('initialize-on') . '\'',
                '\'columns\': ' . $this->params->getValue('columns'),
                '\'rows\': ' . ($this->params->exists('rows') ? $this->params->getValue('rows') : '1'),
                '\'magnify\': ' . ($this->params->checkValue('magnify', 'Yes') ? 'true' : 'false'),
                '\'magnifier-width\': \'' . $this->params->getValue('magnifier-width') . '\'',
                '\'start-column\': ' . ($this->params->checkValue('start-column', 'auto') ? '\'auto\'' : $this->params->getValue('start-column')),
                '\'start-row\': ' . ($this->params->checkValue('start-row', 'auto') ? '\'auto\'' : $this->params->getValue('start-row')),
                '\'loop-column\': ' . ($this->params->checkValue('loop-column', 'Yes') ? 'true' : 'false'),
                '\'loop-row\': ' . ($this->params->checkValue('loop-row', 'Yes') ? 'true' : 'false'),
                '\'reverse-column\': ' . ($this->params->checkValue('reverse-column', 'Yes') ? 'true' : 'false'),
                '\'reverse-row\': ' . ($this->params->checkValue('reverse-row', 'Yes') ? 'true' : 'false'),
                '\'column-increment\': ' . $this->params->getValue('column-increment'),
                '\'row-increment\': ' . $this->params->getValue('row-increment'),
                //'\'filename\': \'' . $this->params->getValue('filename') . '\'',
                //'\'filepath\': \'' . $this->params->getValue('filepath') . '\'',
                //'\'large-filename\': \'' . $this->params->getValue('large-filename') . '\'',
                //'\'large-filepath\': \'' . $this->params->getValue('large-filepath') . '\'',
                '\'magnifier-shape\': \'' . $this->params->getValue('magnifier-shape') . '\'',
                '\'mousewheel-step\': ' . $this->params->getValue('mousewheel-step'),
                '\'autospin-speed\': ' . $this->params->getValue('autospin-speed'),
                '\'fullscreen\': ' . ($this->params->checkValue('fullscreen', 'Yes') ? 'true' : 'false'),
                '\'hint\': ' . ($this->params->checkValue('hint', 'Yes') ? 'true' : 'false'),
            );

        }

        function langOptions() {

            return array(
                '\'loading-text\': \''.str_replace('\'', '\\\'', $this->params->getValue('loading-text')).'\'',
                '\'fullscreen-loading-text\': \''.str_replace('\'', '\\\'', $this->params->getValue('fullscreen-loading-text')).'\'',
                '\'hint-text\': \''.str_replace('\'', '\\\'', $this->params->getValue('hint-text')).'\'',
                '\'mobile-hint-text\': \''.str_replace('\'', '\\\'', $this->params->getValue('mobile-hint-text')).'\'',
            );

        }

        function enabled($data, $id) {

            if($this->optionsClassEnable) {
                if($this->params->exists('enable-effect', 'default') && $this->params->checkValue('enable-effect', 'No')) {
                    return false;
                }
            } else {
                if($this->params->exists('enable-effect') && $this->params->checkValue('enable-effect', 'No')) {
                    return false;
                }
            }
            if(intval($this->params->getValue('columns')) == 0) {
                return false;
            }
            if(is_array($data)) $data = count($data);
            if($data < intval($this->params->getValue('columns'))) {
                return false;
            }
            $ids = trim($this->params->getValue('product-ids'));
            if($ids != 'all' && !in_array($id, explode(',', $ids))) {
                return false;
            }
            return true;

        }

        function template($data, $params = array()) {

            $html = array();

            extract($params);

            if(!$this->params->exists('rows')) {
                $this->params->set('rows', floor(count($data)/$this->params->getValue('columns')));
            }

            // check for width/height
            if(!isset($width) || empty($width)) {
                $width = "";
            } else {
                $width = " width=\"{$width}\"";
            }
            if(!isset($height) || empty($height)) {
                $height = "";
            } else {
                $height = " height=\"{$height}\"";
            }

            // check ID
            if(!isset($id) || empty($id)) {
                $id = '';
            } else {
                $id = ' id="' . addslashes($id) . '"';
            }

            $images = array();// set of small images
            $largeImages = array();// set of large images

            $first = reset($data);
            $src = ' src="' . $first['medium'] . '"';
            // add items
            foreach($data as $item) {
                //NOTE: if there are spaces in the filename
                $images[] = str_replace(' ', '%20', $item['medium']);
                $largeImages[] = str_replace(' ', '%20', $item['img']);
            }

            $this->params->set('images', implode(' ', $images));
            if($this->params->checkValue('magnify', 'Yes') || $this->params->checkValue('fullscreen', 'Yes')) {
                $this->params->set('large-images', implode(' ', $largeImages));
            }
            $rel = ' data-magic360-options="' . $this->getRel() . '"';

            $html[] = '<a' . $id . ' class="Magic360" href="#"' . $rel . '>';
            $html[] = '<img itemprop="image"' . $src . $width . $height . ' />';
            $html[] = '</a>';

            // check message
            if($this->params->checkValue('show-message', 'Yes')) {
                $message = '<div class="MagicToolboxMessage">' . $this->params->getValue('message') . '</div>';
            } else $message = '';

            // add message
            $html[] = $message;

            // create HTML string
            $html = implode('', $html);

            // return result
            return $html;
        }

        function subTemplate() {
            $args = func_get_args();
            call_user_func_array(array($this, 'template'), $args);
        }

        function _paramDefaults() {
            $params = array("rows"=>array("id"=>"rows","group"=>"Magic360","order"=>"0","default"=>"1","label"=>"Number of images on Y-axis {row}","type"=>"num","scope"=>"tool"),"columns"=>array("id"=>"columns","group"=>"Magic360","order"=>"10","default"=>"36","label"=>"Number of images on X-axis {col}","type"=>"num","scope"=>"tool"),"magnifier-shape"=>array("id"=>"magnifier-shape","group"=>"Magic360","order"=>"71","default"=>"inner","label"=>"Magnifier shape","type"=>"array","subType"=>"radio","values"=>array("inner","circle","square"),"scope"=>"tool"),"fullscreen"=>array("id"=>"fullscreen","group"=>"Magic360","order"=>"72","default"=>"Yes","label"=>"Enable full-screen spin","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"spin"=>array("id"=>"spin","group"=>"Magic360","order"=>"110","default"=>"drag","label"=>"Spin","description"=>"Method for spinning the image","type"=>"array","subType"=>"select","values"=>array("drag","hover"),"scope"=>"tool"),"autospin-direction"=>array("id"=>"autospin-direction","group"=>"Magic360","order"=>"111","default"=>"clockwise","label"=>"Auto-spin direction","type"=>"array","subType"=>"radio","values"=>array("clockwise","anticlockwise","alternate-clockwise","alternate-anticlockwise"),"scope"=>"tool"),"speed"=>array("id"=>"speed","group"=>"Magic360","order"=>"120","default"=>"50","label"=>"Spin speed (1-100)","description"=>"1 = very slow, 100 = very fast","type"=>"num","scope"=>"tool"),"mousewheel-step"=>array("id"=>"mousewheel-step","advanced"=>"1","group"=>"Magic360","order"=>"121","default"=>"1","label"=>"Mousewheel step","description"=>"Number of frames to spin on mousewheel","type"=>"num","scope"=>"tool"),"autospin-speed"=>array("id"=>"autospin-speed","group"=>"Magic360","order"=>"122","default"=>"3600","label"=>"Auto-spin speed","description"=>"e.g. 1 = fast / 10000 = slow","type"=>"num","scope"=>"tool"),"smoothing"=>array("id"=>"smoothing","group"=>"Magic360","order"=>"130","default"=>"Yes","label"=>"Smoothing","description"=>"Smoothly stop the image spinning","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"autospin"=>array("id"=>"autospin","group"=>"Magic360","order"=>"140","default"=>"once","label"=>"Auto-spin duration","type"=>"array","subType"=>"select","values"=>array("once","twice","infinite","off"),"scope"=>"tool"),"autospin-start"=>array("id"=>"autospin-start","group"=>"Magic360","order"=>"150","default"=>"load,hover","label"=>"Start auto-spin","description"=>"Start automatic spin on page load, click or hover","type"=>"array","subType"=>"select","values"=>array("load","hover","click","load,hover","load,click"),"scope"=>"tool"),"autospin-stop"=>array("id"=>"autospin-stop","group"=>"Magic360","order"=>"160","default"=>"click","label"=>"Stop auto-spin","description"=>"Stop automatic spin on click or hover","type"=>"array","subType"=>"select","values"=>array("click","hover","never"),"scope"=>"tool"),"initialize-on"=>array("id"=>"initialize-on","group"=>"Magic360","order"=>"170","default"=>"load","label"=>"Initialization","description"=>"When to download images","type"=>"array","subType"=>"select","values"=>array("load","hover","click"),"scope"=>"tool"),"magnify"=>array("id"=>"magnify","group"=>"Magic360","order"=>"200","default"=>"No","label"=>"Magnifier effect","description"=>"requires set of large images","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"magnifier-width"=>array("id"=>"magnifier-width","group"=>"Magic360","order"=>"210","default"=>"80%","label"=>"Magnifier width","description"=>"Magnifier size in % of small image width or fixed size in px","type"=>"text","scope"=>"tool"),"reverse-column"=>array("id"=>"reverse-column","advanced"=>"1","group"=>"Magic360","order"=>"260","default"=>"No","label"=>"Reverse rotation on X-axis (left/right)","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"reverse-row"=>array("id"=>"reverse-row","group"=>"Magic360","order"=>"270","default"=>"No","label"=>"Reverse rotation on Y-axis (up/down)","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"column-increment"=>array("id"=>"column-increment","advanced"=>"1","group"=>"Magic360","order"=>"280","default"=>"1","label"=>"Column increment (left/right)","description"=>"Load only every second (2) or third (3) column so that spins load faster","type"=>"num","scope"=>"tool"),"row-increment"=>array("id"=>"row-increment","advanced"=>"1","group"=>"Magic360","order"=>"290","default"=>"1","label"=>"Row increment (up/down)","description"=>"Load only every second (2) or third (3) row so that spins load faster","type"=>"num","scope"=>"tool"),"class"=>array("id"=>"class","group"=>"Miscellaneous","order"=>"20","default"=>"Magic360","label"=>"Class Name","type"=>"array","subType"=>"select","values"=>array("all","Magic360")),"show-message"=>array("id"=>"show-message","group"=>"Miscellaneous","order"=>"150","default"=>"No","label"=>"Show message under image?","type"=>"array","subType"=>"radio","values"=>array("Yes","No")),"message"=>array("id"=>"message","group"=>"Miscellaneous","order"=>"160","default"=>"Drag to spin the image!","label"=>"Message under images","type"=>"text"),"loading-text"=>array("id"=>"loading-text","group"=>"Miscellaneous","order"=>"258","default"=>"Loading...","label"=>"Loading text","description"=>"Text displayed while images are loading.","type"=>"text"),"fullscreen-loading-text"=>array("id"=>"fullscreen-loading-text","group"=>"Miscellaneous","order"=>"258","default"=>"Loading large spin...","label"=>"Fullscreen loading text","description"=>"Text shown while full-screen images are loading.","type"=>"text"),"hint"=>array("id"=>"hint","group"=>"Miscellaneous","order"=>"259","default"=>"Yes","label"=>"Show hint message","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"hint-text"=>array("id"=>"hint-text","group"=>"Miscellaneous","order"=>"260","default"=>"Drag to spin","label"=>"Hint text appears on desktop","type"=>"text"),"mobile-hint-text"=>array("id"=>"mobile-hint-text","group"=>"Miscellaneous","order"=>"261","default"=>"Swipe to spin","label"=>"Hint text appears on iOS/Android devices","type"=>"text"),"start-column"=>array("id"=>"start-column","group"=>"Miscellaneous","order"=>"500","default"=>"1","label"=>"Start column","description"=>"Column from which to start spin. auto means to start from the middle","type"=>"num","scope"=>"tool"),"start-row"=>array("id"=>"start-row","group"=>"Miscellaneous","order"=>"500","default"=>"auto","label"=>"Start row","description"=>"Row from which to start spin. auto means to start from the middle","type"=>"num","scope"=>"tool"),"loop-column"=>array("id"=>"loop-column","group"=>"Miscellaneous","order"=>"500","default"=>"Yes","label"=>"Loop column","description"=>"Continue spin after the last image on X-axis","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"),"loop-row"=>array("id"=>"loop-row","group"=>"Miscellaneous","order"=>"500","default"=>"No","label"=>"Loop row","description"=>"Continue spin after the last image on Y-axis","type"=>"array","subType"=>"radio","values"=>array("Yes","No"),"scope"=>"tool"));
            $this->params->appendArray($params);
        }

        function getRel($notCheck = false) {
            $rel = array();
            if($this->optionsClassEnable) {
                $rel[] = 'rows:' . ($this->params->exists('rows') ? $this->params->getValue('rows') : '1');
                $rel[] = 'images: ' . $this->params->getValue('images');
                if($this->params->checkValue('magnify', 'Yes') || $this->params->checkValue('fullscreen', 'Yes')) {
                    $rel[] = 'large-images: ' . $this->params->getValue('large-images');
                }
                $rel = implode(';',$rel) . ';';
                return $this->params->serialize() . ';' . $rel;
            }
            $rel = array();
            if(count($this->general->params)) {
                foreach($this->general->params as $name => $param) {
                    if($this->params->checkValue($name, $this->general->getValue($name))) continue;
                    switch($name) {
                        case 'spin':
                            $rel[] = 'spin: ' . $this->params->getValue('spin');
                        break;
                        case 'autospin-direction':
                            $rel[] = 'autospin-direction: ' . $this->params->getValue('autospin-direction');
                        break;
                        case 'speed':
                            $rel[] = 'speed: ' . $this->params->getValue('speed');
                        break;
                        case 'smoothing':
                            $rel[] = 'smoothing: ' . ($this->params->checkValue('smoothing', 'Yes') ? 'true' : 'false');
                        break;
                        case 'autospin':
                            $rel[] = 'autospin: ' . $this->params->getValue('autospin');
                        break;
                        case 'autospin-start':
                            $rel[] = 'autospin-start: ' . $this->params->getValue('autospin-start');
                        break;
                        case 'autospin-stop':
                            $rel[] = 'autospin-stop: ' . $this->params->getValue('autospin-stop');
                        break;
                        case 'initialize-on':
                            $rel[] = 'initialize-on: ' . $this->params->getValue('initialize-on');
                        break;
                        case 'columns':
                            $rel[] = 'columns: ' . $this->params->getValue('columns');
                        break;
                        //case 'rows':
                        //    $rel[] = 'rows: ' . $this->params->getValue('rows');
                        //break;
                        case 'magnify':
                            $rel[] = 'magnify: ' . ($this->params->checkValue('magnify', 'Yes') ? 'true' : 'false');
                        break;
                        case 'magnifier-width':
                            $rel[] = 'magnifier-width: ' . $this->params->getValue('magnifier-width');
                        break;
                        case 'start-column':
                            $rel[] = 'start-column: ' . $this->params->getValue('start-column');
                        break;
                        case 'start-row':
                            $rel[] = 'start-row: ' . $this->params->getValue('start-row');
                        break;
                        case 'loop-columns':
                            $rel[] = 'loop-column: ' . ($this->params->checkValue('loop-column', 'Yes') ? 'true' : 'false');
                        break;
                        case 'loop-row':
                            $rel[] = 'loop-row: ' . ($this->params->checkValue('loop-row', 'Yes') ? 'true' : 'false');
                        break;
                        case 'reverse-column':
                            $rel[] = 'reverse-column: ' . ($this->params->checkValue('reverse-column', 'Yes') ? 'true' : 'false');
                        break;
                        case 'reverse-row':
                            $rel[] = 'reverse-row: ' . ($this->params->checkValue('reverse-row', 'Yes') ? 'true' : 'false');
                        break;
                        case 'column-increment':
                            $rel[] = 'column-increment: ' . $this->params->getValue('column-increment');
                        break;
                        case 'row-increment':
                            $rel[] = 'row-increment: ' . $this->params->getValue('row-increment');
                        break;
                        /*case 'filename':
                            $rel[] = 'filename: ' . $this->params->getValue('filename');
                        break;
                        case 'filepath':
                            $rel[] = 'filepath: ' . $this->params->getValue('filepath');
                        break;
                        case 'large-filename':
                            $rel[] = 'large-filename: ' . $this->params->getValue('large-filename');
                        break;
                        case 'large-filepath':
                            $rel[] = 'large-filepath: ' . $this->params->getValue('large-filepath');
                        break;*/
                        //case 'loading-text':
                        //    $options[] = 'loading-text: ' . $this->params->getValue('loading-text');
                        //    break;
                        case 'magnifier-shape':
                            $rel[] = 'magnifier-shape: ' . $this->params->getValue('magnifier-shape');
                        break;
                        case 'mousewheel-step':
                            $rel[] = 'mousewheel-step: ' . $this->params->getValue('mousewheel-step');
                        break;
                        case 'autospin-speed':
                            $rel[] = 'autospin-speed: ' . $this->params->getValue('autospin-speed');
                        break;
                        case 'fullscreen':
                            $rel[] = 'fullscreen: ' . ($this->params->checkValue('fullscreen', 'Yes') ? 'true' : 'false');
                        break;
                        case 'hint':
                            $rel[] = 'hint: ' . ($this->params->checkValue('hint', 'Yes') ? 'true' : 'false');
                        break;
                    }
                }
            }
            $rel[] = 'rows:' . ($this->params->exists('rows') ? $this->params->getValue('rows') : '1');
            $rel[] = 'images: ' . $this->params->getValue('images');
            if($this->params->checkValue('magnify', 'Yes') || $this->params->checkValue('fullscreen', 'Yes')) {
                $rel[] = 'large-images: ' . $this->params->getValue('large-images');
            }
            if(count($rel)) {
                $rel = implode(';',$rel) . ';';
            } else {
                $rel = '';
            }
            return $rel;

        }

    }

}

?>
<?php
/*

Copyright 2008 MagicToolbox (email : support@magictoolbox.com)
Plugin Name: Magic 360
Plugin URI: http://www.magictoolbox.com/magic360/
Description: Spin products round in 360 degrees and zoom them. The ultimate way to view products! <a target="_blank" href="plugins.php?page=WordPressMagic360-config-page/">Choose your Magic 360 options</a> | <a target="_blank"  href="http://www.magictoolbox.com/magic360/modules/wordpress/">Setup instructions </a> | <a target="_blank" href="http://www.magictoolbox.com/magic360/examples/">Examples</a>
Version: 5.12.31
Author: MagicToolbox
Author URI: http://www.magictoolbox.com/

*/

/*
    WARNING: DO NOT MODIFY THIS FILE!

    NOTE: If you want change Magic 360 settings
            please go to plugin page
            and click 'Magic 360 Configuration' link in top navigation sub-menu.
*/

if(!function_exists('magictoolbox_WordPress_Magic360_init')) {
    /* Include MagicToolbox plugins core funtions */
    require_once(dirname(__FILE__)."/magic360/plugin.php");
}

//MagicToolboxPluginInit_WordPress_Magic360 ();
register_activation_hook( __FILE__, 'WordPress_Magic360_activate');

register_deactivation_hook( __FILE__, 'WordPress_Magic360_deactivate');

magictoolbox_WordPress_Magic360_init();
?>
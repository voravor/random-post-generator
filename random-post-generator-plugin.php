<?php
/**
 * @package RPG
 * @version 1.0
 */
/*
Plugin Name: Random Post Generator
Plugin URI: http://www.voravor.com
Description: Generate random posts
Author: vvor
Version: 1.0
Author URI: http://www.voravor.com
*/

// if this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define("RPG_DIR", plugin_dir_path(__FILE__));

/*this counteracts wp's infuriating escaping of superglobals,
* no matter what, which occurs in wp-settings
*/
global $_REAL_GET, $_REAL_POST, $_REAL_COOKIE, $_REAL_REQUEST;
$_REAL_GET     = $_GET;
$_REAL_POST    = $_POST;
$_REAL_COOKIE  = $_COOKIE;
$_REAL_REQUEST = $_REQUEST;

require_once( dirname( __FILE__ ) . '/lib/plugin.php' );

//autoloader for our namespaced objects
spl_autoload_register(function ($className) {
    
    // Make sure the class included is in this plugins namespace
    if (substr($className, 0, strlen("RPG\\")) === "RPG\\") {
        
        // Remove Helium namespace from the className
        // Replace \ with / which works as directory separator for further namespaces
        $classNameShort = strtolower(str_replace("\\", "/", substr($className, strlen("RPG\\"))));
        
        include_once RPG_DIR . "lib/$classNameShort.php";
    }

});

//call our plugin
RPG\Plugin::instance();

//activation and deactivation, called when installed/uninstalled
register_activation_hook( dirname( __FILE__ ) . '/lib/plugin.php', array( 'RPG\App', 'install' ) );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	register_deactivation_hook( __FILE__, array( 'Helium\Plugin', 'uninstall' ) );
}


<?php
/*
Plugin Name:  PWA
Plugin URI:   https://developer.wordpress.org/plugins/the-basics/
Description:  Plugin that focus on PWA which rightnow help the website to load quickly and implement service workers for better performance navigation
Version:      20160911
Author:       tyohan@gmail.com
Author URI:   https://tyohan.me
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  wporg
Domain Path:  /languages


NOTE: 
*/
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );
// $rewrite=new WP_Rewrite;
// print_r($rewrite->wp_rewrite_rules());die();
require __DIR__ . '/vendor/autoload.php';
require_once(dirname(__FILE__).'/WPWA.php');
WPWA::instance();

require_once(dirname(__FILE__).'/WPWAAssets.php');
WPWAAssets::instance();

require_once(dirname(__FILE__).'/WPWALazyLoad.php');
WPWALazyLoad::instance();






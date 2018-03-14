<?php
/*
Plugin Name:  Minimum Configuration WordPress PWA
Plugin URI:   https://github.com/tyohan/mcw-pwa
Description:  WordPress plugin to optimize loading performance with minimum configuration using PWA approach
Version:      0.1.2
Author:       Yohan Totting
Author URI:   https://tyohan.me
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  mcwpwa
Domain Path:  /languages

Minimum Configuration WordPress PWA is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Minimum Configuration WordPress PWA is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Minimum Configuration WordPress PWA..
*/


defined( 'ABSPATH' ) or die( 'Nope, not accessing this' );
define( 'MCW_PWA_URL',plugin_dir_url(__FILE__));
define( 'MCW_PWA_DIR',plugin_dir_path(__FILE__));
define('MCW_PWA_OPTION','mcw_option_group');
define('MCW_SECTION_PERFORMANCE','mcw_option_performance');
define('MCW_SECTION_PWA','mcw_option_pwa');
define('MCW_PWA_SETTING_PAGE','mcw_setting_page');

require_once(MCW_PWA_DIR . '/vendor/autoload.php');
require_once(MCW_PWA_DIR.'/includes/MCW_PWA_Service_Worker.php');
require_once(MCW_PWA_DIR.'/includes/performance/MCW_PWA_Performance.php');
require_once(MCW_PWA_DIR.'/includes/MCW_PWA_Settings.php');
require_once(MCW_PWA_DIR.'/includes/MCW_PWA_LazyLoad.php');
//require_once(MCW_PWA_DIR.'includes/MCW_PWA_Assets.php');
//require_once(MCW_PWA_DIR.'includes/MCW_PWA_Add_Homescreen.php');

MCW_PWA_Settings::instance();
MCW_PWA_Service_Worker::instance();
MCW_PWA_LazyLoad::instance();
//MCW_PWA_Assets::instance();
//MCW_PWA_Add_Homescreen::instance();


register_deactivation_hook( __FILE__, array(MCW_PWA_Service_Worker::instance(),'flushRewriteRules' ));
register_deactivation_hook(__FILE__,'reset_options');

function reset_options(){
    delete_option('mcw_enable_assets');
    delete_option('mcw_enable_service_workers');
    delete_option('mcw_enable_lazy_load');
    delete_option('mcw_enable_performance');
}

add_action('parse_query','mcw_init');

function mcw_init(){
    if(!is_admin()){

        MCW_PWA_Service_Worker::instance()->run();
        MCW_PWA_Assets::instance()->run();
        
        //Don't use lazy load when in AMP page
        if(AMP_QUERY_VAR!==null && !get_query_var( AMP_QUERY_VAR, false )){
            MCW_PWA_LazyLoad::instance()->run();
            MCW_PWA_Performance::instance();
        }
    }    
}

function deactivate_rules(){
    flush_rewrite_rules();
}
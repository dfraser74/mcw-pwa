<?php
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
 
delete_option('mcw_enable_assets');
delete_option('mcw_enable_service_workers');
delete_option('mcw_enable_lazy_load');
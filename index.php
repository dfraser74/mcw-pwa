<?php
/*
Plugin Name:  Minimum Configuration WordPress PWA
Plugin URI:   https://github.com/tyohan/mcw-pwa
Description:  WordPress plugin to optimize loading performance with minimum configuration using PWA approach
Version:      0.1
Author:       tyohan@gmail.com
Author URI:   https://tyohan.me
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  wporg
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

require_once(dirname(__FILE__).'/MCW_PWA.php');
MCW_PWA::instance();

require_once(dirname(__FILE__).'/MCW_PWA_Assets.php');
MCW_PWA_Assets::instance();

require_once(dirname(__FILE__).'/MCW_PWA_LazyLoad.php');
MCW_PWA_LazyLoad::instance();






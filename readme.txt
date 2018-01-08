=== Minimum Configuration WordPress PWA ===
Contributors: tyohan
Tags: minimum config, PWA, Workbox, performance, lazy loading, Progressive Web App, Accelerator Mobile Page, AMP
Requires at least: 4.6
Tested up to: 4.9.1
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin is developed to enhance the browsing experiences on WordPress site with minimum configuration. 

== Description ==

The plugin's current features:
*   use service worker to cache all static assets like images, JavaScripts, CSS styles, and fonts.
*   use Intersection Observer API to lazy load images on a page.
*   make scripts loaded with async if no dependency found and defer if it has a dependency like jQuery library. **This is a bit risky if themes or plugins not include the scripts with wp_enqueue_script function or not include dependency when they enqueue the script. Make sure you check there is no error on JavaScript console when you activate the plugin.**
*   install service worker if the website has AMP plugin running. AMP is a custom elements library that boost the website loading performance.

**This is still in beta, so please don't put it in productions until you know what you're doing.**

== Installation ==

The goal of this plugin is to give good loading experiences on WordPress site with minimum effort on installation. Once it's installed, by default, it will come with an optimal configuration to run.

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress

==Development==

1. Clone the repo to a directory inside your `wp-content/plugins` directory by running command in your terminal `git clone https://github.com/tyohan/mcw-pwa.git mcw-pwa`
2. Install NPM first because we need it to install Workbox library
3. Switch your terminal working directory to `mcw-pwa/scripts` and run command `npm install` to install the WorkBox library.
4. Activate the plugin inside your WordPress Admin Panel on plugin section.
5. Check with your Browser's DevTool to **make sure there is no JavaScript error in your JavaScript console.**

== Frequently Asked Questions ==

= What PWA features it has? =

Currently, it only provides service workers to cache static assets for better loading performance on repeat visits. Other features like offline access, push notification, and add to home screen haven't supported yet.

= Is it works on all browsers? =
Lazy loading images and critical resources loading enhancement works on all browsers.
For repeat visits optimizing, it's only working on browsers that support service workers. Currently, it's supported in Chrome, Firefox, Opera, and Samsung Internet for a stable release. Support for service workers on UC Browser is not full, also for Safari and Microsoft Edge currently only in Technology Preview version. 

== Credits ==

I use some plugins as my references to develop this plugin. Please check their amazing works 
* [BJ Lazy Load](https://wordpress.org/plugins/bj-lazy-load/)
* [Jetpack](https://wordpress.org/plugins/jetpack/) 
* [Accelerated Mobile Pages - AMP](https://wordpress.org/plugins/amp/)
=== Minimum Configuration WordPress PWA ===
Contributors: tyohan
Tags: minimum config, PWA, Workbox, performance, lazy loading, Progressive Web App, Accelerator Mobile Page, AMP
Requires at least: 4.6
Tested up to: 4.9.1
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin is developed to enhance the browsing experiences on WordPress site with minimum configuration. It's integrated with AMP page and make your website able to start FAST with AMP but stay FAST after on your original website.

== Description ==

This plugin's goal is to improve your WordPress website experiences with [Progressive Web App](https://developers.google.com/web/progressive-web-apps/) enhancement. Right now it will cache your static assets like scripts, stylesheets, images, and fonts. It's also boost your website loading performance especially on first time loading, and website with a lot of images. The main goal is to make sure your website not losing the user by make sure the website loading quickly. 

### Well Integrated with AMP Plugin
[AMP page](https://www.ampproject.org/about/benefits/) is very fast to load. But how to keep the fast experience on your website when user open non AMP pages. If your website open in AMP Page, this plugin will automatically prepare to boost performance of your non AMP pages by caching the static assets like JavaScripts and CSS, so it's will ready once you access your non AMP pages. Then when user click the logo or go to your main website without AMP, those static assets will ready to serve without request to server anymore. Currently only work with [Automattic AMP For WordPress](https://wordpress.org/plugins/amp/) plugin only.

### Work Well With All Performance Plugin
Add all static assets on precache settings like logo, CSS files, JavaScripts files, and fonts to precache list that your plugin generated to make sure it caches by service worker on loaded.

### Keep The Website Fast In All Pages
On first time user open your website, it will cache all the static assets like JavaScripts, CSS, fonts, and images that needed by all pages in your website.  Then later when user navigate to other pages, this static assets will serve directly from browser caches and boost the page loading. 

### Only Load Images That Shows On Screen
Images are nice to see on your website but will hurt performance if you have too many images in one page. On mobile, even these images not shows up on screen, its load and will slowing down the page loading performance. This plugin will detect the images that not shows up on screen and will not load it on first time you open the page. Later when you scroll the page, the plugin will detect if it's getting close to screen and load the image and ready to show once it appear on screen.


== Installation ==

The goal of this plugin is to give good loading experiences on WordPress site with minimum effort on installation. Once it's installed, by default, it will come with an optimal configuration to run.

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Setting precache assets by add your static assets like logo, CSS files, JavaScripts files, and fonts to precaches list to make sure it's precaches on first time your page loaded.


== Frequently Asked Questions ==

= What PWA features it has? =
It focus on performance for now. Well integrated with AMP plugin. It use service worker to cache static assets for better loading performance on repeat visits. Other features like offline access, push notification, and add to home screen haven't supported yet.

= Is it works on all browsers? =
Lazy loading images and critical resources loading enhancement works on all browsers.
For repeat visits optimizing, it's only working on browsers that support service workers. Currently, it's supported in Chrome, Firefox, Opera, and Samsung Internet for a stable release. Support for service workers on UC Browser is not full, also for Safari and Microsoft Edge currently only in Technology Preview version. 

== Credits ==

I use some plugins as my references to develop this plugin. Please check their amazing works
* [BJ Lazy Load](https://wordpress.org/plugins/bj-lazy-load/)
* [Jetpack](https://wordpress.org/plugins/jetpack/) 
* [Accelerated Mobile Pages - AMP](https://wordpress.org/plugins/amp/)
* [Workbox](https://developers.google.com/web/tools/workbox/)
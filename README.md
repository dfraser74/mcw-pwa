### WordPress PWA Plugin

This plugin is developed to focus on enhancing the loading experiences with minimum configuration. The plugin's current features:
* use service worker to cache all static assets like images, JavaScripts, CSS styles, and fonts.
* use Intersection Observer API to lazy load images on page.
* make scripts loaded with async if no dependency found and defer if it has dependency like jQuery library. **This is a bit risky if themes or plugins not include the scripts with wp_enqueue_script function or not include dependency when they enqueue the script. Make sure you check there is no error on JavaScript console when you activate the plugin.**
* install service worker if the website has AMP plugin running. 

**This is still in beta, so please don't put it in productions until you know what you're doing.**

## Setup
To install the plugin follow the steps

1. Clone the repo to a directory inside your `wp-content/plugins` directory by running command in your terminal `git clone https://github.com/tyohan/wpwa.git wpwa`
2. Install NPM first because we need it to install Workbox library
3. Switch your terminal working directory to `wpwa/scripts` and run command `npm install` to install the WorkBox library.
4. Activate the plugin inside your WordPress Admin Panel on plugin section.
5. Check with your Browser's DevTool to **make sure there is no JavaScript error in your JavaScript console.**
### WordPress PWA Plugin

This plugin is developed to focus on enhancing the loading experiences with minimum configuration. For now the method it use are:
* use service worker to cache all static assets like images, JavaScripts, CSS styles, and fonts.
* use Intersection Observer API to lazy load images on page.
* make scripts loaded with async if no dependency found and defer if it has dependency like jQuery library. This is a bit risky if themes or plugins not include the scripts with wp_enqueue_script function or not include dependency when they enqueue the script. Make sure you check there is no error on JavaScript console when you activate the plugin.
* install service worker if the website has AMP plugin running. 
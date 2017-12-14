<?php
class MCW_PWA {
    
    private static $__instance = null;
	/**
	 * Singleton implementation
	 *
	 * @return MCW_PWA instance
	 */
	public static function instance() {
		if ( ! is_a( self::$__instance, 'MCW_PWA' ) ) {
			self::$__instance = new MCW_PWA();
		}

		return self::$__instance;
	}

	private function __construct() {
        add_action('wp_print_footer_scripts', array($this,'registerSW'),1000);
        
        
    }
    
    public function registerAMPServiceworker(){
        // AMP support
		add_action( 'amp_post_template_head', array( $this, 'renderAMPSWScript' ) );
		add_action( 'amp_post_template_footer', array( $this, 'renderAMPSWElement' ) );
    }

    public function fixAmpServiceworker($tag,$handle){
        if($handle==='amp-install-serviceworker'){
            $tag= str_replace(' src', ' custom-element="amp-install-serviceworker" src', $tag);
        }
        return $tag;
    }

    public function renderAMPSWScript(){
        wp_register_script('mcw-amp-install-serviceworker','https://cdn.ampproject.org/v0/amp-install-serviceworker-0.1.js');
        add_filter('script_loader_tag', array($this,'fixAmpServiceworker'), 10, 2);
        wp_enqueue_script('mcw-amp-install-serviceworker');
    }

    public function renderAMPSWElement(){
        echo '<amp-install-serviceworker src="'.$this->getSWUrl().'" layout="nodisplay"></amp-install-serviceworker>';
    }
    
    private function getSWUrl(){
        return plugin_dir_url(__FILE__).'scripts/sw.js';
    }
    
    public function registerSW(){
        echo '
        <script>
        (async function() {
            
            if(!(\'serviceWorker\' in navigator)) {
              return;
            }
            navigator.serviceWorker.register(\''.$this->getSWUrl().'\');
            
            })();
        </script>';
    }

}
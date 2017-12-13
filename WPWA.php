<?php
class WPWA {
    
    private static $__instance = null;
	/**
	 * Singleton implementation
	 *
	 * @return object
	 */
	public static function instance() {
		if ( ! is_a( self::$__instance, 'WPWA' ) ) {
			self::$__instance = new WPWA();
		}

		return self::$__instance;
	}

	private function __construct() {
        add_action('wp_print_footer_scripts', array($this,'registerSW'),1000);
        
        // AMP support
		add_action( 'amp_post_template_head', array( $this, 'renderAMPSWScript' ) );
		add_action( 'amp_post_template_footer', array( $this, 'renderAMPSWElement' ) );
    }
    
    public function renderAMPSWScript(){
        echo '<script async custom-element="amp-install-serviceworker" src="https://cdn.ampproject.org/v0/amp-install-serviceworker-0.1.js"></script>';
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
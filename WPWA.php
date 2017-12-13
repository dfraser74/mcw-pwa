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
    }
    

    public function registerSW(){
        echo '
        <script>
        (async function() {
            
            if(!(\'serviceWorker\' in navigator)) {
              return;
            }
            navigator.serviceWorker.register(\''.plugin_dir_url(__FILE__).'scripts/sw.js\');
            
            })();
        </script>';
    }

}
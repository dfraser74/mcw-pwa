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
        add_action('wp_print_footer_scripts', array($this,'savePwaAssets'),999);
        add_action('wp_print_footer_scripts', array($this,'registerSW'),1000);
    }
    

    public function savePwaAssets(){
        $assets=$this->getPwaAssets();
        $pwaAssets=get_option('pwa_assets');
        if($pwaAssets===false)
            add_option('pwa_assets', $assets, null, false);
        else
            update_option('pwa_assets',$assets);
    }

    public function registerSW(){
        echo '
        <script>
        (async function() {
            
            if(!(\'serviceWorker\' in navigator)) {
              return;
            }
            navigator.serviceWorker.register(\''.plugin_dir_url(__FILE__).'scripts/sw.js\', {scope: \'/\'});
            
            })();
        </script>';
    }

    public function getPwaAssets(){
        $result = [];
        $result['scripts'] = [];
        $result['styles'] = [];
    
        // Print all loaded Scripts
        global $wp_scripts;
        $queued = $wp_scripts->all_deps( $wp_scripts->queue, false );
        
        foreach( $wp_scripts->to_do as $handle ) {
                $registration = $wp_scripts->registered[$handle];
                $result['scripts'][] =  $src;
        }

        foreach( $wp_scripts->queue as $script ) :
           $src=$wp_scripts->registered[$script]->src; 
           $result['scripts'][] =  $src;
        endforeach;
    
        // Print all loaded Styles (CSS)
        global $wp_styles;
        foreach( $wp_styles->queue as $style ) :
            $src=$wp_styles->registered[$style]->src;
            $result['styles'][] =  $src;
        endforeach;
        return $result;
    }

}
<?php
define( 'MCW_SW_QUERY_VAR', 'mcw_pwa_service_worker' );
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
        add_action( 'template_redirect', array( $this, 'renderSW' ), 2 );
        add_filter( 'query_vars', array( $this, 'registerQueryVar' ) );
        add_action( 'init', array( $this, 'registerRewriteRule' ) );
        
        //disable this for now until get response from AMP plugin team
        //$this->registerAMPServiceworker();
        
    }
    public function registerQueryVar( $vars ) {
		$vars[] = MCW_SW_QUERY_VAR;
		return $vars;
    }
    /* 
    //disable this for now until get response from AMP plugin team

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
    } */
    
    private function getSWUrl(){
        return add_query_arg( MCW_SW_QUERY_VAR, '1', trailingslashit( site_url() ) . 'index.php' );
    }

    public function registerRewriteRule() {
		add_rewrite_rule('sw.js$', 'index.php?' . MCW_SW_QUERY_VAR . '=1', 'top');
    }
    
    public function flushRewriteRules(){
        flush_rewrite_rules();
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

    public function renderSW() {
		global $wp_query;

		if ( $wp_query->get( MCW_SW_QUERY_VAR ) ) {
            header( 'Content-Type: application/javascript; charset=utf-8' );
            echo "importScripts('". MCW_PWA_URL ."scripts/node_modules/workbox-sw/build/importScripts/workbox-sw.dev.v2.1.2.js');";
			echo file_get_contents( MCW_PWA_DIR . 'scripts/sw.js' );
			exit;
		}
	}

}
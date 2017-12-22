<?php
define( 'MCW_SW_QUERY_VAR', 'mcw_pwa_service_worker' );
class MCW_PWA_Service_Worker {
    
    private static $__instance = null;
	/**
	 * Singleton implementation
	 *
	 * @return MCW_PWA instance
	 */
	public static function instance() {
		if ( ! is_a( self::$__instance, 'MCW_PWA_Service_Worker' ) ) {
			self::$__instance = new MCW_PWA_Service_Worker();
		}

		return self::$__instance;
	}

	private function __construct() {
        add_action('wp_print_footer_scripts', array($this,'registerSW'),1000);
        add_action( 'template_redirect', array( $this, 'renderSW' ), 2 );
        add_filter( 'query_vars', array( $this, 'registerQueryVar' ) );
        add_action( 'init', array( $this, 'registerRewriteRule' ) );
        
        //amp support
        add_action( 'amp_post_template_head', array( $this, 'renderAMPSWScript' ) );
        add_action( 'amp_post_template_footer', array( $this, 'renderAMPSWElement' ) );
    }
    public function registerQueryVar( $vars ) {
		$vars[] = MCW_SW_QUERY_VAR;
		return $vars;
    }

    public function renderAMPSWScript(){
        echo '<script custom-element="amp-install-serviceworker" src="https://cdn.ampproject.org/v0/amp-install-serviceworker-0.1.js"></script>';
    }

    public function renderAMPSWElement(){
        echo '<amp-install-serviceworker src="'.$this->getSWUrl().'" layout="nodisplay"></amp-install-serviceworker>';
    }
    
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
            echo "importScripts('". MCW_PWA_URL ."scripts/node_modules/workbox-sw/build/importScripts/workbox-sw.prod.v2.1.2.js');";
			echo file_get_contents( MCW_PWA_DIR . 'scripts/sw.js' );
			exit;
		}
	}

}
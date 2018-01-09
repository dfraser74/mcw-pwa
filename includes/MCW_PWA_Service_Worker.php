<?php
define( 'MCW_SW_QUERY_VAR', 'mcw_pwa_service_worker' );
require_once(MCW_PWA_DIR.'includes/MCW_PWA_Module.php');
class MCW_PWA_Service_Worker extends MCW_PWA_Module{
    
    private static $__instance = null;
	/**
	 * Singleton implementation
	 *
	 * @return MCW_PWA_Service_Worker instance
	 */
	public static function instance() {
		if ( ! is_a( self::$__instance, 'MCW_PWA_Service_Worker' ) ) {
			self::$__instance = new MCW_PWA_Service_Worker();
		}
		return self::$__instance;
	}

    protected function getKey(){
        return 'mcw_enable_service_workers';
    }

    public function initScripts(){
        add_action( 'wp_print_footer_scripts', array($this,'registerSW'),1000);
        add_action( 'template_redirect', array( $this, 'renderSW' ), 2 );
        add_filter( 'query_vars', array( $this, 'registerQueryVar' ) );
        add_action( 'init', array( $this, 'registerRewriteRule' ) );
        
        //amp support
        add_action( 'amp_post_template_head', array( $this, 'renderAMPSWScript' ) );
        add_action( 'amp_post_template_footer', array( $this, 'renderAMPSWElement' ) );
    }

    public function settingsApiInit() {
        register_setting( 'mcw_settings_service_workers', 'mcw_enable_service_workers', 
            array(
                'type'=>'boolean',
                'description'=>'Enable service workers',
                'default'=>true,
                //'sanitize_callback'=>array($this,'settingSanitize')
                )
        );
        
        // Add the section to reading settings so we can add our
        // fields to it
        add_settings_section(
            'mcw_settings_service_workers',
            'Service Workers',
            array($this,'sectionCallback'),
            'mcw_setting_page'
        );
        
        // Add the field with the names and function to use for our new
        // settings, put it in our new section
        add_settings_field(
            'mcw_enable_service_workers',
            'Enable service workers',
            array($this,'settingCallback'),
            'mcw_setting_page',
            'mcw_settings_service_workers'
        );
    } 
 

 
  
    // ------------------------------------------------------------------
    // Settings section callback function
    // ------------------------------------------------------------------
    //
    // This function is needed if we added a new section. This function 
    // will be run at the start of our section
    //
    
    public function sectionCallback() {
        echo '<p>You can disable the features by toggle the settings below:</p>';
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
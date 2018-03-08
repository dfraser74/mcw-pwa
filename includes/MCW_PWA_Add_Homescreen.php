<?php
define( 'MCW_MANIFEST_QUERY_VAR', 'mcw_pwa_manifest' );
require_once(MCW_PWA_DIR.'includes/MCW_PWA_Module.php');
class MCW_PWA_Add_Homescreen extends MCW_PWA_Module{
    private static $__instance = null;
	/**
	 * Singleton implementation
	 *
	 * @return MCW_PWA_Add_Homescreen instance
	 */
	public static function instance() {
		if ( ! is_a( self::$__instance, 'MCW_PWA_Add_Homescreen' ) ) {
			self::$__instance = new MCW_PWA_Add_Homescreen();
        }
        
		return self::$__instance;
	}
    protected function __construct() {
        parent::__construct();
        if($this->isEnable()){
            add_action( 'init', array( self::$__instance, 'registerRewriteRule' ) );
            add_action( 'template_redirect', array( $this, 'renderManifest' ), 2 );
            add_filter( 'query_vars', array( $this, 'registerQueryVar' ) );
            add_action('wp_head', array( $this, 'addLinkToHead' ) );
        }
    }

    public function addLinkToHead(){
        echo '<link rel="manifest" href="'.$this->getLinkUrl().'">';    
    }

    private function getLinkUrl(){
        return add_query_arg( MCW_MANIFEST_QUERY_VAR, '1', trailingslashit( site_url() ) . 'index.php' );
    }
    
    public function registerQueryVar( $vars ) {
		$vars[] = MCW_MANIFEST_QUERY_VAR;
		return $vars;
    }

    public function registerRewriteRule() {
		add_rewrite_rule('manifest.json$', 'index.php?' . MCW_MANIFEST_QUERY_VAR . '=1', 'top');
    }
    
    public function flushRewriteRules(){
        flush_rewrite_rules();
    }

    private function getManifestUrl(){
        return add_query_arg( MCW_MANIFEST_QUERY_VAR, '1', trailingslashit( site_url() ) . 'index.php' );
    }

    public function renderManifest(){
        global $wp_query;

		if ( $wp_query->get( MCW_MANIFEST_QUERY_VAR ) ) {
            header( 'Content-Type: application/manifest+json; charset=utf-8' );
			echo json_encode(array(
                "name"=> get_bloginfo('name'),
                "short_name"=> get_bloginfo('name'),
                "start_url"=> get_bloginfo('url'),
                "display"=> "standalone",
                "background_color"=> "#fff",
                "description"=> get_bloginfo('description')
            ));
			exit;
		}
    }

    public function getKey(){
        return 'mcw_enable_add_homescreen';
    }

}
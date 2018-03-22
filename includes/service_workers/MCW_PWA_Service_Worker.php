<?php
/*
Pending task:
    * Add static assets manually from setting page
    * Detect static assets from current page and add it to assets list

*/
define( 'MCW_SW_QUERY_VAR', 'mcw_pwa_service_worker' );
define( 'MCW_PWA_SW_PRECACHES','mcw_pwa_sw_precache');
define( 'MCW_PWA_SW_ASSETS','mcw_pwa_sw_assets');
define( 'MCW_OFFLINE_PAGE','mcw_pwa_sw_offline_page');

require_once(MCW_PWA_DIR.'includes/MCW_PWA_Module.php');

class MCW_PWA_Service_Worker extends MCW_PWA_Module{
    
    private static $__instance = null;
    private $_precaches;
    private $_assets;

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
    protected function __construct() {
        parent::__construct();
        if($this->isEnable()){
            add_action( 'init', array( $this, 'registerRewriteRule' ) );
            add_action( 'template_redirect', array( $this, 'renderSW' ), 2 );
            add_filter( 'query_vars', array( $this, 'registerQueryVar' ) );
        }
        
    }

    public function getKey(){
        return 'mcw_enable_service_workers';
    }

    public function initScripts(){
        add_action( 'wp_print_footer_scripts', array($this,'registerSW'),1000);
        //amp support
        add_action( 'amp_post_template_head', array( $this, 'renderAMPSWScript' ) );
        add_action( 'amp_post_template_footer', array( $this, 'renderAMPSWElement' ) );
    }

    public function settingsApiInit() {
        register_setting( MCW_PWA_OPTION, $this->getKey(), 
            array(
                'type'=>'boolean',
                'description'=>'Enable service workers',
                'default'=>1,
                //'sanitize_callback'=>array($this,'settingSanitize')
                )
        );
        
        
        // Add the field with the names and function to use for our new
        // settings, put it in our new section
        add_settings_field(
            $this->getKey(),
            'Enable Precaches And Dynamic Caches On Static Assets',
            array($this,'settingCallback'),
            MCW_PWA_SETTING_PAGE,
            MCW_SECTION_PWA
        );
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
    
    public function activate(){
        $this->scanAndAddAssets(true);
    }

    public function uninstall(){
        delete_option($this->getKey());
        delete_option(MCW_PWA_SW_PRECACHES);
        delete_option(MCW_PWA_SW_ASSETS);
        flush_rewrite_rules();
    }
    
    public function registerSW(){
        echo '
        <script>
        (async function() {
            
            if(!(\'serviceWorker\' in navigator)) {
              return;
            }
            window.addEventListener(\'load\', function() {
                navigator.serviceWorker.register(\''.$this->getSWUrl().'\');
            });
            
            
            })();
        </script>';
    }

    public function renderSW() {
		global $wp_query;

		if ( $wp_query->get( MCW_SW_QUERY_VAR ) ) {
            header( 'Content-Type: application/javascript; charset=utf-8' );
            echo "importScripts('". MCW_PWA_URL ."scripts/node_modules/workbox-sw/build/workbox-sw.js');";
            echo "self.addEventListener('install', () => self.skipWaiting());
            self.addEventListener('activate', () => self.clients.claim());
            workbox.setConfig({ debug: false });

            workbox.precaching.precacheAndRoute([".implode(',',$this->getPrecachesString())."]);\n";
            if(!empty($this->getOfflinePage())){
                echo "const matcher = ({event}) => event.request.mode === 'navigate';
                const handler = (obj) => fetch(obj.event.request).catch(() => caches.match('".$this->getOfflineUrl()."'));
                
                workbox.routing.registerRoute(matcher, handler);\n";
            }
            
            echo file_get_contents( MCW_PWA_DIR . 'scripts/sw.js' );
			exit;
		}
    }

    protected function getPrecachesString(){
        return array_map(function($file){
            return "'".$file."'";
        },$this->getAllPrecaches());
    }

    public function getPrecaches(){
        if($this->_precaches===null){
            $this->_precaches=get_option(MCW_PWA_SW_PRECACHES,[]);
        }
        return $this->_precaches;
    }
    public function getAllPrecaches(){
        $preaches=$this->getPrecaches();
        // if(MCW_PWA_Performance::instance()->isEnable()){
        //     $preaches=array_merge($preaches,$this->getBundleAssets());    
        // }

        if(!empty($this->getOfflinePage())){
            $preaches[]=$this->getOfflineUrl();
        }

        return $preaches;
    }
    
    protected function getBundleAssets(){
        $bundlesPath=MCW_PWA_Performance::instance()->getAssetsPath();
        $assets=[];
        $allowed =  array('js','css');

        if(is_dir($bundlesPath)){
            $files=scandir($bundlesPath);
            foreach($files as $file){
                $ext = pathinfo($bundlesPath.'/'.$file, PATHINFO_EXTENSION);
                if(in_array($ext,$allowed) ) {
                   $assets[]=MCW_PWA_URL.str_replace(MCW_PWA_DIR,'',$bundlesPath.'/'.$file);
                }
                
            }
        }
        return $assets;
    }

    public function savePrecaches(){
        update_option(MCW_PWA_SW_PRECACHES,$this->_precaches);
    }

    public function removeCache($url){
        if (($key = array_search($url, $this->_precaches)) !== false) {
            unset($_precaches[$key]);
        }
    }

    public function addPrecache($url){
        if (($key = array_search($url, $this->getPrecaches())) === false) {
           $this->_precaches[]=$url;
           return true;
        } else {
            return false;
        }
    }

    public function getAssets(){
        if($this->_assets===null){
            $this->_assets=get_option(MCW_PWA_SW_ASSETS,[]);
            if(!is_array($this->_assets))
                $this->_assets=[];
        }
        return $this->_assets;
    }

    public function scanAssets(){
        $request=wp_remote_get(str_replace('localhost',$_SERVER['REMOTE_ADDR'], home_url()));
        $html=wp_remote_retrieve_body($request);
        
        preg_match_all( '/<link\s*.+href=[\'|"]([^\'|"]+\.css?.+)[\'|"](.+)>/iU' , $html, $css_match );
        preg_match_all( '#<script[^>]+?src=[\'|"]([^\'|"]+\.js?.+)[\'|"].*>(?:<\/script>)#iU' , $html, $js_match );

        return array_merge($css_match[1],$js_match[1]);
    }

    public function addAsset($url){
        if (($key = array_search($url, $this->getPrecaches())) === false && ($key = array_search($url, $this->getAssets())) === false) {
            $this->_assets[]=$url;
            return true;
         } else {
             return false;
         }
    }

    public function saveAssets(){
        update_option(MCW_PWA_SW_ASSETS,$this->_assets);
    }

    public function scanAndAddAssets($precache=false){
        $assets=$this->scanAssets();
        foreach($assets as $asset){
            if($precache)
                $this->addPrecache($asset);
            else
                $this->addAsset($asset);
        }
    }
    public function getOfflinePage(){
        return get_option(MCW_OFFLINE_PAGE,null);
    }

    public function getOfflineUrl(){
        return get_page_link($this->getOfflinePage());
    }

    public function renderSettingOfflinePage(){
        if( isset($_POST['mcw_offline_page'])){
            $this->handleOfflineForm();
        } 
        include MCW_PWA_DIR.'includes/service_workers/MCW_PWA_Offline_Setting.php';
    }

    public function renderSettingCachePage(){
        if( isset($_POST['mcw_precaches']) || isset($_POST['mcw_assets'])){
            $this->handlePrecachesForm();
        } elseif(isset($_POST['mcw_action']) && $_POST['mcw_action']==='scan'){
            $this->scanAndAddAssets();
        }
        include MCW_PWA_DIR.'includes/service_workers/MCW_PWA_Precaches_Setting.php';
    }

    public function handleOfflineForm(){
        if(!check_admin_referer('mcw_offline')){ 
            echo '<div class="error">
                <p>Sorry, your nonce was not correct. Please try again.</p>
                </div>';
                exit;
        } else {
            
            if(!empty($_POST['mcw_offline_page'])){
                update_option(MCW_OFFLINE_PAGE,$_POST['mcw_offline_page']);
            }
            echo '<div class="notice notice-success is-dismissible"><p>The offline page already updated!</p></div>';

        }
    }

    public function handlePrecachesForm(){
        if(!check_admin_referer('mcw_precaches_update')){ 
            echo '<div class="error">
                <p>Sorry, your nonce was not correct. Please try again.</p>
                </div>';
                exit;
        } else {
            //save assets by default then add 
            $assets=$_POST['mcw_assets'];
            $this->_precaches=[];
            if(is_array($_POST['mcw_precaches'])){
                foreach ($_POST['mcw_precaches'] as $cache) {
                    $this->addPrecache($cache);
                }
                $this->savePrecaches();
                $assets=array_diff($assets,$this->getPrecaches());
            }
            
            foreach($assets as $asset){
                $this->addAsset($asset);
            }
            $this->saveAssets();
            echo '<div class="notice notice-success is-dismissible"><p>The precaches has been updated</p></div>';

        }
    }

}
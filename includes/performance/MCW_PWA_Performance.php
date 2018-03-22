<?php
use MatthiasMullie\Minify;
use bdk\CssXpath;

define( 'MCW_CACHE_PREFIX','mcw-');
define('MCW_CACHE_OPTION_KEY','mcw-caches');
require_once(MCW_PWA_DIR.'includes/MCW_PWA_Module.php');
class MCW_PWA_Performance extends MCW_PWA_Module{
	private static $__instance = null;
	private $_scripts=[];
    private $_styles=[];
    private $_currentUrl;
    private $_cacheKey;
    private $_outputBufferedSetting;
    protected $__enableByDefault=false;
    
    public static $_scriptPath='assets/temp';
    public static $_scriptName='bundle.js';
    public static $_styleName='bundle.css';

    
	/**
	 * Singleton implementation
	 *
	 * @return MCW_PWA_Performance instance
	 */
	public static function instance() {
		if ( ! is_a( self::$__instance, 'MCW_PWA_Performance' ) ) {
			self::$__instance = new MCW_PWA_Performance();
		}

		return self::$__instance;
	}

    public function isEnable(){
        $outputbuffering=$this->isSettingEnabled();
        $moduleEnabled=(boolean) get_option( $this->getKey(), $this->_enableByDefault )===true;
        return $moduleEnabled && $outputbuffering ;
    }

    public function isSettingEnabled(){
        if($this->_outputBufferedSetting===null){
            $this->_outputBufferedSetting=ini_get('output_buffering');
        
            if(is_string($this->_outputBufferedSetting)){
                if(strtolower($this->_outputBufferedSetting)==='off')
                    $this->_outputBufferedSetting=false;
            } elseif(is_numeric($this->_outputBufferedSetting)){
                if((int) $this->_outputBufferedSetting>0)
                    $this->_outputBufferedSetting=false;
                else
                    $this->_outputBufferedSetting=true;
            }

            if($this->_outputBufferedSetting===false){
                add_settings_error(
                    $this->getKey(),
                    esc_attr( 'php-setting-output-buffer' ),
                    __('PHP Output Buffering is off, please turn it on in your php.ini to enable this feature. '),
                    'error'
                );
            }
        }

        return $this->_outputBufferedSetting;
    }

    public function getKey(){
        return 'mcw_enable_performance';
    }

	public function __construct(){
        parent::__construct();
        add_action('save_post',array($this,'deletePostCache'));
        add_action('admin_post_mcw_cache_form',array($this,'handleCachesForm'));
    }

    public function initScript(){
        add_action('template_redirect',array($this,'run'));
    }

    public function deletePostCache($postID){
        $post=get_post($postID);
        $this->removeMainCaches();
        $taxonomies=get_object_taxonomies($post);
        foreach($taxonomies as $taxonomy=>$obj){
            $terms=get_terms($taxonomy);
            foreach($terms as $term){
                $this->removeTaxonomyCache($term->term_id);
            }
        }

        if(wp_is_post_revision( $post_id )){
            delete_transient(MCW_CACHE_PREFIX.'post-'.$post->ID);
        }
           
    }

    protected function removeTaxonomyCache($id){
        return delete_transient(MCW_CACHE_PREFIX.'term-'.$id);
    }

    public function getCachedPage($key){
        return false;
        return get_transient($key);
    }

    public function getCurrentCaches(){
        return get_option( MCW_CACHE_OPTION_KEY, [
            'last_update'=>$now,
            'caches'=>[]
        ]);
    }
    public function cachePage($key,$html){
        $now=time();
        $currentCaches=$this->getCurrentCaches();
        $expired=DAY_IN_SECONDS;
        $currentCaches['caches'][$key]=[
            'expired'=>$now+$expired,
            'url'=>$this->getCurrentUrl(),
            'last_update'=>$now
        ];

        update_option( MCW_CACHE_OPTION_KEY, $currentCaches );
        return set_transient($key,$html,$expired);
    }

    public function removeCache($key){
        $now=time();
        $currentCaches=get_option( MCW_CACHE_OPTION_KEY,null);
        if($currentCaches!==null){
            $caches=$currentCaches['caches'];
            unset($caches[$key]);
            delete_transient($key);
            $currentCaches['last_update']=$now;
            $currentCaches['caches']=$caches;
            
            update_option( MCW_CACHE_OPTION_KEY, $currentCaches );
        }
        
    }

    public function flushCache(){
        $caches=get_option( MCW_CACHE_OPTION_KEY);
        if(is_array($currentCaches['caches'])){
            foreach ($currentCaches['caches'] as $key => $value) {
                delete_transient($key);
            }
        }
        delete_option(MCW_CACHE_OPTION_KEY);
    }

    protected function getCurrentUrl(){
        if($this->_currentUrl===null){
            $permalinkStatus=get_option('permalink_structure');
            if(empty($permalinkStatus)){
                $this->_currentUrl="//" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            } else {
                $this->_currentUrl = home_url(add_query_arg(array(),$wp->request));
            }
        }
        return $this->_currentUrl;
    }
    
    public function removeMainCaches(){
        delete_transient(MCW_CACHE_PREFIX.'home');
        delete_transient(MCW_CACHE_PREFIX.'front');
        delete_transient(MCW_CACHE_PREFIX.'blog');
    }

    public function getCacheKeyOnRequest(){
        $queryObj=get_queried_object();
        if($this->_cacheKey===null){
            if(is_home()){
                $this->_cacheKey = MCW_CACHE_PREFIX.'home';
            } elseif(is_front_page()){
                $this->_cacheKey = MCW_CACHE_PREFIX.'front';
            } elseif(is_front_page() && is_home() ) {
                $this->_cacheKey = MCW_CACHE_PREFIX.'blog';
            } elseif ( is_category() || is_tag() || is_tax() ) {
                $this->_cacheKey=MCW_CACHE_PREFIX.'term-'.$queryObj->term_id;
            } elseif ( is_post_type_archive()  ) {
                $this->_cacheKey=MCW_CACHE_PREFIX.'posts-'.sanitize_key($queryObj->name);
            } elseif ( is_singular() ) {
                $this->_cacheKey=MCW_CACHE_PREFIX.'post-'.$queryObj->ID;
            } elseif ( is_author()  ) {
                $this->_cacheKey=MCW_CACHE_PREFIX.'author-'.$queryObj->ID;
            }
        }
        return $this->_cacheKey;
    }

	public function settingsApiInit() {
        $desc='Enable Combine, Minify, and Cache Output';
        if(!$this->isSettingEnabled()){
            $desc.=' (Can\'t enable this because your php.ini output buffering is off)';
        }
        
        register_setting( MCW_PWA_OPTION, $this->getKey(), 
        array(
                'type'=>'boolean',
                'description'=>$desc,
                'default'=>1,
                //'sanitize_callback'=>array($this,'settingSanitize')
                )
        );
        
        
        // Add the field with the names and function to use for our new
        // settings, put it in our new section
        add_settings_field(
            $this->getKey(),
            $desc,
            array($this,'settingCallback'),
            MCW_PWA_SETTING_PAGE,
            MCW_SECTION_PERFORMANCE
        );
    } 

    public function run(){
        if($this->isEnable())
            add_action('template_redirect',array($this,'renderPage'));
    }
    
    public function renderPage(){
        if(!is_admin() && !( $GLOBALS['pagenow'] === 'wp-login.php' ) && get_query_var( MCW_SW_QUERY_VAR, false )===false){
            $pageCached=$this->getCachedPage($this->getCacheKeyOnRequest());
            if($pageCached!==false){
                echo $pageCached;
                echo '<!-- cached version -->';
                die;
            } else {
                ob_start();
                
                add_filter('final_output', function($output) {
                    $output=$this->modifyBuffer($output);
                    $this->cachePage($this->getCacheKeyOnRequest(),$output);
                    $output.= '<!-- non cached version -->';
                    return $output;
                });
               // ob_start( array( $this, 'endBufferHtml' ) );
                add_action('shutdown',function(){
                    $final = '';
                    // We'll need to get the number of ob levels we're in, so that we can iterate over each, collecting
                    // that buffer's output into the final output.
                    $levels = ob_get_level();
            
                    for ($i = 0; $i < $levels; $i++) {
                        $final .= ob_get_clean();
                    }
                    // Apply any filters to the final output
                    echo apply_filters('final_output', $final);
                },0);
            }
            
        }
    }

	public function modifyBuffer($content) {        
                  return $this->optimizeAssets($content);
        // return Minify_HTML::minify( $this->optimizeAssets($content) );
    }

    protected function optimizeAssets($content){
        $cssRegex='/<link\s*.+href=[\'|"]([^\'|"]+\.css?.+)[\'|"](.+)>/iU';
        $jsRegex='#<script[^>]+?src=[\'|"]([^\'|"]+\.js?.+)[\'|"].*>(?:<\/script>)#iU';
        preg_match_all( $cssRegex , $content, $css_matches );
        preg_match_all( $jsRegex , $content, $js_matches );
        //remove all css and js files
        $content=preg_replace([$jsRegex,$cssRegex],'',$content);
            
        $combinedStyles=$this->combineAssetsContent($css_matches[1]);
        $combinedStyles=$this->optimizeCSS($combinedStyles,$content);
        $combinedStyles=$this->minify($combinedStyles,'css',MCW_PWA_DIR.$this->getStylePath());

        $bundleStyleUrl=MCW_PWA_URL.$this->getStylePath();
        $bundleStyleTag='<link rel="stylesheet" href="'.$bundleStyleUrl.'" type="text/css" >';
        

        $combinedScripts=$this->combineAssetsContent($js_matches[1]);
        $scriptPath=MCW_PWA_DIR.$this->getScriptPath();
        $combinedScripts=$this->minify($combinedScripts,'js',$scriptPath);
        $combinedScriptsUrl=$this->getBundledUrl($scriptPath);
        $bundleScriptTag='<script src="'.$combinedScriptsUrl.'" async></script>';
        
        $content=str_replace('</head>',$bundleStyleTag.$bundleScriptTag.'</head>',$content);
        return $content;
    }

    protected function addSWPrecache($url){
        require_once(MCW_PWA_DIR.'includes/MCW_PWA_Service_Worker.php');
        return MCW_PWA_Service_Worker::instance()->addToPrecache($url);
    }
    private function getScriptUrl(){
        return MCW_PWA_URL.self::$_scriptPath.'/'.self::$_scriptName;
    }

    public function getAssetsPath(){
        $path=MCW_PWA_DIR.self::$_scriptPath;
        if(!is_dir($path)){
            mkdir($path);
        }
        return $path;
    }
    private function getStylePath(){
        $path=$this->getAssetsPath;
        return self::$_scriptPath.'/'.self::$_styleName;
    }

    public function getScriptPath(){
        $path=$this->getAssetsPath();
        return self::$_scriptPath.'/'.self::$_scriptName;
    }

    protected function minify($combinedAssets,$type,$saveToPath=null){
        if($type==='css'){
            $minifier = new Minify\CSS();
            $minifier->setMaxImportSize(100);
            $minifier->setImportExtensions(array(
                'woff' => 'application/font-woff',
                'ttf' => 'application/font-ttf',
                'eot' => 'application/vnd.ms-fontobject',
                'otf' => 'application/font-otf',
                'svg' => 'image/svg+xml',
            ));
        } else {
            $minifier = new Minify\JS();
        }
        $minifier->add($combinedAssets);
        if($saveToPath!==null)
            return $minifier->minify($saveToPath);
        else
            return $minifier->minify();
    }

    protected function getBundledUrl($path){
        return MCW_PWA_URL.str_replace(MCW_PWA_DIR,'',$path);
    }

    protected function combineAssetsContent($assets){
        $combinedAssets='';
        foreach ($assets as $url) {
            $path=str_replace(site_url(),'',$url);
            if(strpos($path,'http')===false){
                $path=ABSPATH.$path;
            }
            $combinedAssets.= file_get_contents($path);
        }
        return $combinedAssets;
    }

    
    protected function optimizeCSS($css,$html){
        $cssParser=new Sabberworm\CSS\Parser($css);
        $styles = $cssParser->parse();
        foreach($styles->getAllRuleSets() as $oRuleSet) {
            if($oRuleSet instanceof Sabberworm\CSS\RuleSet\AtRuleSet){
                
                if($oRuleSet->atRuleName()==='font-face'){
                    $fontDisplay=new Sabberworm\CSS\Rule\Rule('font-display');
                    $fontDisplay->setValue(new Sabberworm\CSS\Value\CSSString('fallback',0,false));
                    $oRuleSet->addRule($fontDisplay);
                }
            } 
            //skip this to inline style
            // else if($oRuleSet instanceof Sabberworm\CSS\RuleSet\DeclarationBlock){
                
            //     foreach ($oRuleSet->getSelectors() as $selectors) {
            //         $ruleUsed=false;
            //         foreach($selectors as $selector){
            //             $query=\bdk\CssXpath\CssSelect::select($html,$selector);
            //             if(count($query)>0){
            //                 $ruleUsed=true;
            //                 break;
            //             } else {
            //                // echo $selector;
            //                 //$oRuleSet->removeSelector($selector);die();
            //             }
            //         }
            //         if($ruleUsed===false){
            //             $styles->remove($oRuleSet);
            //         }
            //     }
            // }
        }
        return $styles->render();
    }
    
    public function renderSettingCachePage(){
        
        echo '<h2>Cache Management</h2>';
        echo '<p> Below are all the caches from your website. You can delete each cache or just flush all caches.</p>';
        if( isset($_POST['mcw_caches'])){
            $this->handleCachesForm();
        }
        include MCW_PWA_DIR.'includes/performance/MCW_PWA_Cache_Setting.php';
    }

    public function handleCachesForm(){
        if(!check_admin_referer('mcw_caches_update')){ 
            echo '<div class="error">
                <p>Sorry, your nonce was not correct. Please try again.</p>
                </div>';
                exit;
        } else {
            foreach ($_POST['mcw_caches'] as $key => $cache) {
                $this->removeCache($key);
            }
            echo '<div class="notice notice-success is-dismissible"><p>The caches has been updated</p></div>';

        }
        
        
    }

    public function deactivate(){
        $this->flushCache();
    }
    
    
}
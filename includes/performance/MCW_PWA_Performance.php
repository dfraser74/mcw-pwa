<?php
require_once(MCW_PWA_DIR.'includes/MCW_PWA_Module.php');
class MCW_PWA_Performance extends MCW_PWA_Module{
	private static $__instance = null;
	private $_scripts=[];
    private $_styles=[];
    
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

    public function getKey(){
        return 'mcw_enable_performance';
    }

	public function __construct(){
        parent::__construct();
        add_action('template_redirect',array($this,'run'));
            
    }

    public function run(){
        if($this->isEnable() && !is_admin() && !( $GLOBALS['pagenow'] === 'wp-login.php' ) && get_query_var( MCW_SW_QUERY_VAR, false )===false){
            ob_start();
            add_filter('final_output', function($output) {
                if(!is_file(MCW_PWA_DIR.$this->getStylePath() && !is_file(MCW_PWA_DIR.$this->getScriptPath())))
                    $output=$this->modifyBuffer($output);
                return $output;
            });
            
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

	public function settingsApiInit() {
        register_setting( MCW_PWA_OPTION, $this->getKey(), 
        array(
                'type'=>'boolean',
                'description'=>'Combine, Minify, and Cache Output',
                'default'=>1,
                //'sanitize_callback'=>array($this,'settingSanitize')
                )
        );
        
        
        // Add the field with the names and function to use for our new
        // settings, put it in our new section
        add_settings_field(
            $this->getKey(),
            'Enable Combine, Minify, and Cache Output',
            array($this,'settingCallback'),
            MCW_PWA_SETTING_PAGE,
            MCW_SECTION_PERFORMANCE
        );
    } 

	public function modifyBuffer( $content ) {
		if ( ! class_exists( 'Minify_HTML' ) ) {
			require_once dirname( __FILE__ ) . '/libs/HTML.php';
        }

        $content=$this->combineAssets($content);
        return $content;
        //return Minify_HTML::minify( $content );
    }

    protected function combineAssets($content){
        // do combining and replace scripts with one combined asset for JS and CSS
        $document = new DOMDocument();
        // Ensure UTF-8 is respected by using 'mb_convert_encoding'
        //error_reporting(E_ERROR);
        libxml_use_internal_errors(true);
        $document->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        $doc=$document->documentElement;
        
        //combine styles
        $tags = $doc->getElementsByTagName('link');
        
        $styleSources=[];
        $length=$tags->length;
        for ($i=$length; --$i >= 0;) { 
            $tag= $tags->item($i);
            if($tag->getAttribute('rel')==='stylesheet'){
                $tag=$tags->item($i);
                $src=$tag->getAttribute('href');
                if(!empty($src)){
                    array_unshift($styleSources,$src);
                }
                $tag->parentNode->removeChild($tag);
            }
            
        }
        
        $combinedStylesUrl=$this->combineAssetsContent($styleSources,MCW_PWA_DIR.$this->getStylePath());
        $this->addSWPrecache($combinedStylesUrl);
        //add style to head
        $bundleStyle=$document->createElement('link');
        
        $bundleStyle->setAttribute('rel','stylesheet');
        $bundleStyle->setAttribute('href',$combinedStylesUrl);
        $head=$doc->getElementsByTagName('head')->item(0);
        $head->appendChild($bundleStyle);
        

        //combine scripts
        $tags = $doc->getElementsByTagName('script');
        
        $scriptSources=[];
        $length=$tags->length;
        for ($i=$length; --$i >= 0;) { 
            $tag=$tags->item($i);
            $src=$tag->getAttribute('src');
            if(!empty($src)){
                array_unshift($scriptSources,$src);
                $tag->parentNode->removeChild($tag);
            } 
            
        }
        
        $combinedScriptsUrl=$this->combineAssetsContent($scriptSources,MCW_PWA_DIR.$this->getScriptPath());
        $this->addSWPrecache($combinedScriptsUrl);
        //preload bundle script
        $preload=$document->createElement('link');
        $preload->setAttribute('rel','preload');
        $preload->setAttribute('as','script');
        $preload->setAttribute('href',$combinedScriptsUrl);
        $head->insertBefore($preload,$head->firstChild);

        //add script to bottom body
        $bundleScript=$document->createElement('script','');
        $bundleScript->setAttribute('src',$combinedScriptsUrl);
        
        $bundleScript->setAttribute('async','true');
        $head->appendChild($bundleScript);
        
        return $document->saveHTML();
    }

    protected function addSWPrecache($url){
        require_once(MCW_PWA_DIR.'includes/MCW_PWA_Service_Worker.php');
        return MCW_PWA_Service_Worker::instance()->addToPrecache($url);
    }
    private function getScriptUrl(){
        return MCW_PWA_URL.self::$_scriptPath.'/'.self::$_scriptName;
    }

    private function getAssetsPath(){
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

    protected function combineAssetsContent($assets,$saveToPath){
        $combinedAssets='';
        foreach ($assets as $url) {
            $path=str_replace(site_url(),'',$url);
            if(strpos($path,'http')===false){
                $path=ABSPATH.$path;
            }
            $combinedAssets.= file_get_contents($path);
        }
        file_put_contents($saveToPath,$combinedAssets);
        return MCW_PWA_URL.str_replace(MCW_PWA_DIR,'',$saveToPath);
    }

    
    
}
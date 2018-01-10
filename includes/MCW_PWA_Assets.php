<?php
require_once(MCW_PWA_DIR.'includes/MCW_PWA_Module.php');
class MCW_PWA_Assets extends MCW_PWA_Module{
	private static $__instance = null;
	private $_scripts=[];
	private $_styles=[];

	/**
	 * Singleton implementation
	 *
	 * @return MCW_PWA_Assets instance
	 */
	public static function instance() {
		if ( ! is_a( self::$__instance, 'MCW_PWA_Assets' ) ) {
			self::$__instance = new MCW_PWA_Assets();
		}

		return self::$__instance;
	}

    public function getKey(){
        return 'mcw_enable_assets';
    }

	public function initScripts(){
		if(! is_admin()){
			add_filter('script_loader_tag', array($this,'addDeferAsyncAttribute'), 10, 2);
			// Remove WP Version From Styles	
			add_filter( 'style_loader_src', array($this,'removeVersion'), 9999,1 );
			// Remove WP Version From Scripts
			add_filter( 'script_loader_src', array($this,'removeVersion'), 9999,1 );
		}
	}

	public function settingsApiInit() {
        register_setting( MCW_PWA_OPTION, $this->getKey(), 
        array(
                'type'=>'boolean',
                'description'=>'Enable Async Defer Assets Loading',
                'default'=>1,
                //'sanitize_callback'=>array($this,'settingSanitize')
                )
        );
        
        
        // Add the field with the names and function to use for our new
        // settings, put it in our new section
        add_settings_field(
            $this->getKey(),
            'Enable Async Defer ',
            array($this,'settingCallback'),
            MCW_PWA_SETTING_PAGE,
            MCW_SECTION_PERFORMANCE
        );
    } 
	
	public function removeVersion($src){
		// Function to remove version numbers
		if ( strpos( $src, 'ver=' ) )
			$src = remove_query_arg( 'ver', $src );
		return $src;
	}

	private function scriptHasDepts( $handle ) {
        global $wp_scripts;

				foreach ( $wp_scripts->to_do as $other_script_handle ) {
                        $other_script = $wp_scripts->registered[ $other_script_handle ];
                        
						if (($other_script->deps!==null) && ( in_array( $handle, $other_script->deps ) || ($handle===$other_script_handle && count($other_script->deps)!==0))) {
				            return true;
						}
				}

		return false;
    }
    
    protected function shouldDefer($handle){
        if($this->scriptHasDepts($handle)){
            return true;
        }
        return false;
    }

    public function addDeferAsyncAttribute($tag,$handle){
        if($this->shouldDefer($handle)){
            if(strpos($tag,'defer')===FALSE)
                return str_replace(' src', ' defer="defer" src', $tag);  
        } 
        if(strpos($tag,'async')===FALSE)
            return str_replace(' src', ' async="async" src', $tag);   
        
        return $tag;
    }
}
<?php

class MCW_PWA_Assets{
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

	protected function __construct() {
		add_action( 'admin_init', array($this,'settingsApiInit' ));
    }

	public function initLoader(){
		if(! is_admin()){
			add_filter('script_loader_tag', array($this,'addDeferAsyncAttribute'), 10, 2);
			// Remove WP Version From Styles	
			add_filter( 'style_loader_src', array($this,'removeVersion'), 9999,1 );
			// Remove WP Version From Scripts
			add_filter( 'script_loader_src', array($this,'removeVersion'), 9999,1 );
		}
	}

	public function settingsApiInit() {
        // Add the section to reading settings so we can add our
        // fields to it
        add_settings_section(
            'mcw_settings_assets',
            'Async Defer Resources',
            array($this,'sectionCallback'),
            'mcw_setting_page'
        );
        
        // Add the field with the names and function to use for our new
        // settings, put it in our new section
        add_settings_field(
            'mcw_enable_assets',
            'Enable Async and Defer Loading',
            array($this,'settingCallback'),
            'mcw_setting_page',
            'mcw_settings_assets'
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
        echo '<p>By enable this async defer feature, you can boost your loading performance by optimize your critical resources.</p>';
    }
    
    // ------------------------------------------------------------------
    // Callback function for our example setting
    // ------------------------------------------------------------------
    //
    // creates a checkbox true/false option. Other types are surely possible
    //
    
    public function settingCallback() {
        echo '<input name="mcw_assets" id="mcw_assets" type="checkbox" value="1" class="code" ' . checked( 1, get_option( 'mcw_assets' ), true ) . ' /> Enable';
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
            
            return str_replace(' src', ' defer="defer" src', $tag);  
        } 
        return str_replace(' src', ' async="async" src', $tag);   
          
    }
}
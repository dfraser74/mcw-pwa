<?php
use MatthiasMullie\Minify;

class WPWAAssets{
	private static $__instance = null;
	private $_scripts=[];
	private $_styles=[];

	/**
	 * Singleton implementation
	 *
	 * @return object
	 */
	public static function instance() {
		if ( ! is_a( self::$__instance, 'WPWAAssets' ) ) {
			self::$__instance = new WPWAAssets();
		}

		return self::$__instance;
	}

	protected function __construct() {
		add_filter('script_loader_tag', array($this,'addDeferAsyncAttribute'), 10, 2);
		// Remove WP Version From Styles	
		add_filter( 'style_loader_src', array($this,'removeVersion'), 9999,1 );
		// Remove WP Version From Scripts
		add_filter( 'script_loader_src', array($this,'removeVersion'), 9999,1 );
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
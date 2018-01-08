<?php
class MCW_PWA_LazyLoad {
    private static $__instance = null;
	/**
	 * Singleton implementation
	 *
	 * @return MCW_PWA_LazyLoad instance
	 */
	public static function instance() {
		if ( ! is_a( self::$__instance, 'MCW_PWA_LazyLoad' ) ) {
			self::$__instance = new MCW_PWA_LazyLoad();
		}

		return self::$__instance;
    }
    
    protected function __construct(){
        add_action( 'admin_init', array($this,'settingsApiInit' ));
    }

    public function initScripts(){
            add_action('wp_print_header_scripts', array($this,'addPolyfil'),999);
            $this->filterLazyLoadImages();
            wp_enqueue_script( 'wpwa_lazyload', MCW_PWA_URL. 'scripts/lazyload.js');
    }

    public function settingsApiInit() {
        // Add the section to reading settings so we can add our
        // fields to it
        add_settings_section(
            'mcw_settings_lazy_load',
            'Lazy Loading',
            array($this,'sectionCallback'),
            'mcw_setting_page'
        );
        
        // Add the field with the names and function to use for our new
        // settings, put it in our new section
        add_settings_field(
            'mcw_enable_lazy_load',
            'Enable Lazy Load',
            array($this,'settingCallback'),
            'mcw_setting_page',
            'mcw_settings_lazy_load'
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
        echo '<p>By turn on this feature all images will be loaded if it shows on screen. Recommended to turn on this feature if you have a lot of images.</p>';
    }
    
    // ------------------------------------------------------------------
    // Callback function for our example setting
    // ------------------------------------------------------------------
    //
    // creates a checkbox true/false option. Other types are surely possible
    //
    
    public function settingCallback() {
        echo '<input name="mcw_lazy_load" id="mcw_lazy_load" type="checkbox" value="1" class="code" ' . checked( 1, get_option( 'mcw_lazy_load' ), true ) . ' /> Enable Lazy Load';
    }

    public function lazyloadImages($html) {
        $matches = array();
        
        preg_match_all( '/<img[\s\r\n]+.*?>/is', $html, $matches );
        
        $search = array();
        $replace = array();
    
        foreach ( $matches[0] as $imgHTML ) {
            
            // don't do the replacement if the image is a data-uri
            if ( ! preg_match( "/src=['\"]data:image/is", $imgHTML ) ) {
                $placeholder_url_used = 'data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==';
        
                if( preg_match( '/class=["\'].*?wp-image-([0-9]*)/is', $imgHTML, $id_matches ) ) {
                    $img_id = intval($id_matches[1]);
                    
                }
    
                // replace the src and add the data-src attribute
                $replaceHTML = preg_replace( '/<img(.*?)src=/is', '<img$1src="' . esc_attr( $placeholder_url_used ) . '" data-lazy-type="image" data-lazy-src=', $imgHTML );
                
                // also replace the srcset (responsive images)
                $replaceHTML = str_replace( 'srcset', 'data-lazy-srcset', $replaceHTML );
                // replace sizes to avoid w3c errors for missing srcset
                $replaceHTML = str_replace( 'sizes', 'data-lazy-sizes', $replaceHTML );
                
                // add the lazy class to the img element
                if ( preg_match( '/class=["\']/i', $replaceHTML ) ) {
                    $replaceHTML = preg_replace( '/class=(["\'])(.*?)["\']/is', 'class=$1lazy lazy-hidden $2$1', $replaceHTML );
                } else {
                    $replaceHTML = preg_replace( '/<img/is', '<img class="lazy lazy-hidden"', $replaceHTML );
                }
                
                $replaceHTML .= '<noscript>' . $imgHTML . '</noscript>';
                
                array_push( $search, $imgHTML );
                array_push( $replace, $replaceHTML );
            }
        }
    
        $html = str_replace( $search, $replace, $html );
    
        return $html;
      }
      
      
      protected function filterLazyLoadImages(){
        add_filter('the_content', array($this,'lazyloadImages') );
        add_filter('post_thumbnail_html',array($this,'lazyloadImages'));
        add_filter( 'get_avatar', array( $this, 'lazyloadImages' ), 11 );
      }

      public function addPolyfil(){
          echo '
          <script>
          if (!(IntersectionObserver in window)) {
            let polyfil=document.createElement(\'script\');
            polyfil.setAttribute(\'src\',\''.MCW_PWA_URL.'scripts/intersection-observer.js\');
            document.head.appendChild(polyfil);
          }         
          </script>
          ';
      }
}

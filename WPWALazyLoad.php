<?php

class WPWALazyLoad {
    private static $__instance = null;
	/**
	 * Singleton implementation
	 *
	 * @return object
	 */
	public static function instance() {
		if ( ! is_a( self::$__instance, 'WPWALazyLoad' ) ) {
			self::$__instance = new WPWALazyLoad();
		}

		return self::$__instance;
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

      private function __construct(){
        add_action('wp_print_header_scripts', array($this,'addPolyfil'),999);
        $this->filterLazyLoadImages();
        wp_enqueue_script( 'wpwa_lazyload', plugin_dir_url(__FILE__). 'scripts/lazyload.js');
      }

      public function addPolyfil(){
          echo '
          <script>
          if (!(IntersectionObserver in window)) {
            let polyfil=document.createElement(\'script\');
            polyfil.setAttribute(\'src\',\''.plugin_dir_url(__FILE__).'scripts/intersection-observer.js\');
            document.head.appendChild(polyfil);
          }         
          </script>
          ';
      }
}

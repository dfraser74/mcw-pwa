<?php
use MatthiasMullie\Minify;

class MCW_PWA_CSS_Optimizer{

    /**
	 * Singleton implementation
	 *
	 * @return MCW_PWA_CSS_Optimizer instance
	 */
	public static function instance() {
		if ( ! is_a( self::$__instance, 'MCW_PWA_CSS_Optimizer' ) ) {
			self::$__instance = new MCW_PWA_CSS_Optimizer();
		}

		return self::$__instance;
	}

}
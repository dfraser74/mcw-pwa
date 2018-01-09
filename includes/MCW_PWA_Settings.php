<?php
class MCW_PWA_Settings {

    private static $__instance = null;
	/**
	 * Singleton implementation
	 *
	 * @return MCW_PWA_Settings instance
	 */
	public static function instance() {
		if ( ! is_a( self::$__instance, 'MCW_PWA_Settings' ) ) {
			self::$__instance = new MCW_PWA_Settings();
		}

		return self::$__instance;
	}

	protected function __construct() {
        add_action( 'admin_menu', array($this,'addSettingMenu'));
    }

    
    
    public function addSettingMenu(){
        add_options_page(
            'Setting for Minimum Configuration PWA', 
            'MCW PWA Settings',
            'manage_options', 
            'mcw_setting_page', 
            array( $this, 'renderSettingPage' )
        );
    }

    public function renderSettingPage(){
        // check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
            
        // add error/update messages
        
        // check if the user have submitted the settings
        // wordpress will add the "settings-updated" $_GET parameter to the url
        if ( isset( $_GET['settings-updated'] ) ) {
        // add settings saved message with the class of "updated"
            //add_settings_error( 'mcw_messages', 'mcw_messages', __( 'Settings Saved', 'mcw' ), 'updated' );
            
        }
        ?>
        <div class="wrap">
            <h1>Minimum Configuration WordPress PWA Settings</h1>
            <?php 
                // show error/update messages
                settings_errors( 'mcw_messages' );
            ?>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'mcw_settings_assets' );
                settings_fields( 'mcw_settings_lazy_load' );
                settings_fields( 'mcw_settings_service_workers' );
                do_settings_sections( 'mcw_setting_page' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }
    
  
    // ------------------------------------------------------------------
    // Settings section callback function
    // ------------------------------------------------------------------
    //
    // This function is needed if we added a new section. This function 
    // will be run at the start of our section
    //
    
    public function sectionCallback() {
        echo '<p>You can disable the features by toggle the settings below:</p>';
    }
}
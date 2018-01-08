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

	private function __construct() {
        add_action( 'admin_init', array($this,'settingsApiInit' ));
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
            add_settings_error( 'wporg_messages', 'wporg_message', __( 'Settings Saved', 'wporg' ), 'updated' );
        }
            
        

        // Set class property
        $this->options = get_option( 'mcw_options' );
        ?>
        <div class="wrap">
            <h1>Minimum Configuration WordPress PWA Settings</h1>
            <?php 
                // show error/update messages
                settings_errors( 'wporg_messages' );
            ?>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'mcw_options' );
                do_settings_sections( 'mcw_setting_page' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }
    public function sanitizeOptions(){

    }

    public function settingsApiInit() {
            
        // Register our setting so that $_POST handling is done for us and
        // our callback function just has to echo the <input>
        register_setting( 'mcw_settings_admin', 'mcw_setting_name',array( $this, 'sanitizeOptions' ) );
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
    
    // ------------------------------------------------------------------
    // Callback function for our example setting
    // ------------------------------------------------------------------
    //
    // creates a checkbox true/false option. Other types are surely possible
    //
    
    public function settingCallback() {
        echo '<input name="mcw_service_worker" id="mcw_service_worker" type="checkbox" value="1" class="code" ' . checked( 1, get_option( 'mcw_service_worker' ), false ) . ' /> Enable Service Workers';
    }
}
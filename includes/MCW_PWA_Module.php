<?php 
abstract class MCW_PWA_Module{
    abstract protected function getKey();

    public function settingSanitize($input){
       
        return $input;
    }

    protected function __construct() {
		add_action( 'admin_init', array($this,'settingsApiInit' ));
    }

    public function settingCallback() {
        var_dump(get_option($this->getKey()));
        if(get_option( $this->getKey())){
            echo '<input name="'.$this->getKey().'" id="'.$this->getKey().'" type="checkbox" value="1" class="code" checked/> Enable';
        } else {
            echo '<input name="'.$this->getKey().'" id="'.$this->getKey().'" type="checkbox" value="1" class="code"/> Enable';
        }
        
    }
}
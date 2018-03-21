<?php 
abstract class MCW_PWA_Module{
    protected $_enableByDefault=true;

    abstract public function getKey();

    public function settingSanitize($input){
       
        
        return $input;
    }

    protected function __construct() {
		add_action( 'admin_init', array($this,'settingsApiInit' ));
    }

    public function run(){
        if($this->isEnable())
            $this->initScripts();
    }

    protected function settingsApiInit(){

    }
    
    public function initScript(){
        //won't do anything unless overide 
    }

    public function settingCallback() {
        if(!$this->isSettingEnabled()){
            echo '<input name="'.$this->getKey().'" id="'.$this->getKey().'" type="checkbox" value="1" class="code" disabled/> Enable';
        }
        else{
            if(get_option( $this->getKey())){
                echo '<input name="'.$this->getKey().'" id="'.$this->getKey().'" type="checkbox" value="1" class="code" checked/> Enable';
            } else {
                echo '<input name="'.$this->getKey().'" id="'.$this->getKey().'" type="checkbox" value="1" class="code"/> Enable';
            }
        }
        
    }

    public function isSettingEnabled(){
        return true;
    }

    public function getErrorMessage(){
        return $this->_errorMessage;
    }

    public function isEnable(){
        return (boolean) get_option( $this->getKey(), $this->_enableByDefault )===true;
    }

    public static function debug($msg){
        echo '<script>console.log('.$msg.');</script>';
    }

    public function deactivate(){
       
    }

    public function uninstall(){
        delete_option($this->getKey());
    }
}
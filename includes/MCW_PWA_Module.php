<?php 
abstract class MCW_PWA_Module{
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
        
        if(get_option( $this->getKey())){
            echo '<input name="'.$this->getKey().'" id="'.$this->getKey().'" type="checkbox" value="1" class="code" checked/> Enable';
        } else {
            echo '<input name="'.$this->getKey().'" id="'.$this->getKey().'" type="checkbox" value="1" class="code"/> Enable';
        }
        
    }

    public function isEnable(){
        return (int) get_option( $this->getKey(), 1 )===1;
    }

    public static function debug($msg){
        echo '<script>console.log('.$msg.');</script>';
    }
}
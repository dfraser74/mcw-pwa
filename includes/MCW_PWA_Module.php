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

<<<<<<< HEAD
    protected function settingsApiInit(){

    }
    
    protected function initScript(){
        //won't do anything unless overide 

    }

=======
>>>>>>> 625d923cc4162820d864e5fcac341a133ae12605
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
<<<<<<< HEAD

    public static function debug($msg){
        echo '<script>console.log('.$msg.');</script>';
    }
=======
>>>>>>> 625d923cc4162820d864e5fcac341a133ae12605
}
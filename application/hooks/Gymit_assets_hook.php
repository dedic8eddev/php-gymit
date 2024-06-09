<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Gymit_Assets_Hook{
    private $ci;
    
    public function __construct(){
        $this->ci =& get_instance();
        $this->ci->appAssets = array();
        $this->ci->appAssets['js'] = array();
        $this->ci->appAssets['css'] = array();
    }

    public function assets(){
        $this->ci->app->assets(config_item('app')[$this->ci->__appEnv]['assets']['css'],'css');
        $this->ci->app->assets(config_item('app')[$this->ci->__appEnv]['assets']['js'],'js');
    }
}

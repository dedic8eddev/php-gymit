<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Gymit_Blocks{

    protected $ci;

    public function __construct(){
        $this->ci =& get_instance();
    }

    public function news($articles, $options = []){
        $d = [];
        $d['options'] = $options;
        $d['articles'] = $articles;

        $this->ci->load->view('frontend/blocks/news', $d);
    }

    public function pinnedNews($articles, $options = []){
        $d = [];
        $d['options'] = $options;
        $d['articles'] = $articles;

        $this->ci->load->view('frontend/blocks/pinned_news', $d);
    }    

    public function services($services, $options = []){
        $d = [];
        $d['options'] = $options;
        $d['services'] = $services;

        $this->ci->load->view('frontend/blocks/services', $d);
    }

    public function membership($membership, $options = []){
        $d = [];
        $d['options'] = $options;
        $d['membership'] = $membership;

        $this->ci->load->view('frontend/blocks/membership', $d);
    }

    public function coaches($coaches, $options = []){
        $d = [];
        $d['options'] = $options;
        $d['coaches'] = $coaches;

        $this->ci->load->view('frontend/blocks/coaches', $d);
    }

    public function lesson_calendar($lessons, $options = []){
        $d = [];
        $d['options'] = $options;
        $d['lessons'] = $lessons;

        $this->ci->load->view('frontend/blocks/lesson_calendar', $d);
    }


    public function hero_slider($options = []){
        $d = [];
        $d['options'] = $options;
        $this->ci->load->view('frontend/blocks/hero_slider', $d);
    }

    public function free_entry_cta($options = []){
        $d = [];
        $d['options'] = $options;
        $this->ci->load->view('frontend/blocks/free_entry_cta', $d);
    }

    public function instagram($options = []){
        $d = [];
        $d['options'] = $options;
        $this->ci->load->view('frontend/blocks/instagram', $d);
    }

    public function newsletter($options = []){
        $d = [];
        $d['options'] = $options;
        $this->ci->load->view('frontend/blocks/newsletter', $d);
    }

    public function contact($options = []){
        $d = [];
        $d['options'] = $options;
        $this->ci->load->view('frontend/blocks/contact', $d);
    }   
    
    public function equipment($options = []){
        $d = [];
        $d['options'] = $options;
        $this->ci->load->view('frontend/blocks/equipment', $d);
    }
    
    public function map($options = []){
        $d = [];
        $d['options'] = $options;
        $this->ci->load->view('frontend/blocks/map', $d);
    }  
    
    public function pricelist($options = []){
        $d = [];
        $d['options'] = $options;
        $this->ci->load->view('frontend/blocks/pricelist', $d);
    }        

}
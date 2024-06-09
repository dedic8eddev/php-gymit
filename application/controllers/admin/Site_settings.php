<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site_settings extends Backend_Controller {

    public function __construct(){
        parent::__construct();
    }

    public function sectionName(): string
    {
        return SECTION_SITE_SETTINGS;
    }

    public function index()
	{
	    $this->checkReadPermission();

        $data['pageTitle'] = "Nastavení stránky";
        $data["current_site"] = $this->gyms->getSiteSettings()->current_site;

        $this->app->assets(['tabulator.min.js', 'flatpickr.js', 'flatpickr.cs.js', 'admin.site_settings.main.js'], 'js');
        $this->app->assets(['tabulator.min.css', 'flatpickr.css'], 'css');
		
		$this->load->view('layout/header', $data);
		$this->load->view('layout/menu', $data);
		$this->load->view('admin/site_settings/index', $data);
		$this->load->view('layout/footer');
    }

    public function save(){
        $current_site = $_POST["current_site"];
        if($current_site == "NULL") $current_site = NULL;

        if($this->db->where("gym", current_gym_code())->update("site_settings", ["current_site" => $current_site])){
            echo json_encode(["success" => true]);
        }else{
            echo json_encode(["error" => true]);
        }
    }
}

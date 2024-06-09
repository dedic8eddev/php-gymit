<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jobs extends Public_Controller {
	public function __construct()
	{
		parent::__construct();   
		$this->load->model('cms_model', 'cms');  
	}

	public function index(){
		$data = [];
		foreach ($this->gyms->getGymSettings(['page_jobs']) as $k => $v){
            $data[$v['type']]=json_decode($v['data'],true);
        }
		$data['jobs'] = $this->cms->getAllGymJobs(); // only jobs, not instructors
		$header['pageTitle'] = 'Nabídky práce';
		$this->load->view('frontend/layout/header',$header);
		$this->load->view('frontend/layout/menu', $data);
		$this->load->view('frontend/jobs/index', $data);
		$this->load->view('frontend/layout/footer', $data);
    }
    
    public function detail($slug){
        $slug_parts = explode('-', $slug);
        $id = array_values(array_slice($slug_parts, -1))[0];
		$data = [];
		foreach ($this->gyms->getGymSettings(['page_job_detail']) as $k => $v){
            $data[$v['type']]=json_decode($v['data'],true);
        }		
		$data['job'] = $this->cms->getGymJob($id);
		foreach ($this->cms->getGymJobRequirements($id) as $r){
            $data['requirements'][$r['type']][] = $r['name'];
		} 
		$header['pageTitle'] = 'Detail nabídky práce';        
		$this->load->view('frontend/layout/header',$header);
		$this->load->view('frontend/layout/menu', $header);
		$this->load->view('frontend/jobs/detail', $data);
		$this->load->view('frontend/layout/footer', $data);

    }
}

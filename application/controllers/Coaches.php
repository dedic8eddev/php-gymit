<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Coaches extends Public_Controller {
	public function __construct()
	{
		parent::__construct();   
		$this->load->model('users_model', 'users');  
		$this->load->model('coaches_model', 'coaches');  
	}

	public function index(){
		$data = [];
		$data['coaches'] = $this->users->getAllUsers([PERSONAL_TRAINER, MASTER_TRAINER]); // only coaches, not instructors
		$data['page'] = json_decode($this->gyms->getGymSettings(['page_coaches'])[0]['data'],true);
		
		// page settings
		foreach ($this->gyms->getGymSettings(['page_coaches']) as $k => $v){
            $data[$v['type']]=json_decode($v['data'],true);
		}
		// blocks
		foreach ($this->gyms->getGymSettings($data['page_coaches']['blocks']) as $k => $v){
            $data[$v['type']]=json_decode($v['data'],true);
		}

		$header['pageTitle'] = 'Trenéři';
		$this->load->view('frontend/layout/header',$header);
		$this->load->view('frontend/layout/menu', $data);
		$this->load->view('frontend/coaches/index', $data);
		$this->load->view('frontend/layout/footer', $data);
    }
    
    public function detail($slug){
        $slug_parts = explode('-', $slug);
        $id = array_values(array_slice($slug_parts, -1))[0];
		$data = [];
		$data['coach'] = $this->users->getUser($id);
        $data['users_data'] = $this->users->getUserData($id);
		$data['coach_data'] = $this->coaches->getCoachData($id);
		$data['coach_specializations'] = $this->coaches->getCoachSpecializations($id);
		$header['pageTitle'] = 'Detail trenéra';        
		$this->load->view('frontend/layout/header',$header);
		$this->load->view('frontend/layout/menu', $header);
		$this->load->view('frontend/coaches/detail', $data);
		$this->load->view('frontend/layout/footer', $data);

    }
}

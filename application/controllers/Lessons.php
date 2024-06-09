<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lessons extends Public_Controller {
	public function __construct()
	{
		parent::__construct();   
		$this->load->model('users_model', 'users');  
		$this->load->model('lessons_model', 'lessons');  
	}

	public function index(){
		$data = [];
		$data['lessons'] = $this->lessons->getTableTemplates(true);
		
		// page settings
		foreach ($this->gyms->getGymSettings(['page_lessons']) as $k => $v){
            $data[$v['type']]=json_decode($v['data'],true);
		}
		// blocks
		foreach ($this->gyms->getGymSettings($data['page_lessons']['blocks']) as $k => $v){
            $data[$v['type']]=json_decode($v['data'],true);
		}

		$data['lessons_templates_tags'] = $this->lessons->getAllTemplatesTags(true);
		$header['pageTitle'] = 'Skupinové lekce';
		$this->load->view('frontend/layout/header',$header);
		$this->load->view('frontend/layout/menu', $data);
		$this->load->view('frontend/lessons/index', $data);
		$this->load->view('frontend/layout/footer', $data);
    }
    
    public function detail($slug){
        $slug_parts = explode('-', $slug);
        $id = array_values(array_slice($slug_parts, -1))[0];
		$data = [];

		$data['lesson'] = $this->lessons->getTemplate($id);
		$data['lessonTeachers'] = $this->lessons->getTemplateTeachers($id,true); 
		$data['comingLessons'] = $this->lessons->getComingLessons($id);
		$header['pageTitle'] = 'Deetail lekce';        
		$this->load->view('frontend/layout/header',$header);
		$this->load->view('frontend/layout/menu', $header);
		$this->load->view('frontend/lessons/detail', $data);
		$this->load->view('frontend/layout/footer', $data);

    }
}

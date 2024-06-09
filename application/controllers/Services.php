<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Services extends Public_Controller {
	public function __construct()
	{
		parent::__construct();     
		$this->load->model('cms_model', 'cms');
		$this->load->model('blog_model', 'blog');
		$this->load->model('pricelist_model', 'pricelist');
	}

	public function index(){
		$data = [];
		$header['pageTitle'] = 'Služby';
		
		// page settings
		foreach ($this->gyms->getGymSettings(['page_services','page_exercise_zones','page_coaches','page_lessons','page_wellness']) as $k => $v){
            $data['data'][$v['type']]=json_decode($v['data'],true);
		}

		// blocks
		foreach ($this->gyms->getGymSettings($data['data']['page_services']['blocks']) as $k => $v){
            $data[$v['type']]=json_decode($v['data'],true);
		}	
		
		

		$this->load->view('frontend/layout/header',$header);
		$this->load->view('frontend/layout/menu', $data);
		$this->load->view('frontend/services/index', $data);
		$this->load->view('frontend/layout/footer', $data);
	}
	
	public function detail($slug){
		$type="page_".preg_replace('/-/','_',$slug);
		foreach ($this->gyms->getGymSettings([$type,'block_exercise_zones_equipment','block_wellness_equipment']) as $k => $v){ 
			if($v['type']==$type) $data['service'] = json_decode($v['data'],true);
			elseif($type=='page_wellness' && $v['type']=='block_wellness_equipment') $data['block_equipment']=json_decode($v['data'],true);
			elseif($type=='page_exercise_zones' && $v['type']=='block_exercise_zones_equipment') $data['block_equipment']=json_decode($v['data'],true);
			else $data[$v['type']]=json_decode($v['data'],true);
		}   

		$data['prices'] = $this->pricelist->getFrontServicePrices($data['service']['service_type']);
		$data['equipment'] = $this->gyms->getGymEquipment(preg_replace('/^page_/','',$type));
		$data['pinned_articles'] = $this->blog->getBlogPostsByGym(false,true);

		$header['pageTitle'] = 'Detail lekce';        
		$this->load->view('frontend/layout/header',$header);
		$this->load->view('frontend/layout/menu', $header);
		$this->load->view('frontend/services/detail', $data);
		$this->load->view('frontend/layout/footer', $data);

    }
}

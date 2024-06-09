<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Membership extends Public_Controller {
	public function __construct()
	{
		parent::__construct();     
		$this->load->model('cms_model', 'cms');
		$this->load->model('pricelist_model', 'pricelist');	
	}

	public function index(){
		$data = [];
		$header['pageTitle'] = 'Členství';
		
		$data['membership_prices'] = $this->pricelist->getMembershipPrices4HP();
		$data['single_entry_prices'] = $this->pricelist->getFrontSingleEntryPrices();

		// page settings
		foreach ($this->gyms->getGymSettings(['page_pricelist']) as $k => $v){ 
			$data[$v['type']]=json_decode($v['data'],true);
		}   
		// blocks
		foreach ($this->gyms->getGymSettings($data['page_pricelist']['blocks']) as $k => $v){
            $data[$v['type']]=json_decode($v['data'],true);
		}		

		$this->load->view('frontend/layout/header',$header);
		$this->load->view('frontend/layout/menu', $data);
		$this->load->view('frontend/membership/index', $data);
		$this->load->view('frontend/layout/footer', $data);
	}
	
	public function detail($code){
		$data['memberships'] = $this->pricelist->getFrontMembershipDetail($code);

		$header['pageTitle'] = 'Detail členství';        
		$this->load->view('frontend/layout/header',$header);
		$this->load->view('frontend/layout/menu', $header);
		$this->load->view('frontend/membership/detail', $data);
		$this->load->view('frontend/layout/footer', $data);

    }
}

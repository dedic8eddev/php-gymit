<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Public_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('blog_model', 'blog');
		$this->load->model('users_model', 'users');		
		$this->load->model('cms_model', 'cms');	
		$this->load->model('pricelist_model', 'pricelist');	
		$this->load->model('lessons_model', 'lessons');	
		$this->load->model('payments_model', 'payments');
		$this->load->model('vouchers_model', 'vouchers');	
	}

	/*public function phpinf(){
		phpinfo();
	}

	public function mailtest(){
		$email_body = $this->load->view('emails/invitation', ["user_id"=>1, "token"=>"test"], TRUE);

		$this->mailgun::send([
            'from' => "Gymit <no-reply@gymit.cz>",
            'to' => "lukas.vicanek@nwt.cz",
            'subject' => "Testovací e-mail",
            'html' => $email_body
        ]);
	}*/
	
	public function not_found(){
		$data['pageTitle'] = 'Nenalezeno';
		$this->load->view('layout/header', $data);
		$this->load->view('layout/menu', $data);
		$this->load->view('admin/404', $data);
		$this->load->view('layout/footer', $data);
	}
	
	public function index(){
		$header['bodyClass'] = 'home';
		$header['pageTitle'] = 'Domovská stránka';
		$data = [];

		$data['articles'] = $this->blog->getBlogPostsByGym();
		$data['coaches'] = $this->users->getAllUsers([PERSONAL_TRAINER, MASTER_TRAINER],1,false,true); // only coaches, not instructors
		$data['membership_prices'] = $this->pricelist->getMembershipPrices4HP();
		// page settings
		foreach ($this->gyms->getGymSettings(['page_homepage','page_exercise_zones','page_coaches','page_lessons','page_wellness']) as $k => $v){
            $data[$v['type']]=json_decode($v['data'],true);
		}
		// blocks
		foreach ($this->gyms->getGymSettings($data['page_homepage']['blocks']) as $k => $v){
            $data[$v['type']]=json_decode($v['data'],true);
		}
		$data['calendar']['from'] = date('Y-m-d', strtotime('monday this week'));
		$data['calendar']['to'] = date('Y-m-d', strtotime($data['calendar']['from'].'+ 7 days'));
		$data['calendar']['hp'] = 1;
		$data['calendar']['data'] = $this->lessons->getFrontCalendar($data['calendar']);		

		$this->load->view('frontend/layout/header',$header);
		$this->load->view('frontend/layout/menu', $data);
		$this->load->view('frontend/homepage/index', $data);
		$this->load->view('frontend/layout/footer', $data);
	}

	public function contact(){
		$data = [];
		$header['pageTitle'] = 'Kontakt';
		foreach ($this->gyms->getGymSettings(['page_contact']) as $k => $v){
            $data[$v['type']]=json_decode($v['data'],true);
        }		
		$this->load->view('frontend/layout/header',$header);
		$this->load->view('frontend/layout/menu', $data);
		$this->load->view('frontend/contact/index', $data);
		$this->load->view('frontend/layout/footer', $data);
	}

	public function get_user(){
		$user_id=gym_userid();
		if($user_id>0) echo json_encode(['user'=>array_merge((array) $this->users->getUser($user_id,true),(array) $this->users->getUserData($user_id,true))]);
		else echo json_encode(['user'=>false]);
	}
}

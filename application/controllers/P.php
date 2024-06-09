<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class P extends Override_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('blog_model', 'blog');
		$this->load->model('users_model', 'users');		
		$this->load->model('cms_model', 'cms');	
		$this->load->model('pricelist_model', 'pricelist');	
		$this->load->model('lessons_model', 'lessons');	
		$this->load->model('payments_model', 'payments');
        $this->load->model('vouchers_model', 'vouchers');
        
        // Get site settings
        $this->site_settings = $this->gyms->getSiteSettings();
	}

	public function index(){
		$header['bodyClass'] = 'home';
		$header['pageTitle'] = 'Domovská stránka';
	}

	public function predprodej($page = NULL){
        // Redirect if current site settings doesnt match
        if ($this->site_settings->current_site != "predprodej") redirect("/");

		$data=[];
		$data['subSubmitUrl'] = base_url('p/submit-subscription');
		$data['membership'] = $this->pricelist->getMembershipPrices();

		if(!is_null($page)){
			// LP sub page
			$this->load->view('frontend/landingpage/layout/header', $data);
			$this->load->view('frontend/landingpage/'.$page);
			$this->load->view('frontend/landingpage/layout/footer', $data);
		}else{
			// LP home page
			$this->load->view('frontend/landingpage/layout/header', $data);
			$this->load->view('frontend/landingpage/index', $data);
			$this->load->view('frontend/landingpage/layout/footer', $data);
		}
	}

	public function submit_subscription(){
		$p = $_POST;
		// get client id or invite new user
		$client_id = $this->users->getOrCreateDisposableUser($p)['id'];
		//$client_id = $this->db->where('email', $p['email'])->get('users')->row()->id ?? $this->users->inviteUser($p['email'], CLIENT);
		$price = $this->pricelist->getMembershipPrice($p['membership_id']);
		// count new price after discount
		$price->price = $price->price * ( 1 - ($this->payments->getPresaleMembershipDiscount($price->id) / 100) );
		$this->payments->createWebpay($client_id,$price->price,base_url("p/proceed_webpay_subscription/$client_id/$price->id"));
	}
	public function proceed_webpay_subscription($client_id,$price_id){
		$data=[];
		if($this->payments->proceedWebPay($client_id,$price_id)){ // success
			$data['statusClass'] = 'success';
			$data['headerText'] = 'Vaše platba proběhla úspěšně.';
			$data['headerText2'] = 'VÍTEJTE V GYMIT PREMIUM FITNESS!';
			$data['responseText'] = 'Děkujeme za zakoupení členství. Další informace Vám byly zaslány na Vámi uvedený e-mail.<br />Těšíme se na Vaši návštěvu.<br /><i>Tým Gymit</i>';
			$data['responseText2'] = 'V případě dotazů nás kontaktujte na <a href="tel:+420731103598">+420 702 051 943</a>';
		} else { // error
			$data['statusClass'] = 'error';
			$data['headerText'] = 'Platba se nezdařila';
			$data['headerText2'] = 'Platba se nezdařila';
			$data['responseText'] = 'Vaši platbu nebylo možné provést. Mezi nejčastější důvody nezdařené platby patří: chybné vyplnění informací o vaší kartě, přečerpání denního limitu na platby kartou online, nebo nečekaný výpadek systému.';
			$data['responseText2'] = '<strong>Opakujte prosím platbu. V případě že se platbu nepodaří provést ani během dalšího pokusu, kontaktujte vydavatele své karty</strong>';
		}
		$this->load->view('frontend/landingpage/layout/header', $data);
		$this->load->view('frontend/landingpage/webpay_status', $data);
		$this->load->view('frontend/landingpage/layout/footer', $data);
    }
    

    public function maintenance () {
        // Redirect if current site settings doesnt match
        if ($this->site_settings->current_site != "maintenance") redirect("/");
		$this->load->view('frontend/landingpage/layout/header');
		$this->load->view('frontend/landingpage/maintenance');
		$this->load->view('frontend/landingpage/layout/footer');
    }
}

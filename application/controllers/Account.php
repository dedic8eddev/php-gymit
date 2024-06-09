<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends Account_Controller {
    /** @var Account_Model */
    public $account;

    /** @var Lessons_model */
    public $lessons;

	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('Account_model','account');
		$this->load->model('Lessons_model','lessons');
		$this->load->model('Payments_model','payments');
		$this->load->model('Users_model','users');
		$this->load->model('Cards_model','cards');
	}

	public function index()
	{
		$header['bodyClass'] = 'article';
        $header['menuClass'] = 'bg';
		$header['pageTitle'] = 'Uživatelský panel';

		$clientId = gym_userid();

        $data['subscription'] = $this->payments->getClientSubscription($clientId);
        $data['upcomingLessons'] = $this->lessons->getUpcomingClientLessons($clientId, 5);
        $data['upcomingPayments'] = $this->payments->getClientFuturePayments($clientId, 3);
        $data['currentStateOfPayments'] = $this->payments->getClientCurrentStateOfPayments($clientId, new DateTimeImmutable());

        $this->app->assets(['front.account.css'], 'css');

		$this->load->view('frontend/layout/header',$header);
		$this->load->view('frontend/layout/menu', $header);
		$this->load->view('frontend/account/dashboard', $data);
		$this->load->view('frontend/layout/footer');
    }
    
    public function lessons(){
        $header['bodyClass'] = 'article';
        $header['menuClass'] = 'bg';
		$header['pageTitle'] = 'Moje lekce';

        $data['upcoming'] = $this->lessons->getUpcomingClientLessons(gym_userid(), 10);
        $data['history'] = $this->lessons->getClientLessonsHistory(gym_userid());

        $this->app->assets(['front.account.css'], 'css');

		$this->load->view('frontend/layout/header',$header);
		$this->load->view('frontend/layout/menu', $header);
		$this->load->view('frontend/account/lessons', $data);
		$this->load->view('frontend/layout/footer');
    }
    public function membership(){
        $header['bodyClass'] = 'article';
        $header['menuClass'] = 'bg';
		$header['pageTitle'] = 'Členství';

		$data['subscription'] = $this->payments->getClientSubscription(gym_userid());

        $this->app->assets(['front.account.css'], 'css');

		$this->load->view('frontend/layout/header',$header);
		$this->load->view('frontend/layout/menu', $header);
		$this->load->view('frontend/account/membership', $data);
		$this->load->view('frontend/layout/footer');
    }
    public function payments(){
        $header['bodyClass'] = 'article';
        $header['menuClass'] = 'bg';
		$header['pageTitle'] = 'Platby';

        $clientId = gym_userid();

        $data['historyPayments'] = $this->payments->getClientHistoryPayments($clientId);
        $data['upcomingPayments'] = $this->payments->getClientFuturePayments($clientId, 5);
        $data['currentStateOfPayments'] = $this->payments->getClientCurrentStateOfPayments($clientId, new DateTimeImmutable());

        $this->app->assets(['front.account.css'], 'css');

		$this->load->view('frontend/layout/header',$header);
		$this->load->view('frontend/layout/menu', $header);
		$this->load->view('frontend/account/payments', $data);
		$this->load->view('frontend/layout/footer');
    }
    public function settings(){
        $header['bodyClass'] = 'article';
        $header['menuClass'] = 'bg';
		$header['pageTitle'] = 'Nastavení účtu';

        $userId = gym_userid();

        $data['user']                   = $this->users->getUser($userId);
        $data['userData']               = $this->users->getUserData($userId);
        $data['updatePersonalInfo']     = base_url('account/update_personal_info_ajax');
        $data['updateBillingInfo']      = base_url('account/update_billing_info_ajax');
        $data['updateNotifications']    = base_url('account/update_notifications_ajax');
        $data['updateSecurity']         = base_url('account/update_security_ajax');

        $this->app->assets(['front.account.css'], 'css');
        $this->app->assets(['front.account.main.js'], 'js');

		$this->load->view('frontend/layout/header',$header);
		$this->load->view('frontend/layout/menu', $header);
		$this->load->view('frontend/account/settings', $data);
		$this->load->view('frontend/layout/footer');
    }

    public function update_personal_info_ajax()
    {
        $this->load->library('form_validation');
        if (! $this->form_validation->run('front_user_personal_info')) {
            $this->ajaxErrorResponse(validation_errors());
        }

        if ($this->account->updatePersonalInfo(gym_userid(), $this->input->post())) {
            $this->ajaxSuccessResponse();
        }

        $this->ajaxErrorResponse('Nepodařilo se změnit osobní a kontaktní údaje');
    }

    public function update_notifications_ajax()
    {
        if($this->account->updateNotification($this->input->post())){
            $this->ajaxSuccessResponse();
        }

        $this->ajaxErrorResponse('Nepodařilo se změnit nastavení notifikací');
    }

    public function update_security_ajax()
    {
        $this->load->library('form_validation');
        if (! $this->form_validation->run('front_user_security_info'))  {
            $this->ajaxErrorResponse(validation_errors());
        }

        if($this->account->updateSecurity(gym_userid(), $this->input->post())){
            $this->ajaxSuccessResponse();
        }

        $this->ajaxErrorResponse('Nepodařilo se změnit přihlašovací údaje');
    }

	public function ajax_change_password()
	{
		$user = $this->ion_auth->user()->row_array();
		if($this->ion_auth->change_password($user['email'],$this->input->post('current_password'),$this->input->post('new_password'))){
			echo json_encode(['success' => 'true']);
			return true;
		}else{
			echo json_encode(['error' => 'true']);
			return false;
		}
	}
}

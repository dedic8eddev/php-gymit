<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Eetapp extends Backend_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('eetapp_model', 'eetapp');
        $this->load->model('payments_model', 'payments');
    }

    public function sectionName(): string
    {
        return SECTION_CASH_REGISTER;
    }

    public function index(){
        $this->checkReadPermission();
        $data['pageTitle'] = 'Správa pokladen';

        $data['getAllCheckoutsUrl'] = base_url('admin/eetapp/get_all_checkouts');
        $data['getCheckoutsLogUrl'] = base_url('admin/eetapp/get_checkouts_log');

        $this->app->assets(['tabulator.min.js', 'flatpickr.js', 'flatpickr.cs.js', 'admin.eetapp.main.js'], 'js');
        $this->app->assets(['tabulator.min.css','flatpickr.css'], 'css');

        $this->load->view('layout/header', $data);
		$this->load->view('layout/menu', $data);
		$this->load->view('admin/eetapp/index', $data);
        $this->load->view('layout/footer');
    }

    public function add_checkout(){
        $this->checkCreatePermission();
        $data['saveUrl'] = base_url('admin/eetapp/save_checkout_ajax'); 
        $this->load->view('admin/eetapp/checkout_form', $data);
    }

    public function edit_checkout($id){
        $this->checkEditPermission();
        $data['saveUrl'] = base_url('admin/eetapp/save_checkout_ajax');
        $data['checkout'] = $this->eetapp->getCheckout($id);
        $this->load->view('admin/eetapp/checkout_form', $data);
    } 
    
    public function set_checkout_state($id){
        $lastLog = $this->eetapp->getLastCheckoutLog($id);
        $transData = $this->API->transactions->get_transactions(['from' => ( ($lastLog) ? localDateToMongo($lastLog->date_created) : localDateToMongo(date("Y-m-d H:i:s")) ), 'exactTime'=>TRUE, 'transCategory'=>['HO','VO','BP']]);
        $lastPayments = [];
        if($transData->success && !empty($transData->data)){
            foreach($transData->data as $t){
                if(!array_key_exists($t->transType, $lastPayments)) $lastPayments[$t->transType]=0;
                $lastPayments[$t->transType] += $t->value;
            }
        }
        $data['lastLog'] = $lastLog ?? [];
        $data['transCategories'] = $this->payments->returnTransCategories();
        $data['lastPayments'] = $lastPayments;
        $data['checkout'] = $this->eetapp->getCheckout($id);
        $data['saveUrl'] = base_url('admin/eetapp/save_checkout_state_ajax'); 
        $this->load->view('admin/eetapp/checkout_state', $data);
    } 
    
    public function save_checkout_state_ajax(){
        $this->checkEditPermission(true);
        $state = $this->input->post('state');
        $methodName = $state == 0 ? 'openCheckout' : 'closeCheckout';
        if(call_user_func_array([$this->eetapp,$methodName], [$this->input->post()])) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);  
    }      

    public function save_checkout_ajax(){
        $this->checkEditPermission(true);
        if($this->eetapp->saveCheckout($this->input->post())) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);        
    }

    public function get_all_checkouts(){
        echo json_encode($this->eetapp->getAllCheckouts());       
    }

    public function get_checkouts_log(){
        echo json_encode($this->eetapp->getCheckoutslog());       
    }   
}

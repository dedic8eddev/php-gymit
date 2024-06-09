<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vouchers extends Backend_Controller {

    public function __construct(){
        parent::__construct();
		$this->load->model('vouchers_model', 'vouchers');
	}

    public function sectionName(): string {
        return SECTION_VOUCHERS;
    }

    public function index(){
        $this->checkReadPermission();

        $data = [];

        if(isset($_GET['invoice']) && $_GET['invoice'] > 0){
            $invoice = $this->db->where('id', $_GET['invoice'])->get('invoices')->row();
            $data['createdVouchers'] = $this->db->where(['identification_type' => 'invoice', 'identification_id' => $invoice->invoice_number])->get('vouchers')->num_rows();
            $data['invoiceId'] = $invoice->invoice_number;
            $data['invoiceItems'] = [];
            foreach (json_decode($invoice->items) as $i){
                // only membership or services (vouchers -> service_type 6)
                if($i->item_type == 'membership' || ($i->item_type == 'service' && $this->pricelist->getPrice($i->item_id)['service_type']==6)){
                    array_push($data['invoiceItems'],['type' => $i->item_type, 'id' => $i->item_id, 'name' => $i->item_name, 'amount' => $i->item_amount]);
                }
            }
        }

        $data['pageTitle'] = 'Vouchery';

        $data['vouchersUrl'] = base_url('admin/vouchers/get_vouchers');

		$this->app->assets(['tabulator.min.js', 'flatpickr.js', 'flatpickr.cs.js', 'admin.vouchers.main.js'], 'js');
		$this->app->assets(['tabulator.min.css','flatpickr.css'], 'css');

		$this->load->view('layout/header', $data);
        $this->load->view('layout/menu', $data);
        $this->load->view('admin/vouchers/index', $data);
		$this->load->view('layout/footer');
    }

    public function detail($code){
        $data = [];
        $data['voucher'] = $this->vouchers->getVoucher($code);
        $this->load->view('admin/vouchers/detail', $data);
    }

    public function get_vouchers(){
        $this->checkReadPermission(true);
        echo json_encode($this->vouchers->getAllVouchers());
    }

    public function create_vouchers_ajax(){
        $p = $_POST;
        $this->db->trans_start();
        foreach ($p['items'] as $item){
            for($i=0;$i<$item['amount'];$i++){
                $code = $this->vouchers->createVoucher($item['type'], $item['id'], current_gym_code());
                $this->vouchers->setIdentification([$code],$p['identification_type'],$p['identification_id']);
            }
        }
        $this->db->trans_complete();

        if($this->db->trans_status()) echo json_encode(["success" => "true"]);
        else echo json_encode(["error" => "true"]);
    }

    public function disable_voucher_ajax(){
        $this->checkEditPermission(true);
		if($this->vouchers->disableVoucher($this->input->post('voucher_code'))) echo json_encode(["success" => "true"]);
		else echo json_encode(["error" => "true"]);
    }

    public function get_voucher_ajax($code){
        $voucher = $this->vouchers->getVoucher($code);
        if(isset($_POST['checkout'])){
            if(!$voucher) echo json_encode(["error" => "true", "errMsg" => "Voucher s tímto kódem neexistuje"]);
            else if($voucher->date_disabled) echo json_encode(["error" => "true", "errMsg" => "Voucher byl již použit ".date('j.n.Y H:m:s', strtotime($voucher->date_disabled))]);
            else echo json_encode(["success" => "true", "data" => $voucher]);
        } else echo json_encode($voucher);
    }
}
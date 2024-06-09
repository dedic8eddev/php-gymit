<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payments extends Backend_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('users_model', 'users');
        $this->load->model('pricelist_model', 'pricelist');
        $this->load->model('depot_model', 'depot');
        $this->load->model('dashboard_model', 'dash');
    }

    public function sectionName(): string
    {
        return SECTION_PAYMENTS;
    }

    public function index(){
        $this->checkReadPermission();

        $data['pageTitle'] = 'Pokladna';

        if(isset($_GET['action']) && $_GET['action']=='fillSummary'){ // Get items from transaction buffer
            $data['client_id']=$_GET['client_id'];
            $data['card_id']=$_GET['card_id'];
            $data['membership_id']=$_GET['membership_id'];
            $data['credit']=$this->API->transactions->get_credit($data['client_id'], $data['card_id'])->data->currentValue; // kredit
            $data['que']=$this->dash->getClientQueItems($data['card_id'],$data['membership_id']);
        }

        $data['checkouts'] = $this->eetapp->getAllCheckouts(true);
        $data['terminals'] = $this->payments->getAllTerminals();
        $data['price_list'] = $this->pricelist->getAllPrices(true);
        $data['depot_items'] = $this->depot->getAllDepotItems(true,true);
        $data['depots'] = $this->depot->getAllDepots();
        $data['solariums'] = $this->gyms->getGymSolariums(get_db(),true);
        $data['clients'] = $this->users->getAllUsers([CLIENT,DISPOSABLE],1,true);

        $data['addUrl'] = base_url('admin/payments/add_payment_ajax');
        $data['getAllUrl'] = base_url('admin/payments/get_all_payments');
        $data['getInvoicesUrl'] = base_url('admin/payments/get_all_invoices');

        $this->app->assets(['tabulator.min.js', 'flatpickr.js', 'flatpickr.cs.js', 'admin.payments.main.js', config_item("api")["socket_io"], "admin._terminals.js"], 'js');
        $this->app->assets(['tabulator.min.css', 'flatpickr.css', 'jquery-ui/jquery-ui.min.css', 'admin.payments.main.css'], 'css');

        $this->load->view('layout/header', $data);
		$this->load->view('layout/menu', $data);
		$this->load->view('admin/payments/index', $data);
        $this->load->view('layout/footer');
    }

    public function get_invoice_pdf($invoice_id) {
        $data = [];
        // Subject info, contact info, bank account etc...
        foreach ($this->gyms->getGymSettings(['subject_info','general_info']) as $k => $v){
            $data[$v['type']]=json_decode($v['data']);
        }
        $data['invoice'] = $this->db->select('i.*,c.name as country_name')->from("invoices i")
            ->join('countries c','c.iso=i.client_state','LEFT')
            ->where("id", $invoice_id)->get()->row_array();
        $pdf = $this->mpdf->create(false, [
            "content" => "invoice"
        ], $data);
    }

    public function create_card(){
        $this->checkCreatePermission();
        $this->load->view('/admin/payments/create_card_modal');
    }

    public function refund_credit(){
        $this->load->view('/admin/payments/refund_credit_modal');
    }    

    public function get_transaction_for_editing(){
        $tid = $_POST['trans_id'];
        $this->load->model('pricelist_model', 'pricelist');
        $this->load->model('depot_model', 'depot');

        $transaction = $this->API->transactions->get_transaction_by_id($tid);

        if(isset($transaction->success)){
            $data = $transaction->data;
            $formatted = new stdClass();

            // Depot / pricelist items / credit
            $formatted->items = [];
            $formatted->credit = [];
            if(!empty($data->items)){
                foreach($data->items as $item){
                    $itm = [];

                    if(isset($item->depotId) && $item->itemId != 0) $itm = $this->depot->getDepotItem($item->itemId, true);
                    else if (!isset($item->depotId) && $item->itemId != 0) $itm = $this->pricelist->getPrice($item->itemId);

                    $itm['amount'] = $item->amount;
                    $itm['sale_value'] = $item->value;
                    $itm['sale_vat'] = $item->vat;
                    $itm['sale_vat_value'] = $item->vat_value;
                    $itm['value_discount'] = isset($item->value_discount) ? $item->value_discount : 0;

                    if(isset($item->depotId)) $itm['depotId'] = $item->depotId;

                    if($item->itemId == 0){
                        $formatted->credit[] = $itm;
                    }else{
                        $formatted->items[] = $itm;
                    }
                }
            }

            // Subscriptions
            $formatted->subs = [];
            if($data->subscriptionPayment && !$data->subscriptionSubPaymentId){

                $sub = $this->API->subscriptions->get_subscription_by_invoice_number($data->subscriptionContractNumber)->data;
                if($sub){
                    $itm = [];
                    $itm['id'] = $sub->_id;
                    $itm['sub_type'] = $sub->subType;
                    $itm['sub_period'] = $sub->subPeriod;
                    $itm['sale_value'] = $data->value;
                    $itm['sale_vat'] = $data->vat;
                    $itm['sale_vat_value'] = $data->vat_value;
                    $itm['value_discount'] = isset($data->value_discount) ? $data->value_discount : 0;
                    $itm['contract'] = $data->subscriptionContractNumber;
                    $itm['sub_info'] = $this->pricelist->getMembership($sub->subType);
    
                    $formatted->subs[] = $itm;
                }
            }else if($data->subscriptionSubPaymentId){
                $sub = $this->API->subscriptions->get_subscription_by_invoice_number($data->subscriptionContractNumber)->data;
                if($sub){
                    foreach($sub->payments as $payment){
                        if($payment->_id == $data->subscriptionPaymentId){
                            $itm = [];
                            $itm['id'] = $sub->_id;
                            $itm['sub_type'] = $sub->subType;
                            $itm['sub_period'] = $sub->subPeriod;
                            $itm['sale_value'] = $payment->value;
                            $itm['sale_vat'] = $payment->vat;
                            $itm['sale_vat_value'] = $payment->vat_value;
                            $itm['value_discount'] = isset($data->value_discount) ? $data->value_discount : 0;
                            $itm['contract'] = $data->subscriptionContractNumber;
                            $itm['subscriptionSubPaymentId'] = $payment->_id; // the actual payment id (single timeblock)
            
                            $formatted->subs[] = $itm;
                        }
                    }
                }
            }

            // Vouchers
            /*if ($data->voucherSale){
                $itm['name'] = 'Voucher';
                $itm['amount'] = 1;
                $itm['sale_value'] = $data->value;
                $itm['sale_vat'] = $data->vat;
                $itm['sale_vat_value'] = $data->vat_value;
                $itm['value_discount'] = isset($data->value_discount) ? $data->value_discount : 0;

                $formatted->items[] = $itm;
            }*/

            $formatted->payment_type = $this->payments->returnTransCategories()[$data->transType];
            $formatted->payment_type['transType'] = $data->transType;
            $formatted->total = $data->value;

            echo json_encode(['success' => 'true', 'data' => $formatted]);

        }else{
            echo json_encode(['error' => 'true']);
        }
    }

    public function refund_sub(){
        if($this->payments->refund_sub()) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']); 
    }

    public function refund_transaction_ajax(){
        if($this->payments->refund_payment()) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']); 
    }

    public function edit_transaction_ajax(){
        $this->checkEditPermission(true);
        if($this->payments->edit_payment()) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']); 
    }

    public function choose_checkout_ajax(){
        if(!$this->input->post('checkout_id')>0) echo json_encode(['error' => 'true']);
        $this->session->set_userdata('checkout_id', $this->input->post('checkout_id'));
        $this->session->set_userdata('checkout_name', $this->input->post('checkout_name'));
        echo json_encode(['success' => 'true']);
    }

    // Reserve a variable symbol for a card payment
    public function reserve_cc_payment_ajax(){
        echo $this->payments->reserve_credit_card_payment();
    }
    public function release_cc_payment_ajax(){
        echo $this->payments->release_credit_card_payment();
    }

    public function add_payment_ajax(){
        $this->checkCreatePermission(true);
        $pay = $this->payments->pay();
        if(is_array($pay)) {
            self::ajaxSuccessResponse($pay);
        } else {
            self::ajaxErrorResponse();
        }
    }

    public function get_all_payments(){
        $this->checkReadPermission(true);
        $payments = $this->payments->getAll();
        echo json_encode($payments);
    }
    public function get_all_purchased_items(){
        $this->checkReadPermission(true);
        $items = $this->payments->getAllPurchasedItems();
        echo json_encode($items);
    }    
    public function get_all_invoices(){
        $this->checkReadPermission(true);
        $invoices = $this->payments->getInvoices();
        echo json_encode($invoices);
    }
    public function submit_invoice_ajax(){
        if($this->payments->submit_invoice()) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);  
    }
    public function cancel_invoice($invoice_id){
        if($this->payments->cancel_invoice($invoice_id)) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);  
    }
    public function pay_invoice($invoice_id, $method){
        if($this->payments->pay_invoice($invoice_id, $method)) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);  
    }

    public function submit_subscription_ajax(){
        if($data = $this->payments->subscriptionToTransaction()) echo json_encode(['success' => 'true', 'data' => $data]);
        else echo json_encode(['error' => 'true']);  
    }

    public function get_client_subscription_info_ajax(){
        $this->checkReadPermission(true);
        $client_id = $_POST['client_id'];
        $card = $this->cards->getUserCard($client_id);

        $data = $this->API->subscriptions->get_subscription($client_id, current_gym_code());
        //print_r($data); exit;
        if($data){

            if(!empty($data->data)) $data->data->membership = $this->pricelist->getMembership($data->data->subType);

            echo json_encode($data);
        }else{
            echo json_encode(["error" => "true"]);
        }
    }

    public function set_transactions_as_closed(){
        $this->checkEditPermission(true);
        $transactions = $_POST['transactions'];
        if($this->API->transactions->close_transactions($transactions)){
            echo json_encode(['success' => 'true']);
        }else{
            echo json_encode(['error' => 'true']);
        }
    }
    public function set_day_as_closed(){
        $this->checkEditPermission(true);
        $day = $_POST['day'];
        if($this->API->transactions->close_transactions(FALSE, $day)){
            echo json_encode(['success' => 'true']);
        }else{
            echo json_encode(['error' => 'true']);
        }
    }

    public function refund_credit_ajax(){
        $this->checkEditPermission(true);
        if($this->payments->refundCredit($this->input->post())) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);
    }

    public function print_receipt(){
        foreach (array_unique($_POST['transactions']) as $transId){
            $ret = $this->API->transactions->get_receipt($transId);
            $receipt = json_decode($ret->data->data[0], true);
            $receipt['transactionId'] = $ret->data->transactionId;
            $receipt['gymCode'] = $ret->data->gymCode;
            $receipt['receiptId'] = $ret->data->receiptNumber;
            
            //$this->tmprinter->print($receipt,false);
            $this->API->transactions->print_receipt($receipt, false);
            echo json_encode(['success' => 'true']);
        }
    }

}

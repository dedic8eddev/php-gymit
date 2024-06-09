<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Autocont_model extends CI_Model
{

    public function __construct(){
		parent::__construct();
        
		// Models
        $this->load->model('users_model', 'users'); // user model
        $this->load->model('pricelist_model', 'pricelist'); // pricelist
        $this->load->model('depot_model', 'depot'); // depot
        $this->load->model('payments_model', 'payments'); // payments

        $this->autocont_folder = config_item('app')['autocont_folder'];
        $this->autocont_accounts = config_item("app")["autocont_accounts"];
    }
    
    /** 
     * Add an expected payment into DB to monitor on our side
     */
    public function expect_payment ($data) {
        if($this->db->insert("autocont_expected_payments", $data)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    /**
     * Update the payment status of an existing expected payment
     * If the paid amount is equal to the original amount its 100% paid, if not, the received amount is updated
     * and nothing is set as "paid", waiting for another payment to top it up (mistaken amounts, etc.)
     */
    public function pay_expected_payment ($gymCode, $transNum, int $paid, $payment_date) {
        $payment = $this->db->where("transactionNumber", $transNum)->order_by("ABS( DATEDIFF( created_on, ".$payment_date." ) )")->where("paid", 0)->get("autocont_expected_payments")->row();

        if ($payment) {
            $original_trans = NULL;
            $subscription = NULL;

            if($payment->autocont_type != "Predplat"){
                $original_trans = $this->API->transactions->get_transaction_by_number($transNum, $gymCode);
            }else{
                $subscription = $this->API->subscriptions->get_subscription_by_invoice_number($transNum);

                if(!empty($subscription->data)){
                    $subscription = $subscription->data;

                    // Search for the expected timeframe of the subscriptions to be paid
                    foreach ($subscription->transactions as $subtime) {
                        if(mongoDateToLocal($subtime->start) == $payment->created_on){
                            $original_trans = $subtime;
                            break;
                        }
                    }

                }else{
                    return FALSE;
                }

            }

            if(!is_null($original_trans) && $original_trans !== FALSE){
                $original_amount = $original_trans->value + $original_trans->vat_value;

                $update = [
                    "received" => $payment->received
                ];

                if ($original_amount <= abs($paid)) $update["paid"] = TRUE;
                $update["last_change"] = date("Y-m-d H:i:s");
                $update["received"] += $paid;

                if($this->db->where("transactionNumber", $transNum)->update("autocont_expected_payments", $update)){
                    if (isset($update["paid"])) {
                        // The transaction is paid
                        if ($payment->autocont_type != "Predplat") $this->API->transactions->edit_transaction($original_trans->_id, ["paid" => true]); // Pay the transaction
                        else $this->API->subscriptions->pay_for_subscription_payment($subscription->clientId, $gymCode, [
                            'transactionId' => $original_trans->_id,
                            'contractNumber' => $transNum
                        ]); // Pay the sub timeframe
                    }
    
                    return TRUE; // bye
                }

            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function refund_expected_payment ($transNum) {
        if($this->db->where("transactionNumber", $transNum)->update("autocont_expected_payments", ["cancelled" => TRUE, "autocont_notified" => FALSE, "last_change" => date("Y-m-d H:i:s")])){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    // Read files from autocont
    public function read_xml_files () {
        $files = array_diff(scandir($this->autocont_folder . "01" . "/Export"), array('.', '..'));

        $collected_data = ["payments" => [], "invoices" => []];
        foreach($files as $file){
            if(strpos($file, "xml") === false) continue;

            if(file_exists($this->autocont_folder . "01" . "/Export" . "/" . $file)){
                $xml = simplexml_load_file($this->autocont_folder . "01" . "/Export" . "/" . $file);

                if($xml){
                    foreach($xml->Radek as $row){
                        if(strpos($file, "uhra") !== false){
                            // Úhrady
                            $payment = [
                                "gym" => $row->Klub,
                                "paymentIdentificationNumber" => $row->VarSymbol,
                                "date" => $row->Datum,
                                "value" => $row->Castka,
                                "currency" => $row->Mena
                            ];

                            if(isset($collected_data["payments"][(string)$row->VarSymbol])){
                                $collected_data["payments"][(string)$row->VarSymbol]["value"] += $payment["value"];
                            }else{
                                $collected_data["payments"][(string)$row->VarSymbol] = $payment;
                            }
                            
                        }else{
                            // Faktury
                            $invoice = [
                                "gym" => $row->Klub,
                                "invoiceId" => $row->Faktura,
                                "client" => $row->Odberatel,
                                "date" => date("Y-m-d"),
                                "value" => $row->Zmena,
                                "currency" => $row->Mena
                            ];

                            if(isset($collected_data["invoices"][(string)$row->Faktura])){
                                $collected_data["invoices"][(string)$row->Faktura]["value"] += $invoice["value"];
                            }else{
                                $collected_data["invoices"][(string)$row->Faktura] = $invoice;
                            }
                        }
                    }
                }else{
                    continue;
                }
            }
        }

        if(!empty($collected_data["payments"])) {
            foreach($collected_data["payments"] as $data){
                //if ($data["value"] != 0) echo "Úhrada závazku s var. symbolem ".$data["paymentIdentificationNumber"]." v hodnotě ".$data["value"].$data["currency"]." připsána.".PHP_EOL;
                $this->pay_expected_payment("01", $data["paymentIdentificationNumber"], $data["value"], $data["date"]);
            }
        }

        if(!empty($collected_data["invoices"])) {
            foreach($collected_data["invoices"] as $data){
                //if ($data["value"] != 0) echo "Úhrada faktury #".$data["invoiceId"]." v hodnotě ".$data["value"].$data["currency"]." připsána.".PHP_EOL;
                $this->pay_expected_payment("01", $data["invoiceId"], $data["value"], $data["date"]);
            }
        }

        return TRUE;
    }

    /**
     * Generate subscribers xml for saving
     */
    public function create_subscribers_xml($club, $date = NULL){
        if(!$date) $date = date("Y-m-d 00:00:01");
        $subscribers = [];

        $users = $this->ion_auth->users(CLIENT)->result();
        if(!$users) exit('Nothing to parse.');
        foreach($users as $u) {
            if($u->date_created >= $date OR $u->active_last_change >= $date){
                $u_data = $this->users->getUserData($u->id);
                $u->user_data = $u_data;
                $subscribers[] = $u;   
            }
        }

        $file_name = 'odbe' . $club . date('Ymd') . '01.xml';
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><Odberatele></Odberatele>');
    
        foreach ($subscribers as $s){
            $sub = $xml->addChild('Radek');

            $sub->addChild('Aktualizace', ( ($s->active) ? 'Oprava' : 'Ruseni' ));
            $sub->addChild('Odberatel', $club . sprintf('%06d', (int)$s->id));
            $sub->addChild('Nazev', $s->user_data->first_name . ' ' . $s->user_data->last_name);
            $sub->addChild('PlatceDPH', ( ($s->user_data->vat_enabled) ? 'Ano' : 'Ne' ));
            $sub->addChild('IC', $s->user_data->company_id);
            $sub->addChild('DIC', ( ($s->user_data->vat_enabled) ? $s->user_data->vat_id : '' ));
            $sub->addChild('Telefon', $s->user_data->phone);
            $sub->addChild('Email', $s->user_data->email);
            $sub->addChild('Ulice', $s->user_data->street);
            $sub->addChild('Mesto', $s->user_data->city);
            $sub->addChild('Zeme', $s->user_data->country);
            $sub->addChild('PSC', (strlen($s->user_data->zip) > 0) ? substr($s->user_data->zip, 0, 3) . " " . substr($s->user_data->zip, 3) : "");
        }

        $xml->asXML($this->autocont_folder . $club . "/Import" . "/" . $file_name);
    }

    /**
     * Generate future payments XML from subs and bank transactions
     * This is going to run once a day and go through every necessary payment to be expected
     * TODO: make a function that collects cancelled payment receival
    */
    public function create_future_payments_xml($club, $date = NULL){
        $from = date('Y-m-d 00:00:01');
        $to = date('Y-m-d 23:59:59');

        $awaited = $this->db->where("date_created >=", $from)->where("date_created <=", $to)->where("paid !=", 1)->where("cancelled !=", 1)->where("autocont_notified", 0)->get("autocont_expected_payments")->result();
        $newly_refunded = $this->db->where("cancelled", 1)->where("autocont_notified", 0)->get("autocont_expected_payments")->result();
        $notified = [];

        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><Platby></Platby>');

        if(!empty($awaited)){
            foreach ($awaited as $p){
                $sub = $xml->addChild('Radek');
                $sub->addChild('Klub', $club);
                $sub->addChild('VarSymbol', $p->transactionNumber);
                $sub->addChild('Ucel', $p->autocont_type);

                $notified[] = [
                    "id" => $p->id,
                    "autocont_notified" => TRUE
                ];
            }
        }

        if(!empty($newly_cancelled)){
            foreach ($newly_refunded as $p){
                $sub = $xml->addChild('Radek');
                $sub->addChild('Klub', $club);
                $sub->addChild('VarSymbol', $p->transactionNumber);
                $sub->addChild('Ucel', $p->autocont_type);

                $notified[] = [
                    "id" => $p->id,
                    "autocont_notified" => TRUE
                ];
            }
        }

        if (!empty($notified)) {
            // Mark these records as autocont_notified (sent to autocont)
            if ($this->db->where_in("id", $notified)->update_batch("autocont_expected_payments", $notified, "id")) {
                $xml->asXML($this->autocont_folder . $club . "/Import" . "/" . $file_name);
            }
        }
    }

    /**
    * Process items in items[] of a transaction and append row for each item to the original XML => &xml
     */
    private function processItems($items, $transaction, &$xml){
        foreach($items as $item){
            $price = $item->value * $item->amount;

            if($item->itemId != 0){

                if (isset($item->depotId)) {
                    // DEPOT
                    $depot_item = $this->depot->getDepotItem($item->itemId, true);
                }else {
                    // PRICELIST
                    $pricelist_item = $this->pricelist->getPrice($item->itemId);
                }

                // Row
                $depot_itm = $xml->addChild('Radek');
                $depot_itm->addChild('Klub', $transaction->gymCode);
                $depot_itm->addChild('CisloDokladu', $transaction->transactionNumber);
                $depot_itm->addChild('Datum', date('Y-m-d', strtotime($transaction->paidOn)));
                $depot_itm->addChild('TypUctu', 'HK');

                if(isset($depot_item)){
                    $cat = $depot_item["category"];
                    if ($cat == 1 OR $cat == 2) $depot_itm->addChild('Ucet', config_item("app")["autocont_accounts"][9]);
                        else $depot_itm->addChild('Ucet', config_item("app")["autocont_accounts"][10]);
                }else{
                    $acnum = $pricelist_item["account_number"];
                    $depot_itm->addChild('Ucet', config_item("app")["autocont_accounts"][$acnum]);
                }

                $depot_itm->addChild('Ucet', ''); // *


                $depot_itm->addChild('SazbaDPH', ($item->vat*100)); 
                $depot_itm->addChild('Mena', 'CZK'); // *
                if($transaction->refund == 'true'){
                    $depot_itm->addChild('Castka', $price); // *
                    $depot_itm->addChild('CastkaDPH', ( ($transaction->transCategory == 'PK') ? number_format($price * $item->vat, 2, '.', '') : number_format($price * $item->vat, 2, '.', '')) );
                }else{
                    $depot_itm->addChild('Castka', -$price); // *
                    $depot_itm->addChild('CastkaDPH', -( ($transaction->transCategory == 'PK') ? number_format($price * $item->vat, 2, '.', '') : number_format($price * $item->vat, 2, '.', ''))  );
                }
    
                // Note
                if(isset($item->depotId)) $depot_itm->addChild('Text', 'Zboží: ' . $depot_item['name']);
                else $depot_itm->addChild('Text', 'Služba: ' . $pricelist_item($item->itemId)['name']);

            }else{
                // kredit
                $cred = $xml->addChild('Radek');
                $cred->addChild('Klub', $transaction->gymCode);
                $cred->addChild('CisloDokladu', $transaction->transactionNumber);
                $cred->addChild('Datum', date('Y-m-d', strtotime($transaction->paidOn)));
                $cred->addChild('TypUctu', $transaction->refund ? $transaction->transCategory : 'KR');
                $cred->addChild('Ucet', ''); // *
                if($transaction->refund == 'true'){
                    $cred->addChild('Castka', $price); // *
                }else{
                    $cred->addChild('Castka', -$price); // *
                }
                $cred->addChild('Mena', 'CZK'); // *
                $cred->addChild('Text', 'Navýšení kreditu');
            }

            // Item has a discount applied and it is > 0% (BARTER)
            if (isset($item->value_discount) && $item->value_discount > 0){
                $discount_item = $xml->addChild('Radek');
                $discount_item->addChild('Klub', $transaction->gymCode);
                $discount_item->addChild('CisloDokladu', $transaction->transactionNumber);
                $discount_item->addChild('Datum', date('Y-m-d', strtotime($transaction->paidOn)));
                $discount_item->addChild('TypUctu', 'HK');
                $discount_item->addChild('Ucet', config_item("app")["autocont_marketing_account"]); // *
                $discount_item->addChild('Castka', ($price * ($item->value_discount / 100))); // *
                $discount_item->addChild('Mena', 'CZK'); // *
                $discount_item->addChild('Text', '+Barter');

                $discount_item_negative = $xml->addChild('Radek');
                $discount_item_negative->addChild('Klub', $transaction->gymCode);
                $discount_item_negative->addChild('CisloDokladu', $transaction->transactionNumber);
                $discount_item_negative->addChild('Datum', date('Y-m-d', strtotime($transaction->paidOn)));
                $discount_item_negative->addChild('TypUctu', 'KR');
                $discount_item_negative->addChild('Ucet', config_item("app")["autocont_marketing_account"]); // *
                $discount_item_negative->addChild('Castka', -($price * ($item->value_discount / 100))); // *
                $discount_item_negative->addChild('Mena', 'CZK'); // *
                $discount_item_negative->addChild('Text', '-Barter');
            }
        }
    }
    
    /**
     * Process subscriptions for the XML output and append to original XML => &xml
     */
    private function processSubscription($t, &$xml){
        $sub = $this->API->subscriptions->get_subscription_by_invoice_number($t->subscriptionContractNumber);
        $sub_period = $sub->data->subPeriod;

        $sb_acc = $xml->addChild('Radek');
        $sb_acc->addChild('Klub', $t->gymCode);
        $sb_acc->addChild('CisloDokladu', $t->transactionNumber);
        $sb_acc->addChild('Datum', date('Y-m-d', strtotime($t->paidOn)));
        $sb_acc->addChild('TypUctu', 'HK');

        $sb_acc->addChild('Ucet', config_item("app")["autocont_accounts"][0]); // tržby za členství
        
        $sb_acc->addChild('SazbaDPH', $t->vat); 
        $sb_acc->addChild('Mena', 'CZK'); // *
        if($t->refund == 'true'){
            $sb_acc->addChild('Castka', $t->value); // *
            $sb_acc->addChild('CastkaDPH', number_format($t->vat_value, 2, '.', ''));
        }else{
            $sb_acc->addChild('Castka', -$t->value); // *
            $sb_acc->addChild('CastkaDPH', -number_format($t->vat_value, 2, '.', ''));
        }

        if(!empty($sub->data) && ($sub_period == "year" OR $sub_period == "quarter")){
            // Its a payment for the whole timeline of the sub..
            $sb_acc->addChild('CasOd', date('Y-m-d', strtotime($sub->data->transactions[0]->start)));
            $sb_acc->addChild('CasDo', date('Y-m-d', strtotime(end($sub->data->transactions)->end)));
        }

        $sb_acc->addchild('Text', 'Platba za členství');

            // Sub has a discount applied and it is > 0% (BARTER)
            if (isset($t->value_discount) && $t->value_discount > 0){
                $discount_item = $xml->addChild('Radek');
                $discount_item->addChild('Klub', $t->gymCode);
                $discount_item->addChild('CisloDokladu', $t->transactionNumber);
                $discount_item->addChild('Datum', date('Y-m-d', strtotime($t->paidOn)));
                $discount_item->addChild('TypUctu', 'HK');
                $discount_item->addChild('Ucet', config_item("app")["autocont_marketing_account"]); // *
                $discount_item->addChild('Castka', ($t->value * ($t->value_discount / 100))); // *
                $discount_item->addChild('Mena', 'CZK'); // *
                $discount_item->addChild('Text', '+Barter');

                $discount_item_negative = $xml->addChild('Radek');
                $discount_item_negative->addChild('Klub', $t->gymCode);
                $discount_item_negative->addChild('CisloDokladu', $t->transactionNumber);
                $discount_item_negative->addChild('Datum', date('Y-m-d', strtotime($t->paidOn)));
                $discount_item_negative->addChild('TypUctu', 'KR');
                $discount_item_negative->addChild('Ucet', config_item("app")["autocont_marketing_account"]); // *
                $discount_item_negative->addChild('Castka', -($t->value * ($t->value_discount / 100))); // *
                $discount_item_negative->addChild('Mena', 'CZK'); // *
                $discount_item_negative->addChild('Text', '-Barter');
            }
        
    }

    /**
     * Process voucher sales and rows to the original XML => &xml
     */
    private function processVouchers($t, &$xml, $invoice = FALSE){
            $obj = $xml->addChild('Radek');
            $obj->addChild('Klub', $t->gymCode);
            $obj->addChild('CisloDokladu', $t->transactionNumber);
            $obj->addChild('Datum', date('Y-m-d', strtotime($t->paidOn)));
            $obj->addChild('TypUctu', 'VO');
            $obj->addChild('Ucet', ''); // *

            if($t->refund == 'true'){
                $obj->addChild('Castka', $t->value); // *
            }else{
                $obj->addChild('Castka', -$t->value); // *
            }
            $obj->addChild('Mena', 'CZK'); // *
            $obj->addchild('Text', 'Nákup voucheru => '.$t->voucherIdentification.' ');

            // Sub has a discount applied and it is > 0% (BARTER)
            if (isset($t->value_discount) && $t->value_discount > 0){
                $discount_item = $xml->addChild('Radek');
                $discount_item->addChild('Klub', $t->gymCode);
                $discount_item->addChild('CisloDokladu', $t->transactionNumber);
                $discount_item->addChild('Datum', date('Y-m-d', strtotime($t->paidOn)));
                $discount_item->addChild('TypUctu', 'HK');
                $discount_item->addChild('Ucet', ''); // *
                $discount_item->addChild('Castka', ($t->value * ($t->value_discount / 100))); // *
                $discount_item->addChild('Mena', 'CZK'); // *
                $discount_item->addChild('Text', '+Barter');

                $discount_item_negative = $xml->addChild('Radek');
                $discount_item_negative->addChild('Klub', $t->gymCode);
                $discount_item_negative->addChild('CisloDokladu', $t->transactionNumber);
                $discount_item_negative->addChild('Datum', date('Y-m-d', strtotime($t->paidOn)));
                $discount_item_negative->addChild('TypUctu', 'KR');
                $discount_item_negative->addChild('Ucet', ''); // *
                $discount_item_negative->addChild('Castka', -($t->value * ($t->value_discount / 100))); // *
                $discount_item_negative->addChild('Mena', 'CZK'); // *
                $discount_item_negative->addChild('Text', '-Barter');
            }
    }

    /** 
     * Process invoices
     */
    private function processInvoices($t, &$xml){
        $invoice = $this->db->where("id", $t->invoiceId)->get("invoices")->row();

        $obj = $xml->addChild('Radek');
        $obj->addChild('Klub', $t->gymCode);
        $obj->addChild('CisloDokladu', $t->transactionNumber);
        $obj->addchild('VariabilniSymbol', $t->paymentIdentificationNumber);
        
        $obj->addChild('Datum', date('Y-m-d', strtotime($t->paidOn)));
        $obj->addChild('Splatnost', date('Y-m-d', strtotime($invoice->due_date)));
        $obj->addChild('DUZP', date('Y-m-d', strtotime($invoice->date_created)));

        $obj->addChild('TypUctu', 'HK');
        $obj->addChild('Ucet', ''); // *

        if($t->refund == 'true'){
            $obj->addChild('Castka', $t->value); // *
        }else{
            $obj->addChild('Castka', -$t->value); // *
        }
        $obj->addChild('Mena', 'CZK'); // *
        $obj->addchild('Text', 'Faktura => '.$t->paymentIdentificationNumber.' ');
    }

    /**
     * Generate an XML from a set of transactions
     * 
     * tranKKRRMMDDnn
     * KK => club id (01, 02)
     * RR => year
     * MM => month
     * DD => day
     * nn => iteration in a day
     */
    public function create_transaction_xml($club, $date = FALSE){
        if(!$date) $date("Y-m-d 00:00:01");

        // Check dupes
        $exported = $this->db->where('date', $date)->where('club', $club)->get('autocont_exported_days')->row();
        if($exported) exit('Already parsed.');

        // Check if day is locked
        $unlocked = $this->API->transactions->get_transactions(['locked' => TRUE, 'paidOn' => $date, 'gymCode' => $club]);
        if(is_object($unlocked) && $unlocked->success && isset($unlocked->data) && !empty($unlocked->data)) exit('Day not locked yet OR empty.');
        // TODO: Send an email about non-locked day!!

        // Get transactions & invoices
        $transactions = $this->payments->getAll(["filters" => [ ["field" => "paidOn", "value" => $date], ["field" => "gymCode", "value" => $club] ]]);
        $transactions = isset($transactions["data"]) ? $transactions['data'] : FALSE;
        if(!$transactions) exit('No transactions to parse.');

        $file_name = 'tran' . $club . date('Ymd') . '01.xml';
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><Transakce></Transakce>');

        if( !empty($transactions) ) {
            foreach ($transactions as $t){
                
                $client = $this->users->getUserData($t->clientId);
                $items = (isset($t->items)) ? $t->items : []; // items
    
                // Transaction row
                $trans = $xml->addChild('Radek'); // row
    
                $trans->addChild('Klub', $t->gymCode); // *
                $trans->addChild('CisloDokladu', $t->transactionNumber); // * cislo transakce (custom inkrementál)
                $trans->addChild('Datum', date('Y-m-d', strtotime($t->paidOn))); // *
                $trans->addChild('TypUctu', $t->transCategory); // HO, OD, BA, PK, KR, VO, atd.
                $trans->addChild('Ucet', ''); // todo
                $trans->addChild('Mena', 'CZK'); // *
    if($client) $trans->addChild('DIC', !is_null($client->vat_id) ? $client->country . $client->vat_id : ''); // dič klienta
                if($t->refund == 'true'){ // Storno?
                    $trans->addChild('Storno', 'Ano');
                    $trans->addChild('Castka', -$t->value); // *
                }else{
                    $trans->addChild('Storno', 'Ne');
                    $trans->addChild('Castka', $t->value); // *
                }

                // Faktura
                if($t->transCategory == 'OD' && isset($t->invoiceId)){
                    $invoice = $this->db->where("id", $t->invoiceId)->get("invoices")->row();

                    $trans->addChild('DatumSplatnosti', date('Y-m-d', strtotime($invoice->due_date)));
                    $trans->addChild('DUZP', date('Y-m-d', strtotime($invoice->date_created)));
                    $trans->addchild('VariabilniSymbol', $t->paymentIdentificationNumber);
                }
    
                // Platba kartou
                if($t->transCategory == 'PK'){
                    $trans->addChild('CisloTerminalu', $t->terminalId);
                    $trans->addChild('AutorizacniKod', $t->paymentIdentificationNumber);
                }
                // End transaction row
    

                // Prodej Členství
                if(isset($t->subscriptionPayment) && $t->subscriptionPayment == 'true'){
                    $this->processSubscription($t, $xml);

                    $sub = $this->API->subscriptions->get_subscription_by_invoice_number($t->subscriptionContractNumber);
                    if(!empty($sub->data) && ($sub_period == "year" OR $sub_period == "quarter")){
                        // Its a payment for the whole timeline of the sub..
                        $trans->addChild('CasOd', date('Y-m-d', strtotime($sub->data->transactions[0]->start)));
                        $trans->addChild('CasDo', date('Y-m-d', strtotime(end($sub->data->transactions)->end)));
                    }

                    $trans->addChild('Text', 'Platba za členství');
                }
    
                // Prodej Voucheru
                if(isset($t->voucherSale) && $t->voucherSale){
                    $this->processVouchers($t, $xml);
                    $trans->addchild('Text', 'Nákup voucheru => '.$t->voucherIdentification.' ');
                }
    
                // Prodej na fakturu
                if(isset($t->invoiceId) && $t->invoiceId){
                    $this->processInvoices($t, $xml);
                    $trans->addchild('Text', 'Faktura => '.$t->paymentIdentificationNumber.' ');
                }
    
                // Prodej zboží / služeb
                if(!empty($items)){
                    $this->processItems($items, $t, $xml);
                    $trans->addChild('Text', 'Prodej služeb a zboží');
                }
    
            }
        }

        if($this->db->insert('autocont_exported_days', ['date' => $date, 'club' => $club])){
            $xml->asXML($this->autocont_folder . $club . "/Import" . "/" . $file_name);
        }
    }

}
<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Aktuální mongoDB transactions model
 * 
 *   parentTransaction: { type: mongoose.ObjectId, required: false}, // ObjectId nadřazené transakce (např. v případě rozdělení metody platby)
 *   paidOn: { type: Date, default: Date.now, required: true }, // Kdy zaplaceno
 *   gymId: { type: String, required: true }, // ID klubu
 *   gymCode: { type: String, required: true }, // Kód klubu (01, 02, 03,..)
 *   transCategory: { type: String, default: 'PK' }, // Kategorie transakce (string)
 *   transType: { type: Number, required: true }, // Typ transakce
 *   
 *   depotId: { type: [Number], required: false }, // Id skladu (pole int) ze kterého položka pochází
 *   depotItemId: { type: [Number], required: false }, // Id skladové položky (pole int)
 *   itemId: { type: [Number], required: false}, // Id položky (ne sklad, ale ceník) (pole int)
 *  
 *   terminalId: { type: String, required: false }, // Id terminálu
 *   clientId: { type: String, required: true }, // Id kupujícího klienta
 *   employeeId: { type: String, required: true }, // Id zaměstnance provádějícího platbu
 *   invoiceId: { type: String, required: false }, // Id faktury TODO
 *   cardId: { type: String, required: true }, // Id klubové karty
 *   currency: { type: String, default: 'CZK' }, // Měna, default CZK
 *   value: { type: Number, required: true }, // Hodnota transakce
 *   vat: { type: Number, required: true }, // % hodnota DPH
 *   vat_value: { type: Number, required: true } // částka s DPH
 */

/**
 * Autoloaded model for payments
 */
class Payments_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('depot_model', 'depot');
        $this->load->model('clients_model', 'clients');
        $this->load->model('eetapp_model', 'eetapp');
        $this->load->model('vouchers_model', 'vouchers');
        $this->load->model('cards_model', 'cards');
        $this->load->model('contract_model', 'contract');

        // transaction categories
        $this->transCategories = [
            1 => ['key' => 'HO', 'value' => 'Hotově'],
            2 => ['key' => 'PK', 'value' => 'Platební kartou'], 
            3 => ['key' => 'KR', 'value' => 'Kredit'],
            4 => ['key' => 'MS', 'value' => 'Multisport'],
            5 => ['key' => 'VO', 'value' => 'Voucher'],
            6 => ['key' => 'ET', 'value' => 'E-ticket'],
            7 => ['key' => 'SO', 'value' => 'Sodexo'],
            8 => ['key' => 'BP', 'value' => 'Benefit plus (objednávka)'],
            9 => ['key' => 'BP', 'value' => 'Benefit plus (karta)'],
            10 => ['key' => 'PK', 'value' => 'Benefit plus (platební karta)'],
            11 => ['key' => 'ED', 'value' => 'Edenred'],
            12 => ['key' => 'BE', 'value' => 'Benefit a.s. (karta)'],
            13 => ['key' => 'OD', 'value' => 'Na fakturu'],
            14 => ['key' => 'BA', 'value' => 'Bankovmí převod'],
            15 => ['key' => 'VO', 'value' => 'Voucher'],
        ];
    }

    public function returnTransCategories(){ return $this->transCategories; }

    public function refund_sub(){
        $p = $_POST;
        $note = $p['note'];
        $cat = $p['refund_cat'];

        if($cat == 'unpaid_future'){
            // Nezaplacený měsíc v budoucnu
            $refund_type = $p['refund_type']; // druh storna, 0 => Vynechání , 1 => zrušení členství

            if($refund_type == 0){
                // Cancel the month and dont reschedule it
                if($this->API->subscriptions->cancel_month($p['contract_number'], $p['transaction_id'], FALSE, $note)) return TRUE;
                else return FALSE;
            }else{
                // Subscription cancelation
                // TODO: Manage the cancellation of the whole subscription (if needed)
                if($this->API->subscriptions->remove_subscription($p['contract_number'], $p['transaction_id'])) return TRUE;
                else return FALSE;
            }

        }else{
            // Ostatní => Zaplacené budoucí/minulé i nezaplacený minulý
            $compensation = $p['compensation']; // kompenzace, 0 => žádná, 1 => posun členství

            if($compensation == 1){
                $subscription = $this->API->subscriptions->get_subscription_by_invoice_number($p['contract_number'])->data;
                if($subscription){
                    $months = $subscription->transactions;
                    $last_month = end($months);

                    $start = date('Y-m-d', strtotime($last_month->end . ' +1 day'));
                    if($subscription->subPeriod == 'month'){
                        $end = date('Y-m-d', strtotime($start . '+1 month'));
                    }else if ($subscription->subPeriod == 'quarter'){
                        $end = date('Y-m-d', strtotime($start . '+3 months'));
                    }else{
                        return FALSE; // cant skip a year (?)
                    }

                    $new_month = [
                        'paid' => $p['paid'],
                        'start' => $start,
                        'end' => $end,
                        'vat' => $last_month->vat,
                        'vat_value' => $last_month->vat_value,
                        'value' => $last_month->value,
                        'gymId' => $this->session->gym_id,
                        'gymCode' => (strlen(explode('gymit', $this->session->gym_db)[1]) > 0) ? (string) '0' .explode('gymit', $this->session->gym_db)[1] : (string) '0' .'1',
                        "note" => $note
                    ];

                    if($this->API->subscriptions->cancel_month($p['contract_number'], $p['transaction_id'], $new_month)){
                        // Marketing transaction (?)
                        $this->API->transactions->add_transaction([
                            'marketingSale' => TRUE,
                            'gymId' => $this->session->gym_id,
                            'gymCode' => (strlen(explode('gymit', $this->session->gym_db)[1]) > 0) ? (string) '0' .explode('gymit', $this->session->gym_db)[1] : (string) '0' .'1',
                            'transCategory' => 'MA', // NOTE: pro autocont bez označení!
                            'transType' => 0,
                            'clientId' => $subscription->clientId,
                            'cardId' => $subscription->cardId,
                            'vat' => $last_month->vat,
                            'vat_value' => $last_month->vat_value,
                            'value' => $last_month->value,
                            'text' => strlen($note) > 0 ? $note : 'Náhrada za stornovaný termín členství',
                            'employeeId' => gym_userid()
                        ]);
                        return TRUE;
                    }else{
                        return FALSE;
                    }

                }else{
                    return FALSE;
                }
            }else{
                // Cancel the month and dont reschedule it
                if ($this->API->subscriptions->cancel_month($p['contract_number'], $p['transaction_id'], FALSE, $note)){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }
        }
    }

    /** 
     * Cancel a payment before it is closed (deletes the transaction basically)
     */
    public function refund_payment(){
        $p=$_POST;

        $data = [];
        $transaction_id = $p['transaction_id'];
        $cc_refund = $p["cc_refund"];
        $original = $this->API->transactions->get_transaction_by_id($transaction_id)->data;

        if($cc_refund !== "false" && $cc_refund){

            $original = $this->API->transactions->get_transaction_by_id($transaction_id)->data;
            if(!empty($original)){

                $original->refund = TRUE;
                $original->employeeId = gym_userid();
                $original->paymentIdentificationNumber = $p["variableSymbol"] ?? false;
                $original->terminalId = $p["terminalId"] ?? false;
                unset($original->_id);
                unset($original->transactionNumber);
                unset($original->paidOn);
                $original->text = "Storno transakce";

                $transactionData = $this->API->transactions->add_transaction($original);
                if($transactionData->success==1){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }

        }else{
            if($this->API->transactions->delete_transaction($transaction_id)){

                /**
                 * TODO: Manage restocking back destocked items maybe?
                 */

                return TRUE;
            }else{
                return FALSE;
            }
        }
    }

    public function edit_payment() {
        $p=$_POST;

        $data = [];
        $transaction_id = $p['transaction_id'];
        $original = $this->API->transactions->get_transaction_by_id($transaction_id)->data; // for comparisons

        foreach($p['purchase_type'] as $purchase_type_id => $price){
            $data['transCategory'] = $this->transCategories[$purchase_type_id]['key'];
            $data['transType'] = $purchase_type_id;

            $data['value'] = $price; // value with DPH
            if($purchase_type_id == 1){
                // If cash > round up
                $data['value'] = round($data['value']);
            }

            $data['subscriptionPayment'] = FALSE;

            $depotItems=$serviceItems=[];
            foreach($p['items'] as $type => $items){
                switch($type){
                    case 'depot': 
                        $depotItems = $this->processDepotItems($items); 
                    break;
                    case 'service': 
                        $serviceItems = $this->processServiceItems($items,$data); 
                    break;
                    case 'subscription': 
                        $data['value_discount'] = $items[0]['discount'];
                        $data['subscriptionPayment'] = TRUE;
                        $data['subscriptionContractNumber'] = $p['subscriptionContractNumber'];
                        if(isset($p['subscriptionSubPaymentId'])) $data['subscriptionSubPaymentId'] = $p['subscriptionSubPaymentId'];

                        $data['vat'] = 0.21;
                        $data['vat_value'] = $price * 0.21;
                    break;
                    default: 
                    break;
                }
            }
        }

        $data['items'] = array_merge($depotItems,$serviceItems);
        // Stock control
        if(!empty($data['items'])) foreach ($original->items as $item) {
                foreach($data['items'] as $new_item){
                    if(isset($new_item['depotId']) && $item->itemId == $new_item['itemId'] && $item->depotId == $new_item['depotId']){
                        // stock change
                        if($new_item['amount'] != $item->amount){
                            if($new_item['amount'] == 0){
                                // new amount is 0 => return all previously stocked amounts
                                $this->depot->moveDepotItemStock(['movement_type'=>1, 'to_depot_id'=>$item->depotId, 'item_id'=>$item->itemId, 'buy_price'=>$item->value, 'quantity'=>$item->amount, 'note'=>'Úprava transakce -> Vratka položek']); // naskladneni
                            }else{
                                // new amount is higher/lower
                                $difference = $new_item['amount'] - $item->amount;
                                if($difference < 0) $this->depot->takeDepotItemStock(['depot_id'=>$new_item['depotId'], 'item_id'=>$new_item['itemId'], 'quantity'=>abs($difference), 'note'=>'Úprava transakce -> zvýšení počtu kusů', 'sale_price'=>$new_item['value']], 'sale'); // vyskladneni
                                else if ($difference > 0) $this->depot->moveDepotItemStock(['movement_type'=>1, 'to_depot_id'=>$new_item['depotId'], 'item_id'=>$new_item['depotId'], 'buy_price'=>$new_item['value'], 'quantity'=>$difference, 'note'=>'Úprava transakce -> snížení počtu kusů']); // naskladneni
                            }
                        }
                    }
                }
        }

        // Subscription transaction
        if($data['subscriptionPayment']){
            if(isset($p['subscriptionSubPaymentId'])){
                // payment for exisitng membership (recurring), edit the values in the actual timeblock
                $this->API->subscriptions->update_subscription_subdocument($p['subscriptionSubPaymentId'], $data);
            }else{
                // payment for top level sub
                $this->API->subscriptions->edit_subscription($data['subscriptionContractNumber'], $data);
            }
        }

        $saveTransaction = $this->API->transactions->edit_transaction($transaction_id, $data);

        if($saveTransaction){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    public function pay(){
        $p=$_POST;
        $toReturn = [];

        $data = [];
        $data['gymId'] = $this->session->gym_id;
        $data['gymCode'] = current_gym_code();
        $data['clientId'] = $p['clientId'];
        $data['employeeId'] = gym_userid();
        $data['cardId'] = $p['clientCardId'];
        $data['checkoutId'] = $p['checkoutId'];
        // Pair new card
        if($p['newCard'] && !$this->cards->addCardPair($data['clientId'],$data['cardId'])) echo "cannot pair card";

        $data['text'] = $p['note'];

        $data['parentTransaction'] = null;
        $data['subscriptionPayment'] = FALSE;

        // Missing purchase_type -> Everything is gratis (set purchase type 1 -> HOTOVOST)
        $p['purchase_type'] = $p['purchase_type'] ?? [1=>0]; 

        foreach($p['purchase_type'] as $purchase_type_id => $price){
            $data['transCategory'] = $this->transCategories[$purchase_type_id]['key']; // remap id of purchase type for AUTOCONT
            $data['transType'] = $purchase_type_id; // keep our id of purchase type

            $credit_expected_payment = FALSE;

            if($purchase_type_id == 14) $data["paid"] = FALSE; // Bank transfer is unpaid at first

            // "Authorization code" from the CC terminal
            // Terminal ID that made the payment..
            if($purchase_type_id == 2 && isset($p["paymentIdentificationNumber"])){
                $data["paymentIdentificationNumber"] = $p["paymentIdentificationNumber"];
                $data["terminalId"] = $p["terminalId"];
            }            

            if($purchase_type_id==5){ // Vouchers
                $vouchers = $this->processVoucherActivation($price['voucher_codes'],$data);
                if(is_null($data['parentTransaction'])){ // this is first (PARENT) transaction
                    $data['parentTransaction'] = $vouchers['trans_id']; // set parent transaction
                }
                continue;
            }            

            $depotItems=$serviceItems=$new_sub=$pay_sub=[];
            foreach($p['items'] as $type => $items){
                switch($type){
                    case 'depot': 
                        $depotItems = $this->processDepotItems($items); // returns items for data['items']
                    break;
                    case 'new_subscription': 
                        $new_sub = $this->processNewSubscription($items, $data); // returns transaction data
                        $data['subscriptionParentTransaction'] = empty($data['subscriptionParentTransaction']) ? $new_sub->transaction_id : $data['subscriptionParentTransaction']; 
                        if(is_null($data['parentTransaction']) && !is_null($data['subscriptionParentTransaction'])){ 
                            $data['parentTransaction'] = $data['subscriptionParentTransaction']; // set parent transaction
                        }
                        
                        $data['deposit']=true; // do not create another transaction in processSubscription function

                        $toReturn['contractNumber'] = $new_sub->contractNumber;
                    break;
                    case 'subscription': 
                        $pay_sub = $this->processSubscription($items, $data); // returns transaction data
                        $data['subscriptionParentTransaction'] = empty($data['subscriptionParentTransaction']) ? $pay_sub->transaction_id : $data['subscriptionParentTransaction']; 
                        if(is_null($data['parentTransaction']) && !is_null($data['subscriptionParentTransaction'])){ 
                            $data['parentTransaction'] = $data['subscriptionParentTransaction']; // set parent transaction
                        }
                    break;
                    case 'service': 
                        $services = $this->processServiceItems($items,$data); // returns items for data['items']
                        $serviceItems = $services['items'];
                        if(is_null($data['parentTransaction']) && !is_null($services['trans_id'])){ // There was a voucher in service items and it has own transaction
                            $data['parentTransaction'] = $services['trans_id']; // set parent transaction if not set
                        }

                        if (isset($data["is_credit_expected_payment"])) {
                            $credit_expected_payment = TRUE;
                            unset($data["is_credit_expected_payment"]);
                        }

                        if(!empty($services['vouchers'])){
                            $toReturn['vouchers'] = $this->vouchers->getVouchersByCodes($services['vouchers']);
                        }
                    break;
                    default: 
                    break;
                }
            }

            $data['items']=array_merge($depotItems,$serviceItems);

            // TOTAL VALUE OF TRANSACTION ITEMS (including VAT)
            $data['value']=0;
            foreach ($data['items'] as $item){
                $data['value'] += ($item['value'] + $item['vat_value']) * ($item['amount'] ?? 1);
            }

            if($purchase_type_id == 1 && isset($data['value'])){
                // If cash > round up
                $data['value'] = round($data['value']);
                $openCashdesk = true;
            }
        
            if(!empty($data['items'])){ // create transaction if set items
                // Parent and child transaction because of multiple purchase types

                
                $transactionData = $this->API->transactions->add_transaction($data);
                if(is_null($data['parentTransaction'])){ // this is first (PARENT) transaction
                    $data['parentTransaction'] = $transactionData->transaction_id; // set parent transaction

                    // Log benefits and solarium usage
                    foreach ($data['items'] as $item){
                        if($item['benefit']>0) $this->pricelist->useMembershipBenefit($item['benefit'],$data['clientId'],$data['parentTransaction']);
                        if(!isset($item['depotId']) && $item['solarium']>0) $this->gyms->insertSolariumUsage($item['solarium'],$data['parentTransaction'],$item['amount'],current_gym_db());
                    }                    
                }

                // BA payment (bank transfer)
                if($data["transType"] == 14){
                    // Expect a payment for this one
                    $expected_payment = [
                        "client_id" => $data["clientId"],
                        "transactionNumber" => $transactionData->transaction_number
                    ];

                    // Setup different types
                    if (isset($data["voucherSale"])) $expected_payment["autocont_type"] = "Voucher";
                    else if ($credit_expected_payment) $expected_payment["autocont_type"] = "Kredit";

                    $this->autocont->expect_payment($expected_payment);
                }
            }

            if($data['transType'] == 3){ // Pay by credit
                $currentCredit = $this->API->transactions->get_credit($p['clientId'],$data['cardId'])->data->currentValue;
                $this->API->transactions->set_credit($p['clientId'],$data['cardId'],$currentCredit - $price);                
            }

            // EET
            if($purchase_type_id==1){ // Only pay by cash goes to EET
                $eet=[];
                $eet['payment']=1; // pay by cash (card=2)
                $eet['reference']=$data['parentTransaction']; // transaction number -> parentTransaction
                $eet['device']=$data['checkoutId']; // checkout id
                $eet['items']=$this->eetapp->prepareData4pay($p['items']); // remap items for EET structure
                $this->eetapp_lib->newReceipt($eet);
            }
        }
        
        // Create & print Receipt
        if(isset($vouchers)){ // append credit of all vouchers to receipt
            $item['name']='Dobití kreditu';
            $item['amount']=1;
            $item['vat']=0;
            $item['vat_value']=0;
            $item['value']=$vouchers['credit'];
            $item['discount']=0;
            $p['purchase_type'][5] = $vouchers['credit']; // how much was paid by voucher
            $p['items']['service'][1000] = $item;
        }
        $tmPrinterItems=$this->eetapp->prepareData4pay($p['items']);
        $receipt = [
            'items' => $tmPrinterItems,
            'purchaseTypes' => $p['purchase_type'],
            'transactionId' => $data['parentTransaction'],
            'gymCode' => $data['gymCode'],
            'checkoutId' => $data['checkoutId'],
            'multisportItem' => isset($p['multisport_item']) ? $p['multisport_item'] : false
        ];
        $receipt['receiptId'] = $this->createReceipt($receipt);
        //$this->tmprinter->print($receipt,$openCashdesk ?? false);
        $this->API->transactions->print_receipt($receipt, $openCashdesk ?? false);

        // invoices
        if(isset($p['vatInfo']) && !empty($p['vatInfo']['vat_id'])){
            $invoice_id = $this->processInvoiceCreation($p);
        }

        $toReturn["invoiceId"] = $invoice_id ?? null;
        return $toReturn;
    }

    private function createReceipt(array $data){
        $receipt = [
            'gymCode' => $data['gymCode'],
            'transactionId' => $data['transactionId']
        ];

        unset($data['gymCode']);
        unset($data['transactionId']);
        //$receipt['data'] = json_encode($data);
        $receipt = $this->API->transactions->add_receipt(json_encode($data), $receipt["transactionId"], $receipt["gymCode"]);

        if($receipt && $receipt->receiptNumber) return $receipt->receiptNumber;
        else return false;
    }

    private function processInvoiceCreation(array $data){
        $invoice = [];

        foreach($data['items'] as $type => $items){
            if($type=='depot'){
                foreach($items as $depot_id => $items_id){
                    foreach($items_id as $item_id => $item){
                        $i['item_type']=$type;
                        $i['item_id']=$item_id;
                        $i['item_name']=$item['name'];
                        $i['item_value']=$item['value'] + $item['vat_value'];
                        $i['item_amount']=$item['amount'];
                        $i['item_discount']=$item['discount'] ?? 0;
                        $invoiceItems[]=$i;
                    }
                }
            } else {
                foreach($items as $item_id => $item){
                    $i['item_type']=$type;
                    $i['item_id']=$item_id;
                    $i['item_name']=$item['name'];
                    $i['item_value']=$item['value'] + $item['vat_value'];
                    $i['item_amount']=$item['amount'];
                    $i['item_discount']=$item['discount'] ?? 0;
                    $invoiceItems[]=$i;
                }
            }
        }

        $invoice['value'] = $invoice['vat_value'] = 0;
        foreach($invoiceItems as $item){
            $total = $item['item_value'] - (( ($item["item_discount"]??0) / 100) * $item["item_value"]);

            $invoice['value'] += $total;
            $invoice['vat_value'] += $total;
        }

        $invoice = [
            'invoice_number' => $this->getNextInvoiceNumber($data['clientId'], current_gym_code()),
            'payment_method' => array_keys($data['purchase_type'], max($data['purchase_type']))[0],
            'paid' => 1,
            'created_by' => gym_userid(),
            'client_id' => $data['clientId'],
            'client_name' => $data['vatInfo']['name'],
            'client_street' => $data['vatInfo']['street'],
            'client_city' => $data['vatInfo']['city'],
            'client_zip' => $data['vatInfo']['zip'],
            'client_state' => $data['vatInfo']['country'],
            'client_vat_id' => $data['vatInfo']['vat_id'],
            'client_company_id' => $data['vatInfo']['company_id'],
            'issue_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d',strtotime("+".config_item('app')['invoice_due_days']." day")),
            'items' => json_encode($invoiceItems, JSON_UNESCAPED_UNICODE),
            'value' => $invoice['value'],
            'vat_value' => $invoice['vat_value'],
            'status' => 0 // new (not in autocont payments), 1 => in autocont
        ];

        if($this->db->insert('invoices', $invoice)){
            return $this->db->insert_id();
        } else {
            echo 'Invoice creation fail';
            return false;
        }
    }

    public function processVoucherCreation(array $data){
        $voucherCodes=[];
        for($i=1;$i<=$data['amount'];$i++){
            $voucherCodes[] = $this->vouchers->createVoucher('service',$data['itemId'],$data['gymCode']);
        }
        $data['voucherSale'] = TRUE;
        $data['voucherIdentification'] = $voucherCodes;
        
        $item['itemId'] = $data['itemId'];
        $item['value'] = $data['value'] + $data['vat_value'];
        $item['vat'] = 0; // Voucher is without VAT!
        $item['vat_value'] = 0; // Voucher is without VAT!
        $item['amount'] = $data['amount'];

        $data['items'][]=$item;

        $data['value'] = ($data['value'] + $data['vat_value']) * $data['amount']; // total value

        // Create transaction
        $transactionData = $this->API->transactions->add_transaction($data);
        $this->vouchers->setIdentification($voucherCodes,'payments',$transactionData->transaction_id);
        if(is_null($data['parentTransaction'])){ // this is first (PARENT) transaction
            return ['trans_id' => $transactionData->transaction_id, 'codes' => $voucherCodes]; // set parent transaction
        } else return ['trans_id' => $data['parentTransaction'], 'codes' => $voucherCodes];
    }

    public function processVoucherActivation(array $codes, array $data){
        $totalCredit = 0;
        foreach ($codes as $code){
            $voucher = $this->vouchers->getVoucher($code); // get info about voucher
            $this->vouchers->disableVoucher($code,$data['clientId']); // disable voucher    

            $item['itemId'] = 0;
            $item['value'] = $voucher->vat_price; // price with VAT
            $item['vat'] = 0; // credit is withou VAT!
            $item['vat_value'] = 0; // credit is withou VAT!
            $item['amount'] = 1;

            $data['items'][]=$item;

            $data['value'] = $voucher->vat_price; // total value

            $totalCredit += $item['value'];

            // Set credit
            $currentCredit = $this->API->transactions->get_credit($data['clientId'],$data['cardId'])->data->currentValue;
            $this->API->transactions->set_credit($data['clientId'],$data['cardId'],$currentCredit + $data['value']);

            // Create transaction
            $transactionData = $this->API->transactions->add_transaction($data);
            $data['parentTransaction'] = $transactionData->transaction_id;
        }
        return ['credit'=>$totalCredit,'trans_id'=>$data['parentTransaction']];
    }    

    public function processServiceItems(array $items, array &$p, $edit = FALSE){
        $ret=$voucherCodes=[];
        foreach($items as $item_id=>$item){
            $data=[];
            if($item_id===0){ // credit
                $currentCredit = $this->API->transactions->get_credit($p['clientId'],$p['cardId'])->data->currentValue;
                $data['itemId'] = 0;
                $data['value'] = $item['value'] + $item['vat_value']; // price with VAT
                $data['vat'] = 0; // credit is withou VAT!
                $data['vat_value'] = 0; // credit is withou VAT!
                $data['value_discount'] = $item['discount'];
                
                if ($p["transType"] == 14) $p['is_credit_expected_payment'] = TRUE; // Note that its credit if paying by BA
                if(!$edit) $this->API->transactions->set_credit($p['clientId'],$p['cardId'],$currentCredit + $item['value']);
            } else {
                $isOvertime = filter_var($item['isOvertime'], FILTER_VALIDATE_BOOLEAN);
                $data['itemId']=$item_id;
                if($isOvertime) $data['itemId'] = str_replace('o','',$data['itemId']); // o + itemId -> itemId
                $data['amount']=$item['amount'];
                $data['value']=$item['value'];
                $data['vat_value']=$item['vat_value'];
                $data['vat']=$item['vat'];   
                $data['value_discount'] = $item['discount'];
                $data['isOvertime'] = $isOvertime;

                if($item['service_type']==6){ // Vouchers
                    $vouchersRet = $this->processVoucherCreation(array_merge($data,$p));
                    $voucherCodes = array_merge($voucherCodes,$vouchersRet['codes']);
                    continue; // do not push it to items array
                }
            }
            $data['benefit']=$item['benefit'] ?? false;
            $data['solarium']=$item['solarium'] ?? false;
            array_push($ret,$data);
        }
        return ['items' => $ret, 'trans_id' => $vouchersRet['trans_id'] ?? null, 'vouchers' => $voucherCodes];
    }

    public function processDepotItems(array $items){
        $ret=[];
        foreach($items as $depot_id=>$items_id){
            $data=[];
            foreach($items_id as $item_id=>$item){
                $data['itemId']=$item_id;
                $data['depotId']=$depot_id;
                $data['amount']=$item['amount'];
                $data['value']=$item['value'];
                $data['vat_value']=$item['vat_value'];
                $data['vat']=$item['vat'];
                $data['value_discount'] = $item['discount'];
                $data['benefit']=$item['benefit'] ?? false;
                $this->depot->takeDepotItemStock(['depot_id' => $depot_id, 'item_id' => $item_id, 'quantity' => $item['amount'], 'note' => 'Pokladna', 'sale_price' => $item['value']], 'sale');
            }
            array_push($ret,$data);
        }
        return $ret;
    }

    public function processSubscription(array $items, array $p){
        foreach($items as $item_id=>$item){
            // Reccuring/existing subscription payment and also deposits
            
            // initial data
            $data['gymId'] = $p['gymId'];
            $data['gymCode'] = $p['gymCode'];
            $data['clientId'] = $p['clientId'];
            $data['employeeId'] = $p['employeeId'];
            $data['cardId'] = $p['cardId'];
            $data['text'] = $p['text'];
            $data['transCategory'] = $p['transCategory'];
            $data['transType'] = $p['transType'];

            // memmbership data
            $membership = $this->pricelist->getMembershipPrice($item_id);
            $data['sub_type'] = $membership->code; // type of sub
            $data['sub_period'] = $membership->period_type; // payment period
            $data['subscriptionContractNumber'] = $item['contract_id']; // contract number
            $data['transactionId'] = (isset($item['transaction_id'])) ? $item['transaction_id'] : FALSE; // ID of transaction
            if(isset($item['transaction_id'])) $data['subscriptionSubPaymentId'] = $item['transaction_id'];
            if(!$data['transactionId']){
                $data['transactionId'] = (!empty($p['subscriptionParentTransaction'])) ? $p['subscriptionParentTransaction'] : FALSE; // Deposit of new subscription
            }

            $data['parentTransaction'] = $p['subscriptionParentTransaction']; // transaction with multiple purchase types

            // Add in some special stuff for subs
            $data['subscriptionContractNumber'] = $data['subscriptionContractNumber'];
            $data['subscriptionPayment'] = TRUE;
            $data['value'] = $item['value'];
            $data['vat'] = $item['vat'];
            $data['vat_value'] = $item['vat_value'];
            $data['value_discount'] = $item['discount'];

            if($data['transactionId']){
                $payUp = $this->API->subscriptions->pay_for_subscription_payment($p['clientId'], $data['gymCode'], [
                    'paid' => true,
                    'transactionId' => $data['transactionId'],
                    'contractNumber' => $data['subscriptionContractNumber']
                ]);
                
                if($payUp && !$p['deposit']){
                    $transactionData = $this->API->transactions->add_transaction($data); // create transaction;
                    if($transactionData) {
                        return $transactionData;
                    }

                    else echo "Cannot create transaction on ".$item['name'];
                } else {
                    if($p['deposit']) return true;
                    else {
                        echo "Cannot create pay_for_subscription_payment";
                        return false;
                    }
                }
            }else{
                // TODO figure out what I meant by "zaloha" :D thx
                echo "Missing transaction ID";
                return false;
            }
        }
    }

    public function processNewSubscription(array $items, array $p){
        // WHOLE NEW SUBSCRIPTION SETUP
        // Not for payment for recurring subscriptions!!

        foreach($items as $item_id=>$item){
            // Reccuring/existing subscription payment and also deposits
            
            // initial data
            $data['gymId'] = $p['gymId'];
            $data['gymCode'] = $p['gymCode'];
            $data['clientId'] = $p['clientId'];
            $data['employeeId'] = $p['employeeId'];
            $data['cardId'] = $p['cardId'];
            $data['text'] = $p['text'];
            $data['transCategory'] = $p['transCategory'];
            $data['transType'] = $p['transType'];            

            // memmbership data
            $membership = $this->pricelist->getMembershipPrice($item_id);
            $membership_id = $membership->membership_id;

            $data['sub_type'] = $membership->code; // type of sub
            $data['sub_period'] = $membership->period_type; // payment period

            $data['parentTransaction'] = $p['subscriptionParentTransaction'] ?? null; // transaction with multiple purchase types

            $data['subscriptionPayment'] = TRUE;
            $data['value'] = $item['value'];
            $data['vat'] = $item['vat'];
            $data['vat_value'] = $item['vat_value'];
            $data['value_discount'] = $item['discount'];

            $sub_start = date("Y-m-d");
            if($p['sub_start'] != $sub_start) $sub_start = $p['sub_start'];

            $initial_transaction = [];
            $future_transactions = [];
            if(!isset(explode('_', $data['sub_type'])[1]) OR explode('_', $data['sub_type'])[1] != 'quarter'){
                if($data['sub_type'] != 'trial' && $data['sub_type'] != 'prepaid_card'){
                    if($data['sub_period'] === 'month'){

                        $initial_transaction = [
                            'paid' => true, 
                            'gymId' => $this->session->gym_id,
                            'gymCode' => $p['gymCode'],
                            'value' => ($data['value']),
                            'vat' => $data['vat'],
                            'vat_value' => ($data['vat_value']),
                            'start' => $sub_start,
                            'end' => date('Y-m-d', strtotime($sub_start . ' +1 month'))
                        ];

                        // month
                        $paid = false;
                        for ($m = 1; $m <= 11; $m++){
                            if($m>=11){
                                $paid = true; // 12th month in advance
                            }

                            $ts = [
                                'paid' => $paid, 
                                'gymId' => $this->session->gym_id,
                                'gymCode' => $p['gymCode'],
                                'value' => ($data['value']),
                                'vat' => $data['vat'],
                                'vat_value' => ($data['vat_value']),
                                'start' => date('Y-m-d', strtotime($sub_start . ' +'.$m.' month')),
                                'end' => date('Y-m-d', strtotime($sub_start . ' +'.($m+1).' month'))
                            ];

                            if($m>=11){
                                $ts['deposit'] = true; // deposit payment

                                // DEPOSIT for last month (12th)
                                $data["value"] += $data["value"]; // add to total
                                $data["vat_value"] += $data["vat_value"]; // add to total
                            }else{
                                // Expect a payment for this one
                                $expected_payments[] = [
                                    "client_id" => $data["clientId"],
                                    "date_created" => date("Y-m-d 00:00:01", strtotime($ts["start"])), // Schedule this into the future
                                    "autocont_type" => "Predplat"
                                ];
                            }
                            $future_transactions[] = $ts;
                        }
                    }else{
                        // year
                        // +1 year in advance payment possible

                        $initial_transaction = [
                            'paid' => true, 
                            'gymId' => $this->session->gym_id,
                            'gymCode' => $p['gymCode'],
                            'value' => $data['value'],
                            'vat' => $data['vat'],
                            'vat_value' => $data['vat_value'],
                            'start' => $sub_start,
                            'end' => date('Y-m-d', strtotime($sub_start . ' +1 year'))
                        ];

                        $future_transactions[] = [
                            'paid' => false, 
                            'gymId' => $this->session->gym_id,
                            'gymCode' => $p['gymCode'],
                            'value' => $data['value'],
                            'vat' => $data['vat'],
                            'vat_value' => $data['vat_value'],
                            'start' => date('Y-m-d', strtotime($sub_start . ' +1 years')),
                            'end' => date('Y-m-d', strtotime($sub_start . ' +2 years'))
                        ];
                    }
                }else if($data['sub_type'] == "trial"){
                    // trial
                    // Only initial transaction, no future ones, theres just 1 month..
                    $initial_transaction = [
                        'paid' => true, 
                        'gymId' => $this->session->gym_id,
                        'gymCode' => $p['gymCode'],
                        'value' => $data['value'],
                        'vat' => $data['vat'],
                        'vat_value' => $data['vat_value'],
                        'start' => $sub_start,
                        'end' => date('Y-m-d', strtotime($sub_start . ' +1 month'))
                    ];
                }else if($data['sub_type'] == "prepaid_card"){
                    // Prepaid card
                    // Theres no end, the prepaid_card membership ends with a new membership or deletion..
                    $initial_transaction = [
                        'paid' => true, 
                        'gymId' => $this->session->gym_id,
                        'gymCode' => $p['gymCode'],
                        'value' => $data['value'],
                        'vat' => $data['vat'],
                        'vat_value' => $data['vat_value'],
                        'start' => $sub_start
                    ];
                }
            }else{
                // quarter
                // generate a year worth of quarter subs

                $initial_transaction = [
                    'paid' => true, 
                    'gymId' => $this->session->gym_id,
                    'gymCode' => $p['gymCode'],
                    'value' => $data['value'],
                    'vat' => $data['vat'],
                    'vat_value' => $data['vat_value'],
                    'start' => $sub_start,
                    'end' => date('Y-m-d', strtotime($sub_start . ' +3 months'))
                ];

                for ($q = 1; $q <= 3; $q++){
                    $ts = [
                        'paid' => false, 
                        'gymId' => $this->session->gym_id,
                        'gymCode' => $p['gymCode'],
                        'value' => $data['value'],
                        'vat' => $data['vat'],
                        'vat_value' => $data['vat_value'],
                        'start' => date('Y-m-d', strtotime($sub_start . ' +'.(3*$q).' months')),
                        'end' => date('Y-m-d', strtotime($sub_start . ' +'.(3*$q + 3).' months'))
                    ];

                    $future_transactions[] = $ts;

                    // Expect a payment for this one
                    $expected_payments[] = [
                        "client_id" => $data["clientId"],
                        "date_created" => date("Y-m-d 00:00:01", strtotime($ts["start"])), // Schedule this into the future
                        "autocont_type" => "Predplat"
                    ];

                }
            }

            // Create the subscription in the API
            if($subscriptionData = $this->API->subscriptions->create_subscription($p['clientId'], $p['gymCode'], [
                'subType' => $data['sub_type'],
                'subPeriod' => $data['sub_period'],
                'membershipId' => $membership_id,
                'gymId' => current_gym_id(),
                'initialTransaction' => $initial_transaction,
                'transactions' => $future_transactions
            ])){ // Success

                $this->clients->updateClientMembershipLocal($p['clientId'], $membership_id); // update the local record (not in mongo)

                $data['subscriptionContractNumber'] = $subscriptionData->contractNumber;
                $transactionData = $this->API->transactions->add_transaction($data); // create transaction;
                if ($transactionData){
                    // Expect a payments -> AUTOCONT (add contractNumber)
                    if(isset($expected_payments)){
                        foreach ($expected_payments as $ep){
                            $ep['transactionNumber']=$subscriptionData->contractNumber;
                            $this->autocont->expect_payment($ep);
                        }
                    }
                    // add contract number to return
                    $transactionData->contractNumber = $subscriptionData->contractNumber;
                    return $transactionData;
                }
                else {
                    echo "Cannot create transaction on ".$item['name'];
                    return false;
                }
            } else { // fail
                echo "create subscription fail";
                return false;
            }
        }      
        
    }

    /**
     * Return the clients credit status
     */
    public function get_clients_credit($client_id = NULL, $history = FALSE){
        if(!$client_id) $client_id = gym_userid(); // Current logged in client
        $card_id = $this->cards->getUserCard($client_id);

        if($card_id) $credit = $this->API->transactions->get_credit($client_id, $card_id);
        else $credit = false; // no card = no credit

        if($credit){
            if($history) return $credit->data;
            else return $credit->data->currentValue;
        }else{
            return FALSE;
        }
    }

    public function getInvoices(){
        $g = $_GET;
        $this->gymdb->init(current_gym_db());

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $this->gymdb->init(get_db());
        $this->db->select('*')->from('invoices');

        if($sorter){
            foreach($sorter as $s){
                $order_field = $s['field'];
                $direction = $s['dir'];

                $this->db->order_by('invoices.'.$order_field, $direction);
            }
        }
        if($filter){
            foreach($filter as $f){

                if($f["field"] == 'client_name') $f["field"] = 'client_id';
                if($f["field"] == 'employee_name') $f["field"] = 'created_by';

                if(isset($f["gte"])){
                    $fieldname = 'invoices.'.$f["field"];
                    $this->db->where($fieldname." >=", $f['value']);
                }else{
                    $fieldname = 'invoices.'.$f["field"];
                    $this->db->like($fieldname, $f['value']);
                }
            }
        }

        $countRes = $this->db->count_all_results(null,FALSE);

        if($page != NULL && $limit != NULL){
            $offset = 0;
            if($page > 1){
                $offset = ($page - 1) * $limit;
            }
            $this->db->limit($limit, $offset);
        }

        $result = $this->db->get()->result();
        if($result){
            foreach($result as $invoice){
                $invoice->currency = 'CZK';

                $employee = $this->db->where('user_id', $invoice->created_by)->get('users_data')->row();
                if ($employee) $invoice->employee_name = $employee->first_name . ' ' . $employee->last_name;
                else $invoice->employee_name = '--';
            }
        }

        $reply["data"] = $result;
        if($limit != NULL) $reply['last_page'] = ceil( $countRes / $limit );
        return $reply;
    }

    protected function getNextInvoiceNumber($userId, $gym) {
        $this->gymdb->init(current_gym_db());

        $this->db->trans_start();
            $result = $this->db->select('max(invoice_number) as last_number')->where('client_id', $userId)->get('invoices')->row_array();
        $this->db->trans_complete();

        if($this->db->trans_status()){

            if(!is_null($result['last_number'])){
                $counter = (int) substr($result['last_number'], -2);
            }else{
                $counter = 0;
            }

            $nextCounter = $counter + 1;
            $nextCounterFormatted = sprintf('%02d',$nextCounter);
            $userIdFormatted = sprintf('%06d',$userId, 6);
        }

        return $gym . date('y').$userIdFormatted.$nextCounterFormatted;
    }
    
    public function pay_invoice($invoice_id, $payment_method){
        $this->gymdb->init(current_gym_db());

        $invoice = $this->db->where('id', $invoice_id)->get('invoices')->row_array();
        if($invoice){
            if ($this->db->where('id', $invoice_id)->update('invoices', [
                'payment_date' => date('Y-m-d'), // payment date = cancel date
                'payment_method' => $payment_method, // 1 => cash, 2 => card
                'paid' => 1
            ])){
                // @todo autocont check
                // @todo if($payment_method == 1) blabla (cash payment, do a transaction?)
                return TRUE;
            }else{
                return FALSE;
            }
        }
    }

    // cancel an invoice
    public function cancel_invoice($invoice_id){
        $this->gymdb->init(current_gym_db());

        $invoice = $this->db->where('id', $invoice_id)->get('invoices')->row_array();
        if($invoice){
            if ($this->db->where('id', $invoice_id)->update('invoices', [
                'cancelled' => 1,
                'payment_date' => date('Y-m-d'), // payment date = cancel date
                'status' => 2 // 2 => cancelled
            ])){

                // Refunded transaction
                $gym_id = strlen(explode('gymit', current_gym_db())[1]) > 0 ? explode('gymit', current_gym_db())[1] : '1';
                $this->API->transactions->add_transaction([
                    'paymentIdentificationNumber' => $invoice['invoice_number'], // variabilni sym.
                    'invoiceId' => $invoice_id, // id invoice
                    'gymId' => current_gym_id(),
                    'gymCode' => $gym_id,
                    'transCategory' => 'OD',
                    'transType' => 13, // Bank / Invoice
                    'employeeId' => gym_userid(),
                    'clientId' => $invoice['client_id'],
                    'cardId' => $this->cards->getUserCard($invoice['client_id'])->card_id,
                    'value' => $invoice['value'],
                    'invoiceItems' => $invoice["items"],
                    'text' => 'Storno faktury',
                    'refund' => TRUE
                ]);
    
                return TRUE;
            }else{
                return FALSE;
            }
        }
    }

    // submit an invoice
    public function submit_invoice(){
        $this->gymdb->init(current_gym_db());

        $p = $_POST;
        $items = $p['items'];

        $invoice = [
            'invoice_number' => $this->getNextInvoiceNumber($p['client_id'], current_gym_code()),
            'payment_method' => $p['payment_method'],
            'created_by' => gym_userid(),
            'client_id' => $p['client_id'],
            'client_name' => $p['client_name'],
            'client_street' => $p['client_street'],
            'client_city' => $p['client_city'],
            'client_zip' => $p['client_zip'],
            'client_state' => $p['client_country'],
            'client_vat_id' => $p['client_vat_id'],
            'client_company_id' => $p['client_company_id'],
            'issue_date' => $p['issue_date'],
            'due_date' => $p['due_date'],
            'items' => json_encode($items, JSON_UNESCAPED_UNICODE),
            'value' => 0,
            'vat_value' => 0,
            'status' => 0 // new (not in autocont payments), 1 => in autocont
        ];

        foreach($items as $item){
            $discount = is_numeric($item["item_discount"]) ? $item["item_discount"] : 0;
            $total = $item['item_value'] - (($discount / 100) * $item["item_value"]);

            $invoice['value'] += $total;
            $invoice['vat_value'] += 0;            
        }

        if($this->db->insert('invoices', $invoice)){
            $invoice_id = $this->db->insert_id();
            $this->API->transactions->add_transaction([
                'paymentIdentificationNumber' => $invoice['invoice_number'], // variabilni sym.
                'invoiceId' => $invoice_id, // id invoice
                'gymId' => current_gym_id(),
                'gymCode' => $gym_id,
                'transCategory' => 'OD',
                'transType' => 13, // Bank / Invoice
                'paid' => FALSE, // NOT YET PAID!
                'employeeId' => gym_userid(),
                'clientId' => $p['client_id'],
                'cardId' => $this->cards->getUserCard($p['client_id'])->card_id,
                'value' => $invoice['value'],
                'invoiceItems' => json_encode($items, JSON_UNESCAPED_UNICODE),
                'text' => 'Faktura'
            ]);

            return TRUE;
        }else{
            return FALSE;
        }
    }

    public function getAll($data = FALSE){
        $g = $data ? $data : $_GET;
        $params = [];

        // Pagination and filtering
        $params['page'] = (isset($g['page'])) ? $g['page'] : null;
        $params['size'] = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $sort_by = [];
        if($sorter){
            foreach($sorter as $s){
                $order_field = $s['field'];
                $direction = ($s['dir'] == "asc") ? "1" : "-1";

                $sort_by[$order_field] = $direction;
            }
        }
        $params['sortBy'] = $sort_by;
        
        if($filter){
            foreach($filter as $f){
                if($f['field'] == 'client_name') $params['clientId'] = $f['value'];
                else if($f['field'] == 'employee_name') $params['employeeId'] = $f['value'];
                else $params[$f["field"]] = $f['value'];
            }
        }

        $transactions = $this->API->transactions->get_transactions($params);
        if(!$transactions) return [];

        $userIDs = []; foreach($transactions->data as $v): $userIDs[] = $v->clientId; $userIDs[] = $v->employeeId; endforeach;

        if(!empty($userIDs)){
            $c = $this->db->select('user_id,concat(users_data.first_name," ",users_data.last_name) as user_name')
                        ->where_in('user_id', array_unique($userIDs))
                        ->get('users_data')
                        ->result_array();

            $users = []; foreach($c as $user): $users[$user['user_id']] = $user; endforeach;
        }

        $ret['data'] = $transactions->data;
        foreach($ret['data'] as $k => $v){
            $ret['data'][$k]->transType = $this->transCategories[$v->transType]['value'] ?? $ret['data'][$k]->transType;
            $ret['data'][$k]->client_name = $users[$v->clientId]['user_name'] ?? ' -- ';
            $ret['data'][$k]->employee_name = $users[$v->employeeId]['user_name'] ?? ' -- ';
            $ret['data'][$k]->paidOn = mongoDateToLocal($ret['data'][$k]->paidOn);
        }

        if($params['page'] && $params['size']) $ret['last_page'] = ceil( $transactions->total / $params['size'] ); 
        return $ret;
    }

    public function getAllUserPayments(int $userId, array $params = []){
        $g = $_GET;
        $params['clientId'] = $userId;

        // Pagination and filtering
        $params['page'] = (isset($g['page'])) ? (int) $g['page'] : 1;
        $params['size'] = (isset($g['size'])) ? $g['size'] : 10;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $sort_by = [];
        if($sorter){
            foreach($sorter as $s){
                $order_field = $s['field'];
                $direction = ($s['dir'] == "asc") ? "1" : "-1";

                $sort_by[$order_field] = $direction;
            }
        }
        $params['sortBy'] = $sort_by;

        if($filter){
            foreach($filter as $f){
                if($f['field'] == 'employee_name') $params['employeeId'] = $f['value'];
                else $params[$f["field"]] = $f['value'];
            }
        }

        $transactions = $this->API->transactions->get_transactions($params);

        if(!$transactions) return [];

        $ret['data'] = $transactions->data;
        $ret['count'] = count($ret['data']);
        $ret['page'] = $params['page'];

        foreach($ret['data'] as $k => $v){
            $ret['data'][$k]->transType = $this->transCategories[$v->transType]['value'] ?? $ret['data'][$k]->transType;
            $ret['data'][$k]->paidOn = mongoDateToLocal($ret['data'][$k]->paidOn);
            $ret['data'][$k]->text = isset($v->text) ? $v->text : '';
        }

        if($params['page'] && $params['size']) $ret['last_page'] = ceil( $transactions->total / $params['size'] );

        return $ret;
    }

    public function getAllTerminals(){
        return $this->db->select('*')->from('terminals')->get()->result();
    }    

    public function getAllPurchasedItems(){
        $g = $_GET;
        $params = [];

        // Pagination and filtering
        $params['page'] = (isset($g['page'])) ? $g['page'] : null;
        $params['size'] = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $sort_by = [];
        if($sorter){
            foreach($sorter as $s){
                $order_field = $s['field'];
                $direction = ($s['dir'] == "asc") ? "1" : "-1";

                $sort_by[$order_field] = $direction;
            }
        }
        $params['sortBy'] = $sort_by;
        
        if($filter){
            foreach($filter as $f){
                if($f['field'] == 'itemName'){
                    $f['value'] = explode('-',$f['value']);
                    if($f['value'][0]==1) $params['filterDepotItem'] = TRUE; // depot items
                    $params['itemId'] = $f['value'][1];
                }
                else $params[$f["field"]] = $f['value'];
            }
        }

        $items = $this->API->transactions->get_purchase_history($params);
        if(!$items) return [];

        $serviceItems = $depotItems = []; 
        foreach($items->data as $v){
            if(isset($v->depotId)) $depotItems[] = $v->itemId; 
            else $serviceItems[] = $v->itemId;
        } 

        if(!empty($serviceItems)){ // service items
            $sItems = $this->db->select('id,name')->where_in('id', array_unique($serviceItems))->get('price_list')->result();
            $serviceItems = []; foreach($sItems as $i): $serviceItems[$i->id] = $i; endforeach;
            $serviceItems[0] = (object) ['id' => 0, 'name' => 'Dobití kreditu'];
        }
        if(!empty($depotItems)){ // depot items
            $dItems = $this->db->select('id,name')->where_in('id', array_unique($depotItems))->get('depot_items')->result();
            $depotItems = []; foreach($dItems as $i): $depotItems[$i->id] = $i; endforeach;
        }        

        $ret['data'] = $items->data;
        foreach($ret['data'] as $k => $v){
            if(isset($v->depotId)) $ret['data'][$k]->itemName = $depotItems[$v->itemId]->name ?? ' -- ';
            else $ret['data'][$k]->itemName = $serviceItems[$v->itemId]->name ?? ' -- ';
            $ret['data'][$k]->paidOn = mongoDateToLocal($ret['data'][$k]->paidOn);
            $ret['data'][$k]->vatPrice = $v->value + $v->vat_value;
        }

        $ret['last_page'] = ceil( $items->total / $params['size'] ); 
        return $ret;
    }    


    /**
     * Submit a new subscription for a user
     */
    public function subscriptionToTransaction(){
        $p = $_POST;
        
        $client = $p['client_id'];
        $sub_type = $p['sub_type'];
        $sub_start = $p['start'];

        $sub_period = isset($p['sub_period']) ? $p['sub_period'] : '';

        $memberships = $this->db->select('m.code,p.*')->from('membership m')->join('membership_prices p','p.membership_id=m.id')->get()->result();

        foreach($memberships as $m){
            $membership[$m->code][$m->period_type]['id']=$m->id;
            $membership[$m->code][$m->period_type]['price']=$m->price;
            $membership[$m->code][$m->period_type]['vat']=$m->vat;
            $membership[$m->code][$m->period_type]['purchase_name']=$m->purchase_name;
        }

        $initial_transactions[] = [
            'sub_id' => $membership[$sub_type][$sub_period]['id'],
            'sub_start' => $sub_start,
            'sub_period' => $sub_period,
            'item_name' => $membership[$sub_type][$sub_period]['purchase_name'],
            'value' => $membership[$sub_type][$sub_period]['price'] / (1 + $membership[$sub_type][$sub_period]['vat']),
            'vat_value' => $membership[$sub_type][$sub_period]['price'] - ($membership[$sub_type][$sub_period]['price'] / (1 + $membership[$sub_type][$sub_period]['vat'])),
            'vat' => $membership[$sub_type][$sub_period]['vat']
        ];


        if($sub_period === 'month'){
            // Deposit (12th month)
            $initial_transactions[] = [
                'sub_id' => $membership[$sub_type][$sub_period]['id'],
                'sub_start' => $sub_start,
                'sub_period' => $sub_period,
                'item_name' => 'Záloha na 12. měsíc členství',
                'value' => $membership[$sub_type][$sub_period]['price'] / (1 + $membership[$sub_type][$sub_period]['vat']),
                'vat_value' => $membership[$sub_type][$sub_period]['price'] - ($membership[$sub_type][$sub_period]['price'] / (1 + $membership[$sub_type][$sub_period]['vat'])),
                'vat' => $membership[$sub_type][$sub_period]['vat'],
                'existing_payment' => TRUE
            ];            
        }

        // TODO

        return [
            'membership_id'=> $membership[$sub_type][$sub_period]['id'],
            'items' => $initial_transactions,
            'client_id' => $client
        ];
    }

    public function refundCredit(array $data){

        $data['transType'] = 3; // KR - This is always a KR => HO/BA operation
        $data['transCategory'] = $this->transCategories[$data['transType']]['key']; // remap id of purchase type for AUTOCONT

        if(strlen($data['accountNumber'])>0){
            $data['refundBankAcccount']=$data['accountNumber']; // Bank account refund, supply an acc. num
            $data["paid"] = FALSE; // Unpaid for now
        }

        $item['itemId'] = 0;
        $item['value'] = $data['refundValue']; // price with VAT
        $item['vat'] = 0; // credit is withou VAT!
        $item['vat_value'] = 0; // credit is withou VAT!
        $item['amount'] = 1;
        $item['name'] = 'Vrácení kreditu';

        $data['items'][]=$item;

        $data['refund'] = TRUE;
        $data['value'] = $data['refundValue']; // total value
        $data['employeeId'] = gym_userid();
        $data['gymId'] = $this->session->gym_id;
        $data['gymCode'] = (strlen(explode('gymit', $this->session->gym_db)[1]) > 0) ? (string) '0' .explode('gymit', $this->session->gym_db)[1] : (string) '0' .'1';

        // Set credit
        $currentCredit = $this->API->transactions->get_credit($data['clientId'],$data['cardId'])->data->currentValue;
        $this->API->transactions->set_credit($data['clientId'],$data['cardId'],$currentCredit - $data['value']);

        // Create transaction
        $transactionData = $this->API->transactions->add_transaction($data);   
        if(@$transactionData->success==1){
            $data['items'][0]['value']=-200; // for bill
            $tmPrinterItems=$this->eetapp->prepareData4pay(['service' => $data['items']]);
            $receipt = [
                'items' => $tmPrinterItems,
                'purchaseTypes' => [$data['transType']=>$data['items'][0]['value']],
                'transactionId' => $transactionData->transaction_id,
                'gymCode' => $data['gymCode'],
                'checkoutId' => $data['checkoutId']
            ];
            $receipt['receiptId'] = $this->createReceipt($receipt);
            //$this->tmprinter->print($receipt,strlen($data['accountNumber'])>0?false : true);
            $this->API->transactions->print_receipt($receipt, strlen($data['accountNumber'])>0?false : true);
            
            if(strlen($data["accountNumber"]) > 0){
                // Expected payment
                $this->autocont->expect_payment([
                    "client_id" => $data["clientId"],
                    "transactionNumber" => $transactionData->transaction_number,
                    "autocont_type" => "Kredit"
                ]);
            }
            
            return true;
        } else {
            echo "Transaction creation failed";
            return false;
        }
    }

    // WEB PAYMENTS 

	public function getPresaleMembershipDiscount($id){
		$discount = [ // in percentage!
			1 => 10, // basic_unlimited (month)
			2 => 10, // basic_unlimited (year)
			3 => 10, // basic_off_peak (month)
			4 => 10, // basic_off_peak
			5 => 10, // basic_quarter
			6 => 10, // basic_student (month)
			7 => 10, // basic_student (year)
			8 => 10, // platinum (month)
			9 => 10, // platinum
			10 => 10,// platinum_off_peak (month)
			11 => 10,// platinum_off_peak (year)
			12 => 10,// platinum_quarter
			13 => 10,// platinum_student (month)
			14 => 10,// platinum_student (year)
			15 => 10,// trial
			16 => 10,// prepaid_card
		];
		return $discount[$id];
	}

    public function createWebPay($client_id,$price,$responseUrl){
        $data['webpay_id'] = $this->getWebPayId();
        $data['gym_code'] = (strlen(explode('gymit', $this->session->gym_db)[1]) > 0) ? (string) '0' .explode('gymit', $this->session->gym_db)[1] : (string) '0' .'1';
        $data['client_id'] = $client_id;

        $this->db->insert('gpwebpay_log',$data);
        $internOrderNum = $this->db->insert_id();

        $url = $this->gpwebpay->createRequest($data['webpay_id'],$internOrderNum,$price,$responseUrl);        
        header("Location: $url");
    }

    public function proceedWebPay($client_id,$price_id){
        $price = $this->pricelist->getMembershipPrice($price_id);
        if($response = $this->gpwebpay->proccessResponse()){ // valid response (paid)
            // in case of page update -> check, if webpay was processed
            $webPayLogStatus = $this->db->where('id', $response->responseParams['MERORDERNUM'])->get('gpwebpay_log')->row();
            if($webPayLogStatus->paid) return true; // show success page

            $this->db->update('gpwebpay_log',['paid'=>true,'prcode'=>$response->responseParams['PRCODE'],'srcode'=>$response->responseParams['SRCODE']],['id'=>$response->responseParams['MERORDERNUM']]);
            $code = $this->vouchers->createVoucher('membership',$price_id,'','Předprodej členství', $client_id);
            $this->vouchers->setIdentification([$code],'webpay',$response->responseParams['ORDERNUMBER']);

            $emailData=[];
            $emailData['client'] = $this->users->getUserData($client_id);
            $emailData['membership'] = $price;
            $this->mailgun::send([
                'from' => 'Gymit <no-reply@gymit.cz>',
                'to' => $emailData['client']->email,
                'bcc' => config_item('app')['bccEmails'],
                'subject' => 'Informace ke členství Gymit Premium fitness',
                'html' => $this->load->view('/emails/membership_presale',$emailData,true)
            ]);
            
            // transaction
            $item=[
                'itemId'=>$price_id,
                'value'=>$price->price,
                'vat'=>0,
                'vat_value'=>0,
                'amount'=>1,
                'value_discount'=>$this->getPresaleMembershipDiscount($price->id)
            ];

            $data=[
                'disposablePayment'=>TRUE,
                'gymId'=>current_gym_id(),
                'gymCode'=>current_gym_code(),
                'employeeId'=>1, // system
                'clientId'=>$client_id,
                'transType'=>14,
                'transCategory'=>'BA',
                'paymentIdentificationNumber'=>$response->responseParams['MERORDERNUM'],
                'voucherSale'=>TRUE,
                'voucherIdentification'=>[$code],
                'items'=>[$item],
                'value'=>$price->price * ( 1 - ($this->payments->getPresaleMembershipDiscount($price->id) / 100) ),
                'text'=>'Předprodej členství (webpay)',
            ];

            // Create transaction
            $transactionData = $this->API->transactions->add_transaction($data); 
            $expected_payment = $this->autocont->expect_payment([
                "client_id" => $client_id, 
                "paid" => TRUE, 
                "received" => $price->price * ( 1 - ($this->payments->getPresaleMembershipDiscount($price->id) / 100) ), 
                "transactionNumber" => $response->responseParams['MERORDERNUM'], 
                "autocont_notified" => 0, 
                "autocont_type" => "Voucher"]
            ); // (expected payment) Mainly for log purposes

            return true;
        } else if($_GET['PRCODE']==14){ // duplicate order (should not happen in production)
            $price->price = $price->price * ( 1 - ($this->payments->getPresaleMembershipDiscount($price->id) / 100) );
            $this->createWebPay($client_id,$price->price,current_url()); // create new order number and repeat request
            exit;
        } else { // not paid
            return false; // show error page
        }
    }

    public function getWebPayId(){
        $lastId = $this->db->select('max(webpay_id) webpay_id')->get('gpwebpay_log')->row()->webpay_id ?? 0;
        // new month
        if(substr($lastId,0,6)!=date('Ym')) $lastId=0;
        // first webpay of the month
        if($lastId > 0) $lastId = substr($lastId,6);
        // return format 20191200001
        return (int) date('Ym').sprintf('%06d', $lastId+1);

    }

    public function getClientSubscription(int $userId, array $data = [])
    {
        $card = $this->cards->getUserCard($userId);

        if (empty($card)) {
            return null;
        }

        $response = $this->API->subscriptions->get_subscription($userId, current_gym_code(), $data);
        if(!empty($response->data)) {
            $response->data->name = $this->db->where('code', $response->data->subType)->get('membership')->row()->name;
            $response->data->end = mongoDateToLocal((end($response->data->transactions))->end);
            $response->data->createdOn = mongoDateToLocal($response->data->createdOn);
            return $response->data;
        }

        return null;
    }

    /**
     * @todo mělo by to brát transakce ze všech členství, ne z transactions
     * @param int $userId
     */
    public function getClientHistoryPayments(int $userId)
    {
        $subscriptions = $this->API->subscriptions->get_subscriptions(['clientId' => $userId]);
        $payments = ['data' => [], 'page' => 1, 'count' => 0, 'last_page' => 1];

        if (! empty($subscriptions)) {
            foreach ($subscriptions as $subscription) {
                $period = paymentPeriodToHuman($subscription->subPeriod);

                $subscriptionRow = $this->db->where('code', $subscription->subType)->get('membership')->row();
                $subscriptionText = sprintf('Členství %s %s',
                    $subscriptionRow->name,
                    $period ? '(hrazeno ' . $period . ')' : ''
                );

                foreach ($subscription->transactions as $subscriptionTransaction) {
                    if ($subscriptionTransaction->paid === true || $subscriptionTransaction->cancelled === true) {
                        continue;
                    }
                    $subscriptionTransaction->text = $subscriptionText;
                    $subscriptionTransaction->start = mongoDateToLocal($subscriptionTransaction->start);
                    $payments[] = $subscriptionTransaction;
                }
            }
        }

        return $payments;
    }

    public function getClientCurrentStateOfPayments(int $userId, DateTimeImmutable $compareWithDate): float
    {
        $subscription = $this->getClientSubscription($userId);

        $remaining = 0.0;

        if ($subscription !== null) {
            foreach ($subscription->transactions as $transaction) {
                if ($transaction->paid === false and $transaction->cancelled === false) {
                    if ($compareWithDate >= mongoDateToDatetime($transaction->start)) {
                        $remaining += $transaction->value;
                    }
                }
            }
        }

        return $remaining;
    }

    public function getClientFuturePayments(int $userId, int $numberOfPayments = 10)
    {
        $subscription = $this->getClientSubscription($userId);
        $payments = [];


        if ($subscription !== null) {
            $period = paymentPeriodToHuman($subscription->subPeriod);

            $subscriptionRow = $this->db->where('code', $subscription->subType)->get('membership')->row();
            $subscriptionText = sprintf('Členství %s %s',
                $subscriptionRow->name,
                $period ? '(hrazeno ' . $period . ')' : ''
            );

            foreach ($subscription->transactions as $subscriptionTransaction) {
                if ($subscriptionTransaction->paid === true || $subscriptionTransaction->cancelled === true) {
                    continue;
                }
                $subscriptionTransaction->text = $subscriptionText;
                $subscriptionTransaction->start = mongoDateToLocal($subscriptionTransaction->start);
                $payments[] = $subscriptionTransaction;
                if (count($payments) === $numberOfPayments) {
                    break;
                }
            }
        }

        return $payments;
    }
}
<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Separate transaction API functions
 */
class Transactions extends CI_Model
{
    public function __construct(){
		parent::__construct();
		$this->load->library('curl');
		$this->url = config_item("api")["transactions"];
	}

	/**
	 * Create the credit document for this client/card pairing
	 * 
	 * TODO: Run card sync into READERS on card_edit / card_remove, etc.
	 */
	public function create_user_credit($client_id, $card_id){
		$data = [ 'clientId' => $client_id, 'cardId' => $card_id ];
		$call = 'credit/new';

		$response = $this->API->_send($this->url, $call, $data, 'POST');
		if(is_object($response) && $response->success) return TRUE;
		else return $response;
	}
	public function edit_user_credit($client_id, $new_card_id){
		$data = [ 'clientId' => $client_id, "newCardId" => $new_card_id ];
		$call = 'credit/edit';

		$response = $this->API->_send($this->url, $call, $data, 'POST');
		if(is_object($response) && $response->success) return TRUE;
		else return $response;
	}
	public function remove_user_credit($client_id){
		$data = [ 'clientId' => $client_id];
		$call = 'credit/delete';

		$response = $this->API->_send($this->url, $call, $data, 'POST');
		if(is_object($response) && $response->success) return TRUE;
		else return $response;
	}
	
	/**
	 * Get clients credit
	 */
	public function get_credit($client_id, $card_id){
		$data = [ 'clientId' => $client_id, 'cardId' => $card_id ];
		$call = 'credit/get';

		$response = $this->API->_send($this->url, $call, $data, 'GET');
		if(is_object($response) && $response->success) return $response;
		else return $response;
	}

	/**
	 * Set clients credit to a new value (after subtracting or adding, logs are saved in mongo)
	 */
	public function set_credit($client_id, $card_id, $new_value){
		$data = [ 'clientId' => $client_id, 'cardId' => $card_id, 'newValue' => $new_value ];
		$call = 'credit/set';

		$response = $this->API->_send($this->url, $call, $data, 'POST');
		if(is_object($response) && $response->success) return TRUE;
		else return $response;
	}

    /** Add transaction 
     *  gymId: { type: String, required: true },
        transCategory: { type: String, default: 'global' },
        transType: { type: Number, required: true },
        terminalId: { type: String},
        clientId: { type: String, required: true },
        cardId: { type: String, required: true },
        currency: { type: String, default: 'CZK' },
        value: { type: Number, required: true }
     * 
    */
    public function add_transaction($transaction){
		$data = [ 'transaction' => $transaction ];
		$call = 'add';

		$response = $this->API->_send($this->url, $call, $data, 'POST');
		if(is_object($response) && $response->success) return $response;
		else return FALSE;
	}
	
	public function edit_transaction($transId, $transaction){
		$data = [ 'transaction' => $transaction ];
		$call = 'edit/'.$transId;

		$response = $this->API->_send($this->url, $call, $data, 'POST');
		if(is_object($response) && $response->success) return TRUE;
		else return FALSE;
	}

	public function delete_transaction($transId){
		$call = 'delete/'.$transId;

		$response = $this->API->_send($this->url, $call, [], 'GET');
		if(is_object($response) && $response->success) return TRUE;
		else return FALSE;
	}

    /** Get transaction by ID */
    public function get_transaction_by_id($transaction_id){
		$call = 'get/' . $transaction_id;

		$response = $this->API->_send($this->url, $call, [], 'GET');
		if(is_object($response) && $response->success) return $response;
		else return $response;
	}

	public function get_transaction_by_number($transaction_number, $gymCode){
		$call = 'get/number/' . $transaction_number;

		$response = $this->API->_send($this->url, $call, ["gymCode" => $gymCode], 'GET');
		if(is_object($response) && $response->success) return $response;
		else return FALSE;
	}

	public function get_transaction_by_invoiceId($invoice_id, $gymCode){
		$call = 'get/invoice/' . $invoice_id;

		$response = $this->API->_send($this->url, $call, ["gymCode" => $gymCode], 'GET');
		if(is_object($response) && $response->success) return $response;
		else return FALSE;
	}

    /** Get transactions 
     * $params['from'] / $params['to'] for date range
     * $params['limit'] && $params['offset'] for pagination
     * $params['category'] for filtering transactions based on category
     * $params['client_id'] for filtering based on client_id
     * $params['card_id'] for filtering based on card id
     * $params['gym_id'] for filtering based on gym
    */
    public function get_transactions($params = []){
		$call = 'get';

		$response = $this->API->_send($this->url, $call, $params, 'GET');
		if(is_object($response) && $response->success) return $response;
		else return $response;
	}

	/**
	 * Get clients item purchase history
	 * It is mandatory to send clientId in $params!!
	 */
	public function get_purchase_history($params = []){
		$call = 'get/purchase-history';

		$response = $this->API->_send($this->url, $call, $params, 'GET');
		if(is_object($response) && $response->success) return $response;
		else return $response;
    }

	/**
	 * Close transactions or day 
	 */
	public function close_transactions($transactions, $day = NULL){
		if(!is_null($day)){
			$data = [ 'day' => date('Y-m-d', strtotime($day)) ];
			$call = 'add/close-transaction-day/';
		}else{
			$data = [ 'transactions' => $transactions ];
			$call = 'add/close-transactions/';
		}

		$response = $this->API->_send($this->url, $call, $data, 'POST');
		if(is_object($response) && $response->success) return $response;
		else return FALSE;
	}

	// Receipts

    public function add_receipt($data, $transId, $gymCode){
		$data = [ 'transactionId' => $transId, "gymCode" => $gymCode, "data" => $data ];
		$call = 'receipts/add';

		$response = $this->API->_send($this->url, $call, $data, 'POST');
		if(is_object($response) && $response->success) return $response;
		else return FALSE;
	}

	public function get_receipt($transactionId){
		$data = [ 'transactionId' => $transactionId ];
		$call = 'receipts/get';

		$response = $this->API->_send($this->url, $call, $data, 'GET');
		if(is_object($response) && $response->success) return $response;
		else return FALSE;
	}

	public function remove_receipt($transactionId){
		$data = [ 'transactionId' => $transactionId ];
		$call = 'receipts/remove';

		$response = $this->API->_send($this->url, $call, $data, 'POST');
		if(is_object($response) && $response->success) return TRUE;
		else return FALSE;
	}

	public function print_receipt($data,$openCashdesk = false){
		$data['openCashdesk'] = $openCashdesk;
		$data['date'] = date('j.n.Y H:m:s');
		$data['created_by'] = gym_users_name();
        foreach ($this->gyms->getGymSettings(['general_info','subject_info']) as $k => $v){
            $data[$v['type']]=json_decode($v['data']);
		}
		
		$ptTypes = $data['purchaseTypes'];
		$data['purchaseTypes'] = [];
		foreach($ptTypes as $ptId=>$price){
			$tc = $this->payments->returnTransCategories();
			$pt['id'] = $ptId;;
			$pt['key'] = $tc[$ptId]['key'];
			$pt['title'] = $tc[$ptId]['value'];
			$pt['price'] = $price;
			array_push($data['purchaseTypes'],$pt);
		}
        
		$call = 'receipts/print';

		$response = $this->API->_send($this->url, $call, $data, 'POST');
	}

	public function open_cashdesk(){
		$call = 'receipts/open_cashdesk';
		$response = $this->API->_send($this->url, $call, $data, 'GET');
	}
}
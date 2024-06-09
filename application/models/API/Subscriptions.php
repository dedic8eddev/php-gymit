<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Separate transaction API functions
 */
class Subscriptions extends CI_Model
{
    public function __construct(){
		parent::__construct();
		$this->load->library('curl');
		$this->url = config_item("api")["transactions"];
	}

	/**
	 * Create a subscription object for a particular client
	 */
	public function create_subscription($client_id, $gymCode, $data = []){
        $data['clientId'] = $client_id;
        $data['gymCode'] = $gymCode;
		$call = 'subs/new';

		$response = $this->API->_send($this->url, $call, $data, 'POST');
		if(is_object($response) && $response->success) return $response;
		else return $response;
	}

	// remove a sub
	public function remove_subscription($contractNumber, $monthId){
		$data = [];
		$data['contractNumber'] = $contractNumber;
		$data['transactionId'] = $monthId;
		$call = 'subs/remove';

		$response = $this->API->_send($this->url, $call, $data, 'POST');
		if(is_object($response) && $response->success) return TRUE;
		else return FALSE;
	}

	public function pay_for_subscription_payment($client_id, $gymCode, $data = []){
        $data['clientId'] = $client_id;
        $data['gymCode'] = $gymCode;
		$call = 'subs/pay_transaction';

		$response = $this->API->_send($this->url, $call, $data, 'POST');
		if(is_object($response) && $response->success) return $response;
		else return $response;
	}

	// Update a nested sub document
	public function update_subscription_subdocument($nestedId, $data){
        $data['subId'] = $nestedId;
		$call = 'subs/edit_transaction';

		$response = $this->API->_send($this->url, $call, $data, 'POST');
		if(is_object($response) && $response->success) return $response;
		else return $response;
	}

	// Cancel a selected month
	/**
	 * reschedule => array of the new month to be appended to the end of the subscription
	 */
	public function cancel_month($contractId, $monthId, $reschedule = FALSE, $note = FALSE){
		$data = [];
		$data['contractNumber'] = $contractId;
		$data['transactionId'] = $monthId;

		if($reschedule != FALSE) $data['move_month'] = $reschedule;
		if($note != FALSE) $data['note'] = $note;
		$call = 'subs/cancel_month';

		$response = $this->API->_send($this->url, $call, $data, 'POST');
		if(is_object($response) && $response->success) return $response;
		else return $response;
	}
	
	public function edit_subscription($contractId, $data){
        $data['contractNumber'] = $contractId;
		$call = 'subs/edit_subscription';

		$response = $this->API->_send($this->url, $call, $data, 'POST');
		if(is_object($response) && $response->success) return $response;
		else return $response;
	}

	/**
	 * Get clients subscription
	 */
	public function get_subscription($client_id, $gymCode, $data = []){
        $data['clientId'] = $client_id;
        $data['gymCode'] = $gymCode;
		$call = 'subs/get';

		$response = $this->API->_send($this->url, $call, $data, 'GET');
		if(is_object($response) && $response->success) return $response;
		else return $response;
	}

	// Get sub by invoice num
	public function get_subscription_by_invoice_number($contract_id){
		$call = 'subs/get/' . $contract_id;

		$response = $this->API->_send($this->url, $call, [], 'GET');
		if(is_object($response) && $response->success) return $response;
		else return $response;
	}

	// Get subscriptions by params
	public function get_subscriptions($params = []){
		$call = 'subs/get-all';

		$response = $this->API->_send($this->url, $call, $params, 'GET');
		if(is_object($response) && $response->success) return $response;
		else return $response;
    }

}
<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Separate transaction API functions
 */
class Saleques extends CI_Model
{
    public function __construct(){
		parent::__construct();
		$this->load->library('curl');
		$this->url = config_item("api")["transactions"];
	}

	/**
	 * Create the que for a card
     * just card id needed
	 */
	public function create_que($card_id, $multisport = FALSE){
		$data = [ 'cardId' => $card_id, 'multisportCard' => $multisport ];
		$call = 'saleques/new';

		$response = $this->API->_send($this->url, $call, $data, 'POST');
		if(is_object($response) && $response->success) return TRUE; // Just confirmation
		else return $response;
    }
    
    /**
	 * Get a que from a card/date
	 */
	public function get_que($card_id, $day = NULL){
        if(is_null($day)) $day = date("Y-m-d");
		$data = [ 'cardId' => $card_id, 'date' => $day ];
		$call = 'saleques/get';

		$response = $this->API->_send($this->url, $call, $data, 'GET');
		if(is_object($response) && $response->success) return $response->data; // actual que object
		else return $response;
    }

    /**
	 * Set que as paid
	 */
	public function pay_que($card_id, $day = NULL){
        if(is_null($day)) $day = date("Y-m-d");
		$data = [ 'cardId' => $card_id, 'date' => $day ];
		$call = 'saleques/pay-que';

		$response = $this->API->_send($this->url, $call, $data, 'POST');
		if(is_object($response) && $response->success) return TRUE; // actual que object
		else return $response;
    }
    /**
	 * Set que as to be paid, aka flag it for processing
	 */
	public function flag_que($card_id, $day = NULL){
        if(is_null($day)) $day = date("Y-m-d");
		$data = [ 'cardId' => $card_id, 'date' => $day ];
		$call = 'saleques/flag-que';

		$response = $this->API->_send($this->url, $call, $data, 'POST');
		if(is_object($response) && $response->success) return TRUE; // actual que object
		else return $response;
    }
    
    /**
	 * Add a row to a que
     * 1 row is expected to have this format:
     *  itemId: {type: Number, required: true}, // id položky
     *  depotId: {type: Number, required: false}, // id skladu (pokud se jedná o skladovou položku / pokud ne => pricelist)
     *  amount: {type: Number, required: true, default: 1} // Množství
	 */
	public function add_to_que($card_id, $rows, $day = NULL){
        if(is_null($day)) $day = date("Y-m-d");

		$data = [ 'cardId' => $card_id, 'date' => $day, 'rows' => $rows ];
		$call = 'saleques/add-to-que';

		$response = $this->API->_send($this->url, $call, $data, 'POST');
		if(is_object($response) && $response->success) return TRUE; // confirmation
		else return $response;
    }
    
    /**
	 * Remove a row from a que
     * and _id of the row in String format is expected
	 */
	public function remove_from_que($card_id, $row, $day = NULL){
        if(is_null($day)) $day = date("Y-m-d");

		$data = [ 'cardId' => $card_id, 'date' => $day, 'row' => $row ];
		$call = 'saleques/remove-from-que';

		$response = $this->API->_send($this->url, $call, $data, 'POST');
		if(is_object($response) && $response->success) return TRUE; // confirmation
		else return $response;
	}

	/**
	 * Edit a row in the saleque
	 * You need the mongo _id of the row you want to edit ($itemMongoId)
	 * Then send any of the current model parameters and they'll get overwritten.
	 */
	public function edit_in_que($card_id, $itemMongoId, $itemData){
		$itemData["cardId"] = $card_id;
		$itemData["rowId"] = $itemMongoId;
		$call = 'saleques/edit-in-que';

		$response = $this->API->_send($this->url, $call, $itemData, 'POST');
		if(is_object($response) && $response->success) return TRUE; // confirmation
		else return $response;
	}

}
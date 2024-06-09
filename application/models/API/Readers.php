<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Separate transaction API functions
 */
class Readers extends CI_Model
{

    public function __construct(){
		parent::__construct();
		$this->load->library('curl');
		$this->url = config_item("api")["readers"];
		$this->api_key = config_item("api")["readers_api_key"];
	}

	// Get latest read card by reader via readerId
	// temp data:
	// Personifikátor v kanclu -> 6&32a5dfec&0&2
	// Řídící jednotka v kanclu -> 6&32a5dfec&0&1
	// readerId = reader serial number (reader_id in db NOT id)
	public function get_identificator_card($readerId, $date = NULL){
		$call = 'get/read-card/' . $readerId;

		$options = [];
		if(!is_null($date)) $options['minDate'] = $date;

		$response = $this->API->_send($this->url, $call, $options, 'GET', $this->api_key);
		if(is_object($response) && $response->success) return $response;
		else return FALSE;
	}

	/**
	 * Get a list of card ids that are in the gym
	 * can be filtered by a day sorta (TODO) "Y-m-d"
	 * add "cardId" to options array to only get the card holder data
	 */
	public function get_users_in_gym($date = NULL, $options = []){
		$call = 'get/present-clients';

		if(is_null($date)) $options['date'] = date("Y-m-d");
		else $options['date'] = $date;

		$response = $this->API->_send($this->url, $call, $options, 'GET', $this->api_key);
		if(is_object($response) && $response->success) return $response;
		else return FALSE;
	}
	
	/**
	 * Register a card to a particular reader
	 * readerId = reader serial number (reader_id in db NOT id)
	 */
	public function register_card ($cardId, $readerId, $address) {
		$call = 'readers/register-card/';
		$options = ['cardId' => $cardId, 'readerId' => $readerId, 'readerAddress' => $address];

		$response = $this->API->_send($this->url, $call, $options, 'POST', $this->api_key);
		if(is_object($response) && $response->success) return TRUE;
		else return FALSE;
	}

	/**
	 * Remove a card from a particular reader
	 * Thus removing access permissions
	 * readerId = reader serial number (reader_id in db NOT id)
	 */
	public function deregister_card ($cardId, $readerId, $address, $options = NULL) {
		$call = 'readers/remove-card/';
		if (is_null($options)) $options = ['cardId' => $cardId, 'readerId' => $readerId, 'readerAddress' => $address];
		else {
			$options['cardId'] = $cardId;
			$options['readerId'] = $readerId;
			$options['readerAddress'] = $address;
		}

		$response = $this->API->_send($this->url, $call, $options, 'POST', $this->api_key);
		if(is_object($response) && $response->success) return TRUE;
		else return FALSE;
	}

	/**
	 * Pull data from a particular reader into MongoDB
	 * readerId = reader serial number (reader_id in db NOT id)
	 */
	public function pull_reader_data_to_db ($readerId, $address) {
		$call = 'readers/pull-events/';
		$options = ['readerId' => $readerId, 'readerAddress' => $address];

		$response = $this->API->_send($this->url, $call, $options, 'POST', $this->api_key);
		if(is_object($response) && $response->success) return TRUE;
		else return FALSE;
	}

	/**
	 * Pull reader events from MongoDB, supply readerId && address to target the particular room/reader
	 * You can also send "time", "day", "month", to filter >= by those
	 * readerId = reader serial number (reader_id in db NOT id)
	 */
	public function get_reader_events ($options = []) {
		$call = 'get/reader-events/';

		$response = $this->API->_send($this->url, $call, $options, 'POST', $this->api_key);
		if(is_object($response) && $response->success) return $response;
		else return FALSE;
	}

	public function getSingleReaderEvent($options = []) {
		$call = 'get/reader-event/';

		$response = $this->API->_send($this->url, $call, $options, 'POST', $this->api_key);
		if(is_object($response) && $response->success) return $response;
		else return FALSE;
	}

	/**
	 * Reset a reader
	 * maintenance purposes
	 */
	public function reset_reader ($readerId, $address) {
		$call = 'readers/reset/';
		$options = ['readerId' => $readerId, 'readerAddress' => $address];

		$response = $this->API->_send($this->url, $call, $options, 'POST', $this->api_key);
		if(is_object($response) && $response->success) return TRUE;
		else return FALSE;
	}

	/**
	 * Reset multiple readers, expects multiarray of readers
	 * maintenance purposes
	 */
	public function reset_readers ($readerList) {
		$call = 'readers/reset-readers/';
		$options = ['readerList' => $readerList];

		$response = $this->API->_send($this->url, $call, $options, 'POST', $this->api_key);
		if(is_object($response) && $response->success) return TRUE;
		else return FALSE;
	}

	/**
	 * Get particular readerId (roomID) settings
	 */
	public function get_reader_settings($roomId, $gymId){
		$call = 'get/reader-settings/';
		$data = [ 'roomId' => $roomId, 'gymId' => $gymId ];

		$response = $this->API->_send($this->url, $call, $data, 'GET', $this->api_key);
		if(is_object($response) && $response->success) return $response;
		else return FALSE;
	}

		/**
	 * Add particular readerId (roomID) settings
	 */
	public function add_reader_settings($roomId, $gymId, $data){
		$call = 'add/add-reader-settings/';
		$data['roomId'] = $roomId;
		$data['gymId'] = $gymId;

		$response = $this->API->_send($this->url, $call, $data, 'POST', $this->api_key);
		if(is_object($response) && $response->success) return TRUE;
		else return FALSE;
	}

		/**
	 * Update particular readerId (roomID) settings
	 */
	public function save_reader_settings($roomId, $gymId, $data){
		$call = 'add/save-reader-settings/';
		$data['roomId'] = $roomId;
		$data['gymId'] = $gymId;

		$response = $this->API->_send($this->url, $call, $data, 'POST', $this->api_key);
		if(is_object($response) && $response->success) return TRUE;
		else return FALSE;
	}

		/**
	 * Delete particular readerId (roomID) settings
	 */
	public function delete_reader_settings($roomId, $gymId){
		$call = 'delete/reader-settings/';
		$data = [ 'roomId' => $roomId, 'gymId' => $gymId ];

		$response = $this->API->_send($this->url, $call, $data, 'POST', $this->api_key);
		if(is_object($response) && $response->success) return TRUE;
		else return FALSE;
	}
}
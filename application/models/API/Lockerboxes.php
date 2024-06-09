<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Separate transaction API functions
 */
class Lockerboxes extends CI_Model
{

    public function __construct(){
		parent::__construct();
		$this->load->library('curl');
		$this->url = config_item("api")["lockers"];
		$this->api_key = config_item("api")["lockers_api_key"];
	}

    // Get all lockers and their current status
    public function get_all_lockers($gymId){
        $call = 'get/lockers/';
        $options = ["gymId" => $gymId];

		$response = $this->API->_send($this->url, $call, $options, 'GET', $this->api_key);
		if(is_object($response) && $response->success) return $response->data;
		else return FALSE;
	}

	/**
	 * Get single locker information (gymid + mongo _id of a locker)
	 */
	public function get_locker_status($gymId, $lockerId){
		$call = 'get/locker/';
        $options = ["gymId" => $gymI, "lockerId" => $lockerId];

		$response = $this->API->_send($this->url, $call, $options, 'GET', $this->api_key);
		if(is_object($response) && $response->success) return $response->data;
		else return FALSE;
	}
	
	/**
	 * Remotely open a locker (without card)
	 */
	public function remote_unlock_locker ($lockerId) {
        $call = 'lockers/remote-open-locker';
        $options = ["lockerId" => $lockerId];

		$response = $this->API->_send($this->url, $call, $options, 'GET', $this->api_key);
		if(is_object($response) && $response->success) return TRUE;
		else return FALSE;
	}

	/**
	 * Remotely open a locker with a selected card
	 */
	public function remote_unlock_locker_with_card ($cardId, $lockerId) {
        $call = 'lockers/remote-open-locker-with-card';
        $options = ["lockerId" => $lockerId, "cardId" => $cardId];

		$response = $this->API->_send($this->url, $call, $options, 'GET', $this->api_key);
		if(is_object($response) && $response->success) return TRUE;
		else return FALSE;
	}
}
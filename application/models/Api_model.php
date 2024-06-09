<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Autoloaded model for API communication
 * 
 * This model now includes other models to separate different microservice connections
 * and to keep it organized and clean.
 * see below for details
 */
class Api_model extends CI_Model
{
	private $protocol = 'http';

    public function __construct(){
		parent::__construct();
		$this->load->library('curl');

		/**
		 * All microservices need to be running for the API calls to work properly
		 * Saleques and subscriptions are included in the transactions microservice
		 * Readers microservice looks for COM ports through settings and can work without finding any, operating reader settings and reading from a remote personificator still works
		 */

		// Models
		$this->load->model('API/Transactions', 'transactions'); // $this->API->transactions->xxx(); // Transakce
		$this->load->model('API/Depots', 'depots'); // $this–>API–>depots->xxx(); // Sklad
		$this->load->model('API/Subscriptions', 'subscriptions'); // $this–>API–>subscriptions->xxx(); // Členství
		$this->load->model('API/Readers', 'readers'); // $this–>API–>readers->xxx(); // Čtečky
		$this->load->model('API/Saleques', 'saleques'); // $this–>API–>saleques->xxx(); // Prodejní fronty
		$this->load->model('API/Lockerboxes', 'lockerboxes'); // $this->API->lockerboxes->xxx(); // Šatní skřínky
	}

	public function _send($domain, $call, array $params = [], $method = 'POST', $api_key = FALSE, $debug = FALSE) {
		$url = "{$this->protocol}://{$domain}/{$call}";
		$this->curl->ssl(FALSE);
		switch($method) {
			case 'POST':
				$cURL = $this->curl->create($url);
				$post = $cURL->post($params);
				if ($api_key) $cURL->http_header("X-Api-Key: ".$api_key."");
				$exec = $cURL->execute();
				if ($debug) return $cURL->debug();
				else return json_decode($exec);
			case 'GET':
				if ($api_key) $this->curl->http_header("X-Api-Key: ".$api_key."");
				if ($debug) return $this->curl->simple_get($url, $params);
				else return json_decode($this->curl->simple_get($url, $params));
		}
	}
}
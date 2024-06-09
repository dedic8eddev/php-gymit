<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Separate depot API functions
 */
class Depots extends CI_Model
{

    public function __construct(){
		parent::__construct();
    $this->load->library('curl');
    $this->depot_url = config_item("api")["depot"];
	}

    /** Add depot log 
     *  gymId: { type: String, required: true }, // ID klubu
     *   depotId: { type: Number, required: true }, // Id skladu
     *   itemId: { type: Number, required: true}, // Id skladové položky
     *   amount: { type: Number, required: true }, // Množství
     *   direction: { type: String, required: true }, // Směr pohybu ( to / from / new )
     *   loggedOn: { type: Date, default: Date.now, required: true }, // Datum/čas změny
     *   loggedBy: { type: String, required: true }, // Id zaměstnance provádějícího pohyb
    **/
    public function log_depot_event($log){
      $data = [ 'log' => $log ];
      $call = 'log';
      $response = $this->API->_send($this->depot_url, $call, $data, 'POST');
      if(is_object($response) && $response->success) return TRUE;
      else return $response;
    }

    /** Get depot log by its ID */
    public function get_log_by_id($log_id){
      $call = 'log/' . $log_id;

      $response = $this->API->_send($this->depot_url, $call, [], 'GET');
      if(is_object($response) && $response->success) return $response;
      else return $response;
    }

    /** Get depot log by item ID 
     * $params['from'] / $params['to'] for date range
     * $params['limit'] && $params['offset'] for pagination
     * $params['itemId'] for filtering logs based on depot item id
     * $params['depotId'] for filtering based on the id of the depot
     * $params['loggedBy'] for filtering based on the ID of the user who logged this movement
     * $params['gymId'] for filtering based on gym
     * $params['direction'] for filtering based on the direction of the log (from / to / new)
    */
    public function get_log_by_item_id($item_id, $params = []){
      $call = 'log/item/' . $item_id;

      $response = $this->API->_send($this->depot_url, $call, $params, 'GET');
      if(is_object($response) && $response->success) return $response;
      else return $response;
    }

    /** Get logs 
     * $params['from'] / $params['to'] for date range
     * $params['limit'] && $params['offset'] for pagination
     * $params['itemId'] for filtering logs based on depot item id
     * $params['depotId'] for filtering based on the id of the depot
     * $params['loggedBy'] for filtering based on the ID of the user who logged this movement
     * $params['gymId'] for filtering based on gym
     * $params['direction'] for filtering based on the direction of the log (from / to / new)
    */
    public function get_depot_logs($params = []){
      $call = 'log';
      $response = $this->API->_send($this->depot_url, $call, $params, 'GET');
      if(is_object($response) && $response->success) return $response;
      else return $response;
    }

    public function delete_depot_item_logs($item_id, $params = []){
      $call = 'log/delete_all/' . $item_id;
      $response = $this->API->_send($this->depot_url, $call, $params, 'GET');
      if(is_object($response) && $response->success) return TRUE;
      else return FALSE;
    }

}
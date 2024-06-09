<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lockers extends Backend_Controller {

	function __construct(){
		parent::__construct();
	}

    public function sectionName(): string
    {
        return SECTION_LOCKERS;
	}

	public function index(){
        $data['pageTitle'] = "Skřínky";
        $data["api_url"] = config_item("api")["lockers"];

		$this->app->assets(['tabulator.min.js', 'flatpickr.js', 'flatpickr.cs.js', 'admin.lockers.main.js'], 'js');
		$this->app->assets(['tabulator.min.css','flatpickr.css'], 'css');

		$this->load->view('layout/header', $data);
		$this->load->view('layout/menu', $data);
		$this->load->view('admin/lockers/index', $data);
		$this->load->view('layout/footer');
    }
    
    public function get_locker_data_ajax(){
        $gymId = (strlen(explode('gymit', $this->session->gym_db)[1]) > 0) ? (string) '0' .explode('gymit', $this->session->gym_db)[1] : (string) '0' . '1';

        if($data = $this->API->lockerboxes->get_all_lockers($gymId)){
            echo json_encode(["success" => true, "data" => $data]);
        }else{
            echo json_encode(["error" => true]);
        }
    }

    public function remote_open_locker(){
        $lockerId = $_POST["lockerId"];
        $gymId = (strlen(explode('gymit', $this->session->gym_db)[1]) > 0) ? (string) '0' .explode('gymit', $this->session->gym_db)[1] : (string) '0' . '1';

        if($locker_data = $this->API->lockerboxes->get_locker_status($gymId, $lockerId)){

            if($locker_data->lockerStatus->status == "locked"){

                $card = $this->db->like("card_id", $locker_data->lockerStatus->response)->get("users_cards")->row();

                if($card){
                    if($this->API->remote_unlock_locker_with_card($locker_data->lockerStatus->response, $lockerId)){
                        echo json_encode(["success" => true, "data" => $data]);
                    }else{
                        echo json_encode(["error" => true]);
                    }   
                }else{
                    if($this->API->remote_unlock_locker($lockerId)){
                        echo json_encode(["success" => true, "data" => $data]);
                    }else{
                        echo json_encode(["error" => true]);
                    }
                }
            }else{
                echo json_encode(["error" => true]);
            }
        }else{
            echo json_encode(["error" => true]);
        }
    }

}

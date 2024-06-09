<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cards extends Backend_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('users_model', 'users');
        $this->load->model('clients_model', 'clients');
    }

    public function sectionName(): string
    {
        return SECTION_CARD_MANAGEMENT;
    }

    public function index(){
        $this->checkReadPermission();
        $data['pageTitle'] = 'Karty';

        $data['pairSubmit'] = base_url('admin/cards/submit_pair_ajax');
        $data['cardsUrl'] = base_url('admin/cards/get_all_cards_ajax');

        $this->app->assets(['tabulator.min.js', 'flatpickr.js', 'flatpickr.cs.js', 'admin.cards.main.js'], 'js');
        $this->app->assets(['tabulator.min.css', 'flatpickr.css'], 'css');

        $this->load->view('layout/header', $data);
		$this->load->view('layout/menu', $data);
		$this->load->view('admin/cards/index', $data);
		$this->load->view('layout/footer');
    }

    public function get_all_cards_ajax(){
        $this->checkReadPermission(true);
        if($data = $this->cards->getAllCards()){
            echo json_encode($data);
        }else{
            echo json_encode([]);
        }
    }

    public function submit_pair_ajax(){
        $this->checkCreatePermission(true);
        // add vip or dailypass status
        if(isset($_POST['client_data'])){
            if($_POST['client_data']['multisport']==1){
                // create que with multisport parameter=true
                if(!$this->API->saleques->get_que($p['card_id'])) $this->api->saleques->create_que($p['card_id'],true); 
                // remove it because of update clients_data table
                unset($_POST['client_data']['multisport']); 
            }
            $this->db->update('clients_data',$_POST['client_data'],['client_id' => $_POST['client_id']]);
        }

        $res = $this->cards->addCardPair();

        if(isset($res["success"])){
            self::ajaxSuccessResponse();
        }else{
            self::ajaxErrorResponse($res);
        }
    }

    public function remove_pair_ajax(){
        $this->checkCreatePermission(true);
        $res = $this->cards->removeCardPair($_POST["user_id"]);

        if(isset($res["success"])){
            self::ajaxSuccessResponse();
        }else{
            self::ajaxErrorResponse($res);
        }
    }

    public function get_personificators_ajax(){
        return $this->app_components->getSelectPersonificators(['input_name' => 'reader_id','id' => 'reader_id', 'default-form-class' => 1]);
    }
}

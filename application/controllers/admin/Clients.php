<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clients extends Backend_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('clients_model', 'clients');
        $this->load->model('users_model', 'users');
    }

    public function sectionName() : string {
        return SECTION_CLIENTS;
    }

	public function index()	{
        $this->checkReadPermission();

        $data = array();

        $data['pageTitle'] = 'Správa zákazníků';

        $data['clientsUrl'] = base_url('admin/clients/get_clients_ajax');
        $data['addUrl'] = base_url('admin/clients/add_client_ajax');
        $data['saveUrl'] = base_url('admin/clients/save_client_ajax');

        $data['countries'] = $this->settings->getAllCountries();

        $this->app->assets(['tabulator.min.js', 'flatpickr.js', 'flatpickr.cs.js', 'admin.clients.main.js'], 'js');
        $this->app->assets(['tabulator.min.css', 'flatpickr.css'], 'css');

		$this->load->view('layout/header', $data);
        $this->load->view('layout/menu', $data);
        $this->load->view('admin/clients/overview', $data);
		$this->load->view('layout/footer');
    }

    public function edit($id){
        $this->checkEditPermission();

        $data['pageTitle'] = 'Detail zákazníka';
        $data['user'] = $this->users->getUser($id);
        $data['user_data'] = $this->users->getUserData($id);
        $data['client_data'] = $this->clients->getClientData($id);

        $card = $this->cards->getUserCard($id);

        if($card){
            $data['card'] = $card; // card
            $data['credit'] = $this->API->transactions->get_credit($id, $card->card_id); // kredit
            if(empty($data['credit']->data)){
                if($this->API->transactions->create_user_credit($id, $card->card_id)){
                    $data['credit'] = $this->API->transactions->get_credit($id, $card->card_id);
                }
            }
            $data['subscription'] = $this->API->subscriptions->get_subscription($id, current_gym_code()); // users sub
            if(!empty($data['subscription']->data)){
                $sub_info = $this->db->where('code', $data['subscription']->data->subType)->get('membership')->row();
                $sub_price = $this->db->where('membership_id', $sub_info->id)->where('period_type', $data['subscription']->data->subPeriod)->get('membership_prices')->row();

                $data['subscription_info'] = $sub_info;
                $data['subscription_price'] = $sub_price;
            }
        }

        $data["purchasedItemsUrl"] = base_url('admin/payments/get_all_purchased_items');
        $data["transactionsHistoryUrl"] = base_url('admin/payments/get_all_payments');
        $data["membershipBenefitsUsageUrl"] = base_url('admin/clients/get_all_membership_benefits_usage');
        $data["forbidAccessUrl"] = base_url('admin/clients/forbid_access_ajax');
        $data["saveDetail"] = base_url('admin/clients/save_client_ajax');
        $data["removeUser"] = base_url('admin/users/remove_user_ajax');
        $data["activateUser"] = base_url('admin/users/activate_user_ajax');

        $this->app->assets(['tabulator.min.js', 'flatpickr.js', 'flatpickr.cs.js', 'admin.clients.detail.js'], 'js');
        $this->app->assets(['tabulator.min.css', 'flatpickr.css'], 'css');      

        $this->load->view('layout/header', $data);
        $this->load->view('layout/menu', $data);
        $this->load->view('admin/clients/edit', $data);
        $this->load->view('layout/footer');
    }

    public function get_all_membership_benefits_usage(){
        echo json_encode($this->clients->getAllMembershipBenefitsUsage());
    }

    public function forbid_access_ajax(){
        echo json_encode(['success' => 'true']);
    }

    public function allow_access_ajax(){
        echo json_encode(['success' => 'true']);
    }    

    public function add_client_ajax(){
        $this->checkCreatePermission(true);
        $_POST['role'] = isset($_POST['disposable_user']) ? DISPOSABLE : CLIENT; // client role
        $_POST['user_id'] = $this->users->addUser($this->input->post());
        if($_POST['user_id'] > 0 && $this->clients->saveClientData()) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);
    }

    public function save_client_ajax(){
        $this->checkEditPermission(true);
        $_POST['role'] = isset($_POST['disposable_user']) ? DISPOSABLE : CLIENT; // client role

        if($this->users->saveUser() && $this->clients->saveClientData()) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);
    }

    public function get_client_ajax(){
        $client_id = $this->input->post("client_id");
        $client = $this->users->getUserData($client_id);
        $client->card = $this->users->getUserCard($client_id);

        if($client->card) $client->credit = $this->API->transactions->get_credit($client_id,$client->card->card_id)->data->currentValue ?? 0;
        if($client) echo json_encode(["success" => "true", "data" => $client]);
        else echo json_encode(["error" => "true"]);
    }
    
    public function get_clients_ajax(){
        $active = isset($_GET['active']) ? $_GET['active'] : 1;
        $clients = $this->users->getAllUsers($this->input->get("role"),$active);
        if($clients){
            foreach($clients["data"] as $c){
                $c->role = config_item("app")["roles_names"][$c->group_id];
            }
        }
        echo json_encode($clients);
    }

    public function search_disposable_clients_ajax(){
        $_GET['filters'][0]['field'] = 'full_name';
        $_GET['filters'][0]['type'] = 'like';
        $_GET['filters'][0]['value'] = $this->input->get('term');        
        $_GET['page'] = 1;
        $_GET['size'] = 25;

        $clients = $this->users->getAllUsers([DISPOSABLE],1,true);
        if($clients){
            foreach ($clients as $k=>$v){ $clients[$k]['value']=$v['full_name']; }
            echo json_encode($clients);
        }
        else echo json_encode([]);       
    }

    public function search_clients_ajax(){
        $term = $this->input->get('term');
        $clients = $this->users->searchClients($term);
        if($clients){
            echo json_encode($clients);
        }else{
            echo json_encode([]);
        }
    }

    public function search_memberships_ajax(){
        $term = $this->input->get('term');
        $memberships = $this->pricelist->searchMemberships($term);
        if($memberships){
            echo json_encode($memberships);
        }else{
            echo json_encode([]);
        }
    }

    public function search_employees_ajax(){
        $term = $this->input->get('term');
        $clients = $this->users->searchEmployees($term);
        if($clients){
            echo json_encode($clients);
        }else{
            echo json_encode([]);
        }
    }

    public function get_room_occupation(){
        if($data = $this->gyms->getGymRooms(current_gym_id(), true, false, true)){
            echo json_encode($data);
        }else{
            echo json_encode(['error' => 'true']);
        }
    }

    public function add_checkin_ajax(){
        $res = $this->gyms->checkinRoom();

        if($res === true){
            echo json_encode(['success' => "true"]);
        }else{
            echo json_encode(['error' => $res]);
        }
    }

    public function remove_checkin_ajax(){
        $res = $this->gyms->removeCheckin();

        if($res === true){
            echo json_encode(['success' => "true"]);
        }else{
            echo json_encode(['error' => $res]);
        }
    }

    public function get_checkin_log_ajax(){
        $res = $this->gyms->getCheckinLogForUserId();

        if(!is_string($res)){
            echo json_encode(['success' => "true", "data" => $res]);
        }else{
            echo json_encode(['error' => $res]);
        }
    }
}

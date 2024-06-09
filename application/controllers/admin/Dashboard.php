<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Backend_Controller {

    public function __construct(){
        parent::__construct();
		$this->load->model('dashboard_model', 'dash');
		$this->load->model('users_model', 'users');
		$this->load->model('pricelist_model', 'pricelist');
	}

    public function sectionName(): string
    {
        return SECTION_DASHBOARDS;
	}

	public function index(){
        $this->checkReadPermission();
		$data['rooms'] = $this->gyms->getGymRooms(current_gym_id(), true, false, true);

		$data['rooms'] = $this->gyms->getGymRooms(current_gym_id(), true);

		$data['pageTitle'] = 'Home';

		$this->app->assets(['tabulator.min.js', 'flatpickr.js', 'flatpickr.cs.js', 'admin.dashboard.main.js'], 'js');
		$this->app->assets(['tabulator.min.css','flatpickr.css'], 'css');
		
		$this->load->view('layout/header', $data);
		$this->load->view('layout/menu', $data);
		$this->load->view('admin/dashboard/index', $data);
		$this->load->view('layout/footer');
	}

	public function client_modal($id){
		if(isset($_GET['type']) && $_GET['type']=='card'){
			$client_id = $this->users->getUserIdByCard($id);
			$card_id = $id;
		} else {
			$card_id = $this->users->getUserCard($id)->card_id;
			$client_id = $id;
		}

		$data['user'] = $this->users->getUser($client_id);
		$data['user_data'] = $this->users->getUserData($client_id);

		

        if($card_id){
            $data['card_id'] = $card_id; // card
            $data['credit'] = $this->API->transactions->get_credit($client_id, $card_id); // kredit
            if(empty($data['credit']->data)){
                if($this->API->transactions->create_user_credit($client_id, $card_id)){
                    $data['credit'] = $this->API->transactions->get_credit($client_id, $card_id);
                }
            }
			$data['subscription'] = $this->API->subscriptions->get_subscription($client_id, current_gym_code()); // users sub
            if(!empty($data['subscription']->data)){
                $sub_info = $this->db->where('code', $data['subscription']->data->subType)->get('membership')->row();
                $sub_price = $this->db->where('membership_id', $sub_info->id)->where('period_type', $data['subscription']->data->subPeriod)->get('membership_prices')->row();

                $data['subscription_info'] = $sub_info;
				$data['subscription_price'] = $sub_price;
				$data['addItemModalUrl'] = base_url('admin/dashboard/add_item_modal/'.$sub_info->id);
			} else $data['addItemModalUrl'] = base_url('admin/dashboard/add_item_modal/0');
			
			$data['que']=$this->dash->getClientQueItems($card_id,@$sub_info->id);
		}
				
		$this->load->view('admin/dashboard/client_modal', $data);
	}

	// QUE MANAGE
	public function add_item_modal($membership_id){
		if($membership_id>0){ // has membership
			$data['price_list'] = $this->pricelist->getMembershipServicesPrices($membership_id,true);
		} else { // has not membership
			$data['price_list'] = $this->pricelist->getAllPrices(true);
		}
        $data['depot_items'] = $this->depot->getAllDepotItems(true,true);
        $data['depots'] = $this->depot->getAllDepots();		
		$this->load->view('/admin/dashboard/add_item_modal', $data);
	}

	public function add_items_to_que_ajax(){
		$p=$_POST;
		$queItems=[];
		foreach ($p['items'] as $type=>$items){
			foreach ($items as $k => $v){
				switch($type){
					case 'depot': 
						foreach ($v as $itemId => $item){
							array_push($queItems,['itemId'=>$itemId, 'depotId'=>$k, 'amount'=>$item['amount'], 'discount'=>$item['discount'], 'benefitId' => filter_var($v['benefit'], FILTER_VALIDATE_BOOLEAN) ? $v['benefit'] : null]); 
							$this->API->depot->reserveDepotItemStock(['item_id'=>$itemId, 'depot_id'=>$k, 'quantity'=>$item['amount'], 'note'=>'Domeček - Prodej']);
						}
					break;
					case 'service': 
						if($k==2){ // sheet (prostěradlo) rent
							$itemsInQue = $this->dash->getClientQueItems($p['card_id'],@$p['membership_id'],true);
							if(!in_array($k,$itemsInQue['service']??[])) $v['discount']=100; // firts sheet is free
						}
						array_push($queItems,[/*'timeSpent'=>107, */'itemId'=>$k, 'amount'=>$v['amount'], 'discount'=>$v['discount'], 'benefitId' => filter_var($v['benefit'], FILTER_VALIDATE_BOOLEAN) ? $v['benefit'] : null]);
					break;
				}
			}
		}
		// create que if not exists
		if(!$this->API->saleques->get_que($p['card_id'])) $this->API->saleques->create_que($p['card_id']);
		if($response=$this->API->saleques->add_to_que($p['card_id'], $queItems, $day = NULL)){
			$data['que'] = $this->dash->getClientQueItems($p['card_id'],@$p['membership_id']);
			$queList = preg_replace('/\s+/S', " ", $this->load->view('/admin/dashboard/user_que', $data, true));
			echo json_encode(["success" => "true", "data" => $queList]);
		} else echo json_encode(["error" => "true"]);
	}

	public function remove_item_from_que_ajax(){
		$p=$_POST;
		if($this->API->saleques->remove_from_que($p['card_id'], $p['que_id'], $day = NULL)){
			if($p['depot_id']){
				$this->API->depot->releaseDepotItemStock(['item_id'=>$p['item_id'], 'depot_id'=>$p['depot_id'], 'quantity'=>$p['amount'], 'note'=>'Domeček - Prodej']);
			}
			$data['que'] = $this->dash->getClientQueItems($p['card_id'],@$p['membership_id']);
			$queList = preg_replace('/\s+/S', " ", $this->load->view('/admin/dashboard/user_que', $data, true));
			echo json_encode(["success" => "true", "data" => $queList]);
		} else echo json_encode(["error" => "true"]);
	}

	// CLIENT MOVES

	public function get_client_moving_history_ajax(){
		$occupation = $this->dash->getClientMovingHistory($_POST['card_id'],'2019-11-21');
		$data = $this->load->view('/admin/dashboard/user_moves', $occupation, true);
		echo json_encode(["success" => "true", "data" => $data]);
	}

	// NOTIFICATIONS

	public function get_all_notifications_ajax(){
		if($data = $this->n->getAllNotifications()){
			$ids = [];
			foreach($data["data"] as $n){
				if(!$n->read) $ids[] = $n->id;
			}
			if(!empty($ids)) $this->n->readNotifications($ids);
            echo json_encode($data);
        }else echo json_encode($data);
	}

	public function notifications(){
		$g = $_GET;

		// Display single notification
		// or list
		if(isset($g['n'])){
			$data['pageTitle'] = 'Detail notifikace';

			$n_id = $g['n'];
			$data['notification'] = $this->n->getNotificationById($n_id);
			if(gym_userid() == $data['notification']->target OR gym_in_group($data["notification"]->group) OR gym_in_group([1])){
				$this->load->view('layout/header', $data);
				$this->load->view('layout/menu', $data);
				$this->load->view('admin/dashboard/notification', $data);
				$this->load->view('layout/footer');
			}else{
				redirect(base_url('admin/dashboard/notifications'));
			}
		}else{
			$data['pageTitle'] = 'Notifikace';
			$data['notifications'] = $this->n->getUsersNotifications(NULL, [0]);
	
			$this->app->assets(['flatpickr.js', 'flatpickr.cs.js', 'admin.notifications.main.js'], 'js');
			$this->app->assets(['flatpickr.css'], 'css');
	
			$this->load->view('layout/header', $data);
			$this->load->view('layout/menu', $data);
			$this->load->view('admin/dashboard/notifications', $data);
			$this->load->view('layout/footer');
		}
	}

	// USER CHANGE

	public function f_acc_cancel(){
		if($this->ion_auth->in_group(1)){
			if(gym_restore_account()) echo json_encode([]);
			else echo json_encode(["alert" => "Unable to revert current user view, please refresh and try again."]);
		} else echo json_encode(["error" => "true"]);
	}

	public function f_acc(){
		$id = $this->input->post("uid");
		if(!$id){
			echo json_encode(["error" => "true"]);
		}else{
			// fallback to ion for safety (dont change to cx!)
			if($this->ion_auth->in_group(1)){
				$group = $this->ion_auth->get_users_groups($id)->result()[0];
				if(gym_fake_account($id, $group->id)){
					echo json_encode(['url'=>base_url($group->location)]);
				}else{
					echo json_encode(["alert" => "Nepovedlo se přepnout uživatele."]);
				}
			}else{
				echo json_encode(["error" => "true"]);
			}
		}
	}

	public function get_user_role(){
		echo json_encode(["role" => gym_group()]);
	}
	public function get_current_gymcode(){
		echo json_encode(["gym_code" => current_gym_code()]);
	}

	public function get_user_roles(){
		$roles = $this->db->select('id, description')->from('groups')->get()->result();
		$return = [];
		foreach($roles as $role){
			$return[$role->id] = $role->description;
		}

		echo json_encode($return);
	}

	// PERSONIFICATORS

	// Grab data from a single reader
	public function read_personificator_data(){
		$readerId = $_POST['readerId'];
		$date = (isset($_POST['date'])) ? $_POST['date'] : NULL;
		$data = $this->API->readers->get_identificator_card($readerId, $date);

		if($data) echo json_encode($data->data);
		else echo json_encode(['error' => 'true', 'msg' => 'No data.']);
	}

	// DB SWITCH

	public function switch_db(){
		$dbname = $this->input->post("dbname");

		$this->load->model('gyms_model', 'gyms');
		$gym = $this->gyms->getGymByDbName($dbname);

		if(!$dbname){
			echo json_encode(["error" => "Něco se pokazilo!"]);
		}else{
			// fallback to ion for safety (dont change!)
			if($this->ion_auth->in_group(1)){
				if(switch_gym_db($dbname, $gym[0]['_id']->{'$id'}, $gym[0]['name'])) echo json_encode([]);
				else echo json_encode(["alert" => "Nepovedlo se přepnout provozovnu, zkuste to znovu."]);
			} else echo json_encode(["error" => "Nemáte dostatečné oprávnění!"]);
		}
	}

	public function inf(){ echo phpinfo(); }
}

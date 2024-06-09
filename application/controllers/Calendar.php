<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calendar extends Public_Controller {
	public function __construct(){
		parent::__construct();   
		$this->load->model('lessons_model', 'lessons');
		$this->load->model('users_model', 'users');
	}

	public function index(){
		$data = [];
		$header['pageTitle'] = 'Rozvrh a rezervace';
		$data['lessons'] = $this->lessons->getTableTemplates(true);
		$data['coaches'] = $this->users->getAllUsers([PERSONAL_TRAINER, MASTER_TRAINER]); // only coaches, not instructors

		$data['calendar']['from'] = date('Y-m-d', strtotime('monday this week'));
		$data['calendar']['to'] = date('Y-m-d', strtotime($data['calendar']['from'].'+ 7 days'));
		$data['calendar']['wholeDay'] = 1; // from opening time to closing time
		$data['calendar']['data'] = $this->lessons->getFrontCalendar($data['calendar']);

		// page settings
		foreach ($this->gyms->getGymSettings(['page_calendar']) as $k => $v){
            $data[$v['type']]=json_decode($v['data'],true);
		}
		// blocks
		foreach ($this->gyms->getGymSettings($data['page_calendar']['blocks']) as $k => $v){
            $data[$v['type']]=json_decode($v['data'],true);
		}

		$this->load->view('frontend/layout/header',$header);
		$this->load->view('frontend/layout/menu', $data);
		$this->load->view('frontend/calendar/index', $data);
		$this->load->view('frontend/layout/footer', $data);
	}

	public function get_calendar_data_ajax(){
		$data['from'] = $_POST['from'];
		$data['to'] = date('Y-m-d', strtotime($data['from'].'+ 7 days'));
		$data['wholeDay'] = 1; // from opening time to closing time		
		$data['lesson'] = $_POST['lesson'];
		$data['coach'] = $_POST['coach'];
		$data['data'] = $this->lessons->getFrontCalendar($data);
		$calendar = $this->load->view('frontend/calendar/calendar', $data, true);
		if($calendar) echo json_encode(['success' => 'true', 'data' => $calendar]);
        else echo json_encode(['error' => 'true']);  
	}

	public function reserve_lesson_ajax(){
		$p = $_POST;
		if(!isset($p['user_id'])){ // login first
			if($this->ion_auth->login($p['email'], $p['password'], false)) {
				// Setup session for auth purposes
				$u = $this->ion_auth->user()->row();
				setLoginSession($u->id, $this->ion_auth->get_users_groups()->result()[0]->id);
			}else{
				echo json_encode(['type' => 'error', 'msg' => 'Zadané jméno nebo heslo nejsou správné!']);
				exit;
			}
		}
		$lesson = $this->db->where("l.id", $p['lesson_id'])->where('l.template_id=lt.id')->get("lessons l, lessons_templates lt")->row();
		$lesson_clients = json_decode($this->db->select("concat('[',group_concat(client_id),']') clients")->where('lesson_id',$p['lesson_id'])->get('lessons_clients')->row()->clients,true) ?? [];
		if(!in_array(gym_userid(), $lesson_clients)) array_push($lesson_clients,gym_userid()); // add client to actual clients
		$ret = $this->lessons->processLessonClients($p['lesson_id'], $lesson->starting_on, $lesson->ending_on, $lesson->client_limit, $lesson_clients)[0];
		$ret['openSlots'] = $lesson->client_limit - count($lesson_clients);
		echo json_encode($ret);
	}

	public function cancel_reservation_ajax(){
		$p = $_POST;
		$lesson = $this->db->where("l.id", $p['lesson_id'])->where('l.template_id=lt.id')->get("lessons l, lessons_templates lt")->row();
		$lesson_clients = json_decode($this->db->select("concat('[',group_concat(distinct client_id),']') clients")->where('lesson_id',$p['lesson_id'])->get('lessons_clients')->row()->clients,true) ?? [];
		if (($key = array_search(gym_userid(), $lesson_clients)) !== false) {
			unset($lesson_clients[$key]);
		} else {
			echo json_encode(['type' => 'error', 'msg' => 'Na této lekci nemáte rezervaci']);
			exit;
		}
		$ret = $this->lessons->processLessonClients($p['lesson_id'], $lesson->starting_on, $lesson->ending_on, $lesson_clients)[0];
		$ret['openSlots'] = $lesson->client_limit - count($lesson_clients);
		echo json_encode($ret);	
	}
}

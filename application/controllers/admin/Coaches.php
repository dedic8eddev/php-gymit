<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class coaches extends Backend_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('coaches_model', 'coaches');
        $this->load->model('users_model', 'users');
        $this->load->model('lessons_model', 'lessons');
    }

    public function sectionName(): string
    {
        return SECTION_COACHES;
    }

	public function index(){
        $this->checkReadPermission();

        $data = array();

        $data['pageTitle'] = 'Správa instruktorů a trenérů';

        $data['coachesUrl'] = base_url('admin/coaches/get_coaches_ajax');
        $data['instructorsUrl'] = base_url('admin/coaches/get_coaches_ajax');
        $data['addUrl'] = base_url('admin/coaches/add_coach_ajax');

        $data['countries'] = $this->settings->getAllCountries();
        $data['specializations'] = $this->coaches->getAllSpecializations(true);
        $this->app->assets(['tabulator.min.js', 'flatpickr.js', 'flatpickr.cs.js', 'admin._trumbowyg.js', 'admin.coaches.main.js'], 'js');
        $this->app->assets(['tabulator.min.css', 'flatpickr.css'], 'css');

		$this->load->view('layout/header', $data);
        $this->load->view('layout/menu', $data);
        $this->load->view('admin/coaches/overview', $data);
		$this->load->view('layout/footer');
    }

    public function edit($id){
        $data['coach'] = $this->users->getUser($id);
        $data['pageTitle'] = isTrainer($data['coach']->group_id) ? 'Detail trenéra' : 'Detail instruktora';

        $data['users_data'] = $this->users->getUserData($id);
        $data['coach_data'] = $this->coaches->getCoachData($id);
        $data['coach_specializations'] = array_map(function($row){
            return $row['specialization_id'];
        }, $this->coaches->getCoachSpecializations($id));
        $data['specializations'] = $this->coaches->getAllSpecializations(true);

        $data['getCoachLessonsUrl'] = base_url('admin/coaches/get_coach_lessons_ajax');
        $data["saveDetail"] = base_url('admin/coaches/save_coach_ajax');
        $data["removeUser"] = base_url('admin/users/remove_user_ajax');
        $data["activateUser"] = base_url('admin/users/activate_user_ajax');

        $this->app->assets(['tabulator.min.js', 'flatpickr.js', 'flatpickr.cs.js', 'admin._trumbowyg.js', 'admin.coaches.edit.js'], 'js');
        $this->app->assets(['tabulator.min.css', 'flatpickr.css', 'admin.coaches.main.css'], 'css');      

        $this->load->view('layout/header', $data);
        $this->load->view('layout/menu', $data);
        $this->load->view('admin/coaches/edit', $data);
        $this->load->view('layout/footer');
    }

    // SPECIALIZATIONS

    public function specializations(){
        $data['specializationsUrl'] = base_url('admin/coaches/get_specializations_ajax');
        $this->load->view('admin/coaches/specializations', $data);
    }

	public function save_specialization_ajax(){
        if($this->coaches->saveSpecialization($this->input->post())) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);
	}	

	public function delete_specialization_ajax(){
        if($this->coaches->deleteSpecialization($this->input->post())) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);
    }

    public function get_specializations_ajax(){
        echo json_encode($this->coaches->getAllSpecializations());
    }

    // COACHES

    public function add_coach_ajax(){
        $this->checkCreatePermission(true);

        $_POST['user_id'] = $this->users->addUser($this->input->post());
        if($_POST['user_id'] > 0 && $this->coaches->saveCoachSpecializations() && $this->coaches->saveCoachData()) {
            echo json_encode(['success' => 'true']);
        } else {
            echo json_encode(['error' => 'true']);
        }
    }

    public function save_coach_ajax(){
        $this->checkEditPermission(true);
        if($this->users->saveUser() && $this->coaches->saveCoachData() && $this->coaches->saveCoachSpecializations()) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);
    }

    public function get_coach_ajax(){
        $coach_id = $this->input->post("coach_id");
        $coach = $this->users->getUser($coach_id);
        
        if($coach) echo json_encode(["success" => "true", "data" => $coach]);
        else echo json_encode(["error" => "true"]);
    }
    
    public function get_coaches_ajax(){
        $active = isset($_GET['active']) ? $_GET['active'] : 1;
        $coaches = $this->users->getAllUsers($this->input->get("role"),$active);
        if($coaches){
            foreach($coaches["data"] as $c){
                $c->role = config_item("app")["roles_names"][$c->group_id];
            }
        }
        echo json_encode($coaches);
    }

    // COACH LESSONS
    public function get_coach_lessons_ajax(){
        $coach_id = $this->input->get('coach_id');
        $past = filter_var($this->input->get('past'), FILTER_VALIDATE_BOOLEAN);
        echo json_encode($this->coaches->getCoachLessons($coach_id,$past));
    }

    public function cancel_lessons($teacher_id){
        $data['cancelLessonsUrl'] = base_url('admin/coaches/cancel_lessons_ajax');
        $data['teacher_id'] = $teacher_id;
        $this->load->view('admin/coaches/cancel_lessons', $data);
    }

    public function cancel_lessons_ajax(){
        $action = $this->input->post('action');
        if($action=='cancel') $ret=$this->lessons->cancelLessons($this->input->post());
        else $ret=$this->lessons->substituteLessonTeacher($this->input->post());

        if($ret) echo json_encode(["success" => "true"]);
        else echo json_encode(["error" => "true"]);        
    }
}

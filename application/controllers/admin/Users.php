<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends Backend_Controller {

    const ALL_USERS = [
        ADMINISTRATOR,
        STORE_MANAGER,
        SENIOR_RECEPTIONIST,
        RECEPTIONIST,
        WELLNESS_SERVICE,
        CHILDREN_PLAY_AREA_WORKER,
        WEBMASTER,
        GYM_AND_STUDIO_MANAGER,
        MASTER_TRAINER,
        PERSONAL_TRAINER,
        INSTRUCTOR,
        SERVICE_TECHNICIAN,
    ];

    public function __construct(){
        parent::__construct();
        $this->load->model('users_model', 'users');
        $this->load->model('fields_model', 'fields');
    }

    public function sectionName(): string
    {
        return SECTION_USERS;
    }

    public function index()
	{
        $this->checkReadPermission();

        $data = array();

        $data['pageTitle'] = 'Správa uživatelů';

        $data['usersUrl'] = base_url('admin/users/get_users_ajax');
        $data['inactivesUrl'] = base_url('admin/users/get_inactive_users_ajax');
        $data['inviteUser'] = base_url('admin/users/invite_user_ajax');
        $data['invitesUrl'] = base_url('admin/users/get_invites_ajax');

        $data['addUrl'] = base_url('admin/users/add_user_ajax');
        $data['saveUrl'] = base_url('admin/users/save_user_ajax');

        $data['custom_fields'] = $this->fields->getSectionCustomFields('users');

        $this->app->assets(['tabulator.min.js', 'flatpickr.js', 'flatpickr.cs.js', 'admin.users.main.js'], 'js');
        $this->app->assets(['tabulator.min.css', 'flatpickr.css'], 'css');

		$this->load->view('layout/header', $data);
        $this->load->view('layout/menu', $data);
        $this->load->view('admin/users/overview', $data);
		$this->load->view('layout/footer');
    }

    public function invite_user_ajax(){
        if($this->users->inviteUser($this->input->post("email"), $this->input->post("role"))){
            echo json_encode(['success' => 'true']);
        }else{
            echo json_encode(['error' => 'true']);
        }
    }

    public function get_invites_ajax(){
        $users = $this->users->getAllInvites();
        if($users){
            foreach($users as $u){
                $u->role = config_item("app")["roles_names"][$u->group_id - 1];
                $u->date_created = date('d.m.Y H:i', strtotime($u->date_created));
            }
        }
        echo json_encode($users);
    }

    public function remove_invite_ajax(){
        if($this->users->deleteInvite()){
            echo json_encode(['success' => 'true']);
        }else{
            echo json_encode(['error' => 'true']);
        }
    }

    public function detail($id){
        $data['pageTitle'] = 'Detail uživatele';
        $data['user'] = $this->users->getUser($id);
        $data['user_data'] = $this->users->getUserData($id);

        $this->app->assets(['flatpickr.js', 'flatpickr.cs.js', 'admin.users.detail.js'], 'js');
        $this->app->assets(['flatpickr.css'], 'css');

        $this->load->view('layout/header', $data);
        $this->load->view('layout/menu', $data);
        $this->load->view('admin/users/detail', $data);
        $this->load->view('layout/footer');
    }

    public function edit($id){

        $data['pageTitle'] = 'Úprava uživatele';
        $data['user'] = $this->users->getUser($id);
        $data['user_data'] = $this->users->getUserData($id);

        $data["saveDetail"] = base_url('admin/users/save_user_ajax');
        $data["removeUser"] = base_url('admin/users/remove_user_ajax');
        $data["activateUser"] = base_url('admin/users/activate_user_ajax');

        $data['custom_fields'] = $this->fields->getSectionCustomFields('users');
        $data['custom_fields_values'] = $this->fields->getCustomFieldsValues($id, $data['custom_fields']);

        $this->app->assets(['flatpickr.js', 'flatpickr.cs.js', 'admin.users.edit.js'], 'js');
        $this->app->assets(['flatpickr.css'], 'css');

        $this->load->view('layout/header', $data);
        $this->load->view('layout/menu', $data);
        $this->load->view('admin/users/edit', $data);
        $this->load->view('layout/footer');
    }

    public function add_user_ajax()
    {
        $this->checkCreatePermission(true);
        $data = $this->input->post();
        
        if($this->users->addUser($data)){
            echo json_encode(['success' => 'true']);
        }else{
            echo json_encode(['error' => 'true']);
        }
    }

    public function save_user_ajax(){
        $this->checkEditPermission(true);

        if($this->users->saveUser()){
            echo json_encode(['success' => 'true']);
        }else{
            echo json_encode(['error' => 'true']);
        }
    }

    public function remove_user_ajax(){
        $this->checkDeletePermission(true);

        if($this->users->deleteUser()){
            echo json_encode(['success' => 'true']);
        }else{
            echo json_encode(['error' => 'true']);
        }
    }
    public function activate_user_ajax(){
        if($this->users->activateUser()){
            echo json_encode(['success' => 'true']);
        }else{
            echo json_encode(['error' => 'true']);
        }
    }

    public function get_user_ajax()
    {
        $user_id = $this->input->post("user_id");
        $user = $this->users->getUser($user_id);
        if($user){
            echo json_encode(["success" => "true", "data" => $user]);
        }else{
            echo json_encode(["error" => "true"]);
        }
    }
    
    public function get_users_ajax()
    {
        $users = $this->users->getAllUsers(self::ALL_USERS);
        if($users){
            foreach($users["data"] as $u){
                $u->role = config_item("app")["roles_names"][$u->group_id];
            }
        }
        echo json_encode($users);
    }

    public function get_inactive_users_ajax()
    {
        $users = $this->users->getAllUsers(self::ALL_USERS, 0);
        if($users){
            foreach($users["data"] as $u){
                $u->role = config_item("app")["roles_names"][$u->group_id];
            }
        }
        echo json_encode($users);
    }
}

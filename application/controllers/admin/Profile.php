<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends Backend_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('profile_model', 'profile');
    }

    public function sectionName(): string
    {
        return 'profile';
    }

    public function index()
	{
        $data['pageTitle'] = 'My profile';

        $data['saveProfileUrl'] = base_url('admin/profile/save_profile_ajax');
        $data['changePasswordUrl'] = base_url('admin/profile/change_password_ajax');

        $this->app->assets(['admin.profile.main.js', 'https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.2.0/zxcvbn.js'], 'js');

        $user = $this->ion_auth->user(gym_userid())->row();
        $data['user'] = $user;

        $data['user_data'] = $this->db->where("user_id", gym_userid())->get("users_data")->row();

		$this->load->view('layout/header', $data);
        $this->load->view('layout/menu', $data);
        $this->load->view('admin/profile/index', $data);
		$this->load->view('layout/footer');
    }
    
    public function save_profile_ajax(){
        if($this->profile->saveUserProfile()){
            echo json_encode(["success" => "true"]);
        }else{
            echo json_encode(["error" => "true"]);
        }
    }

    public function change_password_ajax(){
        if($this->profile->changePassword()){
            echo json_encode(["success" => "true"]);
        }else{
            echo json_encode(["error" => "true"]);
        }
    }

}

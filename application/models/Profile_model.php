<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Profile_model extends CI_Model
{
    public function __construct(){
        if(!$this->ion_auth->logged_in()){
            exit('Access forbidden.');
        }
    }

    // Update your profile data (basic data)
    public function saveUserProfile(){
        $current_user = $this->ion_auth->user(gym_userid())->row();

        $data = [
            'email' => $this->input->post("email"),
            'username' => $this->input->post("email")
        ];

        if($this->db->where('id', $current_user->id)->update('users', $data)){
            $this->db->where("user_id", $current_user->id)->update("users_data", ["first_name" => $this->input->post("first_name"), "last_name" => $this->input->post("last_name")]);
            return TRUE;
        }else{
            return FALSE;
        }
    }

    // Change your password
    public function changePassword(){
        $current_user = $this->ion_auth->user(gym_userid())->row();
        $new_password = $this->input->post("password");
        if($this->ion_auth->update($current_user->id, ['password' => $new_password])){
            return TRUE;
        }else{
            return FALSE;
        }
    }
}
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Account_Model extends CI_Model
{
	public function __construct()
    {
	    parent::__construct();
        $this->load->model('users_model', 'users');
    }

    public function updatePersonalInfo(int $userId, array $data): bool
    {
        return $this->users->updateUserDataFromClient($userId, $data);
    }

    /** @todo */
    public function updateNotification(int $userId, array $data): bool
    {
        return false;
    }

    public function updateSecurity(int $userId, array $data): bool
    {
        $changed = false;

        if (! empty($data['current_password']) and ! empty($data['new_password'])) {
            $changed = $this->ion_auth->change_password($userId, (string) $data['current_password'], (string) $data['new_password']);
        }

        if (! empty($data['username'])) {
            $changed = $this->ion_auth->change_username($userId, (string) $data['username']);
        }

        return $changed;
    }

}
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Users_model extends CI_Model
{
    public function __construct(){
        $this->load->model('fields_model', 'fields');
        $this->gymdb->init(); // init default db
    }

    /** Generate a random password string */
    private function generatePassword(){
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $password = array(); 
        $alpha_length = strlen($alphabet) - 1; 
            for ($i = 0; $i < 16; $i++) 
            {
                $n = rand(0, $alpha_length);
                $password[] = $alphabet[$n];
            }
        return implode($password); 
    }

    /**
     * Generate a unique base64 encoded and URL friendly token
     */
    private function createToken(){
        $token = base64_encode(random_bytes(64));
        $token = strtr($token, '+/', '-_');
        return $token;
    }

    /**
     * Send password reset link
     */
    public function resetPassword($email){
        if ($this->ion_auth->email_check($email)){
            $user = $this->db->where('email', $email)->get('users')->row();
            $token = $this->createToken();

            // insert token into db
            $this->db->insert('users_tokens', ['token' => $token, 'user_id' => $user->id, 'type' => 'password_reset']);

            // send mail via mailgun
            $email_body = $this->load->view('emails/reset_password', ["user_id" => $user->id, "token" => $token], TRUE);
            $this->mailgun::send([
                'from' => "Gymit <no-reply@gymit.cz>",
                'to' => $email,
                'subject' => "Gymit - Reset hesla",
                'html' => $email_body
            ]);

            return TRUE;
        }else{
            return FALSE;
        }
    }

    /**
     * Send an invitation to a new user account
     */
    public function inviteUser($email, $role){

        if (!$this->ion_auth->email_check($email)){

            $token = $this->createToken();
            $password = $this->generatePassword();

            if($u = $this->ion_auth->register($email, $password, $email, [
                'active' => 1,
                'created_by' => gym_userid()
            ], [$role])){

                // insert token into db
                $this->db->insert('users_tokens', ['token' => $token, 'user_id' => $u, 'type' => 'invitation']);

                // send mail via mailgun
                $email_body = $this->load->view('emails/invitation', ["user_type" => config_item('app')['roles_names'][$role], "user_id" => $u, "token" => $token], TRUE);
                $this->mailgun::send([
                    'from' => "Gymit <no-reply@gymit.cz>",
                    'to' => $email,
                    'subject' => "Gymit - Pozvánka ke spolupráci!",
                    'html' => $email_body
                ]);
    
                return $u;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    public function getOrCreateDisposableUser(array $data, $returnOnlyId=FALSE){
        $user = $this->db->select('u.id,ud.first_name,ud.last_name,u.email')
            ->from('users u')
            ->join('users_groups ug', 'ug.user_id = u.id')
            ->join('users_data ud', 'ud.user_id = u.id')
            ->where(['u.email' => $data['email']])
            ->get()->row_array();
        if($user){ 
            if($returnOnlyId) return $user->id;
            else return $user;
        } else { // CREATE 
            if(isset($data['email']) && !empty($data['email'])){
                $email = $data['email'];
            } else { // generate disposable email
                $duplicate = 1;
                while($duplicate > 0){ // generate code till code is unique
                    $email = 'disposable'.rand(1,1000000).'@email.com';
                    $duplicate = $this->ion_auth->email_check($email);
                }
            }
            // create disposable user
            if($u = $this->ion_auth->register($email, config_item('app')['disposable_user_password'], $email, ['active' => 1, 'created_by' => gym_userid()], [DISPOSABLE])){        
                // user data object
                $this->db->insert('users_data', [
                    'user_id' => $u, 
                    'first_name' => $data['first_name'], 
                    'last_name' => $data['last_name'],
                    'email' => $email,
                    'phone' => @$data['phone']
                ]);
            }
            if($returnOnlyId) return $u;
            else return ['id'=>$u, 'first_name' => $data['first_name'], 'last_name' => $data['last_name'], 'email'=>$email];
        }
    }

    /**
     * Manually add a user account
     */
    public function addUser($data){
        $data = array_map(function($data){ // handle null values for DB
            if($data=='null' or $data=='') return null;
            else return $data;
        }, $data);     

        if (!$this->ion_auth->email_check($data["email"])){
            $password = $this->generatePassword();

            if($u = $this->ion_auth->register($data['email'], $password, $data['email'], [
                'active' => $data['active'],
                'created_by' => gym_userid()
            ], [$data['role']])){

                // user data object
                $this->db->insert('users_data', [
                    'user_id' => $u, 
                    'first_name' => $data['first_name'], 
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'street' => $data['street'],
                    'city' => $data['city'],
                    'zip' => $data['zip'],
                    'country' => $data['country'],
                    'company_id' => $data['company_id'],
                    'vat_enabled' => isset($data['vat_enabled']) ? $data['vat_enabled'] : 0,
                    'vat_id' => $data['vat_id'],
                    'identification_type' => @$data['identification_type'],
                    'identification' => @$data['identification'],
                    'birth_date' => @$data['birth_date'], 
                    'personal_identification_number' => @$data['personal_identification_number'], // rodne cislo   
                    'photo' => @$data['photo'],
                    'internal_note' => @$data['internal_note']
                ]);

                // Register card to this user
                $user_id = $this->db->insert_id();
                if(isset($data["card_id"])){
                    if($data['role'] == CLIENT) $this->cards->addCardPair($user_id, $data["card_id"]);
                    else $this->cards->addCardPair($user_id, $data["card_id"], TRUE); // employee
                }

                // send mail via mailgun
                $email_body = $this->load->view('emails/registration', ["user_type" => config_item('app')['roles_names'][$data['role']], "login" => $data['email'], "password" => $password], TRUE);
                $this->mailgun::send([
                    'from' => "Gymit <no-reply@gymit.cz>",
                    'to' => $data['email'],
                    'subject' => "Gymit - Nová registrace!",
                    'html' => $email_body
                ]);

                $this->fields->saveCustomFieldsValues($data, $u, 'users');
                return $u;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    /**
     * @param int $userId
     * @param array $userData
     * @return bool
     */
    public function updateUserDataFromClient(int $userId, array $userData): bool
    {
        return $this->db
            ->where('user_id', $userId)
            ->update('users_data', [
                'first_name'    => $userData['first_name'],
                'last_name'     => $userData['last_name'],
                'phone'         => $userData['phone'],
                'email'         => $userData['email'],
                'street'        => $userData['street'],
                'city'          => $userData['city'],
                'zip'           => $userData['zip'],
                'country'       => $userData['country'],
                'birth_date'    => $userData['birth_date'],
        ]);
    }

    /**
     * Save user account profile
     */
    public function saveUser(){
        $u = $_POST;
        $u = array_map(function($d){ // handle null values for DB
            if($d=='null' or $d=='') return null;
            else return $d;
        }, $u);        

        $id = $u['user_id'];

        // Base user object
        $user['email'] = $u['email'];
        $user['username'] = $u['email'];

        $role = $u['role'];
        $current_role = $this->ion_auth->get_users_groups($id)->result()[0]->id;

        // Save
        $save_user = $this->db->where('id', $id)->update('users', $user);
        if($save_user){

            // user data object
            $this->db->where('user_id', $id)->update('users_data', [
                'first_name' => $u['first_name'], 
                'last_name' => $u['last_name'],
                'email' => $u['email'],
                'phone' => $u['phone'],
                'street' => $u['street'],
                'city' => $u['city'],
                'zip' => $u['zip'],
                'country' => $u['country'],
                'company_id' => $u['company_id'],
                'vat_enabled' => isset($u['vat_enabled']) ? $u['vat_enabled'] : 0,
                'vat_id' => $u['vat_id'],
                'identification_type' => @$u['identification_type'],
                'identification' => @$u['identification'],                    
                'birth_date' => @$u['birth_date'],  
                'personal_identification_number' => @$u['personal_identification_number'], // rodne cislo  
                'photo' => @$u['photo'],
                'internal_note' => @$u['internal_note']
            ]);

            $this->fields->saveCustomFieldsValues($u, $id, 'users');

            if(isset($role) && $current_role != $role){
                $this->ion_auth->add_to_group($role, $id);
                $this->ion_auth->remove_from_group($current_role, $id);
            }

            return TRUE;
        }else{
            return FALSE;
        }
    }

    /**
     * Deactivate a user account
     */
    public function deleteUser(){
        $user_id = $this->input->post('user_id');
        if($this->db->where('id', $user_id)->update('users', ['active' => 0, 'active_last_change' => date("Y-m-d H:i:s")])){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    public function activateUser(){
        $user_id = $this->input->post('user_id');
        if($this->db->where('id', $user_id)->update('users', ['active' => 1, 'active_last_change' => date("Y-m-d H:i:s")])){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    /** 
     * Delete an invite + user account with it
     */
    public function deleteInvite(){
        $user_id = $this->input->post('user_id');
        if($this->db->where('id', $user_id)->delete('users') && $this->db->where('type', 'invitation')->where('user_id', $user_id)->delete('users_tokens')){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    /**
     * Change a users account active status (prevent login)
     */
    public function changeUserStatus(){
        $user_id = $this->input->post('user_id');
        $user = $this->db->where('id', $user_id)->get('users')->row();

        $active = 1;
        if($user->active){
            $active = 0;
        }
        if($this->db->where('id', $user_id)->update('users', ['active' => $active])){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    /**
     * Get a specific user account
     */
    public function getUser($id,$front=FALSE){
        if($front) $this->db->select('u.id,u.email');
        else $this->db->select('u.*,c.user_id as created_by_id, concat(c.first_name,\' \',c.last_name) as created_by_name, ug.group_id');
        $user = $this->db->from('users u')
            ->join('users_groups ug', 'u.id=ug.user_id', 'LEFT')
            ->join('users_data c', 'u.created_by=c.user_id', 'LEFT')
            ->where('u.id', $id)->get()->row();
        if($user){
            $user->group_id = $this->db->where('user_id', $id)->get('users_groups')->row()->group_id;
            return $user;
        }else{
            return false;
        }
    }

    public function getUserData($id,$front=FALSE){
        if($front) $this->db->select('u.first_name,u.last_name');
        else $this->db->select('u.*, media.file photo_src, media.meta_tags photo_meta');
        
        $data = $this->db->from('users_data u')
                        ->join('media','media.id = u.photo','left')
                        ->where('u.user_id', $id)->get()->row();
        if($data) return $data;
        else return false;
    }

    public function getUserCard($id){
        $data = $this->db->where('user_id', $id)->get('users_cards')->row();
        if($data) return $data;
        else return false;     
    }

    public function getUserIdByCard($id){
        $data = $this->db->where('card_id', $id)->get('users_cards')->row();
        if($data) return $data->user_id;
        else return false;            
    }

    /**
     * Get users divided by groups
     */
    public function getUsersInGroups () {
        $groups = $this->db->get("groups")->result();

        $return = new stdClass();
        foreach($groups as $group){
            if(!isset($return->{$group->description})) $return->{$group->description} = array();
                $this->db->select('users.id, users.active, users.email, users.created_by, users_groups.group_id,
                                   users_data.first_name, users_data.last_name, users_data.phone, users_data.birth_date,
                                   users_cards.card_id,
                                   concat(users_data.first_name,\' \',users_data.last_name) as full_name');
                                   $this->db->from('users');
                                   
                $this->db->join('users_groups', 'users_groups.user_id = users.id');
                $this->db->join('users_data', 'users_data.user_id = users.id');
                $this->db->join("users_cards", "users_cards.user_id = users.id", "left");

                $this->db->where('users.id NOT IN (SELECT t.user_id FROM users_tokens AS t WHERE t.type = "invitation")');
                $this->db->where_in('users_groups.group_id', $group->id);

                $result = $this->db->get()->result_array();
                $return->{$group->description} = $result;
        }

        return $return;
    }

    /**
     * Get all users
     */
    public function getAllUsers($group = [], $status = 1, $s2 = false, $front=false){

        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $this->db->select('users.id, users.active, users.email, users.created_by, users_groups.group_id,
                        users_data.first_name, users_data.last_name, users_data.phone, users_data.birth_date,
                        media.file photo_src, media.meta_tags photo_meta,
                        concat(users_data.last_name,\' \',users_data.first_name) as full_name');
        $this->db->from('users');
        $this->db->join('users_groups', 'users_groups.user_id = users.id');
        $this->db->join('users_data', 'users_data.user_id = users.id');
        $this->db->join('media','users_data.photo = media.id','LEFT');

        $this->db->where('users.id NOT IN (SELECT t.user_id FROM users_tokens AS t WHERE t.type = "invitation")'); // exclude invites
        $this->db->where('users.active', $status); // exclude/include "banned" users

        if(!empty($group)){
            $this->db->where_in('users_groups.group_id', $group);
            if(in_array(PERSONAL_TRAINER, $group) or in_array(MASTER_TRAINER, $group) or in_array(INSTRUCTOR, $group)) {
                $this->db->select('coach_data.about, coach_data.quote, coach_data.visible');
                $this->db->join('coach_data','coach_data.coach_id = users.id','LEFT');
            }
            if(in_array(CLIENT, $group) or in_array(DISPOSABLE, $group)) {
                $this->db->select('clients_data.multisport_id');
                $this->db->join('clients_data','clients_data.client_id = users.id','LEFT');

                $this->db->select('membership.id as membership_id, membership.name as membership'); // membership
                $this->db->join('membership','clients_data.membership_id = membership.id','LEFT');
            }
        }

        // 3 random coaches on homepage
        if($front) $this->db->order_by('RAND()')->limit(3);

        if($sorter){
            foreach($sorter as $s){
                $order_field = $s['field'];
                $direction = $s['dir'];
                switch($s['field']){
                    case 'role': case 'group_id': $this->db->order_by('users_groups.group_id', $direction); break;     
                    case 'email': $this->db->order_by('users.email', $direction); break;     
                    case 'full_name': $this->db->order_by('users_data.last_name', $direction)->order_by('users_data.first_name', $direction); break;                                             
                    case 'phone': $this->db->order_by('users_data.phone', $direction); break;  
                    case 'birth_date': $this->db->order_by('users_data.birth_date', $direction); break;  
                    case 'multisport_bool': $this->db->order_by('clients_data.multisport_id', $direction); break;
                    default: $this->db->order_by("users.$order_field", $firection); break;
                }  
            }
        } else $this->db->order_by('users_data.last_name'); // DEFAULT SORT

        if($filter){
            foreach($filter as $f){
                $fieldname = $f["field"];
                switch($f['field']){
                    case 'role': case 'group_id': $this->db->where('users_groups.group_id', (int)$f['value']); break; 
                    case 'full_name': $this->db->like('concat(users_data.first_name,\' \',users_data.last_name)',$f['value']); break; 
                    case 'email': $this->db->like('users.email',$f['value']); break; 
                    case 'phone': $this->db->like('users_data.phone', $f['value']); break;
                    case 'birth_date': $this->db->like('users_data.birth_date', $f['value']); break;  
                    case 'membership': $this->db->like('membership.id', $f['value']); break; 
                    case 'multisport_bool': filter_var($f['value'], FILTER_VALIDATE_BOOLEAN) ? $this->db->where('clients_data.multisport_id is not null') : $this->db->where('clients_data.multisport_id is null'); break;  
                    default: $this->db->like("users.$fieldname", $f['value']); break;  
                }                     
            }
        }

        $countRes = $this->db->count_all_results(null,FALSE);

        if($page != NULL && $limit != NULL){
            $offset = 0;
            if($page > 1){
                $offset = ($page - 1) * $limit;
            }
            $this->db->limit($limit, $offset);
        }

        if($s2){
            $result = $this->db->get()->result_array();
            return $result;
        }else{
            $result = $this->db->get()->result();
            $reply["data"] = $result;
            if($limit != NULL) $reply['last_page'] = ceil( $countRes / $limit );
            return $reply;
        }
    }

    public function searchClients($term){

        $g = $_GET;
        $reply = [];

        $this->db->select('users.id, users.active, users_groups.group_id, users_data.first_name, users_data.last_name, concat(users_data.first_name,\' \',users_data.last_name) as full_name');
        $this->db->from('users');
        $this->db->join('users_groups', 'users_groups.user_id = users.id');
        $this->db->join('users_data', 'users_data.user_id = users.id');

        $this->db->where('users.id NOT IN (SELECT t.user_id FROM users_tokens AS t WHERE t.type = "invitation")'); // exclude invites
        $this->db->where_in('users_groups.group_id', CLIENT);

        $this->db->like('users_data.first_name', $term)->or_like('users_data.last_name', $term);

        $result = $this->db->get()->result();
        if($result){
            return $result;
        }else{
            return FALSE;
        }
    }

    public function searchEmployees($term){

        $g = $_GET;
        $reply = [];

        $this->db->select('users.id, users.active, users_groups.group_id, users_data.first_name, users_data.last_name, concat(users_data.first_name,\' \',users_data.last_name) as full_name');
        $this->db->from('users');
        $this->db->join('users_groups', 'users_groups.user_id = users.id');
        $this->db->join('users_data', 'users_data.user_id = users.id');

        $this->db->where('users.id NOT IN (SELECT t.user_id FROM users_tokens AS t WHERE t.type = "invitation")'); // exclude invites
        $this->db->where_in('users_groups.group_id', [1,2,3,4]); // @todo roles who is employee ?

        $this->db->like('users_data.first_name', $term)->or_like('users_data.last_name', $term);

        $result = $this->db->get()->result();
        if($result){
            return $result;
        }else{
            return FALSE;
        }
    }

    /**
     * Get all invites
     * aka pending user accounts
     */
    public function getAllInvites(){
        $this->db->select('users.id, users.active, users.email, users_data.first_name, users_data.last_name, users_groups.group_id, users.date_created')->from('users');
        $this->db->join('users_groups', 'users_groups.user_id = users.id', 'left');
        $this->db->join('users_data', 'users_data.user_id = users.id', 'left');

        $this->db->where('users.id IN (SELECT t.user_id FROM users_tokens AS t WHERE t.type = "invitation")');

        $result = $this->db->get()->result();
        return $result;
    }
}
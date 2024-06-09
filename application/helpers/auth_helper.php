<?php

/**
 * AUTH HELPER
 * This helper "overrides" ion_auth,
 * in other words it puts a thin layer between ion_auth and frontend via SESSION
 * it holds information like user_id, group and if the account is currently faking to be someone else
 * this is the main part of "view as" functionality
*/

// setLoginSession(user_id, group_id)
// clal this after succesful login, sets up the basic SESS vars
if( ! function_exists("setLoginSession") ){
    function setLoginSession($user, $user_group){
        // CI Instance
        $CI =& get_instance();

        // Set some sess
        $CI->session->set_userdata('gym_user_id', $user);
        $CI->session->set_userdata('gym_group', $user_group);
        $CI->session->set_userdata('fake_account', FALSE);

        // DB stuff
        $CI->session->set_userdata('gym_db', config_item('api')['default_gym_db']);
        $CI->session->set_userdata('gym_name', config_item('api')['default_gym_name']);
        $CI->session->set_userdata('gym_id', config_item('api')['default_gym_id']);
    }
}

// gym_name()
// returns current users name
if( ! function_exists("gym_users_name") ){
    function gym_users_name(){
        // CI Instance
        $CI =& get_instance();
        $CI->load->database();

        $user_object = $CI->ion_auth->user()->row();
        $user_data = $CI->db->where('user_id', $user_object->id)->get('users_data')->row();
        return $user_data->first_name. ' ' .$user_data->last_name;
    }
}

// gym_group()
// returns current group id
if( ! function_exists("gym_group") ){
    function gym_group(){
        // CI Instance
        $CI =& get_instance();
        return $CI->session->userdata('gym_group'); // return users group
    }
}

// gym_in_group(group_id)
// check if a user is in a particular group
// also accepts arrays of group ids
if( ! function_exists("gym_in_group") ){
    function gym_in_group($g){
        $CI =& get_instance();

        if(is_array($g)){
            return in_array($CI->session->userdata('gym_group'), $g); // return bool
        }else{
            return $CI->session->userdata('gym_group') == $g; // return bool
        }
    }
}

// gym_is_fake()
// check if a user is faking their current account
if( ! function_exists("gym_is_fake") ){
    function gym_is_fake(){
        $CI =& get_instance();
        return $CI->session->userdata('fake_account'); // return bool
    }
}

// gym_userid()
// returns current user id
if( ! function_exists("gym_userid") ){
    function gym_userid(){
        // CI Instance
        $CI =& get_instance();
        return $CI->session->userdata('gym_user_id'); // return users id
    }
}

// gym_fake_account(account_id, account_group_id)
// accepts the id and group_id of the account you want to fake and starts the faking
if( ! function_exists("gym_fake_account") ){
    function gym_fake_account($id, $group_id){
        // CI Instance
        $CI =& get_instance();

        // Restore original sess
        $CI->session->set_userdata('fake_account', TRUE);
        $CI->session->set_userdata('gym_user_id', $id);
        $CI->session->set_userdata('gym_group', $group_id);

        return TRUE;
    }
}

// gym_restore_account()
// restore current account to its original state (via ion_auth again)
if( ! function_exists("gym_restore_account") ){
    function gym_restore_account(){
        // CI Instance
        $CI =& get_instance();

        // Get fallback from ion_auth
        $user_object = $CI->ion_auth->user()->row();
        $user_group = $CI->ion_auth->get_users_groups()->result();

        // Restore original sess
        $CI->session->set_userdata('gym_user_id', $user_object->id);
        $CI->session->set_userdata('gym_group', $user_group[0]->id);
        $CI->session->set_userdata('fake_account', FALSE);

        return TRUE;
    }
}
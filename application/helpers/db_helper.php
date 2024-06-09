<?php

/**
 * DB HELPER
 * Helps with basic DB tasks since there are multiple DB's in this project
 * 
*/

if( ! function_exists('current_gym_db')){
    function current_gym_db(){
        $CI =& get_instance();
        if (isset($_SESSION['gym_db'])) return $CI->session->userdata('gym_db');
        else return config_item('api')['default_gym_db'];
    }
}

if( ! function_exists('current_gym_id')){
    function current_gym_id(){
        $CI =& get_instance();
        if (isset($_SESSION['gym_id'])) return $CI->session->userdata('gym_id');
        else return config_item('api')['default_gym_id'];
    }
}

if( ! function_exists('current_gym_name')){
    function current_gym_name(){
        $CI =& get_instance();
        if (isset($_SESSION['gym_name'])) return $CI->session->userdata('gym_name');
        else return config_item('api')['default_gym_name'];
    }
}

if( ! function_exists("current_gym_code")){
    function current_gym_code(){
        $CI =& get_instance();
        if (isset($_SESSION['gym_db'])) return (strlen(explode('gymit', $CI->session->gym_db)[1]) > 0) ? (string) '0' .explode('gymit', $CI->session->gym_db)[1] : (string) '0'.'1';
        else return (strlen(explode('gymit', config_item('api')['default_gym_db'])[1]) > 0) ? (string) '0' .explode('gymit', config_item('api')['default_gym_db'])[1] : (string) '0'.'1';
    }
}

/**
 * Return current db name
 */
if( ! function_exists('get_db')){
    function get_db(){
        if(current_gym_db() != config_item('api')['default_gym_db']){
            return current_gym_db();
        }else{
            return config_item('api')['default_gym_db'];
        }
    }
}

if( ! function_exists('switch_gym_db')){
    function switch_gym_db($db_name, $db_id, $gym_name){
        $CI =& get_instance();
        $CI->session->set_userdata('gym_db', $db_name);
        $CI->session->set_userdata('gym_name', $gym_name);
        $CI->session->set_userdata('gym_id', $db_id);
        return TRUE;
    }
}

// gym_restore_primary_db()
// restore to primary DB
if( ! function_exists("gym_restore_primary_db") ){
    function gym_restore_primary_db(){
        // CI Instance
        $CI =& get_instance();

        // Restore original sess
        $CI->session->set_userdata('gym_db', config_item('api')['default_gym_db']);
        $CI->session->set_userdata('gym_name', config_item('api')['default_gym_name']);
        $CI->session->set_userdata('gym_id', config_item('api')['default_gym_id']);

        return TRUE;
    }
}
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_model extends CI_Model
{
    public function __construct(){
        $this->gymdb->init(current_gym_db());
    }

}
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Settings_model extends CI_Model
{
    public function __construct(){}

    public function getAllCountries(){
        $this->gymdb->init(get_db());
        return $this->db->select('iso, name')->order_by('name','asc')->get('countries')->result_array();
    }

    public function getAllLessons(){
        $this->gymdb->init(get_db());
        return $this->db->select('lessons.id, lessons_templates.name, DATE(lessons.starting_on) as lesson_date')->order_by('name','asc')->from('lessons')->join('lessons_templates', 'lessons_templates.id = lessons.template_id')->get()->result_array();
    }    

    public function getAllLessonsTemplates(){
        $this->gymdb->init(get_db());
        return $this->db->select('id, name, duration, client_limit')->order_by('name','asc')->get('lessons_templates')->result_array();
    }

}
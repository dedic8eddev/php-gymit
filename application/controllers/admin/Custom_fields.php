<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Custom_fields extends Backend_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('fields_model', 'fields');
    }

    public function sectionName(): string
    {
        return SECTION_CUSTOM_FIELDS;
    }

    public function index()
	{
	    $this->checkReadPermission();

        $data['pageTitle'] = 'Vlastní pole';

        $data['fieldsUrl'] = base_url('admin/custom-fields/get-fields-ajax');

        $this->app->assets(['tabulator.min.js', 'flatpickr.js', 'flatpickr.cs.js', 'admin.custom_fields.main.js'], 'js');
        $this->app->assets(['tabulator.min.css', 'flatpickr.css'], 'css');
		
		$this->load->view('layout/header', $data);
		$this->load->view('layout/menu', $data);
		$this->load->view('admin/custom_fields/index', $data);
		$this->load->view('layout/footer');
    }

    public function add_field_ajax(){
        $this->checkCreatePermission(true);
        if($this->fields->addField()) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);
    }

    public function save_field_ajax(){
        $this->checkEditPermission(true);
        if($this->fields->saveField()) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);
    }

    public function delete_field_ajax(){
        $this->checkDeletePermission(true);
        if($this->fields->deleteField()) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);
    }

    public function get_field_ajax()
    {
        $field = $this->fields->getField();
        echo json_encode($field);
    }

    public function get_fields_ajax()
    {
        $fields = $this->fields->getAllFields();
        echo json_encode($fields);
    }
}

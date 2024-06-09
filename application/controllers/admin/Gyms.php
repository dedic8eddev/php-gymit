<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gyms extends Backend_Controller {

    public function __construct(){
        parent::__construct();
    }

    public function sectionName(): string
    {
        return SECTION_GYMS;
    }
    
	public function index()
	{
	    $this->checkReadPermission();

        $data['pageTitle'] = 'Provozovny';
        
        $data['gymsUrl'] = base_url('admin/gyms/get_gyms_ajax');
        $data['addUrl'] = base_url('admin/gyms/add_gym_ajax');

        $this->app->assets(['tabulator.min.js', 'flatpickr.js', 'flatpickr.cs.js', 'admin.gyms.main.js'], 'js');
        $this->app->assets(['tabulator.min.css', 'flatpickr.css'], 'css');
		
		$this->load->view('layout/header', $data);
		$this->load->view('layout/menu', $data);
		$this->load->view('admin/gyms/index', $data);
		$this->load->view('layout/footer');
    }

    public function settings($id){
        $this->checkEditPermission();
        $this->gyms->init($id);

        $data['pageTitle'] = 'Nastavení provozovny';

        $data['gym'] = $this->gyms->getGymById($id);

        $data['roomsUrl'] = base_url('admin/gyms/get_rooms_ajax/' . $id);
        $data['roomSubmit'] = base_url('admin/gyms/add_room_ajax/' . $id);

        $data['depotsUrl'] = base_url('admin/gyms/get_depots_ajax/' . $id);
        $data['depotSubmit'] = base_url('admin/gyms/add_depot_ajax/' . $id);

        $data['solariumsUrl'] = base_url('admin/gyms/get_solariums_ajax/' . $id);

        $this->app->assets(['tabulator.min.js', 'flatpickr.js', 'flatpickr.cs.js', 'trumbowyg/trumbowyg.min.js', 'trumbowyg/trumbowyg.cs.js', 'admin._trumbowyg.js', 'admin.gyms.settings.js'], 'js');
        $this->app->assets(['tabulator.min.css', 'flatpickr.css', 'trumbowyg/trumbowyg.min.css'], 'css');
		
		$this->load->view('layout/header', $data);
		$this->load->view('layout/menu', $data);
		$this->load->view('admin/gyms/settings', $data);
		$this->load->view('layout/footer');
    }  

    // GYMS
    
    public function get_gyms_ajax(){
        $this->checkReadPermission(true);
        echo json_encode($this->gyms->getAllGyms());
    }

    public function add_gym_ajax(){
        $this->checkCreatePermission(true);
        echo json_encode($this->gyms->createNewGym());
    }

    public function delete_gym_ajax(){
        $this->checkDeletePermission(true);
        $id = $_POST['id'];
        echo json_encode($this->gyms->removeGym($id));
    }

    // DEPOTS
    public function add_depot_ajax($gym_id){
        $this->checkCreatePermission(true, SECTION_DEPOT);
        if($this->gyms->addGymDepot($gym_id)) echo json_encode(["success" => "true"]);
        else echo json_encode(["error" => "true"]);
    }
    public function get_depots_ajax($gym_id){
        $this->checkReadPermission(true, SECTION_DEPOT);
        if($depots = $this->gyms->getGymDepots($gym_id)) echo json_encode($depots);
        else echo json_encode([]);
    }
    public function edit_depot_ajax($depot_id, $gym_id){
        $this->checkEditPermission(true, SECTION_DEPOT);
        if($this->gyms->editGymDepot($depot_id, $gym_id)) echo json_encode(["success" => "true"]);
        else echo json_encode(["error" => "true"]);
    }
    public function remove_depot_ajax($depot_id, $gym_id){
        $this->checkDeletePermission(true, SECTION_DEPOT);
        if($this->gyms->removeGymDepot($depot_id, $gym_id)) echo json_encode(["success" => "true"]);
        else echo json_encode(["error" => "true"]);
    }

    // ROOMS
    public function get_rooms_ajax($gym_id){
        if($rooms = $this->gyms->getGymRooms($gym_id)) echo json_encode($rooms);
        else echo json_encode([]);
    }
    public function add_room_ajax($gym_id){
        if($this->gyms->addGymRoom($gym_id)) echo json_encode(["success" => "true"]);
        else echo json_encode(["error" => "true"]);
    }
    public function edit_room_ajax($room_id, $gym_id){
        if($this->gyms->editGymRoom($room_id, $gym_id)) echo json_encode(["success" => "true"]);
        else echo json_encode(["error" => "true"]);
    }
    public function remove_room_ajax($room_id, $gym_id){
        if($this->gyms->removeGymRoom($room_id, $gym_id)) echo json_encode(["success" => "true"]);
        else echo json_encode(["error" => "true"]);
    }

    // SOLARIUMS
    public function get_solariums_ajax($gym_id){
        if($solariums = $this->gyms->getGymSolariums($gym_id)) echo json_encode($solariums);
        else echo json_encode([]);
    }
    public function edit_solarium_ajax($solarium_id, $gym_id){
        if($this->gyms->editGymSolarium($solarium_id, $gym_id)) echo json_encode(["success" => "true"]);
        else echo json_encode(["error" => "true"]);
    }
    public function add_solarium_maintenance_ajax($solarium_id, $gym_id){
        if($this->gyms->addGymSolariumMaintenance($solarium_id, $gym_id)) echo json_encode(["success" => "true"]);
        else echo json_encode(["error" => "true"]);
    }
    public function get_solarium_logs($solarium_id, $gym_id){
        $this->gyms->init($gym_id);
        
        $data['pageTitle'] = 'Logy solárií';
        $data['usageUrl'] = base_url('admin/gyms/get_solarium_usage/' . $gym_id);
        $data['maintenanceUrl'] = base_url('admin/gyms/get_solarium_maintenance/' . $gym_id);

        $this->app->assets(['tabulator.min.js', 'flatpickr.js', 'flatpickr.cs.js', 'admin.gyms.solariumLogs.js'], 'js');
        $this->app->assets(['tabulator.min.css', 'flatpickr.css'], 'css');
		
		$this->load->view('layout/header', $data);
        $this->load->view('layout/menu', $data);
        $this->load->view('admin/gyms/solarium_logs', $data);
        $this->load->view('layout/footer');
    }
    public function get_solarium_usage($gym_id){
        if($usage = $this->gyms->getGymSolariumsUsage($gym_id)) echo json_encode($usage);
        else echo json_encode([]);
    }
    public function get_solarium_maintenance($gym_id){
        if($maintenance = $this->gyms->getGymSolariumsMaintenance($gym_id)) echo json_encode($maintenance);
        else echo json_encode([]);
    }

    // EQUIPMENT
    public function equipment(){
        $data['equipmentUrl'] = base_url('admin/gyms/get_equipment_ajax');
        $this->load->view('admin/gyms/equipment', $data);
    }

	public function save_equipment_ajax(){
        $this->checkEditPermission(true);
        if($this->gyms->saveEquipment($this->input->post())) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);
	}	

	public function delete_equipment_ajax(){
        $this->checkDeletePermission(true);
        if($this->gyms->deleteEquipment($this->input->post())) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);
    }
    
    public function get_equipment_ajax(){
        echo json_encode($this->gyms->getAllequipment());
    }     

}

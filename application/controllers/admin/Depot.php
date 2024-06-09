<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Depot extends Backend_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('depot_model', 'model');
        $this->load->model('fields_model', 'fields');
        $this->load->model('pricelist_model', 'pricelist');
	}

    public function sectionName(): string
    {
        return SECTION_DEPOT;
	}

    public function index(){
        $this->checkReadPermission();

        $data['pageTitle'] = 'Sklad';

        $data['depotItems'] = base_url('admin/depot/get-all-depot-items-ajax');
        $data['inventoryUrl'] = base_url('admin/depot/get-inventory-ajax');
        $data['invoicesUrl'] = base_url('admin/depot/get-invoices-ajax');
        $data['statsUrl'] = base_url('admin/depot/get-stats-ajax');
        $data['addItem'] = site_url('admin/depot/add_item_ajax/');
        
        $data['custom_fields'] = $this->fields->getSectionCustomFields('depot');

        $this->app->assets(['tabulator.min.js', 'flatpickr.js', 'flatpickr.cs.js', 'xls_sheet.js', 'admin.depot.main.js'], 'js');
        $this->app->assets(['tabulator.min.css', 'flatpickr.css'], 'css');
		
		$this->load->view('layout/header', $data);
		$this->load->view('layout/menu', $data);
		$this->load->view('admin/depot/index', $data);
		$this->load->view('layout/footer');
    }

    public function get_inventory_ajax(){
        if($data = $this->model->getInventory()){
            echo json_encode($data);
        }else{
            echo json_encode([]);
        }
    }
    public function get_invoices_ajax(){
        if($data = $this->model->getInvoiceHistory()){
            echo json_encode($data);
        }else{
            echo json_encode([]);
        }
    }
    public function get_stats_ajax(){
        if($data = $this->model->getStatistic()){
            echo json_encode($data);
        }else{
            echo json_encode([]);
        }
    }

    public function get_item_info_ajax(){
        $item_id = $this->input->post("item_id");
        if($data = $this->model->getDepotItem($item_id)){
            echo json_encode(["success" => "true", "data" => $data]);
        }else{
            echo json_encode(["error" => "true"]);
        }
    }

    public function get_item_info_simple_ajax(){
        $p=$_POST;
        if(isset($p['card_id'])){ // client has card
            // check if client has benefit
            $benefit = $this->pricelist->checkMembershipBenefit($p['client_id'],$p['card_id'],$p['item_id'],true);
        }  

        if($data = $this->model->getDepotItem($p['item_id'], true)){
            echo json_encode(["success" => "true", "data" => $data, "benefit" => $benefit ?? false]);
        }else{
            echo json_encode(["error" => "true"]);
        }
    }

    public function get_depot_item_stocks(){
        $item_id = $this->input->post("item_id");
        $depot_id = $this->input->post("depot_id");
        if($data = $this->model->getDepotItemStocks($item_id, $depot_id)){
            echo json_encode($data);
        }else{
            echo json_encode(["error" => "true"]);
        }
    }

    public function get_item_logs_ajax($id){

        $params = [];
        if(!empty($_GET)){
            $params['limit'] = $_GET['size'];
            $params['offset'] = $_GET['size'] * $_GET['page'];
            if(isset($_GET['filters'])){
                foreach($_GET['filters'] as $f){
                    $params[$f['field']] = $f['value'];
                }
            }
        }

        if($data = $this->model->getDepotItemLogs($id, $params)){
            echo json_encode($data);
        }else{
            echo json_encode([]);
        }
    }

    public function submit_invoice_ajax(){
        if($this->model->stockInvoiceItems()){
            echo json_encode(['success' => 'true']);
        }else{
            echo json_encode(['error' => 'true']);
        }
    }

    public function move_product_ajax(){
        if($this->model->moveDepotItemStock()){
            echo json_encode(['success' => 'true']);
        }else{
            echo json_encode(['error' => 'true']);
        }
    }

    public function take_product_ajax(){
        if($this->model->takeDepotItemStock()){
            echo json_encode(['success' => 'true']);
        }else{
            echo json_encode(['error' => 'true']);
        }
    }

    public function edit_item_ajax(int $id)
    {
        $this->checkEditPermission(true);
        $data = $this->input->post();
        if($this->model->edit($id,$data)) {
            echo json_encode(["success" => "true"]);
        } else {
            echo json_encode(["error" => "true"]);
        }
    }

    public function add_item_ajax()
    {
        $this->checkCreatePermission(true);
        $data = $this->input->post();
        if($this->model->add($data)) {
            echo json_encode(["success" => "true"]);
        } else {
            echo json_encode(["error" => "true"]);
        }
    }

    public function remove_item_ajax(){
        $this->checkDeletePermission(true);
        if($this->model->removeItem($_POST['item_id'])) {
            echo json_encode(["success" => "true"]);
        } else {
            echo json_encode(["error" => "true"]);
        }
    }

    public function get_all_depot_items_ajax()
    {
        $depotItems = $this->model->getAllDepotItems();
        echo json_encode($depotItems);
    }

    public function get_all_depots_ajax(){
        $this->checkReadPermission(true);

//        $userGroups = $this->ion_auth->get_users_groups()->result();
//
//        if (count($userGroups) === 1) {
//            $group = reset($userGroups);
//            $section = null;
//            if (in_array($group->id, [RECEPTIONIST, SENIOR_RECEPTIONIST])) {
//                $section = DEPOT_RECEPTION;
//            } else if (in_array($group->id, [WELLNESS_SERVICE])) {
//                $section = DEPOT_RECEPTION;
//            }
//            $depots = $this->model->getAllDepotsBySection($section);
//        } else {
//            $depots = $this->model->getAllDepots();
//        }

        $depots = $this->model->getAllDepots();

        echo json_encode($depots);
    }

    public function get_items_from_depot(){
        $depotItems = $this->model->getDepotItemsByDepotId();
        echo json_encode($depotItems);
    }

}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pricelist extends Backend_Controller {

    /** @var Pricelist_model|null */
    public $pricelist;

    public function __construct(){
        parent::__construct();
        $this->load->model('pricelist_model', 'pricelist');
    }

    public function sectionName(): string
    {
        return SECTION_PRICE_LIST;
    }


    // PRICE LIST

    public function index(){
        if (! $this->permissions->hasReadPermissionAtLeastInOneSection([SECTION_PRICE_LIST, SECTION_MEMBERSHIP])) {
            self::dontHavePermissionMessage();
        }

        $data['pageTitle'] = 'Členství a ceník služeb';

        $data['prices']['savePriceUrl'] = base_url('admin/pricelist/save_price_ajax');
        $data['prices']['pricesUrl'] = base_url('admin/pricelist/get_price_list');
        $data['prices']['membershipsUrl'] = base_url('admin/pricelist/get_memberships');  
        $data['prices']['membershipsOverviewsUrl'] = base_url('admin/pricelist/get_memberships_overviews');

        $this->app->assets(['tabulator.min.js', 'flatpickr.js', 'flatpickr.cs.js', 'admin._trumbowyg.js', 'admin.pricelist.main.js'], 'js');
        $this->app->assets(['tabulator.min.css', 'flatpickr.css', 'admin.pricelist.main.css'], 'css');
        
        $this->load->view('layout/header', $data);
        $this->load->view('layout/menu', $data);
        $this->load->view('admin/pricelist/index', $data);
        $this->load->view('layout/footer');                
    }

    public function add_price(){
        $data['saveUrl'] = base_url('admin/pricelist/save_price_ajax');   
        $this->load->view('admin/pricelist/price', $data);
    }    

    public function edit_price($id){
        $data['saveUrl'] = base_url('admin/pricelist/save_price_ajax');   
        $data['price'] = $this->pricelist->getPrice($id);
        $this->load->view('admin/pricelist/price', $data);
    }  

    public function get_price_list(){
        echo json_encode($this->pricelist->getAllPrices());
    }

    public function save_price_ajax(){
        $this->checkEditPermission(true);
        $data = $this->input->post();
        if($this->pricelist->savePrice($data)) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);  
    }

    public function remove_price_ajax(){
        $this->checkDeletePermission(true);
        $id = $this->input->post('price_id');
        if($this->pricelist->removePrice($id)) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);
    }  
    
    public function get_checkout_item_info_ajax(){
        $p=$_POST;
        if(isset($p['card_id'])){ // client has card

            // check if client has benefit
            $benefit = $this->pricelist->checkMembershipBenefit($p['client_id'],$p['card_id'],$p['item_id'],false);

            $membership = $this->API->subscriptions->get_subscription($p['client_id'], current_gym_code()); // users sub
            if(isset($membership->data->active) && $membership->data->active==1){ // client has active membership
                $data = $this->pricelist->getMembershipServicePrice($p['item_id'],$membership->data->subType);
            } else {// client has not active membership
                $data = $this->pricelist->getPrice($p['item_id']);
            }
        } else { // client has not card
            $data = $this->pricelist->getPrice($p['item_id']);
        }
        echo json_encode(['success' => 'true', 'data' => $data, 'benefit' => $benefit ?? false]);
    }

    public function search_pricelist_items_ajax(){ // including depot items
        $term = $this->input->get('term');
        $prices = $this->pricelist->searchItems($term);
        
        if($prices) echo json_encode($prices);
        else echo json_encode([]);
    }

    // MEMBERSHIP
    public function get_memberships_overviews(){
        $this->checkReadPermission(true, SECTION_MEMBERSHIP);
        echo json_encode($this->pricelist->getAllMembershipOverviews());
    }

    // MEMBERSHIP
    public function get_memberships(){
        $this->checkReadPermission(true, SECTION_MEMBERSHIP);
        echo json_encode($this->pricelist->getAllMemberships());
    }

    public function get_membership_price_info(){
        $id = $this->input->post('item_id');
        echo json_encode(['success' => 'true', 'data' => $this->pricelist->getMembershipPrice($id)]); 
    }

    public function edit_membership($id){
        $data['saveUrl'] = base_url('admin/pricelist/save_membership_ajax');   
        $data['servicesPricesUrl'] = base_url("admin/pricelist/get_membership_services_prices_ajax/$id");  
        $data['membership'] = $this->pricelist->getMembership($id);
        $data['membership_benefits'] = $this->pricelist->getMembershipBenefits($id);
        $this->load->view('admin/pricelist/membership', $data);
    }

    public function save_membership_ajax(){
        if($this->pricelist->saveMembership($this->input->post())) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);  
    } 

    // MEMBERSHIP SERVICE PRICES
    public function get_membership_services_prices_ajax($membership_id){
        echo json_encode($this->pricelist->getMembershipServicesPrices($membership_id));
    }

    public function save_membership_service_price_ajax(){
        if($this->pricelist->saveMembershipServicePrice($this->input->post())) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);  
    }

    // MEMBERSHIP BENEFITS
    public function get_membership_benefits(){
        $mb = $this->pricelist->getMembershipBenefits();
        print_r($mb);
    }
}
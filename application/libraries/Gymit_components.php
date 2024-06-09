<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Gymit_Components{
    
    protected $ci;

    public function __construct(){
        $this->ci =& get_instance();
    }
    
    /**
     * Načtení custom fields
     */    
    public function getCustomFields( $custom_fields, $options = [], $values = FALSE ) : void
    {
        $data['options'] = $options;
        $data['custom_fields'] = $custom_fields;
        $data['values'] = $values;
        $this->ci->load->view('components/custom_fields_render',$data);
    }

    /**
     * Načtení custom fields
     */    
    public function getSelect2LessonsTemplates( array $options ) : void
    {
        $data['options'] = $options;
        $data['data'] = $this->ci->settings->getAllLessonsTemplates();
        $this->ci->load->view('components/select2_lessons_templates',$data);
    }

    /**
     * S2 pro pricelist items, $type => druh položky (lekce, apod.) $service_type
     */    
    public function getSelect2PricelistItems( $type, array $options ) : void
    {
        $this->ci->load->model('pricelist_model', 'pricelist');
        $data['options'] = $options;
        $data['data'] = $this->ci->pricelist->getAllPrices(TRUE, ["filters" => [0 => ["field" => 'service_type', "value" => $type]]]);
        $this->ci->load->view('components/select2_pricelist_items',$data);
    }

    /**
     * Select2 pro personifikátory (čtečky pro čtení jen id kartiček)
     */
    public function getSelectPersonificators( array $options ){
        $data['options'] = $options;
        $data['data'] = $this->ci->gyms->getAllPersonificators();
        $this->ci->load->view('components/select_personificators',$data);
    }

    /**
     * Načtení selectu 2 se zeměmi
     */    
    public function getSelect2Country( array $options ) : void
    {
        $data['options'] = $options;
        $data['data'] = $this->ci->settings->getAllCountries();
        $this->ci->load->view('components/select2_countries',$data);
    }

    /**
     * Načtení selectu 2 se šablonami lekcí
     */    
    public function getSelect2Lessons( array $options ) : void
    {
        $data['options'] = $options;
        $data['data'] = $this->ci->settings->getAllLessonsTemplates();
        $this->ci->load->view('components/select2_lessons',$data);
    }    

    /**
     * Načtení selectu 2 s klienty
     */    
    public function getSelect2Clients( array $options ) : void
    {
        $data['options'] = $options;
        $this->ci->load->model('users_model', 'users');
        $data['data'] = $this->ci->users->getAllUsers([CLIENT], 1, true);
        $this->ci->load->view('components/select2_clients',$data);
    }

    /**
     * Načtení selectu 2 s autocont účty
     */    
    public function getSelect2AutocontAccounts( array $options ) : void
    {
        $data['options'] = $options;
        $data['data'] = config_item("app")["autocont_accounts"];
        $this->ci->load->view('components/select2_autocont_accounts',$data);
    }

    /**
     * Načtení selectu 2 s uživateli, podle role
     */    
    public function getSelect2Users( array $options ) : void
    {
        $data['options'] = $options;
        $this->ci->load->model('users_model', 'users');

        $data['data'] = $this->ci->users->getUsersInGroups();

        $this->ci->load->view('components/select2_users',$data);
    }

    /**
     * Načtení s2 pro skupiny
     */
    public function getSelect2Groups( array $options ) : void
    {
        $data['options'] = $options;
        $this->ci->load->model('users_model', 'users');

        $data['data'] = $this->ci->ion_auth->groups()->result();

        $this->ci->load->view('components/select2_groups',$data);
    }

    /**
     * Načtení selectu 2 s místnostmi
     */    
    public function getSelect2Rooms( array $options ) : void
    {
        $data['options'] = $options;
        $this->ci->load->model('gyms_model', 'gyms');
        $data['data'] = $this->ci->gyms->getGymRooms(current_gym_id(), false, true, true);
        $this->ci->load->view('components/select2_rooms',$data);
    }

    /**
     * Načtení selectu 2 s depot_items
     */    
    public function getSelect2DepotItems( array $options ) : void
    {
        $data['options'] = $options;
        $this->ci->load->model('depot_model', 'depot');
        $data['data'] = $this->ci->depot->getAllDepotItems(true);
        $this->ci->load->view('components/select2_depot_items',$data);
    }

    /**
     * Select2 pro typ skladu
     * */
    public function getSelect2Depots( array $options ) : void
    {
        $data['options'] = $options;
        $this->ci->load->model('gyms_model', 'gyms');
        $data['data'] = $this->ci->gyms->getGymDepots(current_gym_id(), true);
        $this->ci->load->view('components/select2_depots',$data);
    }

    /**
     * Načtení selectu 2 s trenéry/instruktory
     */    
    public function getSelect2Teachers( array $options ) : void
    {
        $data['options'] = $options;
        $this->ci->load->model('users_model', 'users');
        $data['data'] = $this->ci->users->getAllUsers([PERSONAL_TRAINER, MASTER_TRAINER, INSTRUCTOR], 1, true);
        $this->ci->load->view('components/select2_teachers',$data);
    }

    /**
     * Načtení selectu 2 s gyms
     */    
    public function getSelect2Gyms( array $options ) : void
    {
        $data['options'] = $options;
        $this->ci->load->model('gyms_model', 'gyms');
        $data['data'] = $this->ci->gyms->getAllGyms();

        $this->ci->load->view('components/select2_gyms',$data);
    }    

    /**
     * Načtení selectu 2 s identifikačními typy
     */    
    public function getSelect2IdentificationTypes( array $options ) : void
    {
        $data['options'] = $options;
        $data['data'] = config_item('app')['identification_types'];
        $this->ci->load->view('components/select2_identification_types',$data);
    }

    public function getPagination( array $results ) : void
    {
        $this->ci->load->view('components/pagination', [
            'page'      => $results['page'],
            'lastPage'  => $results['last_page'],
        ]);
    }

    public function getEventList(array $events) : void
    {
        $this->ci->load->view('components/event_list', [
            'events' => $events,
        ]);
    }
}
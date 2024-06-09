<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cms extends Backend_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('cms_model', 'cms');
        $this->load->model('pricelist_model', 'pricelist');
    }

    public function sectionName(): string
    {
        return SECTION_CMS;
    }

    // CONTACT

    public function contact(){
        $this->checkReadPermission();

        $data['pageTitle'] = 'Kontaktní údaje';

        $data['saveOHUrl'] = base_url('admin/cms/save_opening_hours_ajax');
        $data['saveGeneralInfoUrl'] = base_url('admin/cms/save_general_info_ajax');   

        foreach ($this->gyms->getGymSettings(['opening_hours','general_info']) as $k => $v){
            $data[$v['type']]['id']=$v['id'];
            $data[$v['type']]['data']=json_decode($v['data'],true);
        }  
        
        $this->app->assets(['flatpickr.js', 'flatpickr.cs.js', 'admin.cms.contact.js'], 'js');
        $this->app->assets(['flatpickr.css'], 'css');
		
		$this->load->view('layout/header', $data);
		$this->load->view('layout/menu', $data);
		$this->load->view('admin/cms/contact', $data);
		$this->load->view('layout/footer');        
    }

    public function save_opening_hours_ajax(){
        $data = $this->input->post();
        if($this->gyms->saveGymSettings($data,'opening_hours')) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);
    }    

    public function save_general_info_ajax(){
        $data = $this->input->post();
        if($this->gyms->saveGymSettings($data,'general_info')) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);
    }      

    // PAGES

	public function pages(){
        $this->checkReadPermission();

        $data['pageTitle'] = 'Správa stránek';

        $data['pagesUrl'] = base_url('admin/cms/get_pages_ajax');  

        $this->app->assets(['tabulator.min.js', 'admin._trumbowyg.js', 'admin.cms.pages.js'], 'js');
        $this->app->assets(['tabulator.min.css','admin.blocks.main.css'], 'css');

		$this->load->view('layout/header', $data);
        $this->load->view('layout/menu', $data);
        $this->load->view('admin/cms/pages/index', $data);
		$this->load->view('layout/footer');
    } 
    
    public function edit_page($type){
        $this->checkEditPermission();

        $pageSettings = is_array(@$_GET['blocks']) ? $_GET['blocks'] : [];
        array_push($pageSettings,$type); // page type + blocks
        $data['saveUrl'] = base_url('admin/cms/save_page_ajax');
        foreach ($this->gyms->getGymSettings($pageSettings) as $k => $v){ 
            $data[$v['type']]=json_decode($v['data'],true);
            $data[$v['type']]['id']=$v['id'];
        }   
        if(in_array($type,['page_exercise_zones','page_wellness'])){
            $data['equipment'] = array_map(function($row){
                return $row['equipment_id'];
            }, $this->gyms->getGymEquipment(preg_replace('/^page_/','',$type)));
        }   

        if(in_array('block_membership',$pageSettings)){
            $data['membership_prices']=$this->pricelist->getMembershipPrices4HP();
        }

        $this->load->view('admin/cms/pages/'.preg_replace('/^page_/','',$type), $data);
    }  
    
    public function get_pages_ajax(){
        $pages = $this->cms->getAllPages();
        echo json_encode($pages);
    }    

    public function save_page_ajax(){
        $this->checkEditPermission(true);
        $data = $this->input->post();
        foreach($data as $k=>$v){
            // Equipment
            if(in_array($k,['page_exercise_zones', 'page_wellness']) && !$this->gyms->saveGymEquipment(preg_replace('/^page_/','',$k))){
                echo json_encode(['error' => 'true']);
                exit;                
            } 

            if(!$this->gyms->saveGymSettings($v)){
                echo json_encode(['error' => 'true']);
                exit;
            }
        }
        echo json_encode(['success' => 'true']);
    }    
    

    // GYM JOBS

	public function jobs(){
        $this->checkReadPermission();
        $data = array();

        $data['pageTitle'] = 'Správa pracovních pozic';

        $data['gymJobsUrl'] = base_url('admin/cms/get_gym_jobs');
        $data['addUrl'] = base_url('admin/cms/add_job_ajax');

        $this->app->assets(['tabulator.min.js', 'admin._trumbowyg.js', 'admin.cms.jobs.js'], 'js');
        $this->app->assets(['tabulator.min.css', 'admin.blocks.main.css'], 'css');

		$this->load->view('layout/header', $data);
        $this->load->view('layout/menu', $data);
        $this->load->view('admin/cms/gym_jobs', $data);
		$this->load->view('layout/footer');
    }

    public function add_gym_job(){
        $this->checkCreatePermission();
        $data['saveUrl'] = base_url('admin/cms/save_gym_job_ajax'); 
        foreach ($this->cms->getAllJobsRequirements()['data'] as $r){
            $data['requirements'][$r->type][$r->id] = $r->name;
        }  
        $this->load->view('admin/cms/gym_job', $data);
    }       

    public function edit_gym_job($id){
        $this->checkEditPermission();
        $data['saveUrl'] = base_url('admin/cms/save_gym_job_ajax'); 
        $data['job'] = $this->cms->getGymJob($id);
        $data['requirements'] = array_map(function($row){ 
            return $row['requirement_id'];
        }, $this->cms->getGymJobRequirements($id));        
        foreach ($this->cms->getAllJobsRequirements()['data'] as $r){
            $data['requirements'][$r->type][$r->id] = $r->name;
        }          
        $this->load->view('admin/cms/gym_job', $data);
    }
    
    public function save_gym_job_ajax(){
        $this->checkCreatePermission(true);
        if($this->cms->saveGymJob($this->input->post())) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);
    } 
    
    public function delete_gym_job_ajax(){
        $this->checkDeletePermission(true);
        if($this->cms->deleteGymJob($this->input->post('job_id'))) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);        
    }
    
    public function get_gym_jobs(){
        echo json_encode($this->cms->getAllGymJobs());
    }

    public function gym_jobs_requirements($type){
        $data['jobsRequirementsUrl'] = base_url("admin/cms/get_jobs_requirements_ajax/$type");
        $data['reqType'] = $type;
        $this->load->view('admin/cms/gym_jobs_requirements', $data);
    }

	public function save_job_requirement_ajax(){
        $this->checkEditPermission(true);
        if($this->cms->saveRequirement($this->input->post())) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);
	}	

	public function delete_job_requirement_ajax(){
        $this->checkDeletePermission(true);
        if($this->cms->deleteJobRequirement($this->input->post())) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);
    }
    
    public function get_jobs_requirements_ajax($type){
        echo json_encode($this->cms->getAllJobsRequirements($type));
    }

    // MENU

    public function menu(){
        $this->checkReadPermission();

        $data['pageTitle'] = 'Nastavení menu';  
        
        $data['saveMenuUrl'] = base_url('/admin/cms/save_menu_items');
        foreach ($this->gyms->getGymSettings(['front_menu_items']) as $k => $v){
            $data['menu']=json_decode($v['data'],true);
            $data['menu']['id']=$v['id'];
        }

        $this->app->assets(['admin.cms.menu.js'], 'js');

		$this->load->view('layout/header', $data);
        $this->load->view('layout/menu', $data);
        $this->load->view('admin/cms/menu', $data);
		$this->load->view('layout/footer');
    }    

    public function save_menu_items(){
        $this->checkEditPermission(true);
        if($this->gyms->saveGymSettings($this->input->post())) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);
    }

    // FOOTER
    
    public function footer_modal_gym_settings(){
        $this->checkReadPermission();
        $data['saveGeneralInfoUrl'] = base_url('admin/cms/save_general_info_ajax');
        $data['saveOHUrl'] = base_url('admin/cms/save_opening_hours_ajax');
        
        foreach ($this->gyms->getGymSettings(['opening_hours','general_info']) as $k => $v){
            $data[$v['type']]['id']=$v['id'];
            $data[$v['type']]['data']=json_decode($v['data'],true);
        }  

        $this->load->view('admin/cms/footer_modal_gym_settings', $data);
    }
    
    public function footer(){
        $this->checkReadPermission();

        $data['pageTitle'] = 'Nastavení patičky';  
        
        $data['footerModalurl'] = base_url('/admin/cms/footer_modal_gym_settings');
        foreach ($this->gyms->getGymSettings(['opening_hours','general_info','footer']) as $k => $v){
            $data[$v['type']]['id']=$v['id'];
            $data[$v['type']]['data']=json_decode($v['data'],true);
        }      

        $this->app->assets(['flatpickr.js', 'flatpickr.cs.js', 'sortable.min.js', 'admin.cms.footer.js'], 'js');
        $this->app->assets(['flatpickr.css'], 'css');

		$this->load->view('layout/header', $data);
        $this->load->view('layout/menu', $data);
        $this->load->view('admin/cms/footer', $data);
		$this->load->view('layout/footer');
    }

    public function save_footer_ajax(){
        $this->checkEditPermission(true);
        $data = json_decode($this->input->post('data'),true);
        $data['id'] = $this->input->post('id');
        if($this->gyms->saveGymSettings($data)) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);
    }

}
    
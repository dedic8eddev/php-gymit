<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Media extends Backend_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('media_model', 'media');

	}

    public function sectionName(): string
    {
        return SECTION_CMS;
    }

    public function index(){
	    $this->checkReadPermission();

		$data['pageTitle'] = "Galerie";

		$data['media'] = $this->media->getAllMedia();

		$data['url']['ajax']['media'] = site_url('admin/media/ajax_get_media/');
		$data['url']['ajax']['update'] = site_url('admin/media/ajax_update/');
		$data['url']['ajax']['delete'] = site_url('admin/media/ajax_delete/');

		$data['url']['ajax']['categories'] = site_url('admin/media/ajax_categories/');
        $data['url']['ajax']['labels'] = site_url('admin/media/ajax_labels/');
        
        $data['thumb_width'] = $this->media->getThumbSettings()['width'];
        $data['thumb_height'] = $this->media->getThumbSettings()['height'];

		$data['countryOptions'] = [
            'input_name' => 'countries[]',
            'id' => 'js_media_select2_countries',
            'multiple' => true
		];
		$data['marketsOptions'] = [
            'input_name' => 'markets[]',
            'id' => 'js_media_select2_markets',
            'multiple' => false
        ];		

        $this->app->assets(['shuffle.min.js', 'admin.media.main.js'], 'js');
        $this->app->assets(['media.css'], 'css');

		$this->load->view('layout/header', $data);
		$this->load->view('layout/menu', $data);
		$this->load->view('admin/media/index', $data);
		$this->load->view('layout/footer');
	}

	public function upload(){
		if( ($result = $this->media->upload()) !== FALSE){
			$this->app->setHttpReponse(200,'json',json_encode(['message' => 'Úspěšně nahráno','data' => $result]));
			return true;
		}else{
			$this->app->setHttpReponse(400,'json',json_encode(['error' => 'Nepovedlo se nahrát soubor/y']));
			return false;
		}
	}

	public function ajax_get_media(){
		if( ($data = $this->media->getMedia($this->input->post('id'))) !== FALSE ){
			$this->app->setHttpReponse(200,'json',json_encode(['data' => $data]));
			return true;
		}else{
			$this->app->setHttpReponse(400,'json',json_encode('Došlo k chybě, zksute to později.'));
			return false;
		}
	}

	public function ajax_categories(){
		$return = [];
		$data = $this->media->getAllCategories();

		$return = array_map(function($row) {
			return array(
				'id' => $row['id'],
				'text' => $row['name']
			);
		}, $data);

		$this->app->setHttpReponse(200,'json',json_encode($return));
		return true;
	}

	public function ajax_labels(){
		$return = [];
		$data = $this->media->getAllLabels();

		$return = array_map(function($row) {
			return array(
				'id' => $row['id'],
				'text' => $row['name']
			);
		}, $data);

		$this->app->setHttpReponse(200,'json',json_encode($return));
		return true;
	}

	public function ajax_update($id){
	    $this->checkEditPermission(true);
		if( $this->media->update($id,$this->input->post())){

			$data = $this->media->getMedia($id);

			$this->app->setHttpReponse(200,'json',json_encode(['message' => 'Úspěšně upraveno','data' => $data]));
			return true;
		}else{
			$this->app->setHttpReponse(400,'json',json_encode('Došlo k chybě, zkuste to později'));
			return false;
		}
	}

	public function ajax_delete(){
	    $this->checkDeletePermission(true);
		if( $this->media->delete($this->input->post('id'))){
			$this->app->setHttpReponse(200,'json',json_encode(['message' => 'Úspěšně vymazáno']));
			return true;
		}else{
			$this->app->setHttpReponse(400,'json',json_encode('Došlo k chybě, zkuste to později'));
			return false;
		}
	}
}

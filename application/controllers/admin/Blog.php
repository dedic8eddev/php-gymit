<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blog extends Backend_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('Blog_model','blog');
    }

    public function sectionName(): string
    {
        return SECTION_CMS;
    }

    public function index(){
        $this->checkReadPermission();

        $data['pageTitle'] = 'Blog';

        $data['addUrl'] = base_url('admin/blog/save_post_ajax');
        $data['getAllUrl'] = base_url('admin/blog/get_all_posts');

        $this->app->assets(['tabulator.min.js', 'flatpickr.js', 'flatpickr.cs.js', 'trumbowyg/trumbowyg.min.js', 'trumbowyg/trumbowyg.cs.js', 'admin._trumbowyg.js', 'admin.blog.main.js'], 'js');
        $this->app->assets(['tabulator.min.css', 'flatpickr.css', 'trumbowyg/trumbowyg.min.css'], 'css');

        $this->load->view('layout/header', $data);
		$this->load->view('layout/menu', $data);
		$this->load->view('admin/blog/index', $data);
        $this->load->view('layout/footer');
    }

    public function edit_post($id){
        $this->checkEditPermission();

        $data['pageTitle'] = 'Úprava příspěvku';

        $data['saveUrl'] = base_url('admin/blog/save_post_ajax');  
        $data['post'] = $this->blog->getPost($id);

        $this->app->assets(['tabulator.min.js', 'flatpickr.js', 'flatpickr.cs.js', 'trumbowyg/trumbowyg.min.js', 'trumbowyg/trumbowyg.cs.js', 'admin._trumbowyg.js', 'admin.blog.edit.js'], 'js');
        $this->app->assets(['tabulator.min.css', 'flatpickr.css', 'trumbowyg/trumbowyg.min.css'], 'css');

		$this->load->view('layout/header', $data);
        $this->load->view('layout/menu', $data);
        $this->load->view('admin/blog/edit_post', $data);
		$this->load->view('layout/footer'); 
    }

    public function get_all_posts(){
        $this->checkReadPermission();
        $posts = $this->blog->getAllBlogPosts();
        echo json_encode($posts);
    }

    public function save_post_ajax(){
        $this->checkEditPermission(true);
        $data = $this->input->post();

        if($this->blog->savePost($data)) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);  
    }   
    
    public function remove_post_ajax(){
        $this->checkDeletePermission(true);
        $id = $this->input->post('post_id');

        if($this->blog->removePost($id)) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);  
    }
    
    public function pin_post_ajax(){
        $this->checkEditPermission(true);
        $id = $this->input->post('post_id');
        $pin = $this->input->post('pinned')==0 ? true : false;

        if($this->blog->pinPost($id, $pin)) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);  
    }      
}

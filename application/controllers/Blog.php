<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blog extends Public_Controller {
	public function __construct()
	{
		parent::__construct();
        $this->load->model('blog_model', 'blog');       
	}

	public function index()
	{
        echo 'Hello world';
    }
    
    public function article($slug){
        $slug_parts = explode('-', $slug);
        $article_id = array_values(array_slice($slug_parts, -1))[0];
        $article = $this->blog->getPost($article_id);

        $header['bodyClass'] = 'article';
        $header['menuClass'] = 'bg';
		$header['pageTitle'] = $article['title'];
        
        $data = [];
        $data['article'] = $article;

        $this->app->assets(['front.account.css'], 'css');

		$this->load->view('frontend/layout/header',$header);
		$this->load->view('frontend/layout/menu', $header);
		$this->load->view('frontend/blog/article', $data);
		$this->load->view('frontend/layout/footer', $data);

    }
}

<?php defined('BASEPATH') OR exit('No direct script access allowed');

class mPDF{
    
    protected $ci;

    public function __construct(){
        $this->ci =& get_instance();
    }

    public function create($css,array $structure,array $data=[],array $options=[]){
        $mpdf = new \Mpdf\Mpdf($options);

        if ($css) $css = $this->ci->load->file(APPPATH."views/pdf/$css.css", true);
        
        // header
        if(isset($structure['header'])){
            $header = $this->ci->load->view("pdf/".$structure['header'], $data, true); 
            $mpdf->setHtmlHeader($header);
        }

        // content
        $content = $this->ci->load->view("pdf/".$structure['content'], $data, true);

        // footer
        if(isset($structure['footer'])){
            $footer = $this->ci->load->view("pdf/".$structure['footer'], $data, true); 
            $mpdf->setHtmlFooter($footer);
        }

        if ($css) $mpdf->WriteHTML($css,1); // load css to PDF
        $mpdf->WriteHTML($content); // load content to PDF        
        $mpdf->Output();
    }

}

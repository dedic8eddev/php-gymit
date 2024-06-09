<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reporting extends Backend_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('reporting_model', 'reports');
	}

    public function sectionName(): string
    {
        return SECTION_REPORTING;
    }

    public function index(){
        $this->checkReadPermission();

        $data['pageTitle'] = 'Reporting';

        $this->app->assets(['tabulator.min.js', 'flatpickr.js', 'flatpickr.cs.js', 'admin.reporting.main.js'], 'js');
        $this->app->assets(['tabulator.min.css', 'flatpickr.css'], 'css');

        $this->load->view('layout/header', $data);
		$this->load->view('layout/menu', $data);
		$this->load->view('admin/reporting/index', $data);
        $this->load->view('layout/footer');
    }
    
    public function generate_daily_report(){
        $g = $_GET;
        $this->reports->generate_daily_report([$g['from'], $g['to']]);
    }

    public function generate_manager_report(){
        $g = $_GET;
        $this->reports->generate_manager_report([$g['from'], $g['to']]);
    }

    public function generate_checkouts_report(){
        $g = $_GET;
        $this->eetapp->getCheckoutsLog();
        $logs = $this->eetapp->getCheckoutsLog($g['from'],$g['to'])['data'];
        $data[] = ['Datum a čas','Pokladna','Uživatel','Status','Suma','Poznámka']; // header
        foreach ($logs as $k=>$l){
            $row=[
                25569 + (strtotime($l->date_created) / 86400), // excel date format
                $l->name,
                $l->user,
                $l->state==1 ? 'Otevřel' : 'Uzavřel',
                $l->amount,
                $l->note
            ];
            array_push($data,$row);
        }
        $this->reports->generate_report_from_array($data,'log_pokladen',[1]); // first column is date column
    }
}


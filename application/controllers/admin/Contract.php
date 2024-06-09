<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contract extends Backend_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('Contract_model', 'contract');
    }

    public function sectionName(): string
    {
        return SECTION_MEMBERSHIP;
    }

    public function get_contract_pdf() {
        $this->checkReadPermission();
        $contractNumber = $this->input->get('contractNumber') ?? '';
        $userId = (int) $this->input->get('userId');
        $membershipId = (int) $this->input->get('membershipId') ?? null;
        $mpdf = $this->contract->generateContract($contractNumber, $userId, $membershipId);
    }

}
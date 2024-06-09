<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Contract_model extends CI_Model {

    /** @var string Database table */
    const CONTRACT_NUMBERS_TABLE = 'gym_contract_numbers';

    /** @var string Contract specific constants */
    const CONTRACT_FILE = 'gymit_contract.pdf';
    const CONTRACT_DATE_FORMAT = 'j.n. Y';

    /** @var string Contract number format patterns */
    const PATTERN_GYM           = '[gym]';
    const PATTERN_NUMBER_SERIES = '[number_series]';
    const PATTERN_YEAR          = '[year]';

    public function __construct(){
        parent::__construct();
    }

    public function createContract($userId, $gymCode, $data){
            $this->gymdb->init(current_gym_db());   

            $contractNumber = $this->getNextContractNumber($userId, $gymCode);

            $contract = [
                'contract_number' => $contractNumber,
                'client_id' => $userId,
                'created_by' => gym_userid(),
                'membership_price_id' => $data['membership_price_id']
            ];
    
            if($this->db->insert('contracts', $contract)) return $contractNumber;
            else return false; 
    }

    public function getNextContractNumber($userId, $gymCode){
        $this->gymdb->init(current_gym_db());

        $this->db->trans_start();
            $result = $this->db->select('max(contract_number) as last_number')->where('client_id', $userId)->get('contracts')->row_array();
        $this->db->trans_complete();

        if($this->db->trans_status()){
            if(!is_null($result['last_number'])) $counter = (int) substr($result['last_number'], -6);
            else $counter = 0;

            $nextCounter = $counter + 1;
            $nextCounterFormatted = sprintf('%06d',$nextCounter);
        }

        return $gymCode . date('Y').$nextCounterFormatted;
    }

    public function generateContract(string $contractNumber, int $userId, int $membershipId) {
        $data = [];

        $data['contractNumber'] = $contractNumber;

        // membership / subscription info
        $sub = $this->API->subscriptions->get_subscription_by_invoice_number($contractNumber);
        if($sub && !empty($sub->data)){ // contract is not done
            $data['subPayments'] = $sub->data;
            $data['subInfo'] = $this->pricelist->getMembershipPriceByPeriod($sub->data->subType,$sub->data->subPeriod ?? null);
        } else { // show draft contract
            $data['contractNumber'] = '<span style="color:blue;">NÁVRH SMLOUVY</span>';
            $data['subInfo'] = $this->pricelist->getMembershipPrice($membershipId);
        }

        // Show whole price of membership while pay every month
        if($sub->data->subPeriod === 'month') $data['subInfo']->price = $data['subInfo']->price * 12;

        // Subject info, contact info, bank account etc...
        foreach ($this->gyms->getGymSettings(['subject_info','general_info']) as $k => $v){
            $data[$v['type']]=json_decode($v['data']);
        }

        // user data
        $data['user_data'] = $this->users->getUserData($userId);

        // generate PDF
        $pdf = $this->mpdf->create('contract', [
            'content' => 'contract',
        ], $data, [
            'setAutoTopMargin' => 'stretch',
            'autoMarginPadding' => 7,
            'setAutoBottomMargin' => 'stretch',
        ]);
    }

    
}
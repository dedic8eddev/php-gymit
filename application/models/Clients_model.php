<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Clients_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
    }

    /**
     * Save client data
     * expects POST data
     */
    public function saveClientData(){
        $data=$_POST['client_data'];
        $data['client_id']=$_POST['user_id'];

        $data['vip']=isset($data['vip']) ? 1 : 0;
        $data['dailypass']=isset($data['dailypass']) ? 1 : 0;

        if (strlen($data["multisport_id"]) <= 0) $data["multisport_id"] = NULL;

        $this->db->trans_start();
        if (@$_POST['client_data_id']>0){ // update or insert?
            $this->db->update('clients_data',$data,['id' => $_POST['client_data_id']]);
        } else $this->db->insert('clients_data',$data);
        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    }    
    
    // Just quickly update the local status of the membership of the given user,
    // supply NULL as the membership_id if exiting a membership
    public function updateClientMembershipLocal($client_id, $membership_id){
        if( $this->db->where('client_id', $id)->update('clients_data', ["membership_id" => $membership_id])->row() ){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    public function getClientData($id){
        $data = $this->db->where('client_id', $id)->get('clients_data')->row();
        if($data) return $data;
        else return false;
    }    

    public function getAllMembershipBenefitsUsage($s2 = false){
        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $this->db->select('m.name m_name,coalesce(p.name,d.name) item_name, coalesce(p.vat_price,d.sale_price_vat) item_price, b.discount, u.date_created')->from('membership_benefits_usage u')
                ->join('membership_benefits b', 'b.id=u.benefit_id')
                ->join('membership m', 'm.id=b.membership_id')
                ->join('price_list p', 'p.id=b.item_id and b.depot = 0','LEFT')
                ->join('depot_items d', 'd.id=b.item_id and b.depot = 1','LEFT');

        if($sorter) foreach($sorter as $s){ 
            switch ($s['field']){
                case 'm_name' : $this->db->order_by('m.name', $s['dir']); break;
                case 'item_name' : $this->db->order_by('coalesce(p.name,d.name) '. $s['dir']); break;
                case 'item_price' : $this->db->order_by('coalesce(p.vat_price,d.sale_price_vat) '. $s['dir']); break;
                case 'discount' : $this->db->order_by('b.discount', $s['dir']); break;
                case 'date_created' : $this->db->order_by('u.date_created', $s['dir']); break;
                default: $this->db->order_by($s['field'], $s['dir']);  break;
            }
            
        }
        if($filter) foreach($filter as $f){ 
            switch ($f['field']){
                case 'client_id' : $this->db->where($f["field"], $f['value']); break;
                case 'm_name' : $this->db->like('m.name', $f['value']); break;
                case 'item_name' : $this->db->like('coalesce(p.name,d.name)', $f['value']); break;
                case 'item_price' : $this->db->like('coalesce(p.vat_price,d.sale_price_vat)', $f['value']); break;
                case 'discount' : $this->db->like('b.discount', $f['value']); break;
                case 'date_created' : $this->db->like('u.date_created', $f['value']); break;                
                default: $this->db->like($f["field"], $f['value']);   break;
            }            
            
        }

        $countRes = $this->db->count_all_results(null,FALSE);

        if($page != NULL && $limit != NULL){
            $offset = $page > 1 ? $offset = ($page - 1) * $limit : 0;
            $this->db->limit($limit, $offset);
        }

        if($s2){
            $result = $this->db->get()->result();
            return $result;
        }else{
            $result = $this->db->get()->result();
            $reply["data"] = $result;
            if($limit != NULL) $reply['last_page'] = ceil( $countRes / $limit );
            return $reply;
        }        
    }  

}
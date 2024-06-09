<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Vouchers_model extends CI_Model
{
    public function __construct(){
        $this->gymdb->init(current_gym_db());
    }

    public function createVoucher(string $type, int $id, string $gymCode, string $note=NULL, int $giftedUser=NULL){
        $duplicate = 1;
        while($duplicate > 0){ // generate code till code is unique
            $code = $gymCode.strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 8));
            $duplicate = $this->db->select('id')->where('code',$code)->from('vouchers')->count_all_results();
        }
        $insertData = [
            'price_id' => $type == 'service' ? $id : NULL,
            'membership_price_id' => $type == 'membership' ? $id : NULL,
            'code' => $code,
            'note' => $note,
            'gifted_user' => $giftedUser,
            'created_by' => gym_userid() ?? 1 // system
        ];
        $this->db->insert('vouchers',$insertData);
        return $code;
    } 

    public function disableVoucher(string $code, int $gifted_user=NULL){
        $this->db->set('date_disabled', 'NOW()', FALSE);
        $this->db->update('vouchers',['disabled_by' => gym_userid(), 'gifted_user' => $gifted_user],['code' => $code]);
        return true;
    }

    public function setIdentification(array $codes,string $type, string $id){
        $this->db->where_in('code',$codes)->set(['identification_type' => $type, 'identification_id' => $id])->update('vouchers');
        return;
    }

    public function getVoucher(string $code){
        $v = $this->db->select('v.*,mp.membership_id,
        coalesce(p.vat_price,mp.price) as vat_price,
        coalesce(p.name,concat(m.name,\' (\',mp.purchase_name,\')\')) as name,
        uc.email created_by_email, concat(udc.first_name," ",udc.last_name) as created_by_name,
        ug.email gifted_user_email, concat(udg.first_name," ",udg.last_name) as gifted_user_name,
        ud.email disabled_by_email, concat(udd.first_name," ",udd.last_name) as disabled_by_name')
        ->from('vouchers v')
        ->join('price_list p','p.id=v.price_id','LEFT')
        ->join('membership_prices mp','mp.id=v.membership_price_id','LEFT')
        ->join('membership m','m.id=mp.membership_id','LEFT')
        ->join('users uc','uc.id=v.created_by','LEFT')
        ->join('users_data udc', 'udc.user_id=v.created_by','LEFT')
        ->join('users ug','ug.id=v.gifted_user','LEFT')
        ->join('users_data udg', 'udg.user_id=v.gifted_user','LEFT')
        ->join('users ud','ud.id=v.disabled_by','LEFT')
        ->join('users_data udd', 'udd.user_id=v.disabled_by','LEFT')
        ->where('v.code',$code)->get()->row();

        if($v->identification_type=='invoice') $v->identification_name='faktura';
        else if ($v->identification_type=='webpay') $v->identification_name='webová platební brána';
        else if ($v->identification_type=='payments') $v->identification_name='pokladna';
        else $v->identification_name='Neznámý';

        if($v->identification_type=='webpay'){ // membership presale merch presents
            $depot_id=1;
            $present = $this->db->select('d.id, d.name,d.vat_value vat,d.sale_price price, d.sale_price_vat vat_price, 
                dd.id as depot_id, dd.name as depot_name, (s.stock-s.reserved) as depot_stock')
                ->from('membership_benefits b')
                ->join('membership_prices p','b.membership_id=p.membership_id')
                ->join('depot_items d','d.id=b.item_id')
                ->join('depots_stocks s','s.item_id=b.item_id and depot_id='.$depot_id)
                ->join('depots dd','s.depot_id=dd.id')
                ->where(['purchase_gift'=>true,'depot'=>true,'p.id'=>$v->membership_price_id])
                ->get()->row();
            if($present) $v->present=$present;
        }
        //print_r($v); exit;

        return $v;
    }

    public function getVouchersByCodes($codes){
        return $this->db->select('v.code,
        coalesce(p.vat_price,mp.price) as vat_price,
        coalesce(p.name,concat(m.name,\' (\',mp.purchase_name,\')\')) as name')
        ->from('vouchers v')
        ->join('price_list p','p.id=v.price_id','LEFT')
        ->join('membership_prices mp','mp.id=v.membership_price_id','LEFT')
        ->join('membership m','m.id=mp.membership_id','LEFT')
        ->where_in('v.code',$codes)->get()->result();
    }
    
    public function getAllVouchers($s2 = false){
        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $this->db->select('v.*, coalesce(p.name,concat(m.name,\' (\',mp.purchase_name,\')\')) as name, concat(g.first_name,\' \',g.last_name) as gifted_user_name, ug.email as gifted_user_email, concat(c.first_name,\' \',c.last_name) as created_by_name, concat(d.first_name,\' \',d.last_name) as disabled_by_name')
            ->from('vouchers v')
            ->join('price_list p','p.id=v.price_id','LEFT')
            ->join('membership_prices mp','mp.id=v.membership_price_id','LEFT')
            ->join('membership m','m.id=mp.membership_id','LEFT')
            ->join('users_data c','c.user_id=v.created_by')
            ->join('users_data g','g.user_id=v.gifted_user','LEFT')
            ->join('users_data d','d.user_id=v.disabled_by','LEFT')
            ->join('users ug','ug.id=v.gifted_user','LEFT');
        
        // default sort
        if(!$sorter) $this->db->order_by('v.date_created','DESC');

        if($sorter){
            foreach($sorter as $s){
                $order_field = $s['field'];
                $direction = $s['dir'];
                switch($s['field']){
                    case 'state': $this->db->order_by('v.date_disabled', $direction); break;
                    case 'name': $this->db->order_by('p.name', $direction); break;
                    case 'created_by_name': $this->db->order_by('c.last_name', $direction)->order_by('c.first_name', $direction); break;                                                
                    case 'disabled_by_name': $this->db->order_by('d.last_name', $direction)->order_by('d.first_name', $direction); break;                                                
                    case 'gifted_user_name': $this->db->order_by('g.last_name', $direction)->order_by('g.first_name', $direction); break;                                                
                    default: $this->db->order_by('v.'.$order_field, $direction); break;
                }  
            }
        }
        if($filter){
            foreach($filter as $f){
                switch($f['field']){     
                    case 'state': 
                        if($f['value']==1) $this->db->where('date_disabled is null');
                        else $this->db->where('date_disabled is not null');
                    break;
                    case 'name': $this->db->like('p.name',$f['value']); break; 
                    case 'created_by_name': $this->db->like('concat(c.first_name,\' \',c.last_name)',$f['value']); break; 
                    case 'disabled_by_name': $this->db->like('concat(d.first_name,\' \',d.last_name)',$f['value']); break;
                    case 'gifted_user_name': $this->db->where("concat(g.first_name,' ',g.last_name) like '%".$f['value']."%' or ug.email like '%".$f['value']."%'"); break;
                    default:
                        $fieldname = 'v.'.$f["field"];
                        $this->db->like($fieldname, $f['value']);
                    break;
                }  
            }
        }

        $countRes = $this->db->count_all_results(null,FALSE);

        if($page != NULL && $limit != NULL){
            $offset = $page > 1 ? $offset = ($page - 1) * $limit : 0;
            $this->db->limit($limit, $offset);
        }

        if($s2) return $this->db->get()->result_array();
        else{
            $result = $this->db->get()->result();
            $reply["data"] = $result;
            if($limit != NULL) $reply['last_page'] = ceil( $countRes / $limit );
            return $reply;
        }        
    }     
}
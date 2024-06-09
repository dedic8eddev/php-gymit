<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Eetapp_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
    }

    public function saveCheckout(array $data){       
        $this->db->trans_start();
        if($data['id']>0){
            if(!$this->eetapp_lib->setCheckout($data)) return false;
            $id=$data['id']; unset($data['id']);
            $this->db->update('eet_checkouts',$data,['id' => $id]);
        } else {
            $data['checkout_id'] = $this->eetapp_lib->setCheckout($data);
            if($data['checkout_id']>0) $this->db->insert('eet_checkouts',$data);
            else return false;
        }
        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    }

    public function getCheckout(int $id){
        return $this->db->select('c.*,coalesce(l.state,0) as state')
            ->from('eet_checkouts c')
            ->join('eet_checkouts_log l','l.checkout_id=c.id and l.id=(select max(id) from eet_checkouts_log where checkout_id=c.id)','LEFT')
            ->where('c.id',$id)->get()->row_array();
    }
    
    public function openCheckout(array $data){
        if($this->eetapp_lib->openCheckout($data)){
            $data['checkout_id']=$data['id']; unset($data['id']); // id swap
            $data['state']=1;
            $data['user_id']=gym_userid();
            $this->db->trans_start();
            $this->db->insert('eet_checkouts_log',$data);
            $this->db->trans_complete();
        } else return false;

        if($this->db->trans_status()) return true;
        else return false;        
    }

    public function closeCheckout(array $data){
        if($this->eetapp_lib->closeCheckout($data)){
            $data['checkout_id']=$data['id']; unset($data['id']); // id swap
            $data['state']=0;
            $data['user_id']=gym_userid();
            $this->db->trans_start();
            $this->db->insert('eet_checkouts_log',$data);
            $this->db->trans_complete();
        } else return false;
        
        if($this->db->trans_status()) return true;
        else return false;        
    }    

    public function getAllCheckouts($s2 = false){
        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $this->db->select('c.*,coalesce(l.state,0) as state,coalesce(l.amount,0) as amount')
            ->from('eet_checkouts c')
            ->join('eet_checkouts_log l','l.checkout_id=c.id and l.id=(select max(id) from eet_checkouts_log where checkout_id=c.id)','LEFT');

        if($sorter){
            foreach($sorter as $s){
                switch($s['field']){
                    //case 'lesson_name': $this->db->order_by('ltm.name', $s['dir']); break;                                                
                    default: $this->db->order_by($s['field'], $s['dir']); break;
                }                   
            }
        }
        if($filter){
            foreach($filter as $f){
                switch($f['field']){
                    //case 'lesson_name': $this->db->where('ltm.name', $f['value']); break;
                    default: $this->db->like($f["field"], $f['value']); break;  
                }             
            }
        }
        $countRes = $this->db->count_all_results(null,FALSE);

        if($page != NULL && $limit != NULL){
            $offset = $page > 1 ? $offset = ($page - 1) * $limit : 0;
            $this->db->limit($limit, $offset);
        }
        if($s2) return $this->db->get()->result();
        else {
            $ret["data"] = $this->db->get()->result();
            if($limit != NULL) $ret['last_page'] = ceil( $countRes / $limit );
            return $ret;      
        }
    }

    public function getLastCheckoutLog($checkout_id){
        return $this->db->where(['checkout_id'=>$checkout_id])->order_by('id desc')->limit(1)->get('eet_checkouts_log')->row();
    }

    public function getCheckoutsLog($from=NULL,$to=NULL){
        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $this->db->select('c.*,l.*,concat(u.first_name,\' \',u.last_name) as user')
            ->from('eet_checkouts_log l')
            ->join('eet_checkouts c','l.checkout_id=c.id','LEFT')
            ->join('users_data u','u.user_id=l.user_id','LEFT');

        if($from && $to){
            $this->db->where("date_created >= '$from'");
            $this->db->where("date_created <= '$to'");
        }

        if($sorter){
            foreach($sorter as $s){
                switch($s['field']){
                    case 'user': $this->db->order_by('u.last_name', $s['dir']); break;                                                
                    default: $this->db->order_by($s['field'], $s['dir']); break;
                }                   
            }
        }
        if($filter){
            foreach($filter as $f){
                switch($f['field']){
                    case 'user': $this->db->like('concat(u.first_name,\' \',u.last_name)', $f['value']); break;
                    default: $this->db->like($f["field"], $f['value']); break;  
                }             
            }
        }
        $countRes = $this->db->count_all_results(null,FALSE);

        if($page != NULL && $limit != NULL){
            $offset = $page > 1 ? $offset = ($page - 1) * $limit : 0;
            $this->db->limit($limit, $offset);
        }

        $ret["data"] = $this->db->get()->result();
        if($limit != NULL) $ret['last_page'] = ceil( $countRes / $limit );
        return $ret;      
    }    

    public function prepareData4pay(array $items){
        $ret=[];
        foreach($items as $type => $items){
            if($type=='depot'){
                foreach($items as $depot_id => $items_id){
                    foreach($items_id as $item_id => $item){
                        $i['id']=$item_id;
                        $i['title']=$item['name'];
                        $i['amount']=$item['amount'];
                        $i['dph']=$item['vat'];
                        $i['price']=$item['vat_value'] + $item['value'];
                        $i['discount']=$item['discount'] ?? 0;
                        array_push($ret,$i);
                    }
                }
            } else {
                foreach($items as $item_id => $item){
                    $i['id']=$item_id;
                    $i['title']=$item['name'];
                    $i['amount']=$item['amount'];
                    $i['dph']=$item['vat'];
                    $i['price']=$item['vat_value']+$item['value'];
                    $i['discount']=$item['discount'] ?? 0;
                    array_push($ret,$i);
                }                
            }
        }
        return $ret;
    }

}
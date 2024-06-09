<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pricelist_model extends CI_Model
{
    public function __construct(){
        $this->gymdb->init(current_gym_db());
    }

     // PRICE LIST

    public function savePrice(array $data){
        $data = array_map(function($d){ // handle null values for DB
            return $d=='' ? null : $d;
        }, $data);
        $data['icon_image'] = 1; // removed from form
        $data['visible'] = empty($data['visible']) ? 0 : 1;
        $this->db->trans_start();
        if (@$data['id']>0){ // update or insert?
            $id=$data['id']; unset($data['id']);
            $this->db->update('price_list',$data,['id' => $id]);
        } else $this->db->insert('price_list',$data);
        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    } 
 
    public function removePrice(int $id){
        $this->db->trans_start();
        $this->db->delete('membership_services_prices',['price_id' => $id]);
        $this->db->delete('price_list',['id' => $id]);
        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    }    

    public function getPrice(int $id){
        return $this->db->where('id',$id)->get('price_list')->row_array();
    }
 
    public function getAllPrices($s2 = false, $data = NULL){
        $g = !is_null($data) ? $data : $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $this->db->select('p.*')->from('price_list p');

        if($sorter){
            foreach($sorter as $s){
                $order_field = $s['field'];
                $direction = $s['dir'];

                if($order_field == "name"){
                    $this->db->order_by("coalesce(p.name,l.name) $direction");
                }else{
                    $this->db->order_by('p.'.$order_field, $direction);
                }
            }
        }
        if($filter){
            foreach($filter as $f){
                if($f['field'] == 'name'){
                    $this->db->like('coalesce(p.name,l.name)', $f['value']);
                }else{
                    $fieldname = 'p.'.$f["field"];
                    $this->db->like($fieldname, $f['value']);
                }
            }
        }

        $countRes = $this->db->count_all_results(null,FALSE);

        if($page != NULL && $limit != NULL){
            $offset = $page > 1 ? $offset = ($page - 1) * $limit : 0;
            $this->db->limit($limit, $offset);
        }

        if($s2){
            $result = $this->db->get()->result_array();
            return $result;
        }else{
            $result = $this->db->get()->result();
            $reply["data"] = $result;
            if($limit != NULL) $reply['last_page'] = ceil( $countRes / $limit );
            return $reply;
        }        
    } 

    public function checkOvertimeFee($item,$spent_time,$membership){
        if($item->overtime_fee_price>0 && ( // is there a overtime fee?
            !$membership || // has client membership?
            (@$membership->data->overtimeFee_wellness==1 && $item->id==4) || // has membership overtime fee for wellness and is this item a WELLNESS?
            (@$membership->data->overtimeFee_exercise_zones==1 && $item->id==3) // has membership overtime fee for exercise zones and is this item a EXERCISE ZONE?
        ) ){
            // maximum time, which client can spend without fee
            $maxTime = (idate('h',strtotime($item->duration))*60) + idate('i',strtotime($item->duration));
            if($spent_time > $maxTime){ // client must pay overtime fee
                $overTime = $spent_time - $maxTime;
                return [
                    'fee' => ceil($overTime / $item->overtime_fee_minutes) * $item->overtime_fee_price, 
                    'minutes' => $overTime
                ];
            } else return 0; // no overtime fee
        } else return 0; // there is no overtime fee on this item
    }

    public function searchItems($term){ // including depot items
        $result = [];
        $sql = "SELECT id,name as full_name
        FROM (
            SELECT concat(0,'-',id) as id,name FROM price_list
            UNION
            SELECT concat(1,'-',id) as id,name FROM depot_items
        ) as T
        WHERE T.name like '%$term%'
        ORDER BY T.name LIMIT 50";

        $query = $this->db->query($sql);
        $result = $query->result();

        if(stripos('Dobití kreditu', $term) !== FALSE){
            $result[] = (object) ['id' => '0-0', 'full_name' => 'Dobití kreditu'];
        }   

        if($result) return $result;
        else return FALSE;
    }    
    
    // MEMBERSHIP
    public function saveMembership(array $data){
        $data = array_map(function($d){ // handle null values for DB
            return $d=='' ? null : $d;
        }, $data);

        $id = $data['id']; unset($data['id']);
        $type_name = $data['type_name']; unset($data['type_name']);
        $prices = $data['prices']; unset($data['prices']);
        $data['front_data'] = json_encode($data['front_data']);

        $this->db->trans_start();
        $this->db->update('membership_types',['name' => $type_name],['id' => $data['type_id']]);
        $this->db->update('membership',$data,['id' => $id]);
        foreach ($prices as $p_id=>$p){
            $p = array_map(function($pom){ return $pom=='' ? null : $pom; }, $p);            
            $this->db->update('membership_prices',$p,['id' => $p_id]);
        }
        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    } 

    public function searchMemberships($term){
        $result = $this->db->select('t.name type_name, m.*')->from('membership m')
                ->join('membership_types t', 't.id=m.type_id')
                ->like("m.name", $term)
                ->get()->result();

        if($result){
            return $result;
        }else{
            return FALSE;
        }
    }

    public function getMembership($id){
        // code or id?
        if(!is_numeric($id)) $this->db->where('m.code',$id); // search by code
        else $this->db->where('m.id',$id); // search by id

        $membership = $this->db->select('t.name type_name, m.*')->from('membership m')
                ->join('membership_types t', 't.id=m.type_id')
                ->get()->row();
        
        $membership->data = json_decode($membership->data);
        $membership->prices = $this->db->where('membership_id',$id)->get('membership_prices')->result();
        
        return $membership;
    } 

    public function getMembershipPrice(int $id){
        return $this->db->select('p.*,m.type_id,m.code,m.name')->where('p.id',$id)->from('membership_prices p')->join('membership m','m.id=p.membership_id')->get()->row();
    }
    
    public function getMembershipPriceByPeriod($membership_id, string $period = NULL){
        // code or id?
        if(!is_numeric($membership_id)) $this->db->where('m.code',$membership_id); // search by code
        else $this->db->where('m.id',$membership_id); // search by id

        return $this->db->select('p.*,m.type_id,m.code,m.name')
            ->from('membership_prices p')
            ->join('membership m','m.id=p.membership_id')
            ->where('p.period_type',$period) // prepaid card and trial have null period type
            ->get()->row();
    } 

    public function getMembershipPrices(){
        return $this->db->select('p.id mp_id,m.name,p.purchase_name,p.price')->from('membership_prices p')->join('membership m','m.id=p.membership_id')->get()->result();
    }
    
    public function getMembershipPrices4HP(){
        $data = $this->db->select("t.code type_code, min(mp.price) price")
                    ->from('membership m')
                    ->join('membership_types t', 't.id=m.type_id')
                    ->join('membership_prices mp', 'mp.membership_id=m.id')
                    ->where("m.code not like '%student'")
                    ->group_by('t.code')
                    ->get()->result();
        foreach ($data as $p){ $return[$p->type_code]=number_format($p->price, 0, '', '.'); }
        return $return;
    }

    public function getFrontMembershipDetail($code){
        $data = $this->db->select("t.name type_name, m.*, mp.price")
                    ->from('membership m')
                    ->join('membership_types t', 't.id=m.type_id')
                    ->join('membership_prices mp', 'mp.membership_id=m.id', 'LEFT')
                    ->where("t.code='$code'")
                    ->where("(period_type='month' or period_type is null)")
                    ->get()->result();
        foreach ($data as $k => $v){
            $data[$k]->data=json_decode($v->front_data);
            $data[$k]->price=number_format($v->price, 0, '', '.');
        }
        return $data;
    }

    // Gets all memberships from mongo
    // changed by @JD
    public function getAllMemberships($s2 = false){
        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $this->db->select('t.name type_name, m.*')->from('membership m')
                ->join('membership_types t', 't.id=m.type_id');

        if($sorter) foreach($sorter as $s){ $this->db->order_by($s['field'], $s['dir']); }
        if($filter) foreach($filter as $f){ $this->db->like($f["field"], $f['value']); }

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

    /**
     * Grabs the membership based on contractNum and returns it (from mongo),
     * changed by @JD
     */
    public function getMembershipOverview($contractNumber) {
        $sub = $this->API->subscriptions->get_subscription_by_invoice_number($contractNumber);

        if(!empty($sub->data)){
            return $sub->data;
        }else{
            return FALSE;
        }
    }

    public function getAllMembershipOverviews($s2 = false, $data = FALSE)
    {
        $g = $data ? $data : $_GET;
        $params = [];

        // Pagination and filtering
        $params['page'] = (isset($g['page'])) ? $g['page'] : null;
        $params['size'] = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $sort_by = [];
        if($sorter){
            foreach($sorter as $s){
                $order_field = $s['field'];
                $direction = ($s['dir'] == "asc") ? "1" : "-1";

                $sort_by[$order_field] = $direction;
            }
        }
        $params['sortBy'] = $sort_by;
        
        if($filter){
            foreach($filter as $f){
                if($f['field'] == 'client_name') $params['clientId'] = $f['value'];
                else if ($f["field"] == "membership_name") $params["membershipId"] = $f["value"];
                else $params[$f["field"]] = $f['value'];
            }
        }

        $subs = $this->API->subscriptions->get_subscriptions($params);
        if(!$subs) return [];

        $userIDs = []; foreach($subs->data as $v): $userIDs[] = $v->clientId; endforeach;

        if(!empty($userIDs)){
            $c = $this->db->select('user_id,concat(users_data.first_name," ",users_data.last_name) as user_name')
                        ->where_in('user_id', array_unique($userIDs))
                        ->get('users_data')
                        ->result_array();

            $users = []; foreach($c as $user): $users[$user['user_id']] = $user; endforeach;
        }

        $ret['data'] = $subs->data;
        foreach($ret['data'] as $k => $v){
            $membership = $this->pricelist->getMembershipPriceByPeriod($v->membershipId,$v->subPeriod ?? null);

            $ret['data'][$k]->client_name = $users[$v->clientId]['user_name'] ?? ' -- ';
            $ret['data'][$k]->membership_name = $membership->name ?? ' -- ';
            $ret['data'][$k]->membership_type = $membership->purchase_name;
            $ret['data'][$k]->createdOn = date("d.m. Y", strtotime(mongoDateToLocal($ret['data'][$k]->createdOn)));
            $end = $ret['data'][$k]->transactions[count($ret['data'][$k]->transactions)-1]->end ?? '';
            $ret['data'][$k]->to = !empty($end) ? date("d.m. Y", strtotime(mongoDateToLocal($end))) : null;
        }

        if($params['page'] && $params['size']) $ret['last_page'] = ceil( $subs->total / $params['size'] ); 
        return $ret;
    }
    
    // MEMBERSHIP SERVICES PRICES

    public function saveMembershipServicePrice(array $data){
        $id=$data['item_id']; unset($data['item_id']);
        unset($data['item_id']);

        // update or insert?
        $this->db->trans_start();
        if ($id>0) $this->db->update('membership_services_prices',$data,['id' => $id]);
        else $this->db->insert('membership_services_prices',$data);
        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    }

    public function getMembershipServicesPrices($id,$s2 = false){
        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $this->db->select('msp.id, p.id price_id, p.service_type, p.name, p.duration,
                coalesce(msp.price,p.price) as price,
                coalesce(msp.vat_price,p.vat_price) as vat_price,
                coalesce(msp.vat,p.vat) as vat,
                coalesce(msp.overtime_fee_minutes, p.overtime_fee_minutes) as overtime_fee_minutes,
                coalesce(msp.overtime_fee_price, p.overtime_fee_price) as overtime_fee_price
                ')->from('price_list p')
                ->join('membership_services_prices msp', 'msp.price_id=p.id and msp.membership_id='.$id, 'LEFT');

        if($sorter) foreach($sorter as $s){ $this->db->order_by($s['field'], $s['dir']); }
        if($filter) foreach($filter as $f){ $this->db->like($f["field"], $f['value']); }

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
    
    public function getMembershipServicePrice($item_id,$membership_id){
        if(!is_numeric($membership_id)) $membership_id=$this->db->where('code',$membership_id)->get('membership')->row()->id;

        return $this->db->select('p.id, p.name, p.service_type, p.service_subtype, p.duration,
            coalesce(msp.price,p.price) as price, 
            coalesce(msp.vat_price,p.vat_price) as vat_price, 
            coalesce(msp.vat,p.vat) as vat,
            coalesce(msp.overtime_fee_minutes, p.overtime_fee_minutes) as overtime_fee_minutes,
            coalesce(msp.overtime_fee_price, p.overtime_fee_price) as overtime_fee_price
            ')->from('price_list p')
        ->join('membership_services_prices msp', 'msp.price_id=p.id and msp.membership_id='.$membership_id, 'LEFT')
        ->where('p.id',$item_id)->get()->row();    
    }

    public function getFrontSingleEntryPrices(){
        return $this->db->where('service_subtype',1)->where('visible', 1)->get('price_list')->result();
    }

    public function getFrontServicePrices($service_type){
        $regularPrice = $this->db->where('service_type',$service_type)->where('service_subtype',1)->get('price_list')->row_array();
        $memberships=['basic','platinum'];
        $membershipPrices = $this->db->select('mt.code, min(msp.vat_price) vat_price')
            ->from('membership_services_prices msp')
            ->join('membership m', 'm.id=msp.membership_id')
            ->join('membership_types mt', 'mt.id=m.type_id') 
            ->where('msp.price_id',$regularPrice['id'])
            ->where_in('mt.code', ['basic','platinum'])
            ->group_by('mt.code')->get()->result_array();

        $mp=[];
        foreach ($membershipPrices as $p){
            $mp[$p['code']]=$p['vat_price'];
        }
        // regular price
        $ret['regular']=number_format($regularPrice['vat_price'], 0, '', '.');
        // membership prices
        foreach($memberships as $m){
            $ret[$m]=isset($mp[$m]) ? number_format($mp[$m], 0, '', '.') : number_format($regularPrice['vat_price'], 0, '', '.');
        }
        return $ret;
    }

    // MEMBERSHIP BENEFITS

    public function getMembershipBenefits($membership_id){
        $mb = $this->db->select('mb.*,m.name membership_name, coalesce(p.name,d.name) as item_name')->from('membership_benefits mb')
                ->join('membership m','m.id=mb.membership_id')
                ->join('price_list p','p.id=mb.item_id AND mb.depot=0','LEFT')
                ->join('depot_items d','d.id=mb.item_id AND mb.depot=1','LEFT')
                ->where('mb.membership_id',$membership_id)
                ->get()->result();
        return $mb;
    }

    public function checkMembershipBenefit($client_id,$card_id,$item_id,$depot_item){
        // check if membership active
        $membership = $this->API->subscriptions->get_subscription($client_id, current_gym_code());
        if(isset($membership->data->active) && $membership->data->active==1){
            $membership = $this->getMembership($membership->data->subType);
            
            // Check if benefit exists
            if(!$benefit = $this->db->where(['item_id'=>$item_id,'depot'=>$depot_item,'membership_id'=>$membership->id,'active'=>true])->get('membership_benefits')->row()){
                return false; // there is no benefit
            };

            // Check if client have already used benefit
            if(isset($benefit->period_type)){ // there is period limitation eg. twice per week
                $checkDate = date('Y-m-d', strtotime("-1 $benefit->period_type"));
                $usedBenefitsCount = $this->db->where("date(date_created)>='$checkDate'")->where('benefit_id',$benefit->id)->from('membership_benefits_usage')->count_all_results();
            } else { // there is no period limitations eg. twice per week
                $usedBenefitsCount = $this->db->where('benefit_id',$benefit->id)->from('membership_benefits_usage')->count_all_results();
            }

            // Check quantity used
            if($usedBenefitsCount < $benefit->quantity){
                // Check if time is in specific hour interval?
                if(isset($benefit->specific_hour_start) && isset($benefit->specific_hour_end)){
                    $now = date('Y-m-d H:m:s');
                    if (($now >= date('Y-m-d H:m:s', strtotime($benefit->specific_hour_start))) && ($now <= date('Y-m-d H:m:s', strtotime($benefit->specific_hour_end)))){
                        return $benefit; // now is in specific hours
                    } else return false; // now is not in specific hours
                } else return $benefit; // there is no specific hour and quantity > used
            } else return false; // quantity is not > used
        } else return false; // membership is not active
    }

    public function useMembershipBenefit($benefit_id,$client_id,$trans_id){
        $this->db->insert('membership_benefits_usage',['benefit_id'=>$benefit_id,'client_id'=>$client_id,'transaction_id'=>$trans_id]);
        return true;
    }
}
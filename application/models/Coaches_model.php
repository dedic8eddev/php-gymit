<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Coaches_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
    }

    /**
     * Save coach data
     * expects POST data
     */
    public function saveCoachData(){
        $data=$_POST['coach_data'];
        $data['coach_id']=$_POST['user_id'];
        $data['visible'] = false;

        $this->db->trans_start();
        if (@$_POST['coach_data_id']>0){ // update or insert?
            $this->db->update('coach_data',$data,['id' => $_POST['coach_data_id']]);
        } else $this->db->insert('coach_data',$data);

        $visible = $this->calculateCoachVisibility((int) $data['coach_id']);

        $this->db->update('coach_data', ['visible'=> $visible], ['id' => $_POST['coach_data_id']]);

        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    }

    /**
     * Calculate coaches visibility by defined rules
     */
    public function calculateCoachVisibility(int $userId): bool
    {
        $data = $this->db
            ->select('ud.photo, ud.personal_identification_number, cd.about, cd.quote')
            ->from('coach_data cd')
            ->join('users_data ud','ud.user_id=cd.coach_id','LEFT')
            ->where('cd.coach_id', $userId)
            ->get()
            ->row()
        ;

        if (empty($data)) {
            return false;
        }

        if (empty($data->photo)) {
            return false;
        }

        if (empty($data->personal_identification_number)) {
            return false;
        }

        if (empty($data->about)) {
            return false;
        }

        if (empty($data->quote)) {
            return false;
        }

        return true;
        /** @todo specifications */
    }
    
    public function getCoachData($id){
        $data = $this->db->where('coach_id', $id)->get('coach_data')->row();
        if($data) return $data;
        else return false;
    }

    // SPECIALIZATIONS

    public function saveCoachSpecializations(){
        $specializations = isset($_POST['specializations']) ? $_POST['specializations'] : [];

        $coachId = $_POST['user_id'];

        $specializations = array_map(function($row) use ($coachId) {
            return ['coach_id' => $coachId, 'specialization_id' => $row];
        }, $specializations);

        $this->db->trans_start();
        $this->db->delete('coach_specializations',['coach_id' => $coachId]);
        if(!empty($specializations)) $this->db->insert_batch('coach_specializations',$specializations);
        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    }     
    
    public function getCoachSpecializations($id){
        return $this->db->select('coach_specializations.*,name as specialization_name')->from('coach_specializations')
                        ->join('coach_specializations_items', 'coach_specializations_items.id = coach_specializations.specialization_id','left')
                        ->where('coach_id', $id)->get()->result_array();
    }

    public function getAllSpecializations($s2 = false){
        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $this->db->from('coach_specializations_items');

        if($sorter){
            foreach($sorter as $s){
                $this->db->order_by($s['field'], $s['dir']);
            }
        }
        if($filter){
            foreach($filter as $f){
                $this->db->like($f["field"], $f['value']);
            }
        }

        $countRes = $this->db->count_all_results(null,FALSE);

        if($page != NULL && $limit != NULL){
            $offset = $page > 1 ? $offset = ($page - 1) * $limit : 0;
            $this->db->limit($limit, $offset);
        }

        if($s2) return $this->db->get()->result_array();
        else{
            $ret["data"] = $this->db->get()->result();
            if($limit != NULL) $ret['last_page'] = ceil( $countRes / $limit );
            return $ret;
        }        
    }  
    
    public function saveSpecialization(array $data){       
        $this->db->trans_start();
        if($data['item_id']>0) $this->db->update('coach_specializations_items',['name' => $data['item_name']],['id' => $data['item_id']]);
        else $this->db->insert('coach_specializations_items',['name' => $data['item_name']]);
        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    }
    public function deleteSpecialization(array $data){  
        $this->db->trans_start();
        $this->db->delete('coach_specializations', ['specialization_id' => $data['item_id']]);
        $this->db->delete('coach_specializations_items', ['id' => $data['item_id']]);
        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    }

    // COACH LESSONS

    public function getCoachLessons($coach_id, $past = true){
        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $this->db->select('l.id,l.canceled,l.cancel_reason,ltm.name lesson_name, l.starting_on,l.ending_on,count(lc.client_id) registered_clients, ltm.client_limit, lt.participate, concat(ls.first_name,\' \',ls.last_name) as teacher_substitute')
            ->from('lessons l')
            ->join('lessons_teachers lt','lt.lesson_id=l.id')
            ->join('lessons_clients lc','lc.lesson_id=l.id','LEFT')
            ->join('lessons_templates ltm','ltm.id=l.template_id')
            ->join('users_data ls','ls.user_id=lt.teacher_substitute','LEFT')
            ->where('lt.teacher_id',$coach_id)
            ->group_by('l.id');

        if($past) $this->db->where('now() > l.ending_on'); // past
        else $this->db->where('now() < l.starting_on'); // coming


        if($sorter){
            foreach($sorter as $s){
                switch($s['field']){
                    case 'lesson_name': $this->db->order_by('ltm.name', $s['dir']); break;                                                
                    default: $this->db->order_by($s['field'], $s['dir']); break;
                }                   
            }
        }
        if($filter){
            foreach($filter as $f){
                switch($f['field']){
                    case 'lesson_name': $this->db->where('ltm.name', $f['value']); break;
                    default: $this->db->like($f["field"], $f['value']); break;  
                }             
            }
        }

        $this->db->order_by('l.starting_on'); // default sort

        $countRes = $this->db->count_all_results(null,FALSE);

        if($page != NULL && $limit != NULL){
            $offset = $page > 1 ? $offset = ($page - 1) * $limit : 0;
            $this->db->limit($limit, $offset);
        }

        $ret["data"] = $this->db->get()->result();
        if($limit != NULL) $ret['last_page'] = ceil( $countRes / $limit );
        return $ret;      
    }

}
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cms_model extends CI_Model
{
    public function __construct(){
        $this->gymdb->init(current_gym_db());
    }

    // PAGES

    public function getAllPages($s2 = false){
        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $nameRegex = "REGEXP_REPLACE(data, '.*\"name\":\"(.*?)\".*', '\\\\1')";

        $this->db->select("*,$nameRegex as name")
                ->from('gym_settings')
                ->where("type like 'page_%'");

        if($sorter){
            foreach($sorter as $s){
                $order_field = $s['field'];
                $direction = $s['dir'];
                switch ($order_field) {
                    case 'name':
                        $this->db->order_by("$nameRegex $direction");
                        break;
                    default: $this->db->order_by($order_field, $direction);
                }                       
            }
        }
        if($filter){
            foreach($filter as $f){
                $fieldname = $f["field"];
                switch ($fieldname) {
                    case 'name':
                        $this->db->like("$nameRegex", $f['value']);
                        break;
                    default: $this->db->like($fieldname, $f['value']);
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
    
    // GYM JOBS

    public function getAllGymJobs($s2 = false){
        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $this->db->select("*")
                ->from('gym_jobs');

        if($sorter){
            foreach($sorter as $s){
                $order_field = $s['field'];
                $direction = $s['dir'];
                switch ($order_field) {
                    default: $this->db->order_by($order_field, $direction);
                }                       
            }
        }
        if($filter){
            foreach($filter as $f){
                $fieldname = $f["field"];
                switch ($fieldname) {
                    default: $this->db->like($fieldname, $f['value']);
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

    public function getGymJob(int $id){
        return $this->db->where('id',$id)->get('gym_jobs')->row_array();
    }

    public function saveGymJob(array $data){
        $data = array_map(function($d){ // handle null values for DB
            return $d=='' ? null : $d;
        }, $data);

        $requirements = isset($data['requirements']) ? $data['requirements'] : []; unset($data['requirements']);

        $this->db->trans_start();
        if (@$data['id']>0){ // update or insert?
            $id=$data['id']; unset($data['id']);
            $this->db->update('gym_jobs',$data,['id' => $id]);
        } else {
            $this->db->insert('gym_jobs',$data);
            $id=$this->db->insert_id();
        }

        $requirements = array_map(function($row) use ($id) {
            return ['job_id' => $id, 'requirement_id' => $row];
        }, $requirements);        

        $this->db->delete('gym_jobs_requirements',['job_id' => $id]);
        if(!empty($requirements)) $this->db->insert_batch('gym_jobs_requirements',$requirements);

        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    }

    public function deleteGymJob(int $id){  
        $this->db->trans_start();
        $this->db->delete('gym_jobs_requirements', ['job_id' => $id]);
        $this->db->delete('gym_jobs', ['id' => $id]);
        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    }       

    // GYM JOBS REQUIREMENTS
    
    public function getAllJobsRequirements($type="", $s2 = false){
        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $this->db->from('gym_jobs_requirements_items');
        if(!empty($type)) $this->db->where('type',$type);

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

    public function saveJobRequirement($type){
        $requirement = isset($_POST['requirement']) ? $_POST['requirement'] : [];

        $requirement = array_map(function($row) use ($type) {
            return ['type' => $type, 'requirement_id' => $row];
        }, $requirement);

        $this->db->trans_start();
        $this->db->delete('gym_jobs_requirements',['type' => $type]);
        if(!empty($requirement)) $this->db->insert_batch('gym_jobs_requirements',$requirement);
        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    }     
    
    public function saveRequirement(array $data){       
        $this->db->trans_start();
        if($data['item_id']>0) $this->db->update('gym_jobs_requirements_items',['name' => $data['item_name']],['id' => $data['item_id']]);
        else $this->db->insert('gym_jobs_requirements_items',['name' => $data['item_name'], 'type' => $data['item_type']]);
        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    }
    public function deleteJobRequirement(array $data){  
        $this->db->trans_start();
        $this->db->delete('gym_jobs_requirements', ['requirement_id' => $data['item_id']]);
        $this->db->delete('gym_jobs_requirements_items', ['id' => $data['item_id']]);
        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    }
    
    public function getGymJobRequirements(int $id){
        return $this->db->from('gym_jobs_requirements')
                        ->join('gym_jobs_requirements_items', 'gym_jobs_requirements_items.id = gym_jobs_requirements.requirement_id')
                        ->where('job_id', $id)->get()->result_array();
    } 

}
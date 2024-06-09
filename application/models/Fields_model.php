<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Fields_model extends CI_Model
{
    public function __construct(){}
        
    /**
     * Add new custom field
     * expects POST data
     */
    public function addField(){
        $p = $_POST;

        // insert obj
        $insert = [
            'name' => $p['name'],
            'type' => $p['type'],
            'section' => $p['section'],
            'description' => $p['description'],
            'hidden' => (boolean) $p['hidden'],
            'required' => (boolean) $p['required'],
            'type_params' => NULL
        ];

        // add options for select type
        if($p['type'] == 'select'){
            $insert['type_params'] = json_encode($p["option"]);
        }

        if($this->db->insert('custom_fields', $insert)){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    public function saveField(){
        $p = $_POST;

        $field_id = $p["field_id"];

        // insert obj
        $update = [
            'name' => $p['name'],
            'type' => $p['type'],
            'section' => $p['section'],
            'description' => $p['description'],
            'hidden' => (boolean) $p['hidden'],
            'required' => (boolean) $p['required'],
            'type_params' => NULL
        ];

        // add options for select type
        if($p['type'] == 'select'){
            $insert['type_params'] = json_encode($p["option"]);
        }

        if($this->db->where('id', $field_id)->update('custom_fields', $update)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    public function getcustomFieldsValues($item_id, $custom_fields){
        $formatted = [];
        if(!empty($custom_fields)){
            foreach($custom_fields as $cf){
                $value = $this->db->where('item_id', $item_id)->where('field_id', $cf->id)->get('custom_fields_values')->row();
                $formatted[] = $value;
            }
        }

        return $formatted;
    }

    public function saveCustomFieldsValues($data, $item_id, $section){
        $inserts = [];
        foreach($data as $name => $value){
            if(strpos($name, 'customfield_') !== false){
                $field_id = explode('_', $name)[1];

                $insert = [
                    'field_id' => $field_id,
                    'item_id' => $item_id,
                    'section' => $section,
                    'value' => $value
                ];
            
                $inserts[] = $insert;
                $exists = $this->db->where('field_id', $field_id)->where('item_id', $item_id)->get('custom_fields_values')->row();
                if($exists){
                    $this->db->where('id', $exists->id)->update('custom_fields_values', $insert);
                }else{
                    $this->db->insert('custom_fields_values', $insert);
                }
            }
        }

        return true;
    }

    public function getSectionCustomFields($section){
        $fields = $this->db->where('section', $section)->where('hidden !=', 1)->get('custom_fields')->result();
        if($fields){
            return $fields;
        }else{
            return FALSE;
        }
    }

    public function deleteField(){
        $p = $_POST;
        if($this->db->where('id', $p['field_id'])->delete('custom_fields')){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    public function getField(){
        $p = $_POST;
        if($field = $this->db->where('id', $p['field_id'])->get('custom_fields')->row()){
            return $field;
        }else{
            return FALSE;
        }
    }

    /**
     * Get all fields
     */
    public function getAllFields(){

        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $this->db->select('custom_fields.id, custom_fields.hidden, custom_fields.required, custom_fields.name, custom_fields.type, custom_fields.section, custom_fields.type_params, custom_fields.description')->from('custom_fields');

        if($sorter){
            foreach($sorter as $s){
                $order_field = $s['field'];
                $direction = $s['dir'];

                $this->db->order_by('custom_fields.'.$order_field, $direction);
            }
        }
        if($filter){
            foreach($filter as $f){
                $fieldname = 'custom_fields.'.$f["field"];
                $this->db->like($fieldname, $f['value']);
            }
        }

        $countRes = $this->db->count_all_results(null,FALSE);

        if($page != NULL && $limit != NULL){
            $offset = 0;
            if($page > 1){
                $offset = ($page - 1) * $limit;
            }
            $this->db->limit($limit, $offset);
        }

        $result = $this->db->get()->result();
        $reply["data"] = $result;
        if($limit != NULL) $reply['last_page'] = ceil( $countRes / $limit );
        return $reply;
    }

}
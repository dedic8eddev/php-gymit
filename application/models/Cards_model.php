<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Autoloaded model for payments
 */
class Cards_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
    }

    public function addCardPair($client_id=null, $card_id=null, $employee = FALSE){
        if($client_id>0){
            $p['client_id']=$client_id;
            $p['card_id']=$card_id;
        } else $p = $_POST;

        $insert = [
            'user_id' => $p['client_id'],
            'card_id' => $p['card_id']
        ];

        if(!$this->getUserCard($p["client_id"])){
            if($this->db->insert('users_cards', $insert)){

                // Create the credit document for this user/card pair
                if ( $this->API->transactions->create_user_credit($p['client_id'], $p['card_id']) ){
                    return ["success" => "true"];
                }else{
                    // failed, remove card, send false
                    $this->db->where('user_id', $p['client_id'])->delete('users_cards');
                    return ["error" => "true", "message" => "Operace se nezdařila."];
                }

            }else{
                return ["error" => "true", "message" => "Operace se nezdařila."];
            }
        }else{
            if($this->db->where("user_id", $p['client_id'])->update('users_cards', ["card_id" => $p['card_id']])){
                if ( $this->API->transactions->edit_user_credit($p['client_id'], $p['card_id']) ){
                    return ["success" => "true"];
                }else{
                    return ["error" => "true", "message" => "Operace se nezdařila."];
                }
            }else{
                return ["error" => "true", "message" => "Operace se nezdařila."];
            }
        }
    }

    public function removeCardPair($user_id){
        if ( $this->API->transactions->remove_user_credit($user_id) ){
            if($this->db->where('user_id', $user_id)->delete('users_cards')){
                return ["success" => "true"];
            }else{
                return ["error" => "true", "message" => "Operace se nezdařila."];
            }
        }else{
            return ["error" => "true", "message" => "Operace se nezdařila."];
        }
    }

    public function getUserCard($user_id){
        $card = $this->db->where("user_id", $user_id)->get("users_cards")->row();
        if($card) return $card;
        else return FALSE;
    }

    public function getUserFromCard($cardId){
        $card = $this->db->where("card_id", $cardId)->get("users_cards")->row();
        $user = $this->db->where("id", $card->user_id)->get("users")->row();

        if($user) return $user;
        else return FALSE;
    }

    public function getAllCards($s2 = false){

        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $this->db->select('users_cards.id, users_cards.card_id, users_cards.user_id, users_data.first_name, users_data.last_name, users.email')->from('users_cards');
        $this->db->join('users', 'users.id = users_cards.user_id');
        $this->db->join('users_data', 'users_data.user_id = users_cards.user_id');

        if($sorter){
            foreach($sorter as $s){
                $order_field = $s['field'];
                $direction = $s['dir'];

                if($order_field != "client_name"){
                    $this->db->order_by('users.'.$order_field, $direction);
                }else{
                    if($order_field == "client_name"){
                        $this->db->order_by('users_data.first_name', $direction);
                        $this->db->order_by('users_data.last_name', $direction);
                    }
                }
            }
        }
        if($filter){
            foreach($filter as $f){
                if($f['field'] == 'client_name'){
                    $this->db->where('users_data.first_name', $f['value'])->or_where('users_data.last_name', $f['value']);
                }else{
                    $fieldname = 'users_cards.'.$f["field"];
                    $this->db->like($fieldname, $f['value']);
                }
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
}
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications_model extends CI_Model
{
    public function __construct(){}

        /**
         * Send a notification to a user
         * $type => 1: general (zatím) TODO
         * $title => title of the notif
         * $text => text of the notif
         * $target => userId to send it to (send "admins" to send to all admins)
         * $group => groupId to send it to everyone inside a designated group
         * 
         * $this->n->send($type, "title", "text", $user_id, $group_id);
         */
    public function send($type, $title, $text, $target = NULL, $group = NULL){
        if($target){
            // Specific user notification
            $obj = [
                'type' => $type,
                'title' => $title,
                'message' => $text,
                'target' => $target
            ];

            if($this->db->insert('notifications', $obj)){
                return TRUE;
            }else{
                return FALSE;
            }
        }else if($group){
            // Whole group notification
            if(is_array($group)){
                foreach($group as $g){
                    $obj = [
                        'type' => $type,
                        'title' => $title,
                        'message' => $text,
                        'group' => $g
                    ];
        
                    $this->db->insert('notifications', $obj);
                }

                return TRUE;
            }else{
                $obj = [
                    'type' => $type,
                    'title' => $title,
                    'message' => $text,
                    'group' => $group
                ];
    
                if($this->db->insert('notifications', $obj)){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }
        }
    }

    public function getNotificationById($nId){
        $notification = $this->db->where('id', $nId)->get('notifications')->row();
        return $notification;
    }

    // Get all users notifications (complete)
    public function getUsersNotifications($userId = NULL, $status = [0, 1]){
        if(!$userId){
            $userId = $this->ion_auth->user(gym_userid())->row()->id;
        }

        $group = $this->ion_auth->get_users_groups($userId)->result()[0]->id; // groupid
        $notifications = $this->db->where('target', $userId)->where_in('read', $status)->or_where('group', $group)->where_in('read', $status)->order_by('created_on', 'desc')->get('notifications')->result();
        if($notifications){
            return $notifications;
        }else{
            return FALSE;
        }
    }

    // Get unread notifications for a user collected into counts (eg. 23 new notifications from xx)
    // Gets just type ids and counts 
    public function getUsersNotificationsCollected($userId = NULL){
        if(!$userId){
            $userId = $this->ion_auth->user(gym_userid())->row();
            if(!$userId) return FALSE;
            else $userId = $userId->id;
        }
        $group = $this->ion_auth->get_users_groups($userId)->result()[0]->id; // groupid

        $this->db->select('type, count(*) as total')->from('notifications');
        $this->db->where('read', 0);
        $this->db->where('target', $userId)->or_where('group', $group)->where('read', 0); // id or group id
        $this->db->group_by('type');

        //$this->db->order_by('created_on', 'desc');

        $notifications = $this->db->get()->result();
        if($notifications){

            // if only 1, supply the actual notification
            foreach($notifications as $n){
                if($n->total == 1){
                    $notification = $this->db->where('type', $n->type)->where('read', 0)->where('target', $userId)->or_where('group', $group)->where('type', $n->type)->where('read', 0)->get('notifications')->row();
                    $n->notification = $notification;
                }
            }

            return $notifications;
        }else{
            return FALSE;
        }
    }

    public function getUserUnreadCount($userId){
        if(!$userId){
            $userId = $this->ion_auth->user(gym_userid())->row()->id;
        }
        $notifications = $this->db->where('target', $userId)->where('read', 0)->count_all_results('notifications');
        return $notifications;
    }

     /**
     * Gets notifications for a given user
     */
    public function getAllNotifications(){

        $type = $_GET["type"]; // notification type

        // Pagination and filtering
        if(empty($g)){
            $g = $_GET;
        }

        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $reply = array();

        $this->db->select('*')->from('notifications as n');

        // by group
        $this->db->where('n.group', gym_group());
        if($type != "ALL"){
            $this->db->where('n.type', $type);
        }

        // by userid
        $this->db->or_where('n.target', gym_userid());
        if($type != "ALL"){
            $this->db->where('n.type', $type);
        }

        $countRes = $this->db->count_all_results(null,FALSE);

        if($page != NULL && $limit != NULL){
            $offset = 0;
            if($page > 1){
                $offset = ($page - 1) * $limit;
            }
            $this->db->limit($limit, $offset);
        }

        $this->db->order_by("created_on", "desc");
        $result = $this->db->get()->result();
        $reply["data"] = $result;

        if($limit != NULL) $reply['last_page'] = ceil( $countRes / $limit );
        return $reply;
    }

    /**
     * Mark notification or notifications as read
     * @param array/int an array of ids or a singular id
     */
    public function readNotifications($id){
        if(is_array($id)){
            $this->db->where_in("id", $id)->update("notifications", ["read" => 1]);
        }else{
            $this->db->where("id", $id)->update("notifications", ["read" => 1]);
        }

        return TRUE;
    }

}
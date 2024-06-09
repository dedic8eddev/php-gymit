<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Gyms_model extends CI_Model
{
    public function __construct(){
        $this->load->model('cards_model', 'cards');
        $this->gymId = NULL;
    }

    public function init($gymId){
        $this->gymId = $gymId;
    }

    /** 
     * Get all gyms from Mongo
     */
    public function getAllGyms(){
        $gyms = $this->mongo_db->get('gyms');
        if($gyms){
            return $gyms;
        }else{
            return [];
        }
    }

    public function getGymDb($gym_id){
        $gym = $this->getGymById($gym_id);
        return $gym[0]['dbname'];
    }

    /**
     * Get a single gym by ID from Mongo
     */
    public function getGymById($id){
        $gym = $this->mongo_db->where('_id', mId($id))->find_one("gyms");
        if($gym){
            return $gym;
        }else{
            return false;
        }
    }

    /**
     * Get a single gym by dbname from Mongo
     */
    public function getGymByDbName($name){
        $gym = $this->mongo_db->where('dbname', $name)->find_one("gyms");
        if($gym){
            return $gym;
        }else{
            return false;
        }
    }

    /**
     * Get all personificators
     */
    public function getAllPersonificators(){
        $readers = $this->db->where('personificator', 1)->get('rooms')->result();

        if($readers){
            return $readers;
        }else{
            return false;
        }
    }

    /**
     * Get gyms entrance reader for a particular gym/club
     * This is useful for locking the entrance for a particular user for example
     */
    public function getEntranceReader($gym_id){
        $this->gymdb->init($this->getGymDb($gym_id));
        $reader = $this->db->where('entrance', 1)->get('rooms')->row();

        if($reader){
            return $reader;
        }else{
            return false;
        }
    }

    /**
     * Create a new gym
     * expects POST data
     */
    public function createNewGym(){

        if(!gym_in_group(1)) die('Permission denied');
        
        $name = $_POST['name'];
        $slug = $_POST['slug'];

        $db_name = $this->gymdb->get_available_db_name();

        if($this->gymdb->create_new_database($db_name)){
            // db created
            if($this->mongo_db->insert('gyms', [
                'name' => $name,
                'slug' => $slug,
                'dbname' => $db_name,
                'primary' => false
            ])){
                return ['success' => 'true'];
            }else{
                return ['error' => 'Could not insert gym into Mongo.'];
            }
        }else{
            return ['error' => 'Could not create new SQL database.'];
        }

    }

    /**
     * Remove a gym
     * expects id
     */
    public function removeGym($id){

        if(!gym_in_group(ADMINISTRATOR)) die('Permission denied');

        $gym = $this->mongo_db->where('_id', mId($id))->find_one("gyms");

        if($this->gymdb->delete_database($gym[0]['dbname'])){
            // db created
            if($this->mongo_db->where('_id', mId($id))->delete('gyms')){
                return ['success' => 'true'];
            }else{
                return ['error' => 'Could not delete gym from Mongo.'];
            }
        }else{
            return ['error' => 'Could not delete SQL database.'];
        }
    }

    public function getSiteSettings(){
        return $this->db->where("gym", current_gym_code())->get("site_settings")->row();
    }

    /**
     * Get data about gym
     * expects array of types
     */
    public function getGymSettings(array $types){
        return $this->db->where_in('type',$types)->get('gym_settings')->result_array();
    }    

    /**
     * Save data about gym
     * array POST data, type
     */
    public function saveGymSettings(array $data, $type=""){
        $this->db->trans_start();
        if (@$data['id']>0){ // update or insert?
            $id=$data['id']; unset($data['id']);
            $this->db->update('gym_settings',['data' => json_encode($data, JSON_UNESCAPED_UNICODE)],['id' => $id]);
        } else {
            unset($data['id']);
            $this->db->insert('gym_settings',['type'=>$type,'data' => json_encode($data, JSON_UNESCAPED_UNICODE)]);
        }
        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;        
    }  

    // ADD DEPOT
    public function addGymDepot($gym_id = FALSE){
        $p = $_POST;

        $insert = [
            'name' => $p['name'],
            'description' => isset($p['description']) ? $p['description'] : NULL
        ];

        if($gym_id > 0) $this->gymdb->init($this->getGymDb($gym_id));
        if($this->db->insert('depots', $insert)){

            $depot_id = $this->db->insert_id();
            $depot_items = $this->db->get("depot_items")->result();
            foreach($depot_items as $item){
                $this->db->insert("depots_stocks", ["depot_id" => $depot_id, "item_id" => $item->id, "stock" => 0]);
            }

            return TRUE;
        }else{
            return FALSE;
        }
    }

    /**
     * Update a depot
     */
    public function editGymDepot($depot_id, $gym_id = FALSE){
        $p = $_POST;

        $update = [
            'name' => $p['name'],
            'description' => $p['description']
        ];

        if($gym_id > 0) $this->gymdb->init($this->getGymDb($gym_id));
        if($this->db->where('id', $depot_id)->update('depots', $update)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    
    /**
     * Remove depot from gym
     */
    public function removeGymDepot($depot_id, $gym_id = FALSE){
        if($gym_id > 0) $this->gymdb->init($this->getGymDb($gym_id));
        if($this->db->where('id', $depot_id)->delete('depots')){
            $this->db->where("depot_id", $depot_id)->delete("depots_stocks");
            return TRUE;
        }else{
            return FALSE;
        }
    }

    /**
     * Add a room into a gym
     * Settings:
     *     roomId: {type: Number, required: true}, // ID of the room
    readerId: {type: String, required: true}, // SerialNum
    isPersonificator: {type: Boolean, required: true, default: false}, // Personificator unit?
    isActive: {type: Boolean, required: true, default: true}, // Active or disabled?
    customSettings: [mongoose.Schema.Types.Mixed] // Array of custom option key/value pairs
     */
    public function addGymRoom($gym_id = FALSE){
        $p = $_POST;

        $insert = [
            'name' => $p['name'],
            'description' => $p['description'],
            'category' => $p['category_id'],
            'reader_id' => $p['reader_id'],
            'personificator' => $p['personificator'],
            'entrance' => $p['entrance'],
            'exit' => $p['exit'],
            'wellness' => $p['wellness'],
            'exercise_room' => $p['exercise_room'],
            'address' => $p['address'],
            'priority' => $p['priority']
        ];

        $room_settings = [
            'readerId' => $p['reader_id'],
            'readerAddress' => $p['address'],
            'isPersonificator' => $p['personificator'],
            'isWellness' => $p['wellness'],
            'isExerciseRoom' => $p['exercise_room'],
            'isBuildingEntrance' => $p['entrance'],
            'isBuildingExit' => $p['exit'],
            'roomPriority' => $p['priority']
        ];

        if($p['pin_code_bool'] == "1"){
            $insert["pin_code"] = $p['pin_code'];
            $room_settings['pinCode'] = $p['pin_code'];
        }

        if($gym_id > 0) $this->gymdb->init($this->getGymDb($gym_id));
        if($this->db->insert('rooms', $insert)){
            $room_id = $this->db->insert_id();

            if (isset($p['rooms_users'])){
                // limited to particular user access
                foreach ($p['rooms_users'] as $user_id){
                    $this->db->insert("rooms_users", ["room_id" => $room_id, "user_id" => $user_id]);
                }
            }
    
            if (isset($p['rooms_groups']) && !isset($p['rooms_users'])){
                // limited to only groups
                foreach ($p['rooms_groups'] as $group_id){
                    $this->db->insert("rooms_groups", ["room_id" => $room_id, "group_id" => $group_id]);
                }
            }

            if($this->API->readers->add_reader_settings($room_id, $gym_id, $room_settings)) return TRUE;
            else return FALSE;
        }else{
            return FALSE;
        }
    }

    /**
     * Update a room
     */
    public function editGymRoom($room_id, $gym_id = FALSE){
        $p = $_POST;

        $update = [
            'name' => $p['name'],
            'description' => $p['description'],
            'category' => $p['category_id'],
            'reader_id' => $p['reader_id'],
            'personificator' => $p['personificator'],
            'entrance' => $p['entrance'],
            'exit' => $p['exit'],
            'wellness' => $p['wellness'],
            'exercise_room' => $p['exercise_room'],
            'address' => $p['address'],
            'priority' => $p['priority']
        ];

        $room_settings = [
            'readerId' => $p['reader_id'],
            'readerAddress' => $p['address'],
            'isPersonificator' => $p['personificator'],
            'isWellness' => $p['wellness'],
            'isExerciseRoom' => $p['exercise_room'],
            'isBuildingEntrance' => $p['entrance'],
            'isBuildingExit' => $p['exit'],
            'roomPriority' => $p['priority']
        ];

        if($p['pin_code_bool'] == "1"){
            $insert["pin_code"] = $p['pin_code'];
            $room_settings['pinCode'] = $p['pin_code'];
        }else{
            $insert["pin_code"] = NULL;
        }

        if (isset($p['rooms_users'])){
            // limited to particular user access
            if($this->db->where("room_id", $room_id)->delete("rooms_users")){
                foreach ($p['rooms_users'] as $user_id){
                    $this->db->insert("rooms_users", ["room_id" => $room_id, "user_id" => $user_id]);
                }
            }
        }

        if (isset($p['rooms_groups']) && !isset($p['rooms_users'])){
            // limited to only groups
            if($this->db->where("room_id", $room_id)->delete("rooms_groups")){
                foreach ($p['rooms_groups'] as $group_id){
                    $this->db->insert("rooms_groups", ["room_id" => $room_id, "group_id" => $group_id]);
                }
            }
        }

        if($gym_id > 0) $this->gymdb->init($this->getGymDb($gym_id));
        if($this->db->where('id', $room_id)->update('rooms', $update)){
            if($this->API->readers->save_reader_settings($room_id, $gym_id, $room_settings)) return TRUE;
            else return FALSE;
        }else{
            return FALSE;
        }
    }

    /**
     * Get room settings
     */
    public function getGymRoomSettings($room_id, $gym_id){
        $settings = $this->API->readers->get_reader_settings($room_id, $gym_id);

        if(isset($settings->success)){
            return $settings->data;
        }else{
            return false;
        }
    }

    
    /**
     * Remove room from gym
     */
    public function removeGymRoom($room_id, $gym_id = FALSE){
        if($gym_id > 0) $this->gymdb->init($this->getGymDb($gym_id));
        if($this->db->where('id', $room_id)->delete('rooms')){
            $this->API->readers->delete_reader_settings($room_id, $gym_id);
            return TRUE;
        }else{
            return FALSE;
        }
    }

    /**
     * Update a solarium
     */
    public function editGymSolarium($solarium_id, $gym_id = FALSE){
        $p = $_POST;

        if($gym_id > 0) $this->gymdb->init($this->getGymDb($gym_id));

        if($this->db->where('id', $solarium_id)->update('solariums', ['name' => $p['name'],'usage_minutes_limit' => $p['usage_minutes_limit']*60])) return TRUE;
        else return FALSE;
    }

    /**
     * Add maintenance record
     */
    public function addGymSolariumMaintenance($solarium_id, $gym_id = FALSE){
        $p = $_POST;

        $insert = [
            'solarium_id' => $solarium_id,
            'change_pipes' => isset($p['change_pipes']) ? true : false,
            'note' => $p['note'],
            'created_by' => gym_userid()
        ];

        if($gym_id > 0) $this->gymdb->init($this->getGymDb($gym_id));

        if($this->db->insert('solariums_maintenance_log', $insert)) return TRUE;
        else return FALSE;
    }

    public function insertSolariumUsage($solarium_id, $transaction_id, $duration, $gym_id = FALSE){
        $insert = [
            'solarium_id' => $solarium_id,
            'duration' => $duration,
            'transaction_id' => $transaction_id
        ];

        if($gym_id > 0) $this->gymdb->init($this->getGymDb($gym_id));

        if($this->db->insert('solariums_usage', $insert)) return TRUE;
        else return FALSE;
    }

    public function getGymDepots($gym_id = FALSE, $s2 = FALSE){
        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        if($gym_id>0) $this->gymdb->init($this->getGymDb($gym_id)); // particular db
        $this->db->select('*')->from('depots');

        if($sorter){
            foreach($sorter as $s){
                $order_field = $s['field'];
                $direction = $s['dir'];
                $this->db->order_by('depots.'.$order_field, $direction);
            }
        }
        if($filter){
            foreach($filter as $f){
                $fieldname = 'depots.'.$f["field"];
                $this->db->like($fieldname, $f['value']);
            }
        }

        $countRes = $this->db->count_all_results(null,FALSE);

        if($page != NULL && $limit != NULL){
            $offset = $page > 1 ? $offset = ($page - 1) * $limit : 0;
            $this->db->limit($limit, $offset);
        }

        $result = $this->db->get()->result();

        if($result){
            foreach($result as $row){
                $row->delete_url = base_url('admin/gyms/remove_depot_ajax/' . $row->id . "/" . $gym_id);
                $row->edit_url = base_url('admin/gyms/edit_depot_ajax/' . $row->id) . "/" . $gym_id;
            }
        }

        if($s2){
            return $result;
        }else{
            $reply["data"] = $result;
            if($limit != NULL) $reply['last_page'] = ceil( $countRes / $limit );
            return $reply;
        }
    }

    /**
     * Get all rooms for a particulara gym
     */
    public function getGymRooms($gym_id = FALSE, $current_occupancy = FALSE, $s2 = FALSE, $withoutPersonificators = FALSE){

        if(!$gym_id) $gym_id = $this->gymId;

        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        if($gym_id>0) $this->gymdb->init($this->getGymDb($gym_id)); // particular db
        $this->db->select('*')->from('rooms');

        if( $withoutPersonificators ) {
            // Remove personificators from results
            $this->db->where('personificator',false);
        }

        if($sorter){
            foreach($sorter as $s){
                $order_field = $s['field'];
                $direction = $s['dir'];
                $this->db->order_by('rooms.'.$order_field, $direction);
            }
        }
        if($filter){
            foreach($filter as $f){
                $fieldname = 'rooms.'.$f["field"];
                $this->db->like($fieldname, $f['value']);
            }
        }

        $countRes = $this->db->count_all_results(null,FALSE);

        if($page != NULL && $limit != NULL){
            $offset = $page > 1 ? $offset = ($page - 1) * $limit : 0;
            $this->db->limit($limit, $offset);
        }

        $result = $this->db->get()->result();

        if($result){

            // users/groups
            foreach ($result as $room){
                $rooms_users = $this->db->where("room_id", $room->id)->get("rooms_users")->result();
                if($rooms_users){
                    $room->rooms_users = [];
                    foreach($rooms_users as $u){$room->rooms_users[] = $u->user_id;}
                }

                $rooms_groups = $this->db->where("room_id", $room->id)->get("rooms_groups")->result();
                if(!empty($rooms_groups)){
                    $room->rooms_groups = [];
                    foreach($rooms_groups as $u){$rooms->rooms_groups[] = $u->group_id;}
                }
            }

            if($current_occupancy){
                $occupation = $clientIds = [];
                // get rooms occupation from API
                $usersInGym = $this->API->readers->get_users_in_gym();
                if(isset($usersInGym->data)){
                    foreach ($usersInGym->data as $o){
                        $clientIds[] = $o->cardId;
                        $occupation[$o->readerId][$o->cardId]['checked_in'] = "$o->year-".sprintf('%02d',$o->month)."-".sprintf('%02d',$o->day)." $o->time:00";
                        $checked_in = new DateTime($occupation[$o->readerId][$o->cardId]['checked_in']);
                        $now = new DateTime(date('Y-m-d H:i:s'));
                        $occupation[$o->readerId][$o->cardId]['time_diff'] = $checked_in->diff($now)->format('%H:%i:%s');
                    }
                }

                if($clientIds){
                    // get clients in gym
                    $this->gymdb->init($this->getGymDb($gym_id));
                    $clients = $this->db->select('users_cards.card_id, users_data.*')->from('users_cards')->where_in('users_cards.card_id', array_unique($clientIds))->join('users_data', 'users_cards.user_id = users_data.user_id')->get()->result();
                    foreach ($clients as $k=>$v){
                        $clients[$v->card_id]=$clients[$k]; unset($clients[$k]);
                    }

                    // pass user data into occupation
                    foreach ($occupation as $readerId => $clientIds){ 
                        foreach ($clientIds as $clientId => $data){
                            $occupation[$readerId][$clientId]['user_data'] = $clients[$clientId];
                            //$occupation[$readerId][$clientId]['row'] = preg_replace('/\s+/S', " ", $this->load->view('/admin/dashboard/user_checkin_row',$occupation[$readerId][$clientId], true));
                            $occupation[$readerId][$clientId] = preg_replace('/\s+/S', " ", $this->load->view('/admin/dashboard/user_checkin_row',$occupation[$readerId][$clientId], true));
                        }
                    }
                }
            }

            foreach($result as $row){
                $row->delete_url = base_url('admin/gyms/remove_room_ajax/' . $row->id . "/" . $gym_id);
                $row->edit_url = base_url('admin/gyms/edit_room_ajax/' . $row->id . "/" . $gym_id);

                $row->settings = $this->getGymRoomSettings($row->id, $gym_id); // room settings
                
                if($current_occupancy) $row->occupation=@$occupation[$row->reader_id]; // room occupation
            }
        }

        if($s2){
            return $result;
        }else{
            $reply["data"] = $result;
            if($limit != NULL) $reply['last_page'] = ceil( $countRes / $limit );
            return $reply;
        }
    }

    public function getGymSolariums($gym_id = FALSE, $s2 = FALSE){
        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        if($gym_id>0) $this->gymdb->init($this->getGymDb($gym_id)); // particular db
        $this->db->select('s.*,l.date_created as last_maintenance,
            (select coalesce(sum(duration),0) from solariums_usage u where u.solarium_id=s.id and u.date_created>l.date_created) as used
            ')->from('solariums s')
            ->join('solariums_maintenance_log l','l.solarium_id=s.id and l.id=(select max(id) from solariums_maintenance_log where solarium_id=s.id)','LEFT');;

        if($sorter){
            foreach($sorter as $s){
                switch($s['field']){
                    case 'used': $this->db->order_by('used', $s['dir']); break;
                    case 'last_maintenance': $this->db->order_by('l.date_created', $s['dir']); break;                                                
                    default: $this->db->order_by('s.'.$s['field'], $s['dir']); break;
                }   
            }
        }
        if($filter){
            foreach($filter as $f){
                switch($f['field']){
                    case 'usage_minutes_limit': $this->db->like('s.'.$f["field"], $f['value']*60); break; 
                    case 'last_maintenance': $this->db->like('l.date_created', $f['value']); break;                                                
                    default: $this->db->like('s.'.$f["field"], $f['value']); break;  
                }   
            }
        }

        $countRes = $this->db->count_all_results(null,FALSE);

        if($page != NULL && $limit != NULL){
            $offset = $page > 1 ? $offset = ($page - 1) * $limit : 0;
            $this->db->limit($limit, $offset);
        }

        $result = $this->db->get()->result();

        if($result){
            foreach($result as $row){
                $row->edit_url = base_url('admin/gyms/edit_solarium_ajax/' . $row->id) . "/" . $gym_id;
                $row->maintenance_url = base_url('admin/gyms/add_solarium_maintenance_ajax/' . $row->id) . "/" . $gym_id;
                $row->logs_url = base_url('admin/gyms/get_solarium_logs/' . $row->id) . "/" . $gym_id;
            }
        }

        if($s2){
            return $result;
        }else{
            $reply["data"] = $result;
            if($limit != NULL) $reply['last_page'] = ceil( $countRes / $limit );
            return $reply;
        }
    }   

    public function getGymSolariumsUsage($gym_id = FALSE){
        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        if($gym_id>0) $this->gymdb->init($this->getGymDb($gym_id)); // particular db
        $this->db->select('*')->from('solariums_usage u')
            ->join('solariums s','s.id=u.solarium_id');

        if($sorter){
            foreach($sorter as $s){
                switch($s['field']){
                    case 'name': $this->db->order_by('s.name', $s['dir']); break;                                                
                    default: $this->db->order_by('u.'.$s['field'], $s['dir']); break;
                }   
            }
        }
        if($filter){
            foreach($filter as $f){
                switch($f['field']){
                    case 'name': $this->db->like('s.'.$f["field"], $f['value']); break;                                               
                    default: $this->db->like('u.'.$f["field"], $f['value']); break;  
                }   
            }
        }

        $countRes = $this->db->count_all_results(null,FALSE);

        if($page != NULL && $limit != NULL){
            $offset = $page > 1 ? $offset = ($page - 1) * $limit : 0;
            $this->db->limit($limit, $offset);
        }

        $result = $this->db->get()->result();
        $reply["data"] = $result;
        if($limit != NULL) $reply['last_page'] = ceil( $countRes / $limit );
        return $reply;
    } 
    
    public function getGymSolariumsMaintenance($gym_id = FALSE){
        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        if($gym_id>0) $this->gymdb->init($this->getGymDb($gym_id)); // particular db
        $this->db->select('*,concat(u.last_name,\' \',u.first_name) as full_name')->from('solariums_maintenance_log l')
            ->join('solariums s','s.id=l.solarium_id')
            ->join('users_data u','u.id=l.created_by');  

        if($sorter){
            foreach($sorter as $s){
                switch($s['field']){
                    case 'name': $this->db->order_by('s.name', $s['dir']); break;   
                    case 'full_name': $this->db->order_by('concat(u.last_name,\' \',u.first_name)', $s['dir']); break;                                              
                    default: $this->db->order_by('l.'.$s['field'], $s['dir']); break;
                }   
            }
        }
        if($filter){
            foreach($filter as $f){
                switch($f['field']){
                    case 'name': $this->db->like('s.'.$f["field"], $f['value']); break;     
                    case 'full_name': $this->db->like('concat(u.last_name,\' \',u.first_name)', $f['value']); break;                                          
                    default: $this->db->like('l.'.$f["field"], $f['value']); break;  
                }   
            }
        }

        $countRes = $this->db->count_all_results(null,FALSE);

        if($page != NULL && $limit != NULL){
            $offset = $page > 1 ? $offset = ($page - 1) * $limit : 0;
            $this->db->limit($limit, $offset);
        }

        $result = $this->db->get()->result();
        $reply["data"] = $result;
        if($limit != NULL) $reply['last_page'] = ceil( $countRes / $limit );
        return $reply;
    }      
    

    public function getAllEquipment($s2 = false){
        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $this->db->from('gym_equipment_items');

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

    public function saveGymEquipment($type){
        $equipment = isset($_POST['equipment']) ? $_POST['equipment'] : [];

        $equipment = array_map(function($row) use ($type) {
            return ['type' => $type, 'equipment_id' => $row];
        }, $equipment);

        $this->db->trans_start();
        $this->db->delete('gym_equipment',['type' => $type]);
        if(!empty($equipment)) $this->db->insert_batch('gym_equipment',$equipment);
        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    }     
    
    public function getGymEquipment($type){
        return $this->db->select('gym_equipment.*,name as equipment_name')->from('gym_equipment')
                        ->join('gym_equipment_items', 'gym_equipment_items.id = gym_equipment.equipment_id','left')
                        ->where('type', $type)->get()->result_array();
    }    
    
    public function saveEquipment(array $data){       
        $this->db->trans_start();
        if($data['item_id']>0) $this->db->update('gym_equipment_items',['name' => $data['item_name']],['id' => $data['item_id']]);
        else $this->db->insert('gym_equipment_items',['name' => $data['item_name']]);
        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    }
    public function deleteEquipment(array $data){  
        $this->db->trans_start();
        $this->db->delete('gym_equipment', ['equipment_id' => $data['item_id']]);
        $this->db->delete('gym_equipment_items', ['id' => $data['item_id']]);
        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    }    
}
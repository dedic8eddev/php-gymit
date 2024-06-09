<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Lessons_model extends CI_Model
{
    public function __construct(){
        $this->load->model('payments_model', 'payments');
        $this->gymdb->init(get_db());

        // reservation status texts
        $this->retMsg = [
            'missing_card' => ['backend' => 'Zákazník nemá spárovanou klientskou kartu', 'frontend' => 'Pro online rezervaci je nejdříve potřeba registrace na pobočce'],
            'ended_lesson' => ['backend' => 'Lekce již proběhla', 'frontend' => 'Tato lekce již proběhla'],
            'not_enough_credit' => ['backend' => 'Nedostatek kreditu pro rezervační poplatek', 'frontend' => 'Nemáte dostatek kreditu pro rezervaci lekce'],
            'success_lesson_reservation_refund' => ['backend' => 'Rezervační poplatek byl vrácen', 'frontend' => 'Rezervace byla úspěšně zrušena. Rezervační poplatek byl vrácen'],
            'success_lesson_reservation_without_refund' => ['backend' => 'Rezervace zrušena', 'frontend' => 'Rezervace byla úspěšně zrušena.'],
            'success_lesson_reservation_pay' => ['backend' => 'Úspěšné zaplacení rezervačního poplatku', 'frontend' => 'Rezervace proběhla v pořádku.'],
            'success_lesson_reservation_without_pay' => ['backend' => 'Úspěšná rezervace bez zaplacení rezervačního poplatku', 'frontend' => 'Rezervace proběhla v pořádku.'],
            'error_refund_less_than_Xhrs' => ['backend' => 'Rezervační poplatek nebyl vrácen z důvodu zrušení méně než '.config_item('app')['lesson_reservervation_refund_hours'].' hodin před konáním lekce', 'frontend' => 'Rezervace byla úspěšně zrušena. Rezervační poplatek nebyl vrácen z důvodu zrušení méně než '.config_item('app')['lesson_reservervation_refund_hours'].' hodin před konáním lekce'],
            'overlimit' => ['backend' => 'Nadlimitní registrace', 'frontend' => 'Rezervace se nepovedla z důvodu naplnění kapacity lekce']
        ];   
    }

    /**
     * Get data for the lesson calendar
    */
    public function getCalendarData($start, $end){
        $this->gymdb->init(get_db());
        $this->db->select('lessons.*, lt.name, lt.description, lt.client_limit')->from('lessons');
        $this->db->join('lessons_templates lt', 'lt.id = lessons.template_id'); // template
        $this->db->where('lessons.starting_on <=', $end);
        $data = $this->db->get()->result();

        if($data){
            return $data;
        }else{
            return FALSE;
        }
    }

        /**
     * Get data for the lesson template table
     */
    public function getTableTemplates($s2 = false){

        $g = $_GET; $reply = [];

        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $this->gymdb->init(get_db());
        $this->db->select("lt.*, m.file photo_src, m.meta_tags photo_meta,
                CONCAT('[',GROUP_CONCAT(CONCAT('\"',t.tag_id),'\"'),']') as tags
                ")->from('lessons_templates lt')
                ->join('media m','lt.photo = m.id','LEFT')
                ->join('lessons_templates_tags t','lt.id = t.lesson_id','LEFT')
                ->group_by('lt.id');

        if($sorter){
            foreach($sorter as $s){
                $order_field = $s['field'];
                $direction = $s['dir'];

                $this->db->order_by('lessons_templates.'.$order_field, $direction);
            }
        }
        if($filter){
            foreach($filter as $f){
                $fieldname = 'lessons_templates.'.$f["field"];
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
     * Get data for the lesson table
     */
    public function getTableEvents(){

        $g = $_GET; $reply = [];

        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $start = date('Y-m-d');
        $end = date('Y-m-d', strtotime('+30 days'));

        $this->gymdb->init(get_db());
        $this->db->select('lessons.*, lessons_templates.name, lessons_templates.description')->from('lessons');
        $this->db->join('lessons_templates', 'lessons_templates.id = lessons.template_id', 'left'); // template

        if(!$filter){
            $this->db->where('starting_on <=', $end);
        }else{
            foreach($filter as $f){
                if($f["field"] == "ending_on"){
                    $end = $f['value'];
                    $this->db->where('starting_on <=', $end);
                }
                if($f["field"] == "starting_on"){
                    $start = $f['value'];
                }
            }
        }

        if($sorter){
            foreach($sorter as $s){
                $order_field = $s['field'];
                $direction = $s['dir'];

                $this->db->order_by('lessons.'.$order_field, $direction);
            }
        }
        if($filter){
            foreach($filter as $f){
                if($f["field"] == "starting_on"){
                    $this->db->where('starting_on >=', $f['value'])
                             ->where('repeating', NULL)
                                ->or_where('repeating !=', NULL)
                                ->where('starting_on >=', $f['value'])
                                    ->or_where('repeating !=', NULL)
                                    ->where('starting_on <=', $f['value'])
                                    ->where('repeating_end', NULL);
                }else if($f["field"] == "ending_on"){
                    $this->db->where('ending_on <=', $f['value'])
                             ->where('repeating', NULL)
                                ->or_where('repeating !=', NULL)
                                ->where('repeating_end <=', $f['value'])
                                    ->or_where('repeating_end', NULL)
                                    ->where('repeating !=', NULL)
                                    ->where('ending_on >=', $f['value']);
                }else{
                    $fieldname = 'lessons.'.$f["field"];
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

        $result = $this->db->get()->result_array();
        $reply["data"] = $result;

        foreach($reply["data"] as $i => $row){

            if($row['repeating'] && !$row['repeating_end']){

                $datediff = strtotime(date('Y-m-d', strtotime('+3 years'))) - strtotime($row['starting_on']);
                $until = ( ( round($datediff / (60 * 60 * 24)) ) / $row['repeating'] ) - 1;

                for($x = 1; $x <= $until; $x++){

                    $start_date = strtotime($row['starting_on'] . '+' . ($row['repeating'] * $x) . 'DAYS');
                    $end_date = strtotime($row['ending_on'] . '+' . ($row['repeating'] * $x) . 'DAYS');
                    $s = date("Y-m-d H:i", $start_date);
                    $e = date("Y-m-d H:i", $end_date);

                    if(date('Y-m-d', strtotime($s)) > $end){
                        // Skip for dates further in future than the filter
                        break;
                    }

                    if($row["canceled"]){
                        continue; // skip cancelled events
                    }

                    $recurring = [
                        'id' => $row['id'],
                        'name' => $row['name'],
                        'description' => $row['description'],
                        'allDay' => ($row['all_day']) ? $row['all_day'] : FALSE,
                        'starting_on' => $s,
                        'ending_on' => $e,
                        'client_count' => $this->lessons->getClientCount($row['id'], date('Y-m-d', strtotime($s))),
                        'teacher_count' => $this->lessons->getTeachersCount($row['id'])
                    ];

                    if(date('Y-m-d', strtotime($e)) >= $start && date('Y-m-d', strtotime($s)) <= $end){
                        $reply['data'][] = $recurring;
                        $countRes++;
                    }
                }

                if(date('Y-m-d', strtotime($row['ending_on'])) < $start OR date('Y-m-d', strtotime($row['starting_on'])) > $end){
                    array_splice($reply["data"], $i, 1);
                }else{
                    $reply['data'][$i]['client_count'] = $this->lessons->getClientCount($row['id'], $row['ending_on']);
                    $reply['data'][$i]['teacher_count'] = $this->lessons->getTeachersCount($row['id'], $row['ending_on']);
                }

            }elseif($row['repeating'] && $row['repeating_end']){
                // repeat until .. get difference in days and divide by interval

                $interval = $row['repeating'];
                $repeat_end = $row['repeating_end'];

                $datediff = strtotime($repeat_end) - strtotime($row['starting_on']);
                $until = ( ( round($datediff / (60 * 60 * 24)) ) / $interval ) - 1;
    
                $reply['data'][$i]['client_count'] = $this->lessons->getClientCount($row['id']);
                $reply['data'][$i]['teacher_count'] = $this->lessons->getTeachersCount($row['id']);
                    
                for($x = 0; $x < $until; $x++){
                        $start_date = strtotime($row['starting_on'] . '+' . $interval . 'DAYS');
                        $end_date = strtotime($end . '+' . $interval . 'DAYS');
                        $s = date("Y-m-d H:i", $start_date);
                        $e = date("Y-m-d H:i", $end_date);
    
                        if($row["canceled"]){
                            continue; // skip cancelled events
                        }

                        $recurring = [
                            'id' => $row['id'],
                            'name' => $row['name'],
                            'description' => $row['description'],
                            'allDay' => ($row['all_day']) ? $row['all_day'] : FALSE,
                            'starting_on' => $s,
                            'ending_on' => $e,
                            'client_count' => $this->lessons->getClientCount($row['id'], date('Y-m-d', strtotime($s))),
                            'teacher_count' => $this->lessons->getTeachersCount($row['id'])
                        ];
    
                        if(date('Y-m-d', strtotime($e)) >= $start && date('Y-m-d', strtotime($e)) <= $end){
                            $reply['data'][] = $recurring;
                            $countRes++;
                        }
                    }

            }else{
                if(date('Y-m-d', strtotime($row['ending_on'])) < $start OR date('Y-m-d', strtotime($row['starting_on'])) > $end){
                    array_splice($reply["data"], $i, 1);
                }else{
                    $reply['data'][$i]['client_count'] = $this->lessons->getClientCount($row['id']);
                    $reply['data'][$i]['teacher_count'] = $this->lessons->getTeachersCount($row['id']);
                }
            }
        }

        if($limit != NULL) $reply['last_page'] = ceil( $countRes / $limit );
        return $reply;
    }

    /**
     * Delete an image from a lesson
     */
    public function deleteEventImage(){
        $p = $_POST;
        $lesson_id = $p['lesson_id'];

        $lesson = $this->getEvent($lesson_id);
        if($lesson){
            if($lesson->photo){

                $this->gymdb->init(get_db());
                if($this->db->where('id', $lesson_id)->update('lessons', ['photo' => NULL])){
                    unlink(config_item('app')['lessons_images'] . $lesson->photo);
                    return TRUE;
                }else{
                    return FALSE;
                }

            }else{
                return TRUE;
            }
        }else{
            return FALSE;
        }
    }

    /**
     * Get single event
     * @param int $id event id, if not supplied _POST is used
     */
    public function getEvent($id = FALSE, $date = FALSE){
        if(!$id) $id = $_POST['lesson_id'];
        if(!$date) $date = (isset($_POST["date"])) ? $_POST["date"] : FALSE;

        $this->gymdb->init(get_db());
        $lesson = $this->db->where("id", $id)->get("lessons")->row();
        $lesson_template = $this->db->where('id', $lesson->template_id)->get('lessons_templates')->row();

        if(date('Y-m-d', strtotime($lesson->ending_on)) != $date){
            $lesson->clients = $this->getLessonsClients($id, date('Y-m-d', strtotime($date)));
            $lesson->vipClients = $this->getLessonsVIPClients($id, date('Y-m-d', strtotime($date)));
            $lesson->teachers = $this->getLessonsTeachers($id);
            $lesson->recurring = TRUE; // recurring flag
        }else{
            $lesson->clients = $this->getLessonsClients($id, date('Y-m-d', strtotime($lesson->ending_on)));
            $lesson->vipClients = $this->getLessonsVIPClients($id, date('Y-m-d', strtotime($lesson->ending_on)));
            $lesson->teachers = $this->getLessonsTeachers($id);
        }

        // Use template teachers if no teachers assigned
        if(!$lesson->teachers){
            $lesson->teachers = $this->getTemplateTeachers($lesson->template_id);
        }

        $lesson->photo_url = '';
        if($lesson_template->photo){
            $lesson->photo_url = base_url() . config_item('app')['lessons_images'] . $lesson_template->photo;
        }

        $lesson->name = $lesson_template->name;
        $lesson->description = $lesson_template->description;
        $lesson->text = $lesson_template->text;

        if($lesson){
            return $lesson;
        }else{
            return FALSE;
        }
    }

    /**
     * Get clients assigned to a lesson in array form [x,x,x]
     */
    public function getLessonsClients($lesson_id, $date = NULL){
        $this->gymdb->init(get_db());
       
        $this->db->where('lesson_id', $lesson_id)->where('(state is null or state != 3)')->where('vip', false);
        if($date) $this->db->where('lesson_date',$date);
        
        $clients = $this->db->get('lessons_clients')->result();

        if(!empty($clients)){
            $ids = [];
            foreach($clients as $client){
                $ids[] = $client->client_id;
            }
            return $ids;
        }else{
            return FALSE;
        }
    }

    /**
     * Get clients assigned to a lesson in array form [x,x,x]
     */
    public function getLessonsVIPClients($lesson_id, $date = NULL){
        $this->db->select('ud.first_name, ud.last_name, lc.note')
            ->from('lessons_clients lc')
            ->join('users_data ud','ud.user_id=lc.client_id')
            ->where(['lesson_id' => $lesson_id, 'vip' => true])->where('(state is null or state != 3)');
        if($date) $this->db->where('lesson_date',$date);

        $clients = $this->db->get()->result();
        return $clients;
    }    

    /**
     * Get clients assigned to a lesson in array form [x,x,x]
     */
    public function getLessonsTeachers($lesson_id){
        $this->gymdb->init(get_db());
        $teachers = $this->db->where('lesson_id', $lesson_id)->get('lessons_teachers')->result();
        if(!empty($teachers)){
            $ids = [];
            foreach($teachers as $teacher){
                $ids[] = $teacher->teacher_id;
            }

            return $ids;
        }else{
            return FALSE;
        }
    }

    /**
     * Get teeachers assigned to template
     */
    public function getTemplateTeachers($template_id, $detailed=FALSE){
        $this->gymdb->init(get_db());
        $this->db->where('template_id', $template_id)->from('lessons_templates_teachers');
        
        if($detailed){
            $this->db->select("users_data.id, media.file photo_src, media.meta_tags photo_meta, first_name, last_name, quote, about,
                CONCAT('[',GROUP_CONCAT(CONCAT('\"',coach_specializations_items.name),'\"'),']') as specializations")
                    ->join('coach_data','coach_data.coach_id = lessons_templates_teachers.teacher_id','LEFT')
                    ->join('coach_specializations','coach_specializations.coach_id = coach_data.coach_id','LEFT')
                    ->join('coach_specializations_items','coach_specializations_items.id = coach_specializations.specialization_id','LEFT')
                    ->join('users_data','users_data.user_id = lessons_templates_teachers.teacher_id')
                    ->join('media','media.id = users_data.photo','LEFT')
                    ->group_by(['users_data.user_id', 'user_data.id']);
            return $this->db->get()->result();
        } else $teachers = $this->db->get()->result();

        if(!empty($teachers)){
            $ids = [];
            foreach($teachers as $teacher){
                $ids[] = $teacher->teacher_id;
            }

            return $ids;
        }else{
            return FALSE;
        }
    }

    /**
     * Get count of clients in a particular lesson
     */
    public function getClientCount($lesson_id, $date = NULL){
        $this->gymdb->init(get_db());
            if($date){
                $clients = $this->db->where('lesson_date', $date)->where('lesson_id', $lesson_id)->count_all_results('lessons_clients');
            }else{
                $clients = $this->db->where('lesson_id', $lesson_id)->count_all_results('lessons_clients');
            }
        return $clients;
    }
    public function getTeachersCount($lesson_id){
        $this->gymdb->init(get_db());
        $teachers = $this->db->where('lesson_id', $lesson_id)->count_all_results('lessons_teachers');
        return $teachers;
    }

    /**
     * Is lesson being substituted by another teacher??
     */
    public function isSubstituteTeacher($lesson_id){
        $this->gymdb->init(get_db());
        $teachers = $this->db->where('lesson_id', $lesson_id)->where("teacher_substitute !=", NULL)->get("lessons_teachers")->result();
        if($teachers) return TRUE;
        else return FALSE;
    }

    /**
     * Get a single template by ID
     */
    public function getTemplate($id = FALSE){
        if(!$id) $id = $_POST['template_id'];

        $this->gymdb->init(get_db());
        $template = $this->db->select('lessons_templates.*, media.file photo_src, media.meta_tags photo_meta')->from('lessons_templates')->join('media','lessons_templates.photo = media.id','LEFT')->where('lessons_templates.id', $id)->get()->row();

        if($template){
            $template->teachers = $this->getTemplateTeachers($id);
            return $template;
        } else return FALSE;
    }

    /**
     * Add a lesson template to be used with lessons
     */
    public function addTemplate(){
        $p = $_POST;

        $teachers = (isset($p['teachers'])) ? $p['teachers'] : []; unset($p['teachers']);
        $tags = isset($p['tags']) ? $p['tags'] : []; unset($p['tags']);

        $this->gymdb->init(get_db());
        if($this->db->insert("lessons_templates", $p)){

            $template_id = $this->db->insert_id();

            if(!empty($teachers)){
                foreach($teachers as $teacher_id){
                    if(strlen($teacher_id) <= 0) continue;
                    $this->gymdb->init(get_db());
                    $this->db->insert('lessons_templates_teachers', ['teacher_id' => $teacher_id, 'template_id' => $template_id]);
                }
            }

            if(!empty($tags)){
                $tags = array_map(function($row) use ($template_id) {
                    return ['lesson_id' => $template_id, 'tag_id' => $row];
                }, $tags);   
                $this->db->insert_batch('lessons_templates_tags',$tags);   
            }      

            return $template_id;
        } else return FALSE;
    }

    /**
     * Save a lesson template
     */
    public function saveTemplate(){
        $p = $_POST;

        $template_id = $p['template_id']; unset($p['template_id']);
        $teachers = (isset($p['teachers'])) ? $p['teachers'] : []; unset($p['teachers']);
        $tags = isset($p['tags']) ? $p['tags'] : []; unset($p['tags']);

        $this->gymdb->init(get_db());

        if($this->db->where('id', $template_id)->update("lessons_templates", $p)){

            if(!empty($teachers)){
                $this->gymdb->init(get_db());
                $this->db->where('template_id', $template_id)->delete('lessons_templates_teachers');
                foreach($teachers as $teacher_id){
                    if(strlen($teacher_id) <= 0) continue;
                    $this->gymdb->init(get_db());
                    $this->db->insert('lessons_templates_teachers', ['teacher_id' => $teacher_id, 'template_id' => $template_id]);
                }
            }

            if(!empty($tags)){
                $tags = array_map(function($row) use ($template_id) {
                    return ['lesson_id' => $template_id, 'tag_id' => $row];
                }, $tags);   
                $this->db->where('lesson_id', $template_id)->delete('lessons_templates_tags');
                $this->db->insert_batch('lessons_templates_tags',$tags);   
            }               

            return TRUE;
        } else return FALSE;
    }

    /**
     * Remove a template
     */
    public function deleteTemplate($id = FALSE){
        if(!$id) $id = $_POST["template_id"];

        if($template = $this->getTemplate($id)){
            $this->gymdb->init(get_db());
            
            $this->db->trans_start();
            $this->db->where('template_id', $id)->delete('lessons_templates_teachers');
            $this->db->where('lesson_id', $id)->delete('lessons_templates_tags');
            $this->db->where('id', $id)->delete('lessons_templates');
            $this->db->trans_complete();

            if($this->db->trans_status()) return true;
            else return false;

        }else{
            return FALSE;
        }
    }

    /**
     * Submit an event into the DB
     */
    public function addEvent(){
        $p = $_POST;
        $f = $_FILES;

        $template = $p['template_id'];
        $client_limit = $p['client_limit'];

        $from = $p['time_from']; // separate times
        $to = $p['time_to']; // separate times
        $start = date("Y-m-d ".$from, strtotime($p["starting_on"]));
        $end = date("Y-m-d ".$to, strtotime($p["ending_on"]));
    
        $repeats = $p["repeating"];
        $allday = $p["all_day"];

        $clients = (isset($p['clients'])) ? explode(",", $p['clients'][0]) : [];
        $teachers = (isset($p['teachers'])) ? explode(',', $p['teachers'][0]) : [];

        $insert = array(
            "created_by" => gym_userid(),
            "starting_on" => $start,
            "ending_on" => $end,
            "all_day" => $allday,
            'template_id' => $template
        );

        if($repeats == "true"){
            $interval = $p["repeat_freq"];

            if(!$p["repeating_end"]){
                $repeat_end = false;
            }else{
                $repeat_end = date("Y-m-d H:i", strtotime($p["repeating_end"]));
            }

            $insert["repeating"] = $interval;

            if(!$repeat_end){
                $insert["repeating_end"] = NULL;
                $this->gymdb->init(get_db());
                if($this->db->insert("lessons", $insert)){
    
                    $lesson_id = $this->db->insert_id();
                    
                    if(!empty($teachers)){
                        foreach($teachers as $teacher_id){
                            if(strlen($teacher_id) <= 0) continue;
                            $this->gymdb->init(get_db());
                            $this->db->insert('lessons_teachers', ['teacher_id' => $teacher_id, 'lesson_id' => $lesson_id]);
                        }
                    }
    
                    if(!empty($clients)){
                        $ret['clients'][$start] = $this->processLessonClients($lesson_id,$start,$end,$client_limit,$clients);
                        $ret['registered_clients'][$start] = $ret['clients'][$start]['registered_clients']; unset($ret['clients'][$start]['registered_clients']);
                    }
    
                    return $ret;
                }else{
                    return FALSE;
                }
            }else{
                $insert["repeating_end"] = $repeat_end;

                // repeat until .. get difference in days and divide by interval
                $datediff = strtotime($repeat_end) - strtotime($start);
                $until = ( ( round($datediff / (60 * 60 * 24)) ) / $interval ) - 1;

                

                $this->gymdb->init(get_db());
                if($this->db->insert("lessons", $insert)){
                    
                    $parent_id = $this->db->insert_id();

                    // INSERT teachers+clients for the parent lesson
                    if(!empty($teachers)){
                        foreach($teachers as $teacher_id){
                            if(strlen($teacher_id) <= 0) continue;
                            $this->gymdb->init(get_db());
                            $this->db->insert('lessons_teachers', ['teacher_id' => $teacher_id, 'lesson_id' => $parent_id]);
                        }
                    }
                    
                    if(!empty($clients)){
                        $ret['clients'][$start] = $this->processLessonClients($parent_id,$start,$end,$client_limit,$clients);
                        $ret['registered_clients'][$start] = $ret['clients'][$start]['registered_clients']; unset($ret['clients'][$start]['registered_clients']);
                    }

                    $insert["parent_lesson"] = $parent_id;
                    for($x = 0; $x < $until; $x++){
                        $start_date = strtotime($start . '+' . $interval . 'DAYS');
                        $end_date = strtotime($end . '+' . $interval . 'DAYS');
    
                        $start = date("Y-m-d H:i", $start_date);
                        $end = date("Y-m-d H:i", $end_date);
    
                        $insert["starting_on"] = $start;
                        $insert["ending_on"] = $end;
    
                        $this->gymdb->init(get_db());
                        $this->db->insert("lessons", $insert);
    
                        $lesson_id = $this->db->insert_id();
                        
                        if(!empty($teachers)){
                            foreach($teachers as $teacher_id){
                                if(strlen($teacher_id) <= 0) continue;
                                $this->gymdb->init(get_db());
                                $this->db->insert('lessons_teachers', ['teacher_id' => $teacher_id, 'lesson_id' => $lesson_id]);
                            }
                        }
                        
                        if(!empty($clients)){
                            $ret['clients'][$start] = $this->processLessonClients($lesson_id,$start,$end,$client_limit,$clients);
                            $ret['registered_clients'][$start] = $ret['clients'][$start]['registered_clients']; unset($ret['clients'][$start]['registered_clients']);
                        }
    
                        if(($x + 1) >= $until){
                            return $ret;
                        }
                    }
                    return $ret; // in case of 'for' loop does not run
                }else{
                    return FALSE;
                }
            }
        }else{
            $this->gymdb->init(get_db());
            if($this->db->insert("lessons", $insert)){

                $lesson_id = $this->db->insert_id();
                
                if(!empty($teachers)){
                    foreach($teachers as $teacher_id){
                        if(strlen($teacher_id) <= 0) continue;
                        $this->gymdb->init(get_db());
                        $this->db->insert('lessons_teachers', ['teacher_id' => $teacher_id, 'lesson_id' => $lesson_id]);
                    }
                }

                if(!empty($clients)){
                    foreach($clients as $client_id){
                        if(strlen($client_id) <= 0) continue;
                        $this->gymdb->init(get_db());
                        $this->db->insert('lessons_clients', ['client_id' => $client_id, 'lesson_id' => $lesson_id]);
                    }
                }

                return TRUE;
            }else{
                return FALSE;
            }
        }

    }

    /** Cancel a single repeating event */
    public function cancelEvent($lesson_id){
        $this->gymdb->init(get_db());
        if($this->db->where("id", $lesson_id)->update('lessons', ["canceled" => TRUE])){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    
    /**
     * Submit an event into the DB
     */
    public function saveEvent(){
        $p = $_POST;
        $f = $_FILES;

        $ret = [];

        $template = $p['template_id'];
        $client_limit = $p['client_limit'];

        $from = $p['time_from']; // separate times
        $to = $p['time_to']; // separate times
        $start = date("Y-m-d ".$from, strtotime($p["starting_on"]));
        $end = date("Y-m-d ".$to, strtotime($p["ending_on"]));
        $allday = $p["all_day"];

        $req_lesson_id = $p["lesson_id"];

        $clients = (isset($p['clients'])) ? explode(",", $p['clients'][0]) : [];
        $vipClients = (isset($p['vip_clients'])) ? $p['vip_clients'] : [];
        $teachers = (isset($p['teachers'])) ? explode(',', $p['teachers'][0]) : [];

        $update = array(
            "starting_on" => $start,
            "ending_on" => $end,
            "all_day" => $allday,
            'template_id' => $template
        );

        $this->gymdb->init(get_db());
        $lesson = $this->db->where('id', $req_lesson_id)->get('lessons')->row();
        if( date('Y-m-d', strtotime($lesson->ending_on)) != date('Y-m-d', strtotime($end)) ){
            // Repeating event with the id of an original event
            if(date('D', strtotime($end)) != date('D', strtotime($lesson->ending_on))){
                // Event is being moved to another day
                // Cancel this repeating iteration and create a new event..
                if($this->cancelEvent($req_lesson_id)){

                    $insert = array(
                        "created_by" => gym_userid(),
                        "starting_on" => $start,
                        "ending_on" => $end,
                        "all_day" => $allday
                    );

                    $this->gymdb->init(get_db());
                    if($this->db->insert("lessons", $insert)){
        
                        $lesson_id = $this->db->insert_id();
                        
                        if(!empty($teachers)){
                            foreach($teachers as $teacher_id){
                                if(strlen($teacher_id) <= 0) continue;
                                $this->gymdb->init(get_db());
                                $this->db->insert('lessons_teachers', ['teacher_id' => $teacher_id, 'lesson_id' => $lesson_id]);
                            }
                        }
        
                        $ret['clients'] = $this->processLessonClients($req_lesson_id,$start,$end,$client_limit,$clients);
                        $ret['registered_clients'] = $ret['clients']['registered_clients']; unset($ret['clients']['registered_clients']);
                        $ret['clients'] = array_merge($ret['clients']??[],$this->processLessonVIPClients($req_lesson_id,$start,$end,$client_limit,$vipClients,$ret['registered_clients']));
        
                        return TRUE;
                    }else{
                        return FALSE;
                    }

                }else{
                    return FALSE;
                }
            }else{
                // The day is not changing
                unset($update['starting_on']);
                unset($update['ending_on']);

                $this->gymdb->init(get_db());
                if($this->db->where('id', $req_lesson_id)->update("lessons", $update)){

                    if(!empty($teachers)){
                        $this->gymdb->init(get_db());
                        $this->db->where('lesson_id', $req_lesson_id)->delete('lessons_teachers');
                        foreach($teachers as $teacher_id){
                            if(strlen($teacher_id) <= 0) continue;
                            $this->gymdb->init(get_db());
                            $this->db->insert('lessons_teachers', ['teacher_id' => $teacher_id, 'lesson_id' => $req_lesson_id]);
                        }
                    }
    
                    $ret['clients'] = $this->processLessonClients($req_lesson_id,$start,$end,$client_limit,$clients);
                    $ret['registered_clients'] = $ret['clients']['registered_clients']; unset($ret['clients']['registered_clients']);
                    $ret['clients'] = array_merge($ret['clients']??[],$this->processLessonVIPClients($req_lesson_id,$start,$end,$client_limit,$vipClients,$ret['registered_clients']));
    
                    return $ret;
                }else{
                    return FALSE;
                }
            }

        }else{
            // Actual event
            $this->gymdb->init(get_db());
            if($this->db->where('id', $req_lesson_id)->update("lessons", $update)){

                if(!empty($teachers)){
                    $this->gymdb->init(get_db());
                    $this->db->where('lesson_id', $req_lesson_id)->delete('lessons_teachers');
                    foreach($teachers as $teacher_id){
                        if(strlen($teacher_id) <= 0) continue;
                        $this->gymdb->init(get_db());
                        $this->db->insert('lessons_teachers', ['teacher_id' => $teacher_id, 'lesson_id' => $req_lesson_id]);
                    }
                }

                $ret['clients'] = $this->processLessonClients($req_lesson_id,$start,$end,$client_limit,$clients);
                $ret['registered_clients'] = $ret['clients']['registered_clients']; unset($ret['clients']['registered_clients']);
                $ret['clients'] = array_merge($ret['clients']??[],$this->processLessonVIPClients($req_lesson_id,$start,$end,$client_limit,$vipClients,$ret['registered_clients']));

                return $ret;
            }else{
                return FALSE;
            }
        }
    }

    /**
     * Deletes single event
     * @param int $id if not supplied, POST is used
     * @param date date if supplied cancels a particular recurring event on that date (get from post)
     */
    public function deleteEvent($id = FALSE, $date = FALSE){
        if(!$id) $id = $_POST["lesson_id"];
        if(isset($_POST['lesson_date'])) $date = $_POST['lesson_date'];

        if($lesson = $this->getEvent($id)){

            if($date){
                // only cancel
                if($this->cancelEvent($id)){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }else{
                $this->gymdb->init(get_db());
                if($this->db->where('id', $id)->delete('lessons')){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }

        }else{
            return FALSE;
        }
    }

    /**
     * Deletes all instances of a repeated event by its parent
     * @param int $id if not supplied, POST is used
     */
    public function deleteAllRepeatingEvents($id = FALSE){
        if(!$id) $id = $_POST["lesson_id"];

        if($lesson = $this->getEvent($id)){

            if(!is_null($lesson->parent_lesson)){
                $this->gymdb->init(get_db());
                $lessons = $this->db->where('parent_lesson', $lesson->parent_lesson)->or_where('id', $lesson->parent_lesson)->get('lessons')->result();
            }else{
                $this->gymdb->init(get_db());
                $lessons = $this->db->where('parent_lesson', $id)->get('lessons')->result();
            }

            $ids = [];
            foreach($lessons as $l){ $ids[] = $l->id; }

            $this->gymdb->init(get_db());
            if($this->db->where_in("id", $ids)->delete("lessons")){
                return TRUE;
            }else{
                return FALSE;
            }

        }else{
            return FALSE;
        }
    }

    /**
     * Get coming lessons for front
    */
    public function getComingLessons($id){
        $this->gymdb->init(get_db());
        $this->db->select('lt.name, l.starting_on, l.ending_on')->from('lessons l')
                ->join('lessons_templates lt', 'lt.id = l.template_id') // template
                ->where('lt.id', $id)
                ->where('l.starting_on >= now()')
                ->order_by('l.starting_on','ASC')
                ->limit(5);
        $data['coming'] = $this->db->get()->result();

        // Every Monday, Wednesday, Friday from - to
        $this->db->select('WEEKDAY(l.starting_on) as weekday, MIN(l.starting_on) AS starting_on, MIN(l.ending_on) AS ending_on')->from('lessons l')
                ->join('lessons_templates lt', 'lt.id = l.template_id') // template
                ->where('lt.id', $id)
                ->where('l.starting_on >= now()')
                ->group_by('WEEKDAY(l.starting_on)')
                ->order_by('WEEKDAY(l.starting_on)','ASC');
        $data['period'] = $this->db->get()->result();

        if($data) return $data;
        else return FALSE;
    }

    /**
     * Get coming lessons for user/client front
     */
    public function getUpcomingClientLessons(int $userId, int $limit = 5): array
    {
        $this->db
            ->select('lt.id, lt.name, lt.client_limit, r.name as room, l.starting_on, l.ending_on, ud.first_name, ud.last_name')
            ->from('lessons l')
            ->join('lessons_templates lt', 'lt.id = l.template_id')
            ->join('rooms r', 'r.id = lt.room_id')
            ->join('lessons_clients lc', 'lc.lesson_id = l.id')
            ->join('lessons_teachers ltea', 'ltea.lesson_id = l.id')
            ->join('users_data ud', 'ud.user_id = ltea.teacher_id')
            ->where('lc.client_id', $userId)
            ->where('l.starting_on >= now()')
            ->order_by('l.starting_on','ASC')
            ->limit($limit);

        $result = $this->db->get()->result();

        return empty($result) ? [] : $result;
    }

    public function getClientLessonsHistory(int $userId): array
    {
        $g = $_GET; $reply = [];

        $page = (isset($g['page'])) ? $g['page'] : 1;
        $limit = (isset($g['size'])) ? $g['size'] : 10;

        $this->db
            ->select('lt.id, lt.name, l.starting_on, l.ending_on')
            ->from('lessons l')
            ->join('lessons_templates lt', 'lt.id = l.template_id') // template
            ->join('lessons_clients lc', 'lc.lesson_id = l.id')
            ->where('lc.client_id', $userId)
            ->where('l.starting_on >= now()')
            ->order_by('l.starting_on','ASC')
            ;

        $countRes = $this->db->count_all_results(null, false);

        if($page != null && $limit != null){
            $offset = 0;
            if($page > 1){
                $offset = ($page - 1) * $limit;
            }
            $this->db->limit($limit, $offset);
        }

        $result = $this->db->get()->result();
        $reply['data'] = $result;
        if($limit != null) $reply['last_page'] = (int) ceil( $countRes / $limit );
        $reply['count'] = $countRes;
        $reply['page'] = $page;
        return $reply;
    }
    
    public function getFrontCalendar($params){
        $this->gymdb->init(get_db());
        $this->db->select("lt.client_limit, l.id, lt.name, l.starting_on, l.ending_on,
                CONCAT('[',GROUP_CONCAT(lcl.client_id),']') as registered_clients,
                CONCAT('[',GROUP_CONCAT(CONCAT('\"',c.first_name,' ',c.last_name),'\"'),']') as coaches,
                media.file photo_src, media.meta_tags photo_meta
                ")->from('lessons l')
                ->join('lessons_templates lt', 'lt.id = l.template_id') // template
                ->join('lessons_teachers lc', 'lc.lesson_id = l.id and lc.participate=1', 'LEFT')
                ->join('users_data c', 'c.user_id = lc.teacher_id', 'LEFT')
                ->join('lessons_clients lcl', 'lcl.lesson_id = l.id and state is null', 'LEFT')
                ->join('media','media.id = lt.photo','LEFT')
                ->where('l.canceled',0)
                ->group_by('l.id')
                ->order_by('DATE_FORMAT(l.starting_on, "%H:%i") asc');

        if(@$params['hp']==1) $this->db->where('(now() between starting_on and ending_on) or (starting_on > now() and starting_on < DATE_ADD(now(), INTERVAL 4 HOUR))');
        else $this->db->where("l.starting_on BETWEEN '".$params['from']."' AND '".$params['to']."'");

        if(!empty(@$params['lesson'])) $this->db->where('lt.id',$params['lesson']);
        if(!empty(@$params['coach'])) $this->db->where('lc.teacher_id',$params['coach']);

        $calendar = $this->db->get()->result(); 

        if(@$params['wholeDay']==1){
            // calendar starts at min open hour in week and end at max close time in week
            $minOpenTime = min(new DateTime($this->gymSettings['opening_hours']['data']['monday']['from']),new DateTime($this->gymSettings['opening_hours']['data']['saturday']['from']));
            $maxCloseTime = max(new DateTime($this->gymSettings['opening_hours']['data']['monday']['to']),new DateTime($this->gymSettings['opening_hours']['data']['saturday']['to']));
            $period = new DatePeriod($minOpenTime,new DateInterval('PT1H'),$maxCloseTime );
            foreach ($period as $date) {
                for($i=1;$i<=7;$i++){ // monday - sunday
                    $data[$date->format('H')][$i]=null;
                }
            }
        }

        if(@$params['hp']==1){ // from actual lesson + 4 hours
            $minTime = min(new DateTime(),new DateTime(@$calendar[0]->starting_on));
            $maxTime = clone $minTime; $maxTime->add(new DateInterval('PT4H'));
            $period = new DatePeriod($minTime,new DateInterval('PT1H'),$maxTime);
            foreach ($period as $date) {
                $data[$date->format('H')]=[];
            }
        }

        // remap for front structure
		foreach($calendar as $c){ 
			$hour = date('H', strtotime($c->starting_on));
            $day = date('N', strtotime($c->starting_on));
            $c->registered_clients = json_decode($c->registered_clients,true) ?? [];
            $registered_clients_count = count($c->registered_clients);
            if(in_array(gym_userid(),$c->registered_clients)) $data[$hour][$day][$c->id]['registered']=1;
			$data[$hour][$day][$c->id]['name']=$c->name;
			$data[$hour][$day][$c->id]['coaches']=json_decode($c->coaches);
			$data[$hour][$day][$c->id]['starting_on']=$c->starting_on;
            $data[$hour][$day][$c->id]['ending_on']=$c->ending_on;
            $data[$hour][$day][$c->id]['photo_src']=$c->photo_src;
            $data[$hour][$day][$c->id]['photo_meta']=$c->photo_meta;
            $data[$hour][$day][$c->id]['lesson_clients']=$registered_clients_count;
            $data[$hour][$day][$c->id]['client_limit']=$c->client_limit;
            if(new DateTime() > new DateTime($c->starting_on)) $data[$hour][$day][$c->id]['box_class']='old';
            else if ($c->client_limit <= $registered_clients_count) $data[$hour][$day][$c->id]['box_class']='full';
            else $data[$hour][$day][$c->id]['box_class']=''; 
        }
        if($data) return $data;
        else return FALSE;               
    }

    // LESSONS TEMPLATES TAGS
        
    public function getAllTemplatesTags($s2 = false){
        $g = $_GET;
        $reply = [];

        // Pagination and filtering
        $page = (isset($g['page'])) ? $g['page'] : null;
        $limit = (isset($g['size'])) ? $g['size'] : null;
        $sorter = (isset($g['sorters'])) ? $g['sorters'] : false;
        $filter = (isset($g['filters'])) ? $g['filters'] : false;

        $this->db->from('lessons_templates_tag_items');

        if($sorter) foreach($sorter as $s){ $this->db->order_by($s['field'], $s['dir']); }
        if($filter) foreach($filter as $f){ $this->db->like($f["field"], $f['value']); }

        $countRes = $this->db->count_all_results(null,FALSE);

        if($page != NULL && $limit != NULL){
            $offset = $page > 1 ? $offset = ($page - 1) * $limit : 0;
            $this->db->limit($limit, $offset);
        }

        if($s2) return $this->db->get()->result();
        else{
            $ret["data"] = $this->db->get()->result();
            if($limit != NULL) $ret['last_page'] = ceil( $countRes / $limit );
            return $ret;
        }        
    }      

    public function saveTemplateTag(array $data){       
        $this->db->trans_start();
        if($data['item_id']>0) $this->db->update('lessons_templates_tag_items',['name' => $data['item_name']],['id' => $data['item_id']]);
        else $this->db->insert('lessons_templates_tag_items',['name' => $data['item_name']]);
        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    }
    public function deleteTemplateTag(array $data){  
        $this->db->trans_start();
        $this->db->delete('lessons_templates_tags', ['tag_id' => $data['item_id']]);
        $this->db->delete('lessons_templates_tag_items', ['id' => $data['item_id']]);
        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    }

    public function getLessonTemplateTags(int $id){
        return $this->db->from('lessons_templates_tags t')->join('lessons_templates_tag_items i', 'i.id = t.tag_id')->where('lesson_id', $id)->get()->result_array();
    } 
    
    // LESSON CANCEL

    /**
     * Cancel lesson with reason
    */
    public function cancelLessons(array $data){
        $this->gymdb->init(get_db());

        $this->db->trans_start();
        $query="UPDATE lessons l RIGHT JOIN lessons_teachers lt ON lt.lesson_id = l.id SET l.canceled = 1, l.cancel_reason = '".$data['reason']."' 
                WHERE lt.teacher_id = ".$data['teacher_id']." AND l.starting_on BETWEEN '".$data['cancel_from']." 00:00:00' AND '".$data['cancel_to']." 23:59:59'";
        $this->db->query($query);   
        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    }

    /**
     * Substitute lesson teacher, who cannot participate lesson
    */
    public function substituteLessonTeacher(array $data){
        $this->gymdb->init(get_db());

        $this->db->trans_start();
        $query="UPDATE lessons_teachers lt LEFT JOIN lessons l ON lt.lesson_id = l.id SET lt.participate = 0, lt.reason = '".$data['reason']."', lt.teacher_substitute = '".$data['teacher_substitute']."' 
                WHERE lt.teacher_id = ".$data['teacher_id']." AND l.starting_on BETWEEN '".$data['cancel_from']." 00:00:00' AND '".$data['cancel_to']." 23:59:59'";
        $this->db->query($query);
        $query="INSERT INTO lessons_teachers (lesson_id, teacher_id) 
                SELECT id, '".$data['teacher_substitute']."' FROM lessons l WHERE starting_on BETWEEN '".$data['cancel_from']." 00:00:00' AND '".$data['cancel_to']." 23:59:59'
                AND NOT EXISTS (SELECT * FROM lessons_teachers WHERE lesson_id=l.id and teacher_id = '".$data['teacher_substitute']."' LIMIT 1)"; 
        $this->db->query($query);
        $this->db->trans_complete();

        if($this->db->trans_status()) return true;
        else return false;
    }  
    
    // LESSONS CLIENTS

    public function processLessonClients($lesson_id, $start, $end, $client_limit, $clients){
        $ret = [];
        $registered_clients = 0;
        $this->gymdb->init(get_db());
        $currentClients = $this->db->where(['lesson_id' => $lesson_id,'lesson_date' => date('Y-m-d', strtotime($end)), 'vip' => false])->get('lessons_clients')->result();
        foreach ($currentClients as $cc){
            $registered_clients++;
            // new cancel
            if(!in_array($cc->client_id,$clients) && $cc->state!=3){
                $registered_clients--;
                // Reservation fee
                $fee = $this->refundReservationFee($cc->client_id,$start);
                if(isset($fee['error'])){ // fee was not refunded
                    $ret[] = ['client_id' => $cc->client_id, 'type' => $fee['type'], 'msg' => $this->retMsg[$fee['msg']][$this->__appEnv]];
                    $this->db->update('lessons_clients', ['state' => 3, 'reservation_fee_refunded' => FALSE], ['lesson_id' => $lesson_id, 'client_id' => $cc->client_id]);
                } else { // fee was refunded
                    $ret[] = ['client_id' => $cc->client_id, 'type' => $fee['type'], 'msg' => $this->retMsg[$fee['msg']][$this->__appEnv]];
                    $this->db->update('lessons_clients', ['state' => 3, 'reservation_fee_refunded' => TRUE], ['lesson_id' => $lesson_id, 'client_id' => $cc->client_id]);
                }
            } else if (in_array($cc->client_id,$clients) && $cc->state == 3){ // cancelled client wants renew reservation
                // Client limit check
                if($registered_clients > $client_limit){
                    $ret[] = ['client_id' => $client_id, 'type' => 'danger', 'msg' => $this->retMsg['overlimit'][$this->__appEnv]];
                    unset($clients[array_search($cc->client_id,$clients)]);
                    continue;
                }

                // Reservation fee
                if($cc->reservation_fee_refunded==0){ // fee was already paid and was not refund
                    $this->db->update('lessons_clients', ['state' => NULL], ['lesson_id' => $lesson_id, 'client_id' => $cc->client_id]);
                    $ret[] = ['client_id' => $cc->client_id, 'type' => 'success', 'msg' => $this->retMsg['success_lesson_reservation_without_pay'][$this->__appEnv]];
                } else { // fee was refund so must be paid again
                    $fee = $this->payReservationFee($cc->client_id,$end);
                    if(isset($fee['error'])){ // fee was not paid
                        $ret[] = ['client_id' => $cc->client_id, 'type' => $fee['type'], 'msg' => $this->retMsg[$fee['msg']][$this->__appEnv]];
                    } else { // fee was paid
                        $this->db->update('lessons_clients', ['state' => NULL, 'reservation_fee_paid' => TRUE, 'reservation_fee_refunded' => FALSE], ['lesson_id' => $lesson_id, 'client_id' => $cc->client_id]);
                        $ret[] = ['client_id' => $cc->client_id, 'type' => $fee['type'], 'msg' => $this->retMsg[$fee['msg']][$this->__appEnv]];
                    }                    
                }           
            } 
            unset($clients[array_search($cc->client_id,$clients)]);  
        }

        // new reservations
        foreach($clients as $client_id){
            if(strlen($client_id) <= 0) continue;

            // Client limit check
            $registered_clients++;
            if($registered_clients > $client_limit){
                $ret[] = ['client_id' => $client_id, 'type' => 'danger', 'msg' => $this->retMsg['overlimit'][$this->__appEnv]];
                continue;
            }

            // Reservation fee
            $fee = $this->payReservationFee($client_id,$end);
            if(isset($fee['error'])){ // client has not card or enough credit
                $ret[] = ['client_id' => $client_id, 'type' => $fee['type'], 'msg' => $this->retMsg[$fee['msg']][$this->__appEnv]];
            } else {
                $this->db->insert('lessons_clients', ['client_id' => $client_id, 'lesson_id' => $lesson_id, "reservation_fee_paid" => TRUE, "lesson_date" => date('Y-m-d', strtotime($end))]);
                $ret[] = ['client_id' => $client_id, 'type' => $fee['type'], 'msg' => $this->retMsg[$fee['msg']][$this->__appEnv]];
            }
        }

        $ret['registered_clients']=$registered_clients;
        return $ret;
    }

    // LESSON VIP CLIENTS

    public function processLessonVIPClients($lesson_id, $start, $end, $client_limit, $vipClients, $registered_clients){
        $ret=$clients=[];
        $registered_clients = $registered_clients;
        foreach($vipClients as $k=>$c){ // get ID of disposable user or create 
            $disposableUser=$this->users->getOrCreateDisposableUser($c);
            $clients[$disposableUser['id']]=$disposableUser;
            $clients[$disposableUser['id']]['note']=$c['note'];
        }
        $currentClients = $this->db->select('lc.*,u.email,ud.first_name,ud.last_name')
            ->from('lessons_clients lc')
            ->join('users u', 'u.id = lc.client_id')
            ->join('users_data ud', 'ud.user_id = lc.client_id')
            ->where(['lesson_id' => $lesson_id, 'lesson_date' => date('Y-m-d', strtotime($end)),'vip' => true])
            ->get()->result();
        foreach ($currentClients as $cc){
            $registered_clients++;
            if(!array_key_exists($cc->client_id,$clients) && $cc->state!=3){ // new cancel
                $registered_clients--;
                $this->db->update('lessons_clients', ['state' => 3], ['lesson_id' => $lesson_id, 'client_id' => $cc->client_id]);
                $ret[] = ['client_id' => $cc->client_id, 'client_name' => $cc->first_name.' '.$cc->last_name.' ('.$cc->email.')', 'type' => 'success', 'msg' => $this->retMsg['success_lesson_reservation_without_refund'][$this->__appEnv]];    
            } else if(array_key_exists($cc->client_id,$clients) && $cc->state == 3){ // cancelled client wants renew reservation
                // Client limit check
                if($registered_clients > $client_limit){
                    $ret[] = ['client_id' => $client_id, 'client_name' => $cc->first_name.' '.$cc->last_name.' ('.$cc->email.')', 'type' => 'danger', 'msg' => $this->retMsg['overlimit'][$this->__appEnv]];
                    unset($clients[$cc->client_id]); 
                    continue;
                }
                $this->db->update('lessons_clients', ['state' => NULL, 'note' => $clients[$cc->client_id]['note']], ['lesson_id' => $lesson_id, 'client_id' => $cc->client_id]);
                $ret[] = ['client_id' => $cc->client_id, 'client_name' => $cc->first_name.' '.$cc->last_name.' ('.$cc->email.')', 'type' => 'success', 'msg' => $this->retMsg['success_lesson_reservation_without_pay'][$this->__appEnv]];    
            }
            unset($clients[$cc->client_id]);  
        }

        // new reservations
        foreach($clients as $id => $c){
            if(strlen($id) <= 0) continue;

            // Client limit check
            $registered_clients++;
            if($registered_clients > $client_limit){
                $ret[] = ['client_id' => $id, 'client_name' => $clients[$id]['first_name'].' '.$clients[$id]['last_name'].' ('.$clients[$id]['email'].')', 'type' => 'danger', 'msg' => $this->retMsg['overlimit'][$this->__appEnv]];
                continue;
            }

            if(new DateTime() < new DateTime($end)){ // is it ended lesson?
                $this->db->insert('lessons_clients', ['client_id' => $id, 'lesson_id' => $lesson_id, 'vip' => true, "lesson_date" => date('Y-m-d', strtotime($end)), 'note' => $clients[$id]['note']]);
                $ret[] = ['client_id' => $id, 'client_name' => $clients[$id]['first_name'].' '.$clients[$id]['last_name'].' ('.$clients[$id]['email'].')', 'type' => 'success', 'msg' => $this->retMsg['success_lesson_reservation_without_pay'][$this->__appEnv]];
            }
        }        
        return $ret;
    }

    // LESSONS FEE

    public function payReservationFee($client_id,$end){
        if(new DateTime() < new DateTime($end)){
            $card_id = $this->cards->getUserCard($client_id)->card_id ?? FALSE;
            if($card_id){
                $currentCredit = $this->API->transactions->get_credit($client_id, $card_id)->data->currentValue;
                if($currentCredit>=50){ // enough credit
                    $this->API->transactions->set_credit($client_id,$card_id,$currentCredit - config_item('app')['lesson_reservation_fee']);
                    return ['type' => 'success', 'msg' => 'success_lesson_reservation_pay'];
                } else return ['error' => TRUE, 'type' => 'danger', 'msg' => 'not_enough_credit'];
            } else return ['error' => TRUE, 'type' => 'danger', 'msg' => 'missing_card'];
        } else return ['error' => TRUE, 'type' => 'danger', 'msg' => 'ended_lesson']; 
    }    

    public function refundReservationFee($client_id,$start){
        $start = new DateTime($start);
        $now = new DateTime();
        if($now < $start){
            $diff = $now->diff($start);
            $hours = $diff->h;
            $hours = $hours + ($diff->days*24);
            if($hours >= config_item('app')['lesson_reservervation_refund_hours']){
                $card_id = $this->cards->getUserCard($client_id)->card_id;
                if($card_id){
                    $currentCredit = $this->API->transactions->get_credit($client_id, $card_id)->data->currentValue;
                    $this->API->transactions->set_credit($client_id,$card_id,$currentCredit + config_item('app')['lesson_reservation_fee']);
                    return ['type' => 'success', 'msg' => 'success_lesson_reservation_refund'];
                } else return ['error' => TRUE, 'type' => 'danger', 'msg' => 'missing_card'];    
            } else return ['error' => TRUE, 'type' => 'danger', 'msg' => 'error_refund_less_than_Xhrs'];
        } else return ['error' => TRUE, 'type' => 'danger', 'msg' => 'ended_lesson']; 
    }

    public function leaveLessonByClient(int $userId)
    {
        // vratit kredity / penize ?
    }
}
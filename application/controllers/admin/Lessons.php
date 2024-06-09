<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lessons extends Backend_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('lessons_model', 'lessons');
    }

    public function sectionName(): string
    {
        return SECTION_LESSONS;
    }
    
	public function index()
	{
	    $this->checkReadPermission();

        $data['pageTitle'] = 'Kalendář';

        $data['lessonsUrl'] = base_url('admin/lessons/calendar_get_table');

        $this->app->assets(['tabulator.min.js', 'flatpickr.js', 'flatpickr.cs.js', 'admin.lessons.main.js'], 'js');
        $this->app->assets(['tabulator.min.css', 'flatpickr.css', 'jquery-ui/jquery-ui.min.css'], 'css');
		
		$this->load->view('layout/header', $data);
		$this->load->view('layout/menu', $data);
		$this->load->view('admin/lessons/index', $data);
		$this->load->view('layout/footer');
    }

    public function templates(){
        $data['pageTitle'] = 'Lekce';

        $data['templatesUrl'] = base_url('admin/lessons/templates_get_table');

        $this->app->assets(['tabulator.min.js', 'flatpickr.js', 'flatpickr.cs.js', 'trumbowyg/trumbowyg.min.js', 'trumbowyg/trumbowyg.cs.js', 'admin._trumbowyg.js', 'admin.lessons_templates.main.js'], 'js');
        $this->app->assets(['tabulator.min.css', 'flatpickr.css', 'trumbowyg/trumbowyg.min.css'], 'css');

		$this->load->view('layout/header', $data);
		$this->load->view('layout/menu', $data);
		$this->load->view('admin/lessons/templates', $data);
		$this->load->view('layout/footer');
    }

    public function add_template(){
        $this->checkCreatePermission();
        $data['saveUrl'] = base_url('/admin/lessons/add_template_ajax');   
        $data['tags'] = $this->lessons->getAllTemplatesTags(true);
        $this->load->view('admin/lessons/template', $data);
    }    

    public function edit_template($id){
        $this->checkEditPermission();
        $data['saveUrl'] = base_url('/admin/lessons/save_template_ajax');   
        $data['lesson'] = $this->lessons->getTemplate($id);
        $data['lesson_tags'] = array_map(function($row){
            return $row['tag_id'];
        }, $this->lessons->getLessonTemplateTags($id));
        $data['tags'] = $this->lessons->getAllTemplatesTags(true);       
        $this->load->view('admin/lessons/template', $data);
    }    

    public function calendar_get_table(){
        if($data = $this->lessons->getTableEvents()) echo json_encode($data);
        else echo json_encode([]);
    }

    public function templates_get_table(){
        if($data = $this->lessons->getTableTemplates()) echo json_encode($data);
        else echo json_encode([]);
    }

    public function calendar_get(){
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        
        $data = $this->lessons->getCalendarData($start, $end);
        $return = array('data' => array());
        
        if($data){
            foreach($data as $row){

                if($row->repeating && !$row->repeating_end){

                    $datediff = strtotime(date('Y-m-d', strtotime('+3 years'))) - strtotime($row->starting_on);
                    $until = ( ( round($datediff / (60 * 60 * 24)) ) / $row->repeating ) - 1;

                    $event = [
                        'id' => $row->id,
                        'name' => $row->name,
                        'description' => $row->description,
                        'canceled' => $row->canceled,
                        'substitute' => "0",
                        'allDay' => ($row->all_day) ? $row->all_day : FALSE,
                        'start' => date('Y-m-d H:i', strtotime($row->starting_on)),
                        'end' => date('Y-m-d H:i', strtotime($row->ending_on)),
                        'client_count' => $this->lessons->getClientCount($row->id),
                        'client_limit' => $row->client_limit,
                        'teachers' => $this->lessons->getTeachersCount($row->id)
                    ];

                    // Substitute teacher marking
                    $substitute = $this->lessons->isSubstituteTeacher($row->id);
                    if($substitute) $event["substitute"] = "1";

                    $return['data'][] = $event;

                    for($x = 1; $x <= $until; $x++){

                        $start_date = strtotime($row->starting_on . '+' . ($row->repeating * $x) . 'DAYS');
                        $end_date = strtotime($row->ending_on . '+' . ($row->repeating * $x) . 'DAYS');
                        $s = date("Y-m-d H:i", $start_date);
                        $e = date("Y-m-d H:i", $end_date);

                        if($row->canceled){
                            //continue; // skip cancelled events
                        }

                        $recurring = [
                            'id' => $row->id,
                            'name' => $row->name,
                            'description' => $row->description,
                            'canceled' => $row->canceled,
                            'substitute' => "0",
                            'allDay' => ($row->all_day) ? $row->all_day : FALSE,
                            'start' => $s,
                            'end' => $e,
                            'client_count' => $this->lessons->getClientCount($row->id, date('Y-m-d', strtotime($s))),
                            'client_limit' => $row->client_limit,
                            'teachers' => $this->lessons->getTeachersCount($row->id)
                        ];

                        // Substitute teacher marking
                        $substitute = $this->lessons->isSubstituteTeacher($row->id);
                        if($substitute) $event["substitute"] = "1";

                        $return['data'][] = $recurring;
                    }

                }else{
                    $e = date("Y-m-d H:i", strtotime($row->ending_on));
                    if($row->canceled){
                        //continue; // skip cancelled events
                    }

                    $event = [
                        'id' => $row->id,
                        'name' => $row->name,
                        'description' => $row->description,
                        'canceled' => $row->canceled,
                        'substitute' => "0",
                        'allDay' => ($row->all_day) ? $row->all_day : FALSE,
                        'start' => date('Y-m-d H:i', strtotime($row->starting_on)),
                        'end' => date('Y-m-d H:i', strtotime($row->ending_on)),
                        'client_count' => $this->lessons->getClientCount($row->id),
                        'client_limit' => $row->client_limit,
                        'teachers' => $this->lessons->getTeachersCount($row->id)
                    ];

                    $substitute = $this->lessons->isSubstituteTeacher($row->id);
                    if($substitute) $event["substitute"] = "1";
    
                    $return['data'][] = $event;
                }
            }
        }

        echo json_encode($return);
    }

    public function add_event_ajax(){
        $this->checkCreatePermission(true);
        $ret = $this->lessons->addEvent();
        if($ret) echo json_encode(["success" => true, "data" => $ret]);
        else echo json_encode(["error" => true]);
    }

    public function save_event_ajax(){
        $this->checkEditPermission(true);
        $ret = $this->lessons->saveEvent();
        if($ret) echo json_encode(["success" => true, "data" => $ret]);
        else echo json_encode(["error" => true]);
    }

    public function get_event_data_ajax(){
        if($event = $this->lessons->getEvent()) echo json_encode(["success" => true, "data" => $event]);
        else echo json_encode(["error" => true]);
    }

    public function remove_image_ajax(){
        if($this->lessons->deleteEventImage()) echo json_encode(["success" => true]);
        else echo json_encode(["error" => true]);
    }

    public function delete_event_ajax(){
        $this->checkDeletePermission(true);
        if($this->lessons->deleteEvent()) echo json_encode(["success" => true]);
        else echo json_encode(["error" => true]);
    }
    public function delete_all_repeating_events_ajax(){
        $this->checkDeletePermission(true);
        if($this->lessons->deleteAllRepeatingEvents()) echo json_encode(["success" => true]);
        else echo json_encode(["error" => true]);
    }


    // Templates

    public function add_template_ajax(){
        $this->checkCreatePermission(true);
        if($this->lessons->addTemplate()) echo json_encode(["success" => true]);
        else echo json_encode(["error" => true]);
    }

    public function save_template_ajax(){
        $this->checkEditPermission(true);
        if($this->lessons->saveTemplate()) echo json_encode(["success" => true]);
        else echo json_encode(["error" => true]);
    }

    public function delete_template_ajax(){
        $this->checkDeletePermission(true);
        if($this->lessons->deleteTemplate()) echo json_encode(["success" => true]);
        else echo json_encode(["error" => true]);
    }

    // Templates tags

    public function get_template_tags_ajax(){
        echo json_encode($this->lessons->getAllTemplatesTags());
    }      

    public function templates_tags_modal(){
        $this->checkReadPermission();
        $data['templatesTagsUrl'] = base_url("admin/lessons/get_template_tags_ajax");
        $this->load->view('admin/lessons/templates_tags', $data);
    }

	public function save_template_tag_ajax(){
        $this->checkEditPermission(true);
        if($this->lessons->saveTemplateTag($this->input->post())) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);
	}	

	public function delete_template_tag_ajax(){
        $this->checkDeletePermission(true);
        if($this->lessons->deleteTemplateTag($this->input->post())) echo json_encode(['success' => 'true']);
        else echo json_encode(['error' => 'true']);
    }
     
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends Public_Controller {
	public function __construct(){
		parent::__construct();   
		//$this->load->model('users_model', 'users'); 
		$this->load->model('dashboard_model', 'dash'); 
		$this->load->model("autocont_model", "autocont");
	}

	public function generate_subscribers_xml($gym){
		$this->autocont->create_subscribers_xml($gym);
	}

	public function generate_future_payments_xml($gym){
		$date = date("Y-m-d 00:00:01");
		$this->autocont->create_future_payments_xml($gym, $date);
	}

	public function generate_transactions_xml($gym){
		$date = date("Y-m-d 00:00:01");
		$this->autocont->create_transaction_xml($gym, $date);
	}

	public function read_xml_import(){
		$this->autocont->read_xml_files();
	}

	// TODO: Create a backend cron system that monitors all of this and collects errors and possibly does re-runs? :( 

	/**
	 * Daily maintenance cron for renewing soon to be expired subscriptions
	 * TOOD
	 */
	public function renew_subscriptions () {

	}

	/**
	 * Daily maintenance cron, ran during the night
	 * Cleans every reader in the Gym and pushes all valid cards into every single one
	 * 
	 * TODO: some sort of logging of this? 100% afraid this is gonna bite us in the ass but hey
	 */
	public function cleanup_readers () {
		$gyms = $this->gyms->getAllGyms();

		foreach ($gyms as $gym){
			$gym_id = $gym['_id']->{'$id'};
			$rooms = $this->gyms->getGymRooms($gym_id, FALSE, FALSE, TRUE);
			$cards = $this->db->get("users_cards")->result();

			$allowed_cards = [];
			$errors = [];
			foreach($cards as $card){
				$user_group = $this->db->where("user_id", $card->user_id)->get("users_groups")->row()->group_id;

				if($user_group == CLIENT){
					// Handle clients
					// Check for credit balance OR active subscription
					$sub = $this->API->subscriptions->get_subscription($card->user_id, current_gym_code());
					$credit = $this->API->transactions->get_credit($card->user_id, $card->card_id);

					$has_paid_sub = false;
					$has_enough_credit = false;

					if (!empty($sub->data)){
						$active_end = FALSE;
                        foreach($sub->data->transactions as $t){
                            if($t->paid){
								$active_end = date("Y-m-d", strtotime($t->end));
								if($active_end >= date("Y-m-d")) $has_paid_sub = true;
                            }else{
                                break;
                            }
                        }
					}

					if($credit > 0) $has_enough_credit = TRUE; // Set this to some higher treshold (?)
					if($has_paid_sub OR $has_enough_credit) $allowed_cards[] = $card->card_id; // allowed
				}else{
					// Handle everyone else, aka admin/manager/receptionist/etc.
					// No need to check for anything, just allow access..
					$allowed_cards[] = $card->card_id; // <--'
				}
			}

			if ($this->API->readers->reset_readers($rooms)) {
				foreach ( $allowed_cards as $card_id ) {
					foreach ($rooms as $room) {
						// Todo: log failed attempts or retry ?
						if( $this->API->readers->register_card($card_id, $room->reader_id, $room->address) ) continue;
						else $errors[] = ["error" => "Failed registering card", "cardId" => $card_id, "roomId" => $room->id];
					}
				}
			}
		}
	}

	/**
	 * Process clients credit balances and control their exit
	 * this + process_reader_events have to be run quite regularly! [ 1243 ]
	 * TODO
	 */
	public function control_client_exit () {
		$gyms = $this->gyms->getAllGyms();

		foreach ($gyms as $gym){
			$gym_id = $gym['_id']->{'$id'};

			$usersInGym = $this->API->readers->get_users_in_gym(NULL, ["gymId" => $gym_id]);
			$clientIds = [];
	
			$gym_entrance = $this->gyms->getEntranceReader($gym_id);
			if(!$gym_entrance) exit("No gym entrance was setup");

			if(isset($usersInGym->data)){
				foreach ($usersInGym->data as $o){ 
					$clientIds[] = $o->cardId; 
					$que = $this->API->saleques->get_que($o->cardId);

					$clientId = $this->users->getUserIdByCard($o->cardId);
					$credit = $this->payments->get_clients_credit($clientId);

					$spent = 0;

					// HH -> returns complete detailed que with total Value of purchase
					$membership_id=1;
					$spent = $this->dash->getClientQueItems($o->cardId,$membership_id)->totalPrice;					

					/*if (!empty($que)) foreach ($que as $row) {
						$spent += $row->value;
					}*/
	
					if($spent > $credit){
						// Block their exit
						$this->API->readers->deregister_card($o->cardId, $gym_entrance->readerId, $gym_entrance->address);
					}else{
						continue;
					}
				}
			}else{
				return FALSE; // noone in gym
			}

		}
	}
	
	/** 
	 * Process walkins through the gym parts etc.
	 * 
	 * create saleques on entrance, manage pricing of certain sections etc.
	 */
	public function process_reader_events () {
		$day = date("d");
		$month = date("m");
		$data = false;

		$gyms = $this->gyms->getAllGyms();
		$this->load->model("lessons_model", "lessons"); // for calendar

		// Some "caching" vars to not repeat some DB requests
		$processed_users = []; // cache
		$processed_benefits = [];
		$room_events = []; // cache
		$rooms = []; // cache

		// Get off-peak times
		$offpeak_times = ["basic_off_peak" => ["from" => NULL, "to" => NULL], "platinum_off_peak" => ["from" => NULL, "to" => NULL]];
		$offpeak_data = $this->db->where_in("code", ["platinum_off_peak", "basic_off_peak"])->get("membership")->result();
		foreach ($offpeak_data as $offpeak){
			$m_data = json_decode($offpeak->data);

			$offpeak_times[$offpeak->code]["from"] = date("Y-m-d ".$m_data->from);
			$offpeak_times[$offpeak->code]["to"] = date("Y-m-d ".$m_data->to);
		}


		foreach ($gyms as $gym){
			$gym_id = $gym['_id']->{'$id'};
		
			$last = $this->db->order_by("date_processed", "desc")->where("date_processed >=", date("Y-m-d 00:00:00"))->get("reader_processing")->row();
			if($last) $data = $this->API->readers->get_reader_events(["createdOn" => $last->last_event_created_on, "day" => $day, "month" => $month, "gymId" => $gym_id]);
			else $data = $this->API->readers->get_reader_events(["day" => $day, "month" => $month, "gymId" => $gym_id]);
	
			if($data){
	
				$gym_entrance = $this->gyms->getEntranceReader($gym_id);
				$events = $data->data; // actual event rows
	
				foreach ($events as $event) {
					$cardId = $event->cardId;
					$readerId = $event->readerId;
					$readerAddress = $event->readerAddress;
					$eventStatus = $event->eventStatus;

					if(!isset($processed_users[$cardId])) {
						$user = $this->cards->getUserFromCard($cardId); // $user->id, etc.

						$sub = $this->API->subscriptions->get_subscription($user->id, current_gym_code());
						$user->subscription = (!empty($sub->data)) ? $sub->data : FALSE; // Get this users subscription
						
						$processed_users[$cardId] = $user;
					}else{
						$user = $processed_users[$cardId];
					}

					if (!isset($rooms[$readerId.'_'.$readerAddress])){
						$room = $this->db->where("reader_id", $readerId)->where("address", $readerAddress)->get("rooms")->row(); // the room
						$rooms[$readerId.'_'.$readerAddress] = $room; // "cache"
					}else{
						$room = $rooms[$readerId.'_'.$readerAddress];
					}
					//$room_settings = $this->gyms->getGymRoomSettings($room->id, $gym_id); // rooms settings

					if($eventStatus == "entrance"){
						// Entrance
						// Gotta check for paid entrance (if) + existence of saleque
						if(!$this->API->saleques->get_que($cardId)) $this->api->saleques->create_que($cardId); // create if not existing

					}else if($eventStatus == "exit"){
						// Exit
						// Gotta check for previous entrance and calculate timed pricing -> add to que if needed
						//$que = $this->API->saleques->get_que($cardId);
						$entrance = $this->API->readers->getSingleReaderEvent(["cardId" => $cardId, "readerId" => $readerId, "readerAddress" => $readerAddress, "eventStatus" => "entrance"])->data;
						$time_diff = (strtotime($event->time) - strtotime($entrance->time)) / 60; // actual time difference in minutes

						if($time_diff >= 5){

							// Full datetimes
							$enter_time = "$event->year-".sprintf('%02d',$entrance->month)."-".sprintf('%02d',$entrance->day)." $entrance->time:00";
							$exit_time = "$event->year-".sprintf('%02d',$event->month)."-".sprintf('%02d',$event->day)." $event->time:00";

							// Get all todays lessons for this ROOM that were not CANCELLED
							if (!isset($room_events[$room->id])){
								$this->db->select("lessons.starting_on, lessons.ending_on, lessons.id, lessons_templates.room_id, lessons_templates.pricelist_id")->from("lessons");
										$this->db->join("lessons_templates", "lessons_templates.id = lessons.template_id");
										$this->db->where("DATE(lessons.starting_on) >=", date("Y-m-d", strtotime($enter_time)))
													->where("DATE(lessons.ending_on) <=", date("Y-m-d", strtotime($exit_time)))
													->where("lessons_templates.room_id", $room->id)
													->where("lessons.canceled !=", 1)
													->order_by("lessons.starting_on", "desc");
								$lessons = $this->db->get()->result();
								$room_events[$room->id] = $lessons; // "cache"
							}else{
								$lessons = $room_events[$room->id];
							}

							// Loop trough them and assign to an array
							$visited_lessons = [];
							if($lessons){
								foreach ($lessons as $lesson) {
									$lessons_price_ids[$lesson->id]=$lesson->pricelist_id;
									// Calculate time differences between lessons start/end and checkin enter/exit
									if($enter_time > $lesson->starting_on) $starting_diff = (strtotime($lesson->starting_on) - strtotime($enter_time)) / 60;
									else $starting_diff = (strtotime($enter_time) - strtotime($lesson->starting_on)) / 60;

									if($exit_time > $lesson->ending_on) $ending_diff = (strtotime($exit_time) - strtotime($lesson->ending_on)) / 60;
									else $ending_diff = (strtotime($lesson->ending_on) - strtotime($exit_time)) / 60;

									// Client comes =<15 minutes before lesson start and spends at least 15 minutes
									if($starting_diff >= -10 && abs((strtotime($exit_time) - strtotime($lesson->starting_on)) / 60) >= 10) $visited_lessons[] = $lesson->id;
									// Client leaves a lesson <= 15 minutes and has spent at least 15 minutes in
									if($ending_diff <= 10 && abs((strtotime($enter_time) - strtotime($lesson->ending_on)) / 60) >= 10) $visited_lessons[] = $lesson->id;
									// In the middle lessons, basically the client stays in a lesson after another lesson (TODO: This might not work lol)
									if($starting_diff < -10 && $ending_diff > 10 && $enter_time < $lesson->starting_on && $exit_time > $lesson->ending_on) $visited_lessons[] = $lesson->id;
								}

								$visited_lessons = array_unique($visited_lessons); // remove any accidental duplicates (ids are unique and cant visit twice)
							}

							// Control off-peak timings
							$peak_minutes = 0;
							if ($user->membership && strpos($user->membership->subType, "off_peak")) {
								
								// enter time is before offpeak time start
								if($enter_time < $offpeak_times[$user->membership->subType]["from"]){
									// If still exited before off-peak starts => whole timeslot is peak minutes
									if($exit_time < $offpeak_times[$user->membership->subType]["from"]){
										$peak_minutes += $time_diff;
									}else{
										// Exited after off-peak time started, just the timeslot before off-peak time start is peak minutes
										$peak_minutes += $time_diff - (strtotime($exit_time) - strtotime($offpeak_times[$user->membership->subType]["from"])) / 60;
									}

								}

								// exit time is after offpeak time end
								if($exit_time > $offpeak_times[$user->membership->subType]["to"]){
									// if entered after off_peak time ends => everything is peak minutes
									if($enter_time > $offpeak_times[$user->membership->subType]["to"]){
										$peak_minutes += $time_diff;
									}else{
										// Entered before offpeak time ends, just the timeslot after off-peak time end is peak minutes
										$peak_minutes += ((strtotime($offpeak_times[$user->membership->subType]["to"] - strtotime($enter_time))) / 60) - $time_diff;
									}
								}
							}

							// Set up wellness if this was a wellness room
							$wellness_minutes = 0;
							if ($room->wellness) $wellness_minutes = $time_diff;

							// Set up gymroom if this was a basic excersice room
							$exercise_minutes = 0;
							if ($room->exercise_room) $exercise_minutes = $time_diff;

							//
							// showdown -> mange clients que here!
							if(!empty($visited_lessons)){
								foreach($visited_lessons as $vl){
									$itemId = $lessons_price_ids[$vl];
									
									if (!isset($processed_benefits[$user->id . "_" . $cardId . "_" . $itemId])){
										$benefit = $this->pricelist->checkMembershipBenefit($user->id,$cardId,$itemId,false);
										$processed_benefits[$user->id . "_" . $cardId . "_" . $itemId] = $benefit; // "cache"
									}else{
										$benefit = $processed_benefits[$user->id . "_" . $cardId . "_" . $itemId];
									}
									
									$items[]=['itemId'=>$itemId, 'amount'=>1, 'discount'=>$benefit->discount??0, 'benefitId' => $benefit->id??null];
									$this->API->saleques->add_to_que($cardId, $items, $day = NULL);
								}
							}

							// Minutes in wellness
							if($wellness_minutes > 0){
								$itemId = 2; $note='';
								
								if (!isset($processed_benefits[$user->id . "_" . $cardId . "_" . $itemId])){
									$benefit = $this->pricelist->checkMembershipBenefit($user->id,$cardId,$itemId,false);
									$processed_benefits[$user->id . "_" . $cardId . "_" . $itemId] = $benefit; // "cache"
								}else{
									$benefit = $processed_benefits[$user->id . "_" . $cardId . "_" . $itemId];
								}

								$items[]=['itemId'=>$itemId, 'amount'=>1, 'discount'=>$benefit->discount??0, 'benefitId' => $benefit->id??null, 'timeSpent' => $wellness_minutes];
								$this->API->saleques->add_to_que($cardId, $items, $day = NULL);
							}

							// Minutes in excercise
							if($exercise_minutes > 0){
								$itemId = 3;
								
								if (!isset($processed_benefits[$user->id . "_" . $cardId . "_" . $itemId])){
									$benefit = $this->pricelist->checkMembershipBenefit($user->id,$cardId,$itemId,false);
									$processed_benefits[$user->id . "_" . $cardId . "_" . $itemId] = $benefit; // "cache"
								}else{
									$benefit = $processed_benefits[$user->id . "_" . $cardId . "_" . $itemId];
								}
								
								$items[]=['itemId'=>$itemId, 'amount'=>1, 'discount'=>$benefit->discount??0, 'benefitId' => $benefit->id??null, 'timeSpent' => $exercise_minutes];
								$this->API->saleques->add_to_que($cardId, $items, $day = NULL);
							}

							// Minutes over off-peak limit
							if($peak_minutes > 0){

							}

						}else{
							// Skip if the time difference is under, or 5 minutes since it can be a mistaken entrance
							continue;
						}
					}else{
						// forbidden OR unknown (more like forbidden entry)
						// Probably do nothing for now (or alert the overlords and start a RIOT?!?)
						continue;
					}
				}
	
			}else{
				continue;
			}
		
		}
	}

	/** 
	 * Disable DailyPass, VIP etc.
	 */
	public function disable_clients_advantages () {
		$this->db->update('clients_data',['dailypass'=>false,'vip'=>false]);
	}
}
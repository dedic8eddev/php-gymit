<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model
{
	public function __construct(){
		$this->load->model('pricelist_model', 'pricelist');
		$this->load->model('clients_model', 'clients');
	}

	public function getClientQueItems($card_id,$membership_id=NULL,$onlyIDs=false){

		$depotIDs=$serviceIDs=[];
		$multisportDiscount = false;

		$clientId = $this->users->getUserIdByCard($card_id);
		$clientData = $this->clients->getClientData($clientId);

		$que = $this->API->saleques->get_que($card_id);
		if($membership_id) $membership = $this->pricelist->getMembership($membership_id);
		
		if(isset($que->rows)){
			if($onlyIDs){ // return only IDs of items
				$ret=[];
				foreach ($que->rows as $k => $item){
					if(isset($item->depotId)) $ret['depot'][]=$item->itemId; // depot items
					else $ret['service'][]=$item->itemId;
				}
				return $ret;
			} else { // return detailed list of items
				$que->totalPrice=0;
				$que->note='';
				foreach ($que->rows as $k => $item){
					if(isset($item->depotId)){ // depot items
						$que->rows[$k]->itemInfo = $this->db->select('name,sale_price_vat vat_price, vat_value vat')->where('id',$item->itemId)->get('depot_items')->row();
						$que->rows[$k]->itemInfo->depot_name = $this->db->select('name')->where('id',$item->depotId)->get('depots')->row()->name;
						$depot_stock = $this->db->select('stock, reserved')->where('depot_id',$item->depotId)->where('item_id',$item->itemId)->get('depots_stocks')->row();
						$que->rows[$k]->itemInfo->depot_stock = $depot_stock->stock - $depot_stock->reserved;
						$price_after_discount = $que->rows[$k]->itemInfo->vat_price - ($que->rows[$k]->itemInfo->vat_price * ($item->discount / 100));
						$que->totalPrice += $price_after_discount*$item->amount; 
					} else { // service items						
						if($membership_id){ // client has membership
							$itemInfo = $this->pricelist->getMembershipServicePrice($item->itemId,$membership_id);
							$que->rows[$k]->itemInfo = $itemInfo;
						} else { // client has not membership
							$itemInfo = (object) $this->pricelist->getPrice($item->itemId);
							$que->rows[$k]->itemInfo = $itemInfo;				
						}

						// Multisport has one service free (Let's choose the cheapest item), Item must not have discount
						$que->multisportCard=true;
						if($que->multisportCard && ($item->discount??0)==0 && in_array($que->rows[$k]->itemInfo->service_type,[1,3,4])){
							$possibleMultisportItems[$k] = $que->rows[$k]->itemInfo->vat_price;
						}

						// Wellness discount after exercise zones -> was client in wellness && exercise zone?
						if($item->itemInfo->id==4) $clientWasInWellness=$k; // store key of que row
						if($item->itemInfo->id==3) $clientWasInExerciseZones=$k; // store key of que row
						if(isset($clientWasInWellness) && isset($clientWasInExerciseZones)){ // client was in wellness && exercise zone
							$key = $clientWasInWellness;
							$originalWellnessPrice = $que->rows[$key]->itemInfo->vat_price;
							$newPrice = @$membership->data->wellness_after_excercise_zones_price > 0 ? $membership->data->wellness_after_excercise_zones_price : $que->multisportCard ? 90 : false;
							if($newPrice && $newPrice < $originalWellnessPrice && $que->rows[$key]->discount==0){ // if 
								$que->rows[$key]->itemInfo->vat_price = $newPrice;
								$que->rows[$key]->itemInfo->price = $newPrice/1.21; // without vat
								$que->note .= $item->itemId."|".$que->rows[$key]->itemInfo->name." - Wellness po cvičení ($newPrice Kč)\n";
							}
						}						

						// Off-peak times
						if(@$item->timeSpentPeak > 0){
							if($item->timeSpentPeak == $item->timeSpent){ // Client was whole time outside of off-peak time -> behaviour as different membership
								// get price from different membership
								$que->rows[$k]->itemInfo = $this->pricelist->getMembershipServicePrice($item->itemId,$membership->peak_behaviour_as_membership);
								$que->note .= $item->itemId."|".$que->rows[$k]->itemInfo->name." - Cena z předplacené karty (mimo offpeak)";
							} else { // Just check over off-peak time
								$overtime = $this->pricelist->checkOvertimeFee($itemInfo,$item->timeSpent??0);
								$que->rows[$k]->overtimeMinutes=$overtime['minutes'];
								$que->rows[$k]->overtimeFee=$overtime['fee'];
								$que->note .= $item->itemId."|".config_item('app')['services'][$itemInfo->service_type]." - Off-peak přesčas (minuty: ".$overtime['minutes'].", doplatek: ".$overtime['fee']."Kč)\n";
							}
						}

						// overtime fee check
						$overtime = $this->pricelist->checkOvertimeFee($itemInfo,$item->timeSpent??0,$membership??NULL);
						if($overtime['fee']>0){ // add overtime info and note
							$que->rows[$k]->overtimeMinutes=$overtime['minutes'];
							$que->rows[$k]->overtimeFee=$overtime['fee'];
							$que->note .= $item->itemId."|".config_item('app')['services'][$itemInfo->service_type]." - Přesčas (minuty: ".$overtime['minutes'].", doplatek: ".$overtime['fee']."Kč)\n";
						}

						// dailyPass & VIP
						if($clientData->dailypass || $clientData->vip){
							// exercise zones, wellness and lessons are gratis
							if(in_array($que->rows[$k]->itemInfo->service_type,[1,3,4])){
								$type = $clientData->dailypass ? 'DailyPass' : 'VIP';
								$que->note .= $item->itemId."|".$que->rows[$k]->itemInfo->name." - $type\n";
								unset($que->rows[$k]); // remove item for payments
							}
						}

						$price_after_discount = $que->rows[$k]->itemInfo->vat_price - ($que->rows[$k]->itemInfo->vat_price * ($que->rows[$k]->discount / 100));
						
						// total price including discount and overtime fee
						$que->totalPrice += ($price_after_discount*$item->amount) + ($overtime['fee']??0); 
					}

					// benefit note
					$que->note .= @$item->benefitId>0 ? $item->itemId."|".$que->rows[$k]->itemInfo->name." - Benefit (sleva $item->discount %)\n" : '';
				}
				
				// Multisport evaluation
				$multisportItemSet=false;
				if(isset($possibleMultisportItems)){
					$k = array_keys($possibleMultisportItems, min($possibleMultisportItems))[0];
					$que->multisportItemId = $que->rows[$k]->itemId;
					$que->multisportItemPrice = (int) $que->rows[$k]->itemInfo->vat_price;
					$que->totalPrice -= $que->rows[$k]->itemInfo->vat_price;
				}

				return $que;
			}
		}
		return $que;
	}

	public function getClientMovingHistory($card_id,$date=null){
		$gymRooms = $this->gyms->getGymRooms(false,false,true,true);
		foreach ($gymRooms as $r){
			$rooms[$r->reader_id]=$r->name;
		}
		$occupation=[];
		$clientMoves = $this->API->readers->get_users_in_gym($date,['cardId'=>$card_id]);
		if(isset($clientMoves->data)){
			foreach ($clientMoves->data as $o){
				$occupation['now']['checked_in'] = "$o->year-".sprintf('%02d',$o->month)."-".sprintf('%02d',$o->day)." $o->time:00";
				$occupation['now']['room'] = $rooms[$o->readerId];
				$checked_in = new DateTime($occupation['now']['checked_in']);
				$now = new DateTime(date('Y-m-d H:i:s'));
				$occupation['now']['time_diff'] = $checked_in->diff($now)->format('%H:%I:%S');
				$checked_out=$checked_in;
				if(isset($o->previous_events)){
					foreach ($o->previous_events as $k => $p){
						$occupation['prev'][$k]['checked_in'] = "$o->year-".sprintf('%02d',$o->month)."-".sprintf('%02d',$o->day)." $o->time:00";
						$occupation['prev'][$k]['checked_out'] = $checked_out->format('Y-m-d H:i:s');
						$occupation['prev'][$k]['room'] = $rooms[$p->readerId];
						$checked_in = new DateTime($occupation['prev'][$k]['checked_in']);
						$occupation['prev'][$k]['time_diff'] = $checked_in->diff($checked_out)->format('%H:%I:%S');	
						$checked_out=$checked_in;					
					}
				}
			}
		}
		return $occupation;	
	}
}
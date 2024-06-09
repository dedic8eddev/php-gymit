<div class="row justify-content-between">
    <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
        <li><a class="nav-link active" id="v-pills-info-tab" data-toggle="pill" href="#v-pills-info" role="tab" aria-controls="v-pills-info"><i class="icon icon-info"></i>Informace</a></li>                    
        <li><a class="nav-link" id="v-pills-cart-tab" data-toggle="pill" href="#v-pills-cart" role="tab" aria-controls="v-pills-cart"><i class="icon icon-shopping-cart"></i>Transakční fronta</a></li>
        <li><a class="nav-link" id="v-pills-moving-history-tab" data-toggle="pill" href="#v-pills-moving-history" role="tab" aria-controls="v-pills-moving-history"><i class="icon icon-history"></i>Dnešní historie pohybu</a></li>                                                               
    </ul>
</div>
<div class="tab-content my-3" id="v-pills-tabContent">
    <div class="tab-pane animated fadeInUpShort show active go" id="v-pills-info" role="tabpanel" aria-labelledby="v-pills-info-tab">
        <div class="row my-3">
            <div class="col-md-12">
                <div class="card r-0 shadow">
                    <div class="card-header white">
                        <h6 class="pull-left">Informace o zákazníkovi</h6>
                        <a href="<?php echo base_url('/admin/clients/edit/'.$user->id); ?>" class="btn btn-primary btn-xs float-right" target="_blank">Přejít do detailu zákazníka</a>                      
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <img <?php echo $this->app->getMedia($user_data->photo_src,$user_data->photo_meta); ?> class="card-img-top">
                                    <div class="card-body p-3">
                                        <input type="hidden" id="client_id" value="<?php echo $user->id; ?>" />
                                        <h5 class="card-title"><?php echo $user_data->first_name." ".$user_data->last_name; ?></h5>
                                        <p class="card-text mb-0"><i class="text-primary icon-email mr-2"></i><?php echo $user->email; ?></p>
                                        <p class="card-text mb-0"><i class="text-primary icon-phone mr-2"></i><?php echo $user_data->phone; ?></p>
                                        <?php if (!empty($user_data->street) && !empty($user_data->zip) && !empty($user_data->city)): ?>
                                        <p class="card-text mb-0"><i class="text-primary icon-address-book-o mr-2"></i><?php echo "$user_data->street, $user_data->zip $user_data->city"; ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($user_data->internal_note)): ?>
                                        <hr class="text-primary bg-primary my-2">
                                        <p class="card-text mb-0"><?php echo $user_data->internal_note; ?><p>
                                        <?php endif; ?>
                                    </div>
                                </div>                       
                            </div>
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header white"><h6>Karta</h6></div>
                                    <div class="card-body b-b">
                                        <?php if(isset($card_id)): ?>
                                            <strong>ID Karty:</strong> <span id="card_id"><?php echo $card_id; ?></span>
                                            <?php if(!empty($credit->data)): ?>
                                                <br /><strong>Aktuální kredit:</strong> <?php echo '<span id="card_credit">'.$credit->data->currentValue.'</span> CZK'; ?>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <p class="text-muted text-center mt-2">Klient nemá žádnou klubovou kartu</p>
                                        <?php endif; ?>
                                    </div>                                    
                                </div>
                                <div class="card mt-2">
                                    <div class="card-header white"><h6>Členství</h6></div>
                                    <div class="card-body b-b">
                                        <?php if(!empty($subscription->data)): ?>
                                            <span id="membership_id" class="d-none"><?php echo $subscription_info->id; ?></span>
                                            Klient má aktivní členství <strong><?php echo $subscription_info->name; ?></strong>
                                            <?php $last_payment = array_values(array_slice($subscription->data->transactions, -1))[0]; ?>
                                            <?php 
                                                $active_end;
                                                foreach($subscription->data->transactions as $t){
                                                    if($t->paid){
                                                        $active_end = date("d.m.Y", strtotime($t->end));
                                                    }else{
                                                        break;
                                                    }
                                                }
                                            ?>
                                            <br />Členství je aktivní od <strong><?php echo date("d.m.Y", strtotime($subscription->data->createdOn)) ?></strong> do <strong><?php echo date("d.m.Y", strtotime($last_payment->end)) ?></strong> a zaplaceno do <strong><?php echo $active_end; ?></strong>
                                            <?php //print_r($subscription->data) ?>
                                        <?php else: ?>
                                            <p class="text-muted text-center mt-2">Klient nemá aktivní členství</p>
                                        <?php endif; ?>
                                    </div>
                                </div>                                
                            </div>
                        </div>                 
                    </div>                            
                </div>
            </div>
        </div>                
    </div>           
    <div class="tab-pane animated fadeInUpShort go" id="v-pills-cart" role="tabpanel" aria-labelledby="v-pills-cart-tab">
        <div class="row my-3">
            <div class="col-md-12">
                <div class="card r-0 shadow">
                    <div class="card-header white">
                        <h6 class="pull-left">Položky k zaplacení</h6>
                        <?php if(hasCreatePermission(SECTION_PAYMENTS)): ?>             
                        <a href="<?php echo base_url('admin/payments/index?action=fillSummary&client_id='.$user->id.'&card_id='.@$card_id.'&membership_id='.@$subscription_info->id); ?>" id="go2Checkout" class="btn btn-default btn-xs float-right ml-1 shadow-none" target="_blank">Přejít do pokladny</a>                      
                        <?php endif; ?>
                        <?php if(hasCreatePermission()): ?>             
                        <button id="btnOpenAddQueModal" class="btn btn-primary btn-xs float-right shadow-none" data-toggle="modal" data-remote="<?php echo $addItemModalUrl; ?>" data-target="#modalOverModal" data-modal-title="Přidat položky" data-modal-submit="Přidat do transakční fronty" data-modal-submit-id="btnAddItems2Que">Přidat položku</button>                      
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php $this->load->view('/admin/dashboard/user_que', $que); ?>
                        </div>                    
                    </div>
                </div>
            </div>
        </div>    
    </div>                                    
    <div class="tab-pane animated fadeInUpShort go" id="v-pills-moving-history" role="tabpanel" aria-labelledby="v-pills-moving-history-tab">
        <div class="row my-3">
            <div class="col-md-12">
                <div class="card r-0 shadow">
                    <div class="card-header white">
                        <h6 class="pull-left">Historie od příchodu</h6>
                    </div>
                    <div class="card-body p-0">
                    </div>
                </div>
            </div>
        </div>
    </div>                        
</div>
<div id="clientEditPage" class="page has-sidebar-left has-sidebar-tabs height-full">
    <header class="white relative nav-sticky">
        <div class="container-fluid text-primary">
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li><a class="nav-link active" id="v-pills-info-tab" data-toggle="pill" href="#v-pills-info" role="tab" aria-controls="v-pills-info"><i class="icon icon-info"></i>Informace</a></li>
                    <li><a class="nav-link" id="v-pills-client-transactions-tab" data-toggle="pill" href="#v-pills-client-transactions" role="tab" aria-controls="v-pills-client-transactions"><i class="icon icon-history"></i>Historie transakcí</a></li>      
                    <!--<li><a class="nav-link" id="v-pills-benefits-tab" data-toggle="pill" href="#v-pills-benefits" role="tab" aria-controls="v-pills-benefits"><i class="icon icon-star-3"></i>Využité členské výhody</a></li>-->                        
                    <li><a class="nav-link" id="v-pills-purchased-items-tab" data-toggle="pill" href="#v-pills-purchased-items" role="tab" aria-controls="v-pills-purchased-items"><i class="icon icon-barcode"></i>Historie produktů</a></li>  
                    <li><a class="nav-link" id="v-pills-sub-payments-tab" data-toggle="pill" href="#v-pills-sub-payments" role="tab" aria-controls="v-pills-sub-payments"><i class="icon icon-money"></i>Očekávané platby</a></li>  
                    <li><a class="nav-link" id="v-pills-edit-tab" data-toggle="pill" href="#v-pills-edit" role="tab" aria-controls="v-pills-edit"><i class="icon icon-pencil"></i>Upravit</a></li>      
                </ul>
            </div>
        </div>
    </header>
    <?php $this->load->view('layout/alerts'); ?>
    <div class="container-fluid relative animatedParent animateOnce my-3">
        <div class="tab-content my-3" id="v-pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active go" id="v-pills-info" role="tabpanel" aria-labelledby="v-pills-info-tab">
            <div class="card r-0 shadow">
                    <div class="card-header white">
                        <h6 class="pull-left">Informace o zákazníkovi</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <img <?php echo $this->app->getMedia($user_data->photo_src,$user_data->photo_meta); ?> class="card-img-top">
                                    <div class="card-body p-3">
                                        <span id="client_id" class="d-none"><?php echo $user->id; ?></span>
                                        <h5 class="card-title mb-0"><?php echo $user_data->first_name." ".$user_data->last_name; ?></h5>
                                        <p class="card-text" style="font-size:12px;"><b>Zákazník Založen uživatelem:</b> <?php echo $user->created_by_name; ?></p>
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
                                                <br /><strong>Aktuální kredit:</strong> <?php echo $credit->data->currentValue . ' CZK'; ?>
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
                                            <br />Číslo smlouvy:
                                            <strong><?php echo $subscription->data->contractNumber; ?></strong>
                                        <?php else: ?>
                                            <p class="text-muted text-center mt-2">Klient nemá aktivní členství</p>
                                        <?php endif; ?>
                                    </div>
                                </div>                                
                            </div>
                        </div>                 
                    </div>                            
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button class="btn btn-danger btn-sm btn-forbid-access">Zakázat vstup</button>                    
                    </div>
                </div>
            </div>

            <div class="tab-pane animated fadeInUpShort go" id="v-pills-client-transactions" role="tabpanel" aria-labelledby="v-pills-client-transactions-tab">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Seznam transakcí</h6>
                                <button class="btn btn-danger btn-xs float-right js-clear-filter" data-table="transactions_history_table">Zrušit filtr</button>
                            </div>
                            <div class="table-responsive">
                                <table id="transactionsHistoryTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $transactionsHistoryUrl; ?>"></table>
                            </div>                            
                        </div>
                    </div>
                </div>
            </div> 

            <div class="tab-pane animated fadeInUpShort go" id="v-pills-benefits" role="tabpanel" aria-labelledby="v-pills-benefits-tab">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Využité členské výhody</h6>
                                <button class="btn btn-danger btn-xs float-right js-clear-filter" data-table="membership_benefits_usage_table">Zrušit filtr</button>
                            </div>
                            <div class="table-responsive">
                                <table id="membershipBenefitsUsageTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $membershipBenefitsUsageUrl; ?>"></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>            

            <div class="tab-pane animated fadeInUpShort go" id="v-pills-purchased-items" role="tabpanel" aria-labelledby="v-pills-purchased-items-tab">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Seznam zakoupených produktů a služeb</h6>
                                <button class="btn btn-danger btn-xs float-right js-clear-filter" data-table="purchased_items_table">Zrušit filtr</button>
                            </div>
                            <div class="table-responsive">
                                <table id="purchasedItemsTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $purchasedItemsUrl; ?>"></table>
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>                 

            <div class="tab-pane fade" id="v-pills-sub-payments" role="tabpanel" aria-labelledby="v-pills-sub-payments">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Očekávané platby</h6>
                            </div>
                            <div class="table-responsive">
                                <table id="subscriptionPaymentTable" class="table table-striped table-hover r-0"></table>
                            </div>
                        </div>
                    </div>
                </div>    
            </div>               

            <div class="tab-pane animated fadeInUpShort go" id="v-pills-edit" role="tabpanel" aria-labelledby="v-pills-edit-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header white"><h6>Úprava zákazníka</h6></div>
                            <div class="card-body b-b">
                                <form id="saveClientForm">                      
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="email">Datum registrace</label>
                                            <input class="form-control" disabled type="text" name="date_created" value="<?php echo date('d.m.Y H:i', strtotime(@$user->date_created)); ?>">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="phone">Poslední přihlášení</label>
                                            <input class="form-control" disabled type="text" name="last_login" value="<?php if(@$user->last_login != NULL) echo date('d.m.Y H:i', strtotime(@$user->last_login)); ?>">
                                        </div>                           
                                    </div>                          
                                    <?php $this->load->view('admin/clients/client_form'); ?>    
                                    <hr>        
                                    <?php if(hasEditPermission() || hasDeletePermission()): ?>                   
                                        <button class="btn btn-sm btn-primary save-user-submit" data-ajax="<?php echo $saveDetail; ?>" data-id="<?php echo $user->id; ?>">Uložit klienta</button>&nbsp;
                                        <?php if($user->active): ?>
                                            <button class="btn btn-sm btn-danger remove-user" data-ajax="<?php echo $removeUser; ?>" data-id="<?php echo $user->id; ?>">Deaktivovat klienta</button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-success activate-user" data-ajax="<?php echo $activateUser; ?>" data-id="<?php echo $user->id; ?>">Aktivovat klienta</button>
                                        <?php endif; ?>   
                                    <?php endif; ?>                          
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>   
           
        </div>         
        <hr>
        <div class="row">
            <div class="col-md-12">
                <a href="/admin/clients" class="btn btn-primary btn-sm">
                    <i class="icon icon-chevron-left"></i>
                    Zpět na přehled
                </a>
            </div>
        </div>
    </div>    
    <!-- POPOVER -->
    <div id="forbid-access-popover" class="d-none">
        <form>
            <textarea name="reason" class="form-control js-reason-textarea" placeholder="Důvod.." required></textarea>
            <button class="btn btn-primary btn-sm btn-block mt-2 btn-submit-forbid-access" data-ajax="<?php echo $forbidAccessUrl; ?>">Potvrdit</button>
        </form>
    </div>      
</div>   
<div id="paymentsPage" class="page has-sidebar-left has-sidebar-tabs">
    <header class="white relative nav-sticky">
        <div class="container-fluid text-primary">
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist" style="width:auto;">
                    <li>
                        <a class="nav-link active" id="v-pills-add-tab" data-toggle="pill" href="#v-pills-new" role="tab" aria-controls="v-pills-new"><i class="icon icon-plus-circle"></i>Nová transakce</a>
                    </li>
                    <li>
                        <a class="nav-link" id="v-pills-subs-tab" data-toggle="modal" href="#subModal"><i class="icon icon-account_circle"></i>Správa členství</a>
                    </li>
                    <li>
                        <a class="nav-link" id="v-pills-voucher-tab" data-toggle="modal" href="#voucherModal"><i class="icon icon-gift"></i>Aplikovat voucher</a>
                    </li>        
                    <li>
                        <a class="nav-link" id="v-pills-invoices-tab" data-toggle="pill" href="#v-pills-invoices" role="tab" aria-controls="v-pills-invoices"><i class="icon icon-document-table2"></i>Faktury</a>
                    </li>            
                    <li>
                        <a class="nav-link" id="v-pills-list-tab" data-toggle="pill" href="#v-pills-list" role="tab" aria-controls="v-pills-list"><i class="icon icon-list-alt"></i>Seznam transakcí</a>
                    </li>                               
                </ul>
                <div class="float-right pt-2 pr-2 mr-2 text-dark">
                    <b>Pokladna:</b> <span id="checkout_name"></span>
                    <a class="mr-1" id="show-close-checkout-modal" href="javascript:;" data-toggle="modal" data-remote="" data-target="#modal" title="Zavřít pokladnu" data-modal-title="Zavřít pokladnu" data-modal-submit="Zavřít pokladnu"><i class="icon-lock3 s-18 text-danger"></i></a>
                    <a class="d-none mr-1" id="show-open-checkout-modal" href="javascript:;" data-toggle="modal" data-remote="" data-target="#modal" title="Otevřít pokladnu" data-modal-title="Otevřít pokladnu" data-modal-submit="Otevřít pokladnu"><i class="icon-lock-open2 text-success"></i></a>

                    <a id="show-devices-picker" href="javascript:;" title="Vybrat pokladnu"><i class="icon-open_in_new text-dark"></i></a>&nbsp;
                </div>                
            </div>
        </div>
    </header>
    <div class="container-fluid animatedParent animateOnce">
        <div class="tab-content my-3" id="v-pills-tabContent">
            
            <div class="tab-pane animated fadeInUpShort show active go" id="v-pills-new" role="tabpanel" aria-labelledby="v-pills-new-tab">
                <div class="row my-3">
                    <div style="width:63%;" class="pl-2">
                        <div class="card r-0 shadow">
                            <div class="card-body b-b">
                                <div class="row">
                                    <div class="col-md-8">
                                        <label for="clientId" class="sr-only">Klient</label>
                                        <select id="clientId" name="clientId" class="select2">
                                            <?php if(!isset($client_id)): ?>
                                            <option value="" selected disabled>Vyberte klienta</option>
                                            <?php endif; ?>
                                            <?php foreach($clients as $item): ?>
                                                <option value="<?php echo $item['id']; ?>" <?php echo is_numeric($item['created_by']) && $item['created_by']==0 ? 'data-non-client="1"':''; ?> <?php echo @$client_id==$item['id'] ? "data-cardId='".@$card_id."' selected":''; ?>><?php echo $item['last_name'].' '.$item['first_name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>                                        
                                    </div>  
                                    <div id="clientData" class="col-md-4 pt-2">
                                        Kredit: <span id="c_credit" class="font-weight-bold"><?php echo @$credit; ?></span>
                                    </div>                                                                                
                                </div>                                   
                            </div>                 
                        </div>                        
                        <div class="card r-0 shadow mt-1">
                            <div class="card-header white">
                                <h6 class="pull-left">Položky</h6>                            
                            </div>
                            <div class="card-body b-b">                     
                                <div class="row">
                                    <div class="col-2 mb-3" style="border-right:1px solid #e1e8ee;">
                                        <ul class="nav nav-pills flex-column" id="myTab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="service-tab" data-toggle="tab" href="#service" role="tab" aria-controls="service" aria-selected="true">Služby</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="credit-tab" data-toggle="tab" href="#credit" role="tab" aria-controls="credit" aria-selected="false">Kredit</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="depot-tab" data-toggle="tab" href="#depot" role="tab" aria-controls="depot" aria-selected="false">Sklad</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-10">
                                        <div class="tab-content" id="myTabContent">
                                            <div class="tab-pane fade show active" id="service" role="tabpanel" aria-labelledby="service-tab">                                   
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label for="service_item">Položka</label>
                                                        <select id="service_item" name="service_item" class="select2">
                                                            <option value="" selected disabled>Vyberte položku</option>
                                                            <?php foreach($price_list as $item): ?>
                                                                <option value="<?php echo $item['id']; ?>" data-service-type="<?php echo $item['service_type']; ?>" data-service-subtype="<?php echo $item['service_subtype']; ?>" data-price="<?php echo $item['vat_price']; ?>" data-vat="<?php echo $item['vat']; ?>"><?php echo $item['name']; ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>                                                                                                                                                   
                                                </div> 
                                                <button id="btnAddServiceItem" class="btn btn-sm btn-primary">Přidat položku</button>&nbsp;                                                
                                            </div>
                                            <div class="tab-pane fade" id="depot" role="tabpanel" aria-labelledby="depot-tab">
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label for="depot_id">Sklad</label>
                                                        <select id="depot_id" name="depot_id" class="select2">
                                                            <?php foreach($depots as $depot): ?>
                                                                <option value="<?php echo $depot->id; ?>"><?php echo $depot->name; ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="depot_item">Položka</label>
                                                        <select id="depot_item" name="depot_item" class="select2"></select>
                                                    </div>
                                                </div>  
                                                <button id="btnAddDepotItem" class="btn btn-sm btn-primary">Přidat položku</button>
                                            </div>
                                            <div class="tab-pane fade" id="credit" role="tabpanel" aria-labelledby="credit-tab">
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label for="credit">Hodnota (s DPH)</label>
                                                        <input id="creditValue" type="number" name="credit" class="form-control">
                                                    </div>
                                                </div>
                                                <button id="btnTopUpCredit" class="btn btn-sm btn-primary">Dobít kredit</button>&nbsp;
                                                <?php if(hasEditPermission(SECTION_TRANSACTIONS)): ?>
                                                <button id="btnRefundCredit" class="btn btn-sm btn-warning" data-toggle="modal" data-remote="<?php echo base_url('/admin/payments/refund_credit'); ?>" data-target="#modal" data-modal-title="Vrátit kredit" data-modal-submit="">Vrátit kredit</button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>                                                                                                                                                                                           
                            </div>                          
                        </div>
                        <div class="card r-0 shadow mt-1">
                            <div class="card-header white">
                                <h6>Typ platby</h6>
                                <div class="form-row mt-3">
                                    <div class="form-group col-md-12 mb-0">
                                        <label class="purchase-type">
                                            <input type="checkbox" name="purchase_type" class="card-input-element d-none" value="1">
                                            <div class="card card-body bg-light p-2"><i class="icon icon-money"></i><span>Hotově</span></div>
                                        </label>
                                        <label class="purchase-type">
                                            <input type="checkbox" name="purchase_type" class="card-input-element d-none" value="2">
                                            <div class="card card-body bg-light p-2"><i class="icon icon-credit-card-alt"></i><span>Kartou</span></div>

                                            <a class="terminal-menu"><i class="icon icon-menu"></i></a>
                                        </label>
                                        <label class="purchase-type">
                                            <input type="checkbox" name="purchase_type" class="card-input-element d-none" value="3">
                                            <div class="card card-body bg-light p-2"><i class="icon icon-card_membership"></i><span>Kredit</span></div>
                                        </label>  
                                        <label class="purchase-type">
                                            <input type="checkbox" name="purchase_type" class="card-input-element d-none" value="14">
                                            <div class="card card-body bg-light p-2"><i class="icon icon-bank"></i><span>Převodem</span></div>
                                        </label>
                                        <!--<label class="purchase-type">
                                            <input type="checkbox" name="purchase_type" class="card-input-element d-none" value="6">
                                            <div class="card card-body bg-light p-2"><i class="icon icon-ticket"></i><span>E-ticket</span></div>
                                        </label>-->
                                    </div>        
                                </div>   
                                <div class="form-row">
                                    <div class="form group col-md-12 mb-2">
                                        <label class="purchase-type">
                                            <input type="checkbox" name="purchase_type" class="card-input-element d-none" value="7">
                                            <div class="card card-body bg-light p-2"></span><img class="img-sodexo" src="/public/assets/img/payments_methods/sodexo.svg" /><span class="d-none">Sodexo (poukázky)</span></div>
                                        </label> 
                                        <label class="purchase-type">
                                            <input type="checkbox" name="purchase_type" class="card-input-element d-none" value="11">
                                            <div class="card card-body bg-light p-2"></span><img class="img-edenred" src="/public/assets/img/payments_methods/edenred.png" /><span class="d-none">Edenred (poukázky)</span></div>
                                        </label> 
                                        <label class="purchase-type">
                                            <input type="checkbox" name="purchase_type" class="card-input-element d-none" value="12">
                                            <div class="card card-body bg-light p-2"></span><img class="img-benefity" src="/public/assets/img/payments_methods/benefity.png" /><span class="d-none">Benefity (karta)</span></div>
                                        </label>                                            
                                        <label class="purchase-type">
                                            <input id="benefit_plus" type="checkbox" name="purchase_type" class="card-input-element d-none parentPT" disabled>
                                            <div class="card card-body bg-light p-2 btn-pt-popover" data-poptitle="Zvolte typ" data-target="#benefitPlusPopover"></span><img class="img-benefitplus" src="/public/assets/img/payments_methods/benefitplus.png" /><span class="d-none">Benefit plus</span></div>
                                        </label>                                                                                                                                                                                                             
                                    </div>
                                </div>
                                <h6>Zvýhodněná platba</h6>         
                                <div class="form-row mt-3">
                                    <div class="form-group col-md-12">
                                        <label class="purchase-type">
                                            <input type="checkbox" name="purchase_type" class="card-input-element d-none" value="4">
                                            <div class="card card-body bg-light p-2"></span><img class="img-multisport" src="/public/assets/img/payments_methods/multisport.png" /><span class="d-none">Multisport (karta)</span></div>
                                        </label>                                                                                                                                                                                                  
                                    </div>
                                </div>                      
                            </div>
                        </div>                        
                    </div>
                    <div class="px-2" style="width:37%;">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Rekapitulace</h6>
                            </div>   
                            <div class="card-body b-b">
                                <form id="addPaymentForm" data-ajax="<?php echo $addUrl; ?>">
                                    <table id="summaryTable" class="w-100">
                                        <thead>
                                            <tr>
                                                <th>Položka</th>
                                                <th class='text-right' style="width:80px;">Počet</th>
                                                <th class='text-right' style="width:80px;">Sleva (%)</th>
                                                <th class='text-right'>Cena</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(isset($que)): ?>
                                            <?php foreach ($que->rows as $item): ?>
                                                <?php $price = ($item->itemInfo->vat_price - ($item->itemInfo->vat_price * ($item->discount ?? 0) / 100)) * $item->amount; ?>
                                                <?php if(isset($item->depotId)): ?>
                                                    <tr>
                                                        <td><?php echo $item->itemInfo->name; ?><br /><small>Sklad: <?php echo $item->itemInfo->depot_name; ?></small></td>
                                                        <td class='text-right' <?php echo @$item->benefitId>0 ? "data-benefit='$item->benefitId'" : ''; ?> data-vat='<?php echo $item->itemInfo->vat; ?>' data-stock='<?php echo $item->itemInfo->depot_stock; ?>' data-depotid='<?php echo $item->depotId; ?>' data-id='<?php echo $item->itemId; ?>' data-price='<?php echo number_format($item->itemInfo->vat_price, 2); ?>'>
                                                            <input min="1" max="999" class='form-control input-count' type='number' value='<?php echo $item->amount; ?>' />
                                                        </td>
                                                        <td>
                                                            <input step="5" min="0" max="100" class='form-control input-discount' type='number' value='<?php echo $item->discount ?? 0; ?>' />
                                                        </td>                                                        
                                                        <td class='text-right'><?php echo number_format($price, 2, '.', ' '); ?></td><td><i class="icon-close text-danger float-right" onclick="PAYMENTS.removeItem(this);"></i></td>
                                                    </tr>                                                
                                                <?php else: ?>
                                                    <tr>
                                                        <td><?php echo $item->itemInfo->name; ?></td>
                                                        <td class='text-right' <?php echo @$item->benefitId>0 ? "data-benefit='$item->benefitId'" : ''; ?> data-id='<?php echo $item->itemId; ?>' data-service-type="<?php echo $item->itemInfo->service_type; ?>" data-service-subtype="<?php echo $item->itemInfo->service_subtype; ?>" data-price='<?php echo number_format($item->itemInfo->vat_price, 2); ?>' data-vat='<?php echo $item->itemInfo->vat; ?>' data-service='1'>
                                                            <input min="1" max="999" class='form-control input-count' type='number' value='<?php echo $item->amount; ?>' />
                                                        </td>
                                                        <td>
                                                            <input step="5" min="0" max="100" class='form-control input-discount' type='number' value='<?php echo $item->discount ?? 0; ?>' />
                                                        </td>
                                                        <td class='text-right'><?php echo number_format($price, 2, '.', ' '); ?></td><td><i class="icon-close text-danger float-right" onclick="PAYMENTS.removeItem(this);"></i></td>
                                                    </tr>  
                                                    <?php if(isset($item->overtimeFee)): ?>
                                                    <tr>
                                                        <td><?php echo config_item('app')['services'][$item->itemInfo->service_type]; ?> - Přesčas<br /><small>Minuty: <?php echo $item->overtimeMinutes; ?></small></td>
                                                        <td class='text-right' data-id='<?php echo $item->itemId; ?>' data-vat='0' data-price='<?php echo $item->itemInfo->overtime_fee_price; ?>' data-service='1' data-overtime='1'>
                                                            <input class='form-control input-count' type='number' value='<?php echo $item->overtimeFee / $item->itemInfo->overtime_fee_price; ?>' disabled />
                                                        </td>
                                                        <td>
                                                            <input step="5" min="0" max="100" class='form-control input-discount' type='number' value='0' />
                                                        </td>
                                                        <td class='text-right'><?php echo number_format($item->overtimeFee, 2, '.', ' '); ?></td><td><i class="icon-close text-danger float-right" onclick="PAYMENTS.removeItem(this);"></i></td>
                                                    </tr>
                                                    <?php endif; ?>                                              
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                    <table id="purchaseTypeTable" class="w-100 mt-4">
                                        <thead>
                                            <tr>
                                                <th>Typ platby</th>
                                                <th class='text-right' style="width:110px;">Cena</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(@$que->multisportItemPrice > 0): ?>
                                                <tr id="pt_4"><td>Multisport (karta)</td><td class="text-right"><input min="1" max="999999" step="0.0001" class="form-control" type="number" data-id="4" value="<?php echo $que->multisportItemPrice; ?>" onchange="PAYMENTS.checkPurchaseTypeValueChange(this);"></td><td><i class="icon-close text-danger float-right" onclick="PAYMENTS.removePurchaseTypeItem(this,4);"></i></td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>  
                                    <p class="font-weight-bold mb-0 collapsed mt-3" style="padding:5px">
                                        Systémové a interní poznámky
                                    </p>
                                    <hr class="mt-0" style="height:1px; background:var(--primary); color:var(--primary);">
                                    <div id="systemNotes" <?php echo isset($que->note) && !empty($que->note) ? '' : 'style="display:none;"'; ?>>
                                    <?php if(isset($que->note) && !empty($que->note)): ?>
                                        <?php foreach(explode('\n',$que->note) as $n): ?>  
                                            <?php list($id, $text) = explode("|", $n); ?>
                                            <p class="mb-0" data-id="<?php echo $id; ?>"><?php echo $text; ?></p>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    </div>  
                                    <textarea rows="2" id="note" class="form-control mt-1" placeholder="Interní poznámka..."></textarea>    
                                    <div id="vatInfo" class="row mt-3">
                                        <div class="col-md-12">
                                            <p class="font-weight-bold mb-0 collapsed" style="padding:5px; cursor:pointer;" data-toggle="collapse" href="#vatInfoForm" onclick="$(this).find('i').toggleClass('icon-keyboard_arrow_up icon-keyboard_arrow_down');">
                                                Doplňující informace k subjektu
                                                <i class="icon-keyboard_arrow_up float-right"></i>
                                            </p>
                                            <hr class="mt-0" style="height:1px; background:var(--primary); color:var(--primary);">
                                        </div>
                                        <div class="collapse col-md-12 mb-3" id="vatInfoForm">
                                            <div class="row">
                                                <div class="col-md-6 pr-2">
                                                    <input name="subject_name" type="text" class="form-control" placeholder="Název subjektu" />
                                                </div>
                                                <div class="col-md-6 pl-2">
                                                    <input name="subject_street" type="text" class="form-control" placeholder="Ulice" />                                            
                                                </div>
                                                <div class="col-md-8 pr-2 mt-2">
                                                    <input name="subject_city" type="text" class="form-control" placeholder="Město" />                                            
                                                </div>  
                                                <div class="col-md-4 pl-2 mt-2">
                                                    <input name="subject_zip" type="text" class="form-control" placeholder="PSČ" />                                            
                                                </div>
                                                <div class="col-md-6 pr-2 mt-2">
                                                    <input name="subject_id" type="text" class="form-control" placeholder="IČ" />                                            
                                                </div>
                                                <div class="col-md-6 pl-2 mt-2">
                                                    <input name="subject_vat_id" type="text" class="form-control" placeholder="DIČ" />                                            
                                                </div>    
                                                <div class="col-md-12 mt-2">
                                                    <?php $this->app_components->getSelect2Country(['input_name' => 'country','id' => 'country_id']); ?>                                          
                                                </div>                                                                                                                                                                                               
                                            </div>
                                        </div>                                                                               
                                    </div>

                                    <div id="cardCashback" class="row" style="display:none;">
                                        <div class="col-md-12">
                                            <p class="font-weight-bold mb-0 collapsed" style="padding:5px; cursor:pointer;" data-toggle="collapse" href="#cashbackForm" onclick="$(this).find('i').toggleClass('icon-keyboard_arrow_up icon-keyboard_arrow_down');">
                                                Cashback (výběr hotovosti z karty)
                                                <i class="icon-keyboard_arrow_up float-right"></i>
                                            </p>
                                            <hr class="mt-0" style="height:1px; background:var(--primary); color:var(--primary);">
                                        </div>
                                        <div class="collapse col-md-12" id="cashbackForm">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <input name="cashback" type="number" step="1" class="form-control" placeholder="Hodnota cashbacku" />
                                                </div>
                                            </div>
                                        </div> 
                                    </div>

                                    <button id="pay" type="submit" class="btn btn-sm btn-primary btn-block mt-4">Zaplatit <span id="price_total"><?php echo isset($que->totalPrice) ? number_format($que->totalPrice, 2, '.', ' ') : '0.00'; ?></span> Kč</button>&nbsp;
                                    <br />
                                    <a href="<?php echo url('admin/contract/preview'); ?>" target="_blank" id="printContractPreview" class="btn btn-md btn-primary hidden mt-4">Náhled smlouvy</a>
                                </form>
                            </div>                         
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane animated fadeInUpShort go" id="v-pills-list" role="tabpanel" aria-labelledby="v-pills-list-tab">
                <p><strong>Nápověda: </strong>Podržením tlačítka SHIFT označíte více položek najednou</p>
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Seznam transakcí</h6>
                                
                                <button class="btn btn-danger btn-xs float-right js-payments-clear-filter">Zrušit filtr</button>
                                <button class="btn btn-danger btn-xs purple lighten-2 float-right mr-2 print-receipts">Vytisknout doklady</button>
                                <?php if(hasEditPermission(SECTION_TRANSACTIONS)): ?>
                                <button class="btn btn-success btn-xs float-right close-transaction mr-2 disabled">Uzavřít transakce</button>
                                <button class="btn btn-warning btn-xs float-right close-day mr-2 disabled">Uzavřít den</button>
                                <?php endif; ?>
                            </div>
                            <div class="table-responsive">
                                <table id="paymentsTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $getAllUrl; ?>"></table>
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>   
            
            <div class="tab-pane animated fadeInUpShort go" id="v-pills-invoices" role="tabpanel" aria-labelledby="v-pills-invoices-tab">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Faktury</h6>
                                
                                <button class="btn btn-danger btn-xs float-right js-invoices-clear-filter">Zrušit filtr</button>
                                <?php if(hasCreatePermission(SECTION_INVOICES)): ?>
                                <button class="btn btn-success btn-xs float-right add-invoice mr-2">Vytvořit fakturu</button>
                                <?php endif; ?>
                            </div>
                            <div class="table-responsive">
                                <table id="invoicesTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $getInvoicesUrl; ?>"></table>
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>   

        </div>
    </div> 
    <!-- jQuery UI - DIALOG -->
    <div id="dialog" style="display:none;"></div>  
    <!-- CARD MODAL TRIGGER -->
    <a class="d-none" id="btn-create-card-modal" href="javascript:;" data-toggle="modal" data-remote="<?php echo base_url('/admin/payments/create_card'); ?>" data-target="#modal" data-modal-title="Klient bez karty" data-modal-submit="Přidat"></a>
    <!-- AJAX MODAL -->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header r-0">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button id="btn-dismiss-modal" type="button" class="btn btn-default" data-dismiss="modal">Zrušit</button>
                    <button type="button" class="btn btn-primary" id="modalSubmit">Přidat</button>                    
                </div>
            </div>
        </div>
    </div> 
    <!-- POPOVER -->
    <div id="benefitPlusPopover" class="d-none">
        <button data-parent="benefit_plus" data-id="8" data-title="Benefit plus (objednávka)" class="js-pt-subtype btn btn-block btn-default shadow-none">Objednávka</button>
        <button data-parent="benefit_plus" data-id="9" data-title="Benefit plus (karta)" class="js-pt-subtype btn btn-block btn-default shadow-none">Karta</button>
        <button data-parent="benefit_plus" data-id="10" data-title="Benefit plus (platební karta)" class="js-pt-subtype btn btn-block btn-default shadow-none mt-1">Platební karta</button>
    </div>   
    <div id="solariumPopover" class="d-none">
        <select class="solarium_id form-control">
            <?php foreach($solariums as $s): ?>
                <option value="<?php echo $s->id; ?>"><?php echo $s->name; ?></option>
            <?php endforeach; ?>
        </select>
        <input type="number" class="form-control solarium-minutes mt-1" placeholder="Počet minut" />
        <button onclick="PAYMENTS.addSolariumToSummary(this);" class="btn btn-primary btn-block mt-2">Přidat položku</button>
    </div>   
</div>                       

<div class="modal fade" id="voucherModal" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header r-0">
                <h5 class="modal-title">Aplikovat voucher</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <label for="voucher_code">Kód voucheru</label>
                <input id="voucher_code" class="form-control" placeholder="Vložte kód voucheru" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Zrušit</button>
                <button type="button" class="btn btn-primary" id="applyVoucher">Potvrdit</button>                    
            </div>
        </div>
    </div>
</div> 
<?php $this->load->view('admin/payments/sub_modal'); ?>
<?php $this->load->view('admin/payments/add_sub_modal'); ?>
<?php $this->load->view('admin/payments/checkout_modal'); ?>
<?php $this->load->view('admin/payments/trans_edit_modal'); ?>
<?php $this->load->view('admin/payments/trans_refund_modal'); ?>
<?php $this->load->view('admin/payments/add_invoice_modal'); ?>
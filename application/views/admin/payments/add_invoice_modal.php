<div class="modal fade" id="invoicePayModal" tabindex="-1" role="dialog" aria-labelledby="invoicePayModal" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-body">

            <div class="row">
                <div class="col-md-12 text-center mb-3"><strong>Jak chcete fakturu zaplatit?</strong></div>
                <div class="col-md-6">
                    <a class="btn btn-primary pay-by-card" style="width: 100%;">Kartou</a>
                </div>
                <div class="col-md-6">
                <a class="btn btn-primary pay-by-cash" style="width: 100%;">Hotově</a>
                </div>
            </div>

        </div>
    </div>
  </div>
</div>

<!-- INVOICE MODAL -->
<div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog" aria-labelledby="invoiceModal" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">

    <div class="fader"></div>

      <div class="modal-header">
        <div class="select-container float-left" style="width: 50%;">
            Nová faktura
        </div>
      </div>
      <div class="modal-body">
            <form id="addInvoiceForm">
                <div class="container-fluid">
                    <div class="form-row">
                        <div class="col-md-12">
                            <label for="invoiceClient" class="sr-only">Klient <span class="required">*</span></label>
                            <select id="invoiceClient" name="client_id" class="select2" required>
                                <option value="" selected disabled>Vyberte klienta</option>
                                <?php foreach($clients as $item): ?>
                                    <option value="<?php echo $item['id']; ?>" <?php echo is_numeric($item['created_by']) && $item['created_by']==0 ? 'data-non-client="1"':''; ?> <?php echo @$client_id==$item['id'] ? 'selected':''; ?> ><?php echo $item['last_name'].' '.$item['first_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row client-data-container" style="display: none;">
                        <input type="hidden" name="client_card_id" />
                        <div class="col-md-6">
                            <label for="client_name">Jméno</label>
                            <input type="text" name="client_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="client_street">Ulice</label>
                            <input type="text" name="client_street" class="form-control">
                        </div>

                        <div class="col-md-4 mt-1">
                            <label for="client_city">Město</label>
                            <input type="text" name="client_city" class="form-control">
                        </div>
                        <div class="col-md-4 mt-1">
                            <label for="client_zip">PSČ</label>
                            <input type="text" name="client_zip" class="form-control">
                        </div>
                        <div class="col-md-4 mt-1">
                            <label for="client_country">Stát</label>
                            <?php $this->app_components->getSelect2Country(['input_name' => 'client_country','id' => 'invoice_client_country']); ?>                                          
                        </div>

                        <div class="col-md-6 mt-1">
                            <label for="client_company_id">IČ</label>
                            <input type="text" name="client_company_id" class="form-control">
                        </div>
                        <div class="col-md-6 mt-1">
                            <label for="client_vat_id">DIČ</label>
                            <input type="text" name="client_vat_id" class="form-control">
                        </div>
                    </div>

                    <div class="form-row mt-2">
                        <div class="col-md-12">
                            <label for="items">Položky</label>
                            <div class="invoice-items"></div>
                        </div>

                        <div class="col-md-3">
                            <a class="btn btn-primary btn-sm btn-block mt-2 addInvoiceItem" data-type="custom"><i class="icon icon-plus-circle"></i> Vlastní</a> 
                        </div>
                        <div class="col-md-3">
                            <a class="btn btn-primary btn-sm btn-block mt-2 addInvoiceItem" data-type="service"><i class="icon icon-plus-circle"></i> Ceník</a> 
                        </div>
                        <div class="col-md-3">
                            <a class="btn btn-primary btn-sm btn-block mt-2 addInvoiceItem" data-type="membership"><i class="icon icon-plus-circle"></i> Členství</a> 
                        </div>
                        <div class="col-md-3">
                            <a class="btn btn-primary btn-sm btn-block mt-2 addInvoiceItem" data-type="depot"><i class="icon icon-plus-circle"></i> Sklad</a> 
                        </div>
                    </div>

                    <div class="form-row mt-2">
                        <div class="col-md-6">
                            <label for="issue_date">Platební metoda <span class="required">*</span></label>
                            <select required class="select2 shadow-none" data-minimum-results-for-search="Infinity" name="payment_method">
                                <option value="14">Bankovní převod</option>
                                <option value="1">Hotovost</option>
                                <option value="2">Kartou</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="issue_date">Datum vystavení <span class="required">*</span></label>
                            <input required type="date" class="form-control" name="issue_date">
                        </div>
                        <div class="col-md-3">
                            <label for="due_date">Datum splatnosti <span class="required">*</span></label>
                            <input required type="date" class="form-control" name="due_date">
                        </div>
                    </div>

                </div>
            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Zrušit</button>
        <button type="button" class="btn btn-primary" id="addInvoiceSubmit">Přidat fakturu</button>
      </div>
    </div>
  </div>
</div>

<div id="invoiceItemCustomTemplate" style="display: none;">
    <div class="row item-row mb-2">
        <div class="col-md-4">
            <input required type="text" name="item_name" class="form-control" placeholder="Název">
        </div>
        <div class="col-md-2">
            <input type="number" name="item_discount" class="form-control" placeholder="Sleva %">
        </div>
        <div class="col-md-2">
            <input required type="number" min="1" name="item_amount" class="form-control" placeholder="Ks">
        </div>
        <div class="col-md-3">
            <input required type="number" name="item_value" class="form-control" placeholder="Cena/ks">
        </div>
        <div class="col-md-1">
            <a class="remove-invoice-item btn btn-sm btn-danger text-center" style="display: block;"><i class="icon-trash"></i></a>
        </div>
        <div class="col-md-12 mt-2">
            <?php $this->app_components->getSelect2AutocontAccounts(['input_name' => 'account_number', 'required' => true, 'init_later' => true]); ?>
        </div>
    </div>
</div>

<div id="invoiceItemDepotTemplate" style="display: none;">
    <div class="row item-row mb-2">
        <div class="col-md-4">
            <?php $this->app_components->getSelect2DepotItems(['input_name' => 'item_name', "required" => true, "default_empty" => true, "init_later" => true]); ?>
        </div>
        <div class="col-md-2">
            <input type="number" name="item_discount" class="form-control" placeholder="Sleva %">
        </div>
        <div class="col-md-2">
            <input required type="number" min="1" name="item_amount" class="form-control" placeholder="Ks">
        </div>
        <div class="col-md-3">
            <input required type="text" name="item_value" class="form-control" placeholder="Cena/ks" readonly>
        </div>
        <div class="col-md-1">
            <a class="remove-invoice-item btn btn-sm btn-danger text-center" style="display: block;"><i class="icon-trash"></i></a>
        </div>
    </div>
</div>

<div id="invoiceItemServiceTemplate" style="display: none;">
    <div class="row item-row mb-2">
        <div class="col-md-4">
            <select data-type="service" name="item_name" class="" required>
                <option value="" selected disabled>Ceníková Položka</option>
                <?php foreach($price_list as $item): ?>
                    <option value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" name="item_discount" class="form-control" placeholder="Sleva %">
        </div>
        <div class="col-md-2">
            <input required type="number" min="1" name="item_amount" class="form-control" placeholder="Ks">
        </div>
        <div class="col-md-3">
            <input required type="text" name="item_value" class="form-control" placeholder="Cena/ks" readonly>
        </div>
        <div class="col-md-1">
            <a class="remove-invoice-item btn btn-sm btn-danger text-center" style="display: block;"><i class="icon-trash"></i></a>
        </div>
    </div>
</div>

<div id="invoiceItemMembershipTemplate" style="display: none;">
    <div class="row item-row mb-2">
        <div class="col-md-4">
            <select data-type="membership" name="item_name" class="" required>
                <option value="" selected disabled>Členství</option>
                <?php foreach ($this->pricelist->getMembershipPrices() as $m): ?>
                        <option value="<?php echo $m->mp_id;?>"><?php echo "$m->name ($m->purchase_name)";?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" name="item_discount" class="form-control" placeholder="Sleva %">
        </div>
        <div class="col-md-2">
            <input required type="number" min="1" name="item_amount" class="form-control" placeholder="Ks">
        </div>
        <div class="col-md-3">
            <input required type="text" name="item_value" class="form-control" placeholder="Cena/ks" readonly>
        </div>
        <div class="col-md-1">
            <a class="remove-invoice-item btn btn-sm btn-danger text-center" style="display: block;"><i class="icon-trash"></i></a>
        </div>
    </div>
</div>
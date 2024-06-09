<div class="page has-sidebar-left has-sidebar-tabs height-full">

    <header class="white relative nav-sticky">
        <div class="container-fluid text-primary">
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active switch-to-depot-all" id="v-pills-all-tab" data-toggle="tab" href="#v-pills-all" role="tab" aria-controls="v-pills-all"><i class="icon icon-box6"></i>Přehled produktů</a>
                    </li>
                    <?php if(hasEditPermission() || hasCreatePermission()): ?>
                    <li class="nav-item">
                        <a class="nav-link switch-to-invoice" id="v-pills-new-import-tab" data-toggle="tab" href="#v-pills-new-import" role="tab" aria-controls="v-pills-new-import"><i class="icon icon-document-add2"></i> Faktura</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link switch-to-inventory" id="v-pills-inventory-tab" data-toggle="tab" href="#v-pills-inventory" role="tab" aria-controls="v-pills-inventory"><i class="icon icon-inbox4"></i> Inventura</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link switch-to-statistics" id="v-pills-stats-tab" data-toggle="tab" href="#v-pills-stats" role="tab" aria-controls="v-pills-stats"><i class="icon icon-area-chart"></i> Statistika</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="v-pills-new-item-tab" data-toggle="tab" href="#v-pills-new-item" role="tab" aria-controls="v-pills-new-item"><i class="icon icon-plus-circle"></i> Nová položka</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </header>

    <?php $this->load->view('layout/alerts'); ?>

    <div class="container-fluid relative">

        <div class="tab-content my-3" id="v-pills-tabContent">
        
            <div class="tab-pane animated fadeInUpShort show active go" id="v-pills-all" role="tabpanel" aria-labelledby="v-pills-all-tab">
                <div class="row mb-3 my-3">
                    <div class="col-sm-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Seznam všech položek</h6>
                                <button class="btn btn-danger btn-xs float-right js-depot-home-clear-filter">Zrušit filtr</button>
                            </div>
                            <div class="table-responsive">
                                <table id="depotTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $depotItems; ?>"></table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="tab-pane animated fadeInUpShort go" id="v-pills-new-import" role="tabpanel" aria-labelledby="v-pills-new-import">
                <div class="row mb-3 my-3">
                    <div class="col-sm-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Naskladnění z faktury</h6>
                            </div>
                            <div class="card-body">
                                <form id="addItemsForm">

                                    <div class="form-row">
                                        <div class="col-md-12 mb-3">
                                            <label for="invoice_number">Identifikátor faktury <span class="required">*</span></label>
                                            <input type="text" name="invoice_number" class="form-control" id="invoice_number" placeholder="Identifikátor faktury" required>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="invoice_name">Dodavatel</label>
                                            <input type="text" name="invoice_name" class="form-control" id="invoice_name" placeholder="Název faktury">
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="col-md-12 mb-3">
                                            <a class="btn btn-primary r-20 btn-sm" style="width: 100%;" data-toggle="modal" data-target="#invoiceProductModal">Přidat skladové položky</a>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="col-md-12 mb-3">
                                            <table id="invoiceFormTable"></table>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                    <div class="invoice-divider"></div>

                                    <div class="col-md-4 mb-3">
                                            <div class="invoice-totals">
                                                <h4>Nákupní cena celkem: <span class="invoice-total">0</span>Kč</h4>
                                                <h4>Nákupní cena s DPH celkem: <span class="invoice-total-vat">0</span>Kč</h4>
                                            </div>
                                        </div>

                                        <div class="col-md-8 mb-3">
                                            <label for="note">Poznámka k naskladnění</label>
                                            <textarea name="note" class="form-control"></textarea>
                                        </div>

                                        <div class="invoice-divider"></div>
                                    </div>

                                    <div class="form-row">
                                        <div class="col-md-12 mb-3">
                                            <a class="btn btn-primary" id="submitInvoice">Naskladnit položky</a>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row mb-3 my-3">
                    <div class="col-sm-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Historie faktur</h6>
                            </div>
                            <div class="card-body">
                                <table id="depotInvoiceTable" data-ajax="<?php echo $invoicesUrl; ?>"></table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="tab-pane animated fadeInUpShort go" id="v-pills-inventory" role="tabpanel" aria-labelledby="v-pills-inventory">
                <div class="row mb-3 my-3">
                    <div class="col-sm-12">
                        <div class="card r-0 shadow">
                            <div class="card-body">
                                <input type="date" name="day" class="form-control mb-2" id="inventory_day">
                                <?php $this->app_components->getSelect2Depots(['input_name' => 'depot_id', 'id' => 'inventory_depot_id']); ?>

                                <a id="printInventory" class="btn btn-sm btn-primary r-20 mt-3" style="width: 100%;">Vytisknout inventuru</a>
                            </div>
                        </div>

                    </div>
                </div>
                
                <div class="row mb-3 my-3">
                    <div class="col-sm-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Inventura</h6>
                            </div>
                            <div class="card-body">
                                <table id="inventoryTable" data-ajax="<?php echo $inventoryUrl; ?>"></table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="tab-pane animated fadeInUpShort go" id="v-pills-stats" role="tabpanel" aria-labelledby="v-pills-stats">
                <div class="row mb-3 my-3">
                    <div class="col-sm-12">
                        <div class="card r-0 shadow">
                            <div class="card-body">
                                <input type="date" name="stats_from" class="form-control mb-2" id="stats_from" style="width: 50%;display: inline-block;float: left;">
                                <input type="date" name="stats_to" class="form-control mb-2" id="stats_to" style="width: 50%;display: inline-block;float: left;">
                                <?php $this->app_components->getSelect2DepotItems(['input_name' => 'stat_items', 'id' => 'stat_items']); ?>

                                <a id="xlsStatistic" class="btn btn-sm btn-primary r-20 mt-3" style="width: 100%;">Exportovat statistiku</a>
                            </div>
                        </div>

                    </div>
                </div>
            
                <div class="row mb-3 my-3">
                    <div class="col-sm-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Statistika</h6>
                            </div>
                            <div class="card-body">
                                <table id="statsTable" data-ajax="<?php echo $statsUrl; ?>"></table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="tab-pane animated fadeInUpShort go" id="v-pills-new-item" role="tabpanel" aria-labelledby="v-pills-new-item-tab">
                <div class="d-flex row">
                    <div class="col-md-12">
                        <div class="card mb-3 shadow no-b r-0">
                            <div class="card-header white">
                                <h6>Nová položka</h6>
                            </div>
                            <div class="card-body">
                            <form id="addItemForm">

                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="name">Název <span class="required">*</span></label>
                                        <input type="text" name="name" class="form-control" id="name" placeholder="Název" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="number">Číslo položky</label>
                                        <input type="text" name="number" class="form-control" id="number" placeholder="Číslo položky">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="category">Kategorie</label>
                                        <select name="category" id="item_category" class="form-control">
                                            <option disabled selected>Vyberte kategorii</option>
                                            <?php foreach(config_item('app')['depot_item_categories'] as $key => $name): ?>
                                            <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="sale_price">Prodejní cena za jednotku bez DPH<span class="required">*</span></label>
                                        <input disabled type="number" min="0" step="0.01" name="sale_price" id="sale_price" class="form-control" placeholder="Prodejní cena bez DPH" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="vat_value">Hodnota DPH (21% bez výběru)</label>
                                        <select name="vat_value" id="vat_value" class="form-control">
                                            <option disabled selected>Vyberte % DPH</option>
                                            <?php foreach(config_item('app')['vat_values'] as $value => $name): ?>
                                                <option value="<?php echo $value; ?>"><?php echo $name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="sale_price_vat">Prodejní cena za jednotku s DPH <span class="required">*</span></label>
                                        <input type="number" min="0" step="0.01" name="sale_price_vat" id="sale_price_vat" class="form-control" placeholder="Prodejní cena s DPH" required>
                                    </div>

                                    <div class="col-md-12 text-center">
                                        <small>Cena bez DPH bude vypočítána automaticky na základě ceny s DPH a výběru % DPH.</small>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                        <label for="description">Popisek</label>
                                        <textarea name="description" id="description" class="form-control" rows="3" placeholder="Specifikace položky.."></textarea>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                        <label for="note">Poznámka</label>
                                        <textarea name="note" id="note" class="form-control" rows="2" placeholder="Poznámka"></textarea>
                                    </div>
                                </div>

                                <?php $this->app_components->getCustomFields($custom_fields); ?>

                                <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                        <label for="unit">Jednotka (kg, ks,..) <span class="required">*</span></label>
                                        <select name="unit" required class="form-control">
                                            <option disabled selected>Vyberte jednotku</option>
                                            <?php foreach(config_item('app')['depot_item_units'] as $unit): ?>
                                                <option value="<?php echo $unit; ?>"><?php echo $unit; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="active">Aktivní <span class="required">*</span></label>
                                        <select name="active" id="active" class="form-control" required>
                                            <option value="1">Ano</option>
                                            <option value="0">Ne</option>
                                        </select>
                                    </div>
                                </div>

                                <button class="btn btn-primary" id="js_depot_add_new_item_form_btn" data-ajax="<?php echo $addItem; ?>" type="submit">Uložit</button>
                            </form>
                        </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- invoice product modal -->

<div class="modal fade" id="invoiceProductModal" role="dialog" aria-labelledby="invoiceProductModal">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content b-0">
            <div class="modal-header r-0 bg-primary">
                <h6 class="modal-title text-white" id="exampleModalLabel">Přidání položek z faktury</h6>
                <a href="#" data-dismiss="modal" aria-label="Close"
                   class="paper-nav-toggle paper-nav-white active"><i></i></a>
            </div>
            <form id="">
                <div class="modal-body">
                        <div class="form-row">
                            <label for="existing_products">Skladové položky</label>
                            <?php $this->app_components->getSelect2DepotItems(['input_name' => 'existing_products', 'id' => 'existing_products', 'multiple' => true]); ?>
                            <small class="mt-1">Můžete vybrat i více položek najednou</small>
                        </div>

                        <div class="form-row mt-3">
                            <label for="new_products">Nové položky</label>
                        </div>

                        <div class="new-products-container">
                        </div>

                        <div class="form-row mt-2">
                            <a class="btn btn-primary" id="addNewProductRow" style="width: 100%;">Přidat nový produkt</a>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="addProdFromInvoice" class="btn btn-primary">Přidat</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- PRODUCT MODAL -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">

    <div class="fader"></div>

      <div class="modal-header">
        <h5 class="align-self-center modal-title" id="productModalTitle"></h5>
        <div class="align-self-end float-right">
            <ul class="nav nav-pills" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active show" id="buttonstocktab" data-toggle="tab" href="#stocktab" role="tab" aria-controls="stocktab" aria-expanded="true" aria-selected="true">Sklady</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="buttonlogtab" data-toggle="tab" href="#logtab" role="tab" aria-controls="logtab" aria-selected="false">Historie</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="buttondetailtab" data-toggle="tab" href="#detailtab" role="tab" aria-controls="detailtab" aria-selected="false">Detail</a>
                </li>
            </ul>
        </div>
      </div>
      <div class="modal-body">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="stocktab" role="tabpanel" aria-labelledby="stocktab">
                <table id="productStockTable" class="table table-striped table-hover r-0"></table>
            </div>
            <div class="tab-pane fade" id="logtab" role="tabpanel" aria-labelledby="logtab">
                <table id="productLogTable" class="table table-striped table-hover r-0"></table>
            </div>
            <div class="tab-pane fade" id="detailtab" role="tabpanel" aria-labelledby="detailtab">
                <form id="productDetailForm">
                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="name">Název <span class="required">*</span></label>
                                        <input type="text" name="name" class="form-control" id="name_edit" placeholder="Název" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="number">Číslo položky</label>
                                        <input type="text" name="number" class="form-control" id="number_edit" placeholder="Číslo položky">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="category">Kategorie</label>
                                        <select name="category" id="item_category_edit" class="form-control">
                                            <option disabled selected>Vyberte kategorii</option>
                                            <?php foreach(config_item('app')['depot_item_categories'] as $key => $name): ?>
                                            <option value="<?php echo $key ?>"><?php echo $name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-4 mb-3">
                                        <label for="sale_price">Prodejní cena za jednotku (bez DPH) <span class="required">*</span></label>
                                        <input type="number" min="0" step="0.01" name="sale_price" id="sale_price_edit" class="form-control" placeholder="Prodejní cena bez DPH" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="vat_value">Hodnota DPH (21% bez výběru)</label>
                                        <select name="vat_value" id="vat_value_edit" class="form-control">
                                            <option disabled selected>Vyberte % DPH</option>
                                            <?php foreach(config_item('app')['vat_values'] as $value => $name): ?>
                                                <option value="<?php echo $value; ?>"><?php echo $name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="sale_price_vat">Prodejní cena za jednotku s DPH <span class="required">*</span></label>
                                        <input type="number" min="0" step="0.01" name="sale_price_vat" id="sale_price_vat_edit" class="form-control" placeholder="Prodejní cena s DPH" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                        <label for="description">Popisek</label>
                                        <textarea name="description" id="description_edit" class="form-control" rows="3" placeholder="Specifikace položky.."></textarea>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                        <label for="note">Poznámka</label>
                                        <textarea name="note" id="note_edit" class="form-control" rows="2" placeholder="Poznámka"></textarea>
                                    </div>
                                </div>

                                <?php $this->app_components->getCustomFields($custom_fields); ?>

                                <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                        <label for="unit">Jednotka (kg, ks,..) <span class="required">*</span></label>
                                        <select name="unit" required class="form-control">
                                            <option disabled selected>Vyberte jednotku</option>
                                            <?php foreach(config_item('app')['depot_item_units'] as $unit): ?>
                                                <option value="<?php echo $unit; ?>"><?php echo $unit; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="active">Aktivní <span class="required">*</span></label>
                                        <select name="active" id="active_edit" class="form-control" required>
                                            <option value="1">Ano</option>
                                            <option value="0">Ne</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="text" class="hidden form-control item-id" disabled>
                </form>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Zavřít</button>
        <?php if(hasEditPermission()): ?>
        <button type="button" class="btn btn-primary" id="saveProductDetail">Uložit</button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- MOVE PRODUCT -->
<div class="modal fade" id="moveProductModal" role="dialog" aria-labelledby="moveProductModal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content b-0">
            <div class="modal-header r-0 bg-primary">
                <h6 class="modal-title text-white" id="exampleModalLabel">Nový příjem</h6>
                <a href="#" data-dismiss="modal" aria-label="Close"
                   class="paper-nav-toggle paper-nav-white active"><i></i></a>
            </div>
            <form id="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="movement_type">Druh pohybu <span class="required">*</span></label>
                        <select name="movement_type" class="form-control toggle-select" id="movement_type" required>
                            <option disabled selected>Vyberte druh pohybu..</option>
                            <option value="1">Naskladnění</option>
                            <option value="2">Přesun</option>
                            <option value="3">Rezervace</option>
                            <option value="4">Uvolnění</option>
                        </select>
                    </div>

                    <div class="form-group rollout-field" data-name="movement_type" data-selected="4">
                        <div class="parent">
                            <label for="depot_id">Ze skladu <span class="required">*</span></label>
                            <?php $this->app_components->getSelect2Depots(['required' => true, 'input_name' => 'depot_id', 'id' => 'depot_id']); ?>
                        </div>
                    </div>
                    <div class="form-group rollout-field" data-name="movement_type" data-selected="3">
                        <div class="parent">
                            <label for="depot_id">Ze skladu <span class="required">*</span></label>
                            <?php $this->app_components->getSelect2Depots(['required' => true, 'input_name' => 'depot_id', 'id' => 'depot_id']); ?>
                        </div>
                    </div>
                    <div class="form-group rollout-field" data-name="movement_type" data-selected="2">
                        <div class="parent">
                            <label for="from_depot_id">Ze skladu <span class="required">*</span></label>
                            <?php $this->app_components->getSelect2Depots(['required' => true, 'input_name' => 'from_depot_id', 'id' => 'from_depot_id']); ?>
                        </div>
                        <div class="parent">
                            <label for="to_depot_id" class="mt-2">Na sklad <span class="required">*</span></label>
                            <?php $this->app_components->getSelect2Depots(['required' => true, 'input_name' => 'to_depot_id', 'id' => 'to_depot_id']); ?>
                        </div>
                    </div>
                    <div class="form-group rollout-field" data-name="movement_type" data-selected="1">
                        <div class="parent">
                            <label for="to_depot_id">Na sklad <span class="required">*</span></label>
                            <?php $this->app_components->getSelect2Depots(['required' => true, 'input_name' => 'to_depot_id', 'id' => 'to_depot_id']); ?>
                        </div>
                        <label for="buy_price" class="mt-2">Nákupní cena za jednotku bez DPH <span class="required">*</span></label>
                        <input type="number" class="form-control" name="buy_price" placeholder="" required>
                    </div>

                    <div class="form-group">
                        <label for="quantity">Množství <span class="required">*</span></label>
                        <input type="number" name="quantity"  min="0" step="1" class="form-control" placeholder="Množství" required>
                    </div>
                    <div class="form-group">
                        <label for="note">Poznámka</label>
                        <textarea name="note" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <input class="hidden item-id" type="text" value="">
                    <input class="hidden depot-id" type="text" value="">
                    <button type="submit" id="moveprod" class="btn btn-primary">Zadat</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- REMOVE PRODUCT -->
<div class="modal fade" id="removeProductModal" role="dialog" aria-labelledby="removeProductModal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content b-0">
            <div class="modal-header r-0 bg-primary">
                <h6 class="modal-title text-white" id="exampleModalLabel">Vyskladnění položky</h6>
                <a href="#" data-dismiss="modal" aria-label="Close"
                   class="paper-nav-toggle paper-nav-white active"><i></i></a>
            </div>
            <form id="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="quantity">Množství <span class="required">*</span></label>
                        <input type="number" name="quantity"  min="0" step="1" class="form-control" placeholder="Množství" required>
                    </div>
                    <div class="form-group">
                        <label for="note">Poznámka <span class="required">*</span></label>
                        <textarea name="note" class="form-control" required></textarea>
                        <small>Pokud se jedná o prodej, proveďte prosím vyskladnění skrze pokladnu! Zde není možné provádět prodej, pouze vyskladnění s odůvodněním.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <input class="hidden item-id" type="text" value="">
                    <input class="hidden depot-id" type="text" value="">
                    <button type="submit" id="removeprod" class="btn btn-primary">Zadat</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT PRODUCT LOG -->
<div class="modal fade" id="logtModal" role="dialog" aria-labelledby="logtModal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content b-0">
            <div class="modal-header r-0 bg-primary">
                <h6 class="modal-title text-white" id="exampleModalLabel">Záznam</h6>
                <a href="#" data-dismiss="modal" aria-label="Close"
                   class="paper-nav-toggle paper-nav-white active"><i></i></a>
            </div>
            <form id="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="note">Poznámka <span class="required">*</span></label>
                        <textarea name="note" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <input class="hidden log-id" type="text" value="">
                    <button type="submit" id="removeprod" class="btn btn-primary">Uložit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="newProductRowTemplate" class="hidden">
                                <div class="product-row">
                                    <div class="form-row">
                                        <div class="col-md-6">
                                            <label for="name">Název <span class="required">*</span></label>
                                            <input type="text" name="name" class="form-control" id="name_new" placeholder="Název" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="unit">Jednotka (kg, ks,..) <span class="required">*</span></label>
                                            <select name="unit" required class="form-control">
                                                <option disabled selected>Vyberte jednotku</option>
                                                <?php foreach(config_item('app')['depot_item_units'] as $unit): ?>
                                                    <option value="<?php echo $unit; ?>"><?php echo $unit; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-row mt-2">
                                        <div class="col-md-4">
                                            <label for="sale_price">Prod. cena/jednotka (bez DPH) <span class="required">*</span></label>
                                            <input type="number" min="0" step="0.01" name="sale_price" class="form-control sale-price-new" placeholder="Prodejní cena za jednotku bez DPH" required>
                                        </div>
                                        <div class="col-md-4">
                                                <label for="vat_value">DPH</label>
                                                <select name="vat_value" class="form-control vat-value-new">
                                                    <option disabled selected>Vyberte % DPH</option>
                                                    <?php foreach(config_item('app')['vat_values'] as $value => $name): ?>
                                                        <option value="<?php echo $value; ?>"><?php echo $name; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="sale_price_vat">Prod. cena/jednotka s DPH <span class="required">*</span></label>
                                            <input type="number" min="0" step="0.01" name="sale_price_vat" class="form-control sale-price-vat-new" placeholder="Prodejní cena za jednotku s DPH" required>
                                        </div>
                                    </div>
                                    <div class="form-row delete-row">
                                        <a class="btn btn-danger"><i class="icon-times-circle"></i> Odstranit</a>
                                    </div>
                                </div>
</div>
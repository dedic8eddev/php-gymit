<div class="page has-sidebar-left has-sidebar-tabs height-full">
    <header class="white relative nav-sticky">
        <div class="container-fluid text-primary">
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li><a class="nav-link active switch-to-clients" id="v-pills-all-tab" data-toggle="pill" href="#v-pills-all" role="tab" aria-controls="v-pills-all"><i class="icon icon-users"></i>Přehled zákazníků</a></li>
                    <li><a class="nav-link switch-to-inactive" id="v-pills-inactive-tab" data-toggle="pill" href="#v-pills-inactive" role="tab" aria-controls="v-pills-inactive"><i class="icon icon-user-times"></i>Neaktivní Zákazníci</a></li>
                    <?php if(hasCreatePermission()) { ?>
                    <li><a class="nav-link" id="v-pills-buyers-tab" data-toggle="pill" href="#v-pills-buyers" role="tab" aria-controls="v-pills-buyers"><i class="icon icon-plus-circle"></i> Nový zákazník</a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </header>

    <?php $this->load->view('layout/alerts'); ?>
    <div class="container-fluid animatedParent animateOnce">
        <div class="tab-content my-3" id="v-pills-tabContent">
            
            <div class="tab-pane animated fadeInUpShort show active go" id="v-pills-all" role="tabpanel" aria-labelledby="v-pills-all-tab">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Seznam všech zákazníků</h6>
                                <button class="btn btn-danger btn-xs float-right js-clear-filter" data-table="clients_table">Zrušit filtr</button>
                            </div>
                            <div class="table-responsive">
                                    <table id="clientsTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $clientsUrl; ?>">
                                    </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane animated fadeInUpShort go" id="v-pills-inactive" role="tabpanel" aria-labelledby="v-pills-inactive-tab">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Seznam neaktivních zákazníků</h6>
                                <button class="btn btn-danger btn-xs float-right js-clear-filter" data-table="inactive_table">Zrušit filtr</button>
                            </div>
                            <div class="table-responsive">
                                <table id="inactiveTable" class="table table-striped table-hover r-0">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           
            <?php if (hasCreatePermission()) { ?>
            <div class="tab-pane animated fadeInUpShort go" id="v-pills-buyers" role="tabpanel" aria-labelledby="v-pills-buyers-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header white">
                                <h6>Vytvoření nového účtu</h6>
                            </div>
                            <div class="card-body b-b">
                                <form id="addClientForm">
                                    <?php $this->load->view('admin/clients/client_form'); ?>                                                          
                                    <hr>
                                    <div class="form-row d-none">
                                        <div class="form-group col-md-12 mb-0" data-children-count="1">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="representative" name="representative" required>
                                                <label class="form-check-label" for="representative">
                                                    Souhlas zákonného zástupce.
                                                </label>
                                            </div>
                                        </div>
                                    </div>                                    
                                    <div class="form-row">
                                        <div class="form-group col-md-12" data-children-count="1">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="agreement" name="agreement" required>
                                                <label class="form-check-label" for="agreement">
                                                    Souhlas se zpracováním osobních údajů.
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="active" value="1" />
                                    <button class="btn btn-sm btn-primary add-client-submit" data-ajax="<?php echo $addUrl; ?>">Přidat zákazníka</button>
                                </form>                            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

</div>
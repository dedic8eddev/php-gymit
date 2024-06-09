<div id="priceListPage" class="page has-sidebar-left has-sidebar-tabs height-full">
    <?php $this->load->view('layout/alerts'); ?>
    <header class="white relative nav-sticky">
        <div class="container-fluid text-primary">
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <?php if (hasReadPermission(SECTION_PRICE_LIST)): ?>
                    <li>
                        <a class="nav-link active" id="v-pills-prices-tab" data-toggle="pill" href="#v-pills-prices" role="tab" aria-controls="v-pills-prices"><i class="icon icon-th-list"></i>Ceník služeb</a>
                    </li>
                    <?php endif; ?>
                    <?php if (hasReadPermission(SECTION_MEMBERSHIP)): ?>
                    <li>
                        <a class="nav-link" id="v-pills-membership-tab" data-toggle="pill" href="#v-pills-membership" role="tab" aria-controls="v-pills-membership"><i class="icon icon-card_membership"></i>Členství</a>
                    </li>
                    <li>
                        <a class="nav-link" id="v-pills-membership-overview-tab" data-toggle="pill" href="#v-pills-membership-overview" role="tab" aria-controls="v-pills-membership-overview"><i class="icon icon-users"></i>Přehled členství</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </header>    
    <div class="container-fluid animatedParent animateOnce">
        <div class="tab-content my-3" id="v-pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active go" id="v-pills-prices" role="tabpanel" aria-labelledby="v-pills-prices-tab">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Ceník služeb</h6>
                                <button class="btn btn-danger btn-xs float-right js-clear-filter" data-table="price_list_table">Zrušit filtr</button>
                                <?php if (hasCreatePermission()): ?>
                                <a class="btn btn-primary btn-xs float-right mr-1" href="javascript:;" data-toggle="modal" data-remote="/admin/pricelist/add_price" data-target="#modal" data-modal-title="Nová položka" data-modal-submit="Přidat">Přidat položku</a>
                                <?php endif; ?>
                            </div>
                            <div class="table-responsive">
                                <table id="pricelistTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $prices['pricesUrl']; ?>"></table>
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane animated fadeInUpShort go" id="v-pills-membership" role="tabpanel" aria-labelledby="v-pills-membership-tab">
            <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Členství</h6>
                                <button class="btn btn-danger btn-xs float-right js-clear-filter" data-table="membership_table">Zrušit filtr</button>
                            </div>
                            <div class="table-responsive">
                                <table id="membershipTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $prices['membershipsUrl']; ?>"></table>
                            </div>                            
                        </div>
                    </div>
                </div>                
            </div>
            <div class="tab-pane animated fadeInUpShort go" id="v-pills-membership-overview" role="tabpanel" aria-labelledby="v-pills-membership-overview-tab">
            <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Přehled členství</h6>
                                <button class="btn btn-danger btn-xs float-right js-clear-filter" data-table="membership_overview_table">Zrušit filtr</button>
                            </div>
                            <div class="table-responsive">
                                <table id="membershipOverviewTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $prices['membershipsOverviewsUrl']; ?>"></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- AJAX MODAL -->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header r-0 bg-primary">
                    <h5 class="modal-title text-white"></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Zrušit</button>
                    <?php if(hasEditPermission() || hasCreatePermission()): ?><button type="button" class="btn btn-primary" id="modalSubmit">Přidat</button><?php endif; ?>                    
                </div>
            </div>
        </div>
    </div>       
</div>
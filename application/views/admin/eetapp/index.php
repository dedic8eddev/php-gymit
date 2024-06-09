<div id="eetappPage" class="page has-sidebar-left has-sidebar-tabs height-full">
    <header class="white relative nav-sticky">
        <div class="container-fluid text-primary">
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li>
                        <a class="nav-link active" id="v-pills-table-tab" data-toggle="pill" href="#v-pills-table" role="tab" aria-controls="v-pills-table"><i class="icon icon-list2"></i>Přehled</a>
                    </li>
                    <li>
                        <a class="nav-link" id="v-pills-log-tab" data-toggle="pill" href="#v-pills-log" role="tab" aria-controls="v-pills-log"><i class="icon icon-list-alt"></i>Log pokladen</a>
                    </li>
                    <?php if (hasCreatePermission()): ?>
                    <li>
                        <a class="nav-link" href="javascript:;" data-toggle="modal" data-remote="/admin/eetapp/add-checkout" data-target="#modal" data-modal-title="Nová pokladna" data-modal-submit="Přidat"><i class="icon icon icon-plus-circle"></i>Přidat pokladnu</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </header>

    <?php $this->load->view('layout/alerts'); ?>
    <div class="container-fluid animatedParent animateOnce">
        <div class="tab-content my-3" id="v-pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active go" id="v-pills-table" role="tabpanel" aria-labelledby="v-pills-table-tab">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">EET pokladny</h6>
                                <button class="btn btn-danger btn-xs float-right js-clear-filter" data-table="checkouts_table">Zrušit filtr</button>
                            </div>
                            <div class="table-responsive">
                                <table id="checkoutsTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $getAllCheckoutsUrl; ?>">
                                </table>
                            </div>                            
                        </div>
                    </div>
                </div> 
            </div>
            <div class="tab-pane animated fadeInUpShort go" id="v-pills-log" role="tabpanel" aria-labelledby="v-pills-log-tab">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Log pokladen</h6>
                                <button class="btn btn-danger btn-xs float-right js-clear-filter" data-table="log_table">Zrušit filtr</button>
                            </div>
                            <div class="table-responsive">
                                <table id="checkoutsLogTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $getCheckoutsLogUrl; ?>">
                                </table>
                            </div>                            
                        </div>
                    </div>
                </div> 
            </div>            
        </div> 
    </div>
    <!-- AJAX MODAL -->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header r-0">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Zrušit</button>
                    <button type="button" class="btn btn-primary" id="modalSubmit">Přidat</button>                    
                </div>
            </div>
        </div>
    </div>            
</div>
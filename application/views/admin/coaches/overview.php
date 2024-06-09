<div class="page has-sidebar-left has-sidebar-tabs height-full">
    <header class="white relative nav-sticky">
        <div class="container-fluid text-primary">
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li><a class="nav-link active switch-to-coaches" id="v-pills-coaches-tab" data-toggle="pill" href="#v-pills-coaches" role="tab" aria-controls="v-pills-coaches"><i class="icon icon-users"></i>Přehled trenérů</a></li>
                    <li><a class="nav-link switch-to-instructors" id="v-pills-instructors-tab" data-toggle="pill" href="#v-pills-instructors" role="tab" aria-controls="v-pills-instructors"><i class="icon icon-users"></i>Přehled instruktorů</a></li>      
                    <li><a class="nav-link switch-to-inactive" id="v-pills-inactive-tab" data-toggle="pill" href="#v-pills-inactive" role="tab" aria-controls="v-pills-inactive"><i class="icon icon-user-times"></i>Neaktivní uživatelé</a></li>
                    <?php if (hasCreatePermission()) : ?>
                    <li><a class="nav-link" id="v-pills-new-coach-tab" data-toggle="pill" href="#v-pills-new-coach" role="tab" aria-controls="v-pills-new-coach"><i class="icon icon-plus-circle"></i> Nový účet</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </header>

    <?php $this->load->view('layout/alerts'); ?>
    <div class="container-fluid animatedParent animateOnce">
        <div class="tab-content my-3" id="v-pills-tabContent">
            
            <div class="tab-pane animated fadeInUpShort show active go" id="v-pills-coaches" role="tabpanel" aria-labelledby="v-pills-coaches-tab">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Seznam všech trenérů</h6>
                                <button class="btn btn-danger btn-xs float-right" id="js-clear-coaches-filter">Zrušit filtr</button>
                            </div>
                            <div class="table-responsive">
                                <table id="coachesTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $coachesUrl; ?>">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane animated fadeInUpShort go" id="v-pills-instructors" role="tabpanel" aria-labelledby="v-pills-instructors-tab">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Seznam všech instruktorů</h6>
                                <button class="btn btn-danger btn-xs float-right" id="js-clear-instructors-filter">Zrušit filtr</button>
                            </div>
                            <div class="table-responsive">
                                <table id="instructorsTable" class="table table-striped table-hover r-0">
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
                                <h6 class="pull-left">Seznam neaktivních trenérů a instruktorů</h6>
                                <button class="btn btn-danger btn-xs float-right" id="js-clear-inactive-filter">Zrušit filtr</button>
                            </div>
                            <div class="table-responsive">
                                <table id="inactiveTable" class="table table-striped table-hover r-0">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>                          

            <div class="tab-pane animated fadeInUpShort go" id="v-pills-new-coach" role="tabpanel" aria-labelledby="v-pills-new-coach-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header white">
                                <h6>Vytvoření nového účtu</h6>
                            </div>
                            <div class="card-body b-b">
                                <form id="addCoachForm">
                                    <?php $this->load->view('admin/coaches/coach_form'); ?>                                                          
                                    <hr>
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
                                    <button class="btn btn-sm btn-primary add-coach-submit" data-ajax="<?php echo $addUrl; ?>">Přidat účet</button>
                                </form>
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
                    <button type="button" class="btn btn-primary" id="modalSubmit">Přidat</button>                    
                </div>
            </div>
        </div>
    </div>    
</div>
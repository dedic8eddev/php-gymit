<div id="lessonsTemplatesPage" class="page has-sidebar-left has-sidebar-tabs height-full">
    <header class="white relative nav-sticky">
        <div class="container-fluid text-primary">
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li>
                        <a class="nav-link switch-to-table" id="v-pills-table-tab" data-toggle="pill" href="#v-pills-table" role="tab" aria-controls="v-pills-table"><i class="icon icon-list2"></i>Přehled</a>
                    </li>
                    <li>
                        <a class="nav-link" href="javascript:;" data-toggle="modal" data-remote="/admin/lessons/add_template" data-target="#modal" data-modal-title="Nová položka" data-modal-submit="Přidat"><i class="icon icon icon-plus-circle"></i>Přidat lekci</a>
                    </li>
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
                    <div class="card no-r no-b shadow">
                        <div class="card-body p-0">
                            <table id="lessonTable" data-ajax="<?php echo $templatesUrl; ?>"></table>
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
    <div class="modal fade" id="tagsModal" tabindex="-1" role="dialog" aria-labelledby="tagsModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header r-0 bg-primary">
                    <h5 class="modal-title text-white"></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Zavřít</button>                
                </div>
            </div>
        </div>
    </div>               

</div>
<div id="cmsServicesPage" class="page has-sidebar-left has-sidebar-tabs height-full">
    <?php $this->load->view('layout/alerts'); ?>
    <div class="container-fluid animatedParent animateOnce">
        <div class="row my-3">
            <div class="col-md-12">
                <div class="card r-0 shadow">
                    <div class="card-header white">
                        <h6 class="pull-left">Nabízené služby</h6>
                        <button class="btn btn-danger btn-xs float-right" id="js_services_clear_filter">Zrušit filtr</button>
                        <a class="btn btn-primary btn-xs float-right mr-1" href="javascript:;" data-toggle="modal" data-remote="/admin/cms/add_gym_service" data-target="#modal" data-modal-title="Nová služba" data-modal-submit="Přidat">Přidat službu</a>
                    </div>
                    <div class="table-responsive">
                        <table id="gymServicesTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $gymServicesUrl; ?>">
                        </table>
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
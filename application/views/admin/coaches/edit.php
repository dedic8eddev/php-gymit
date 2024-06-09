<div id="coachEditPage" class="page has-sidebar-left has-sidebar-tabs height-full">
    <header class="white relative nav-sticky">
        <div class="container-fluid text-primary">
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li><a class="nav-link active" id="v-pills-coming-lessons-tab" data-toggle="pill" href="#v-pills-coming-lessons" role="tab" aria-controls="v-pills-coming-lessons"><i class="icon icon-calendar"></i>Přehled nadcházejících lekcí</a></li>
                    <li><a class="nav-link" id="v-pills-ended-lessons-tab" data-toggle="pill" href="#v-pills-ended-lessons" role="tab" aria-controls="v-pills-ended-lessons"><i class="icon icon-calendar-check-o"></i>Přehled proběhlých lekcí</a></li>
                    <?php if (hasEditPermission()) : ?>
                    <li><a class="nav-link" id="v-pills-edit-tab" data-toggle="pill" href="#v-pills-edit" role="tab" aria-controls="v-pills-edit"><i class="icon icon-pencil"></i>Upravit</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </header>
    <?php $this->load->view('layout/alerts'); ?>
    <div class="container-fluid relative animatedParent animateOnce my-3">
        <div class="tab-content my-3" id="v-pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active go" id="v-pills-coming-lessons" role="tabpanel" aria-labelledby="v-pills-coming-lessons-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header white">
                                <h6 class="pull-left">Přehled naplánovaných lekcí</h6>
                                <button class="btn btn-danger btn-xs float-right js-clear-filter" data-table="comingLessons_table">Zrušit filtr</button>
                                <a id="btnCancelModal" class="btn btn-primary btn-xs float-right mr-1" href="javascript:;" data-toggle="modal" data-remote="/admin/coaches/cancel_lessons/<?php echo $coach->id; ?>" data-target="#modal" data-modal-title="Neúčast na lekcích" data-modal-submit="Uložit">Založit neúčast</a>
                            </div>
                            <div class="table-responsive">
                                <table id="comingLessons_table" class="table table-striped table-hover r-0" data-ajax="<?php echo $getCoachLessonsUrl; ?>">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>            
            </div>
            <div class="tab-pane animated fadeInUpShort go" id="v-pills-ended-lessons" role="tabpanel" aria-labelledby="v-pills-ended-lessons-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                        <div class="card-header white">
                                <h6 class="pull-left">Přehled proběhlých lekcí</h6>
                                <button class="btn btn-danger btn-xs float-right js-clear-filter" data-table="endedLessons_table">Zrušit filtr</button>
                            </div>
                            <div class="table-responsive">
                                <table id="endedLessons_table" class="table table-striped table-hover r-0" data-ajax="<?php echo $getCoachLessonsUrl; ?>">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>            
            </div>
            <div class="tab-pane animated fadeInUpShort go" id="v-pills-edit" role="tabpanel" aria-labelledby="v-pills-edit-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header white">
                                <h6><?php echo $coach->group_id == 6 ? 'Úprava trenéra' : 'Úprava instruktora'?></h6>
                            </div>
                            <div class="card-body b-b">
                                <form id="saveUserForm">                      
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="email">Datum registrace</label>
                                            <input class="form-control" disabled type="text" name="date_created" value="<?php echo date('d.m.Y H:i', strtotime(@$coach->date_created)); ?>">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="phone">Poslední přihlášení</label>
                                            <input class="form-control" disabled type="text" name="last_login" value="<?php if(@$coach->last_login != NULL) echo date('d.m.Y H:i', strtotime(@$coach->last_login)); ?>">
                                        </div>                           
                                    </div>                          
                                    <?php $this->load->view('admin/coaches/coach_form'); ?>    
                                    <hr>              
                                    <?php if(hasEditPermission() || hasDeletePermission()): ?>             
                                        <button type="submit" class="btn btn-sm btn-primary save-user-submit" data-ajax="<?php echo $saveDetail; ?>" data-id="<?php echo $coach->id; ?>"><?php echo $coach->group_id == 6 ? 'Uložit trenéra' : 'Uložit instruktora'?></button>&nbsp;
                                        <?php if($coach->active): ?>
                                            <button class="btn btn-sm btn-danger remove-user" data-ajax="<?php echo $removeUser; ?>" data-id="<?php echo $coach->id; ?>"><?php echo $coach->group_id == 6 ? 'Deaktivovat trenéra' : 'Deaktivovat instruktora'?></button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-success activate-user" data-ajax="<?php echo $activateUser; ?>" data-id="<?php echo $coach->id; ?>"><?php echo $coach->group_id == 6 ? 'Aktivovat trenéra' : 'Aktivovat instruktora'?></button>
                                        <?php endif; ?>                            
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <a href="/admin/coaches" class="btn btn-primary btn-sm">
                    <i class="icon icon-chevron-left"></i>
                    Zpět na přehled
                </a>
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
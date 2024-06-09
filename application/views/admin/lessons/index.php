<div class="page has-sidebar-left has-sidebar-tabs height-full">
    <header class="white relative nav-sticky">
        <div class="container-fluid text-primary">
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li>
                        <a class="nav-link active switch-to-calendar" id="v-pills-all-tab" data-toggle="pill" href="#v-pills-all" role="tab" aria-controls="v-pills-all"><i class="icon icon-calendar"></i>Kalendář</a>
                    </li>
                    <li>
                        <a class="nav-link switch-to-table" id="v-pills-table-tab" data-toggle="pill" href="#v-pills-table" role="tab" aria-controls="v-pills-table"><i class="icon icon-list2"></i>Seznam</a>
                    </li>
                    <?php if (hasCreatePermission()): ?>
                    <li>
                        <a href="javascript:;" class="nav-link" data-toggle="modal" data-target="#addEventModal"><i class="icon icon icon-plus-circle"></i>Přidat lekci</a>
                    </li>
                    <?php endif; ?>
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
                    <div class="card no-r no-b shadow">
                        <div class="card-body p-0">
                            <div id="lesson_cal" class="fc fc-unthemed fc-ltr">
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>

            <div class="tab-pane animated fadeInUpShort go" id="v-pills-table" role="tabpanel" aria-labelledby="v-pills-table-tab">
                <div class="row my-3">
                <div class="col-md-12">
                    <div class="card no-r no-b shadow">
                        <div class="card-body p-0">
                            <table id="lessonTable" data-ajax="<?php echo $lessonsUrl ?>"></table>
                        </div>
                    </div>
                </div>
                </div>
            </div>

        </div>
    </div>  
</div>

<!-- jQuery UI - DIALOG -->
<div id="dialog" style="display:none;"></div>  

<!-- Přidání lekce -->
<div class="modal fade" id="addEventModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header r-0 bg-primary">
                <h6 class="modal-title text-white" id="exampleModalLabel">Přidání lekce</h6>
                <a style="padding-top:2px;" href="#" data-dismiss="modal" aria-label="Close"
                   class="paper-nav-toggle paper-nav-white active"><i></i></a>
            </div>
             <div class="modal-body">
                  <form action="" method="post" id="addeventform">
                        <div class="form-group">
                            <label for="template_id">Lekce</label>
                            <?php $this->app_components->getSelect2LessonsTemplates(['input_name' => 'template_id','id' => 'template_id', 'required' => 'true']); ?>
                        </div>

                      <div class="row">
                        <div class="form-group col-md-4">
                            <label for="starting_on">Začátek <span class="required">*</span></label>
                            <input type="date" name="starting_on" required id="starting_on" class="form-control dateonly">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="time_from">Od <span class="required">*</span></label>
                            <input type="date" name="time_from" required id="time_from" class="form-control timeonly">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="ending_on">Konec <span class="required">*</span></label>
                            <input type="date" name="ending_on" required id="ending_on" class="form-control dateonly">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="time_to">Do <span class="required">*</span></label>
                            <input type="date" name="time_to" required id="time_to" class="form-control timeonly">
                        </div>
                      </div>

                      <div class="form-group">
                          <label for="teachers">Trenéři / Instruktoři</label>
                          <?php $this->app_components->getSelect2Teachers(['input_name' => 'teachers[]','id' => 'teachers', 'multiple' => true]); ?>
                      </div>

                      <div class="form-group">
                          <label for="clients">Zákazníci</label>
                          <?php $this->app_components->getSelect2Clients(['input_name' => 'clients[]','id' => 'clients', 'multiple' => true]); ?>
                      </div>

                        <div class="form-group checkers">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    Opakuje se
                                    <div class="material-switch float-right" data-children-count="1">
                                        <input id="repeating" name="repeating" type="checkbox">
                                        <label for="repeating" class="bg-secondary"></label>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    Celý den
                                    <div class="material-switch float-right" data-children-count="1">
                                        <input id="all_day" name="all_day" type="checkbox">
                                        <label for="all_day" class="bg-secondary"></label>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div class="form-group repeat-form" style="display: none;">
                                    <label for="repeat_freq">Interval opakování <span class="required">*</span></label>
                                    <select class="form-control" placeholder="Opakuje se" name="repeat_freq" required>
                                        <option selected disabled>Vyberte interval..</option>
                                        <option value="1">Každý den</option>
                                        <option value="7">Každý týden</option>
                                        <option value="14">Každé 2 týdny</option>
                                        <option value="28">Každý měsíc</option>
                                    </select>
                        </div>
                        <div class="form-group repeat-form" style="display: none;">
                                    <label for="repeat_end">Opakovat do</label>
                                    <input type="date" name="repeating_end" id="repeat" class="form-control dateonly">
                        </div>

                  </form>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-default" data-dismiss="modal">Zrušit</button>
                 <button type="button" class="btn btn-primary" id="addeventsubmit">Přidat</button>
             </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editEventModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header r-0 bg-primary">
                <h6 class="modal-title text-white" id="exampleModalLabel">Editace lekce</h6>
                <a style="padding-top:2px;" href="#" data-dismiss="modal" aria-label="Close" class="paper-nav-toggle paper-nav-white active"><i></i></a>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="editeventform">

                    <div class="form-group">
                        <label for="template_id_edit">Lekce</label>
                        <?php $this->app_components->getSelect2LessonsTemplates(['input_name' => 'template_id','id' => 'template_id_edit', 'required' => 'true']); ?>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="starting_on">Začátek <span class="required">*</span></label>
                            <input type="date" name="starting_on" required id="starting_on_edit" class="form-control dateonly">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="time_from">Od <span class="required">*</span></label>
                            <input type="date" name="time_from" required id="time_from_edit" class="form-control timeonly">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="ending_on">Konec <span class="required">*</span></label>
                            <input type="date" name="ending_on" required id="ending_on_edit" class="form-control dateonly">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="time_to">Do <span class="required">*</span></label>
                            <input type="date" name="time_to" required id="time_to_edit" class="form-control timeonly">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="teachers">Trenéři / Instruktoři</label>
                        <?php $this->app_components->getSelect2Teachers(['input_name' => 'teachers[]','id' => 'teachers_edit', 'multiple' => true]); ?>
                    </div>

                    <div class="form-group">
                        <label for="clients">Zákazníci</label>
                        <?php $this->app_components->getSelect2Clients(['input_name' => 'clients[]','id' => 'clients_edit', 'multiple' => true]); ?>
                    </div>

                    <!--<div class="from-group">
                        <label for="clients">VIP zákazníci <small>(Bez registrace a rezervačního poplatku)</small></label>
                        <table class="vip-clients">
                        </table>
                        <div class="input-group mb-3 mt-2">
                            <div class="col-md-3 px-0">
                                <input type="text" class="vip-client-name form-control vip-client-ac" placeholder="Jméno">
                            </div>
                            <div class="col-md-3 px-0">
                                <input type="text" class="vip-client-surname form-control vip-client-ac" placeholder="Příjmení">
                            </div>
                            <input type="text" class="form-control col-md-6 vip-client-note" placeholder="Poznámka...">
                            <div class="input-group-append">
                                <a href="javascript:;" class="btn btn-primary" onclick="LESSONS.addVIP(this);">Přidat</a>
                            </div>
                        </input>                       
                    </div>-->        

                    <div class="form-group checkers">
                        <ul class="list-group">
                            <li class="list-group-item">
                                Celý den
                                <div class="material-switch float-right" data-children-count="1">
                                    <input id="all_day_edit" name="all_day" type="checkbox">
                                    <label for="all_day_edit" class="bg-secondary"></label>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <input type="hidden" name="lesson_id" id="lesson_id" value="0" />

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Zpátky</button>
                <?php if(hasEditPermission() || hasDeletePermission()): ?>
                <button type="button" class="btn btn-warning" id="delete_all_repeating_events" style="display:none;">Smazat všechny opakující</button>
                <button type="button" class="btn btn-danger" id="delete_event">Smazat</button>
                <button type="button" class="btn btn-primary" id="editeventsubmit">Uložit</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
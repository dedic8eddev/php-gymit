<div class="page has-sidebar-left has-sidebar-tabs height-full">
    <header class="white relative nav-sticky">
        <div class="container-fluid text-primary">
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li>
                        <a class="nav-link active switch-to-fields" id="v-pills-all-tab" data-toggle="pill" href="#v-pills-all" role="tab" aria-controls="v-pills-all"><i class="icon icon-star"></i>Přehled polí</a>
                    </li>
                    <li>
                        <a href="javascript:;" class="nav-link" data-toggle="modal" data-target="#addFieldModal"><i class="icon icon icon-plus-circle"></i>Přidat pole</a>
                    </li>
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
                                <h6 class="pull-left">Seznam všech polí</h6>
                                <button class="btn btn-danger btn-xs float-right" id="js_depot_home_clear_filter">Zrušit filtr</button>
                            </div>
                            <div class="table-responsive">
                                    <table id="fieldTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $fieldsUrl; ?>">
                                    </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Přidání pole -->
<div class="modal fade" id="addFieldModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header r-0 bg-primary">
                <h6 class="modal-title text-white" id="exampleModalLabel">Přidání pole</h6>
                <a style="padding-top:2px;" href="#" data-dismiss="modal" aria-label="Close"
                   class="paper-nav-toggle paper-nav-white active"><i></i></a>
            </div>
             <div class="modal-body">
                  <form action="" method="post" id="addeventform">
                      <div class="form-group">
                          <label for="name">Název <span class="required">*</span></label>
                          <input type="text" required name="name" id="name" class="form-control">
                      </div>
                      <div class="form-group">
                          <label for="description">Popisek</label>
                          <textarea name="description" id="description" class="form-control"></textarea>
                      </div>

                      <div class="form-group">
                          <label for="section">Sekce</label>
                            <select class="form-control" name="section">
                                <?php foreach(config_item("app")['custom_field_sections'] as $section => $section_name): ?>
                                <option value="<?php echo $section; ?>"><?php echo $section_name ?></option>
                                <?php endforeach; ?>
                            </select>
                      </div>

                      <div class="form-group">
                          <label for="type">Druh pole</label>
                            <select class="form-control" name="type">
                                <?php foreach(config_item("app")['custom_field_types'] as $type => $type_name): ?>
                                <option value="<?php echo $type; ?>"><?php echo $type_name ?></option>
                                <?php endforeach; ?>
                            </select>
                      </div>

            <div class="row select-params" style="background: #f1f1f1; padding-bottom: 10px; margin-bottom: 12px; padding-top: 4px; text-align: center;display:none;">
                    <div class="form-row params_rows col-md-12">
                        <div class="col-md-12 param-row">
                            <div class="row">
                                <div class="col-md-12 mt-2">
                                    <input data-store="" name="option" type="text" class="form-control" placeholder="Název položky">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row col-md-12 mt-3">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-sm btn-primary add-param-row"><i class="icon icon-plus-circle"></i> Přidat položku</button>
                        </div>
                    </div>
            </div>

                        <div class="form-group checkers">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    Povinný údaj
                                    <div class="material-switch float-right" data-children-count="1">
                                        <input id="is_required" name="is_required" type="checkbox">
                                        <label for="is_required" class="bg-secondary"></label>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    Skryté
                                    <div class="material-switch float-right" data-children-count="1">
                                        <input id="hidden" name="hidden" type="checkbox">
                                        <label for="hidden" class="bg-secondary"></label>
                                    </div>
                                </li>
                            </ul>
                        </div>

                  </form>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-default" data-dismiss="modal">Zrušit</button>
                 <button type="button" class="btn btn-primary" id="addfieldsubmit">Přidat</button>
             </div>
        </div>
    </div>
</div>

<div id="selectTemplate" class="row" style="display: none;">
<div class="col-md-12 param-row">
    <div class="row">
        <div class="col-md-12 mt-2">
            <input data-store="" name="option" type="text" class="form-control" placeholder="Název položky">
        </div>
    </div>
</div>
</div>

<!-- Editace pole -->
<div class="modal fade" id="editFieldModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header r-0 bg-primary">
                <h6 class="modal-title text-white" id="exampleModalLabel">Editace pole</h6>
                <a style="padding-top:2px;" href="#" data-dismiss="modal" aria-label="Close"
                   class="paper-nav-toggle paper-nav-white active"><i></i></a>
            </div>
             <div class="modal-body">
                  <form action="" method="post" id="addeventform">
                      <div class="form-group">
                          <label for="name">Název <span class="required">*</span></label>
                          <input type="text" required name="name" id="name" class="form-control">
                      </div>
                      <div class="form-group">
                          <label for="description">Popisek</label>
                          <textarea name="description" id="description" class="form-control"></textarea>
                      </div>

                      <div class="form-group">
                          <label for="section">Sekce</label>
                            <select class="form-control" name="section">
                                <?php foreach(config_item("app")['custom_field_sections'] as $section => $section_name): ?>
                                <option value="<?php echo $section; ?>"><?php echo $section_name ?></option>
                                <?php endforeach; ?>
                            </select>
                      </div>

                      <div class="form-group">
                          <label for="type">Druh pole</label>
                            <select class="form-control" name="type">
                                <?php foreach(config_item("app")['custom_field_types'] as $type => $type_name): ?>
                                <option value="<?php echo $type; ?>"><?php echo $type_name ?></option>
                                <?php endforeach; ?>
                            </select>
                      </div>

            <div class="row select-params" style="background: #f1f1f1; padding-bottom: 10px; margin-bottom: 12px; padding-top: 4px; text-align: center;display:none;">
                    <div class="form-row params_rows col-md-12">
                        <div class="col-md-12 param-row">
                            <div class="row">
                                <div class="col-md-12 mt-2">
                                    <input data-store="" name="option" type="text" class="form-control" placeholder="Název položky">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row col-md-12 mt-3">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-sm btn-primary add-param-row"><i class="icon icon-plus-circle"></i> Přidat položku</button>
                        </div>
                    </div>
            </div>

                        <div class="form-group checkers">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    Povinný údaj
                                    <div class="material-switch float-right" data-children-count="1">
                                        <input id="is_required" name="is_required" type="checkbox">
                                        <label for="is_required" class="bg-secondary"></label>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    Skryté
                                    <div class="material-switch float-right" data-children-count="1">
                                        <input id="hidden" name="hidden" type="checkbox">
                                        <label for="hidden" class="bg-secondary"></label>
                                    </div>
                                </li>
                            </ul>
                        </div>

                  </form>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-default" data-dismiss="modal">Zrušit</button>
                 <button type="button" class="btn btn-primary" id="savefieldsubmit">Uložit</button>
             </div>
        </div>
    </div>
</div>
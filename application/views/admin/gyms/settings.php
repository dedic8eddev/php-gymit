<div id="gymSettingsPage" class="page has-sidebar-left has-sidebar-tabs height-full">
    <header class="white relative nav-sticky">
        <div class="container-fluid text-primary">
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li><a class="nav-link active switch-to-rooms" id="v-pills-rooms-tab" data-toggle="pill" href="#v-pills-rooms" role="tab" aria-controls="v-pills-rooms"><i class="icon icon-building2"></i>Místnosti</a></li> 
                    <li><a class="nav-link switch-to-depots" id="v-pills-depots-tab" data-toggle="pill" href="#v-pills-depots" role="tab" aria-controls="v-pills-depots"><i class="icon icon-box6"></i>Sklady</a></li> 
                    <li><a class="nav-link switch-to-solariums" id="v-pills-solariums-tab" data-toggle="pill" href="#v-pills-solariums" role="tab" aria-controls="v-pills-solariums"><i class="icon icon-wb_sunny"></i>Solária</a></li>                                                    
                </ul>
            </div>
        </div>
    </header>
    <div class="container-fluid animatedParent animateOnce">
        <div class="tab-content my-3" id="v-pills-tabContent">

            <div class="tab-pane animated fadeInUpShort show active go" id="v-pills-rooms" role="tabpanel" aria-labelledby="v-pills-rooms">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Místnosti v budově</h6>
                                <button class="btn btn-danger btn-xs float-right js-clear-filter" data-table="rooms_table">Zrušit filtr</button>
                                <a class="btn btn-primary btn-xs float-right mr-1" href="javascript:;" data-toggle="modal" data-target="#addRoomModal">Přidat místnost</a>
                            </div>
                            <div class="table-responsive">
                                <table id="roomsTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $roomsUrl; ?>">
                                </table>
                            </div>                            
                        </div>
                    </div>
                </div>     
            </div>
            
            <div class="tab-pane animated fadeInUpShort go" id="v-pills-depots" role="tabpanel" aria-labelledby="v-pills-depots">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Sklady</h6>
                                <button class="btn btn-danger btn-xs float-right js-clear-filter" data-table="depots_table">Zrušit filtr</button>
                                <a class="btn btn-primary btn-xs float-right mr-1" href="javascript:;" data-toggle="modal" data-target="#addDepotModal">Přidat sklad</a>
                            </div>
                            <div class="table-responsive">
                                <table id="depotsTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $depotsUrl; ?>">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>     
            </div>

            <div class="tab-pane animated fadeInUpShort go" id="v-pills-solariums" role="tabpanel" aria-labelledby="v-pills-solariums">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Solária</h6>
                                <button class="btn btn-danger btn-xs float-right js-clear-filter" data-table="solariums_table">Zrušit filtr</button>
                            </div>
                            <div class="table-responsive">
                                <table id="solariumsTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $solariumsUrl; ?>">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>     
            </div>            
                        
        </div>
    </div>       
</div>                       

<div class="modal fade" id="addDepotModal" role="dialog" aria-labelledby="addDepotModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content b-0">
            <div class="modal-header r-0 bg-primary">
                <h6 class="modal-title text-white" id="exampleModalLabel">Přidání skladu</h6>
                <a href="#" data-dismiss="modal" aria-label="Close"
                   class="paper-nav-toggle paper-nav-white active"><i></i></a>
            </div>
            <form id="pairNewCardForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Název <span class="required">*</span></label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Popisek</label>
                        <textarea class="form-control" name="description"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" data-ajax="<?php echo $depotSubmit; ?>" id="addDepotSubmit" class="btn btn-primary">Přidat</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="editDepotModal" role="dialog" aria-labelledby="editDepotModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content b-0">
            <div class="modal-header r-0 bg-primary">
                <h6 class="modal-title text-white" id="exampleModalLabel">Úprava skladu</h6>
                <a href="#" data-dismiss="modal" aria-label="Close"
                   class="paper-nav-toggle paper-nav-white active"><i></i></a>
            </div>
            <form id="pairNewCardForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Název <span class="required">*</span></label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Popisek</label>
                        <textarea class="form-control" name="description"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="depotSave" class="btn btn-primary">Uložit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addRoomModal" role="dialog" aria-labelledby="addRoomModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content b-0">
            <div class="modal-header r-0 bg-primary">
                <h6 class="modal-title text-white" id="exampleModalLabel">Přidání místnosti</h6>
                <a href="#" data-dismiss="modal" aria-label="Close"
                   class="paper-nav-toggle paper-nav-white active"><i></i></a>
            </div>
            <form id="addNewRoomForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Název <span class="required">*</span></label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Popisek</label>
                        <textarea class="form-control" name="description"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="col-md-2">
                            <label for="reader_id">COM Port <span class="required">*</span></label>
                            <input type="text" name="reader_id" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label for="personificator">Personifikátor <span class="required">*</span></label>
                            <select name="personificator" class="form-control">
                                <option value="0" selected>Ne</option>
                                <option value="1">Ano</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="entrance">Vstup <span class="required">*</span></label>
                            <select name="entrance" class="form-control">
                                <option value="0" selected>Ne</option>
                                <option value="1">Ano</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="exit">Exit <span class="required">*</span></label>
                            <select name="exit" class="form-control">
                                <option value="0" selected>Ne</option>
                                <option value="1">Ano</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="wellness">Wellness <span class="required">*</span></label>
                            <select name="wellness" class="form-control">
                                <option value="0" selected>Ne</option>
                                <option value="1">Ano</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="exercise_room">Sál <span class="required">*</span></label>
                            <select name="exercise_room" class="form-control">
                                <option value="0" selected>Ne</option>
                                <option value="1">Ano</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="address">Adresa <span class="required">*</span></label>
                            <select name="address" class="form-control">
                                <?php for($i = 0; $i < 250; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="priority">Priorita <span class="required">*</span></label>
                            <select name="priority" class="form-control">
                                <?php for($i = 1; $i <= 3; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="pin_code_bool">PIN? <span class="required">*</span></label>
                            <select name="pin_code_bool" class="form-control">
                                <option value="0" selected>Ne</option>
                                <option value="1">Ano</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="pin_code">PIN kód <span class="required">*</span></label>
                            <input type="number" class="form-control" name="pin_code" max="9999" disabled>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <label for="category_id">Kategorie</label>
                        <select name="category_id" class="form-control">
                            <option selected disabled>Kategorie místnosti</option>
                            <?php foreach(config_item("app")['gym_rooms_categories'] as $id => $name): ?>
                                <option value="<?php echo $id ?>"><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small>Kategorie se využívá pro výpočet souhrných statistik.</small>
                    </div>

                    <div class="form-group mt-2">
                        <label for="rooms_groups">Skupiny</label>
                        <?php $this->app_components->getSelect2Groups(['input_name' => 'rooms_groups','id' => 'rooms_groups', "multiple" => TRUE]); ?>
                        <small>Povolte přístup jen určitým skupinám, nechte prázdné pro přístup všem.</small>
                    </div>

                    <div class="form-group mt-2" style="margin-bottom: -12px !important;">
                        <label for="rooms_users">Uživatelé</label>
                        <?php $this->app_components->getSelect2Users(['input_name' => 'rooms_users','id' => 'rooms_users', "multiple" => TRUE, "onlyCards" => TRUE]); ?>
                        <small>Povolte přístup jen určitým osobám, tento výběr má přednost před skupinami!</small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" data-ajax="<?php echo $roomSubmit; ?>" id="addRoomSubmit" class="btn btn-primary">Přidat</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="editRoomModal" role="dialog" aria-labelledby="editRoomModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content b-0">
            <div class="modal-header r-0 bg-primary">
                <h6 class="modal-title text-white" id="exampleModalLabel">Úprava místnosti</h6>
                <a href="#" data-dismiss="modal" aria-label="Close"
                   class="paper-nav-toggle paper-nav-white active"><i></i></a>
            </div>
            <form id="editRoomForm">
                <div class="modal-body">
                <div class="form-group">
                        <label for="name">Název <span class="required">*</span></label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Popisek</label>
                        <textarea class="form-control" name="description"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="col-md-2">
                            <label for="reader_id">COM Port <span class="required">*</span></label>
                            <input type="text" name="reader_id" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label for="personificator">Personifikátor <span class="required">*</span></label>
                            <select name="personificator" class="form-control">
                                <option value="0" selected>Ne</option>
                                <option value="1">Ano</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="entrance">Vstup <span class="required">*</span></label>
                            <select name="entrance" class="form-control">
                                <option value="0" selected>Ne</option>
                                <option value="1">Ano</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="exit">Exit <span class="required">*</span></label>
                            <select name="exit" class="form-control">
                                <option value="0" selected>Ne</option>
                                <option value="1">Ano</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="wellness">Wellness <span class="required">*</span></label>
                            <select name="wellness" class="form-control">
                                <option value="0" selected>Ne</option>
                                <option value="1">Ano</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="exercise_room">Sál <span class="required">*</span></label>
                            <select name="exercise_room" class="form-control">
                                <option value="0" selected>Ne</option>
                                <option value="1">Ano</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="address">Adresa <span class="required">*</span></label>
                            <select name="address" class="form-control">
                                <?php for($i = 0; $i < 250; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="priority">Priorita <span class="required">*</span></label>
                            <select name="priority" class="form-control">
                                <?php for($i = 1; $i <= 3; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="pin_code_bool">PIN? <span class="required">*</span></label>
                            <select name="pin_code_bool" class="form-control">
                                <option value="0" selected>Ne</option>
                                <option value="1">Ano</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="pin_code">PIN kód <span class="required">*</span></label>
                            <input type="number" class="form-control" name="pin_code" max="9999" disabled>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <label for="category_id">Kategorie</label>
                        <select name="category_id" class="form-control">
                            <option selected disabled>Kategorie místnosti</option>
                            <?php foreach(config_item("app")['gym_rooms_categories'] as $id => $name): ?>
                                <option value="<?php echo $id ?>"><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small>Kategorie se využívá pro výpočet souhrných statistik.</small>
                    </div>

                    <div class="form-group mt-2">
                        <label for="rooms_groups">Skupiny</label>
                        <?php $this->app_components->getSelect2Groups(['input_name' => 'rooms_groups','id' => 'rooms_groups_edit', "multiple" => TRUE]); ?>
                        <small>Povolte přístup jen určitým skupinám, nechte prázdné pro přístup všem.</small>
                    </div>

                    <div class="form-group mt-2" style="margin-bottom: -12px !important;">
                        <label for="rooms_users">Uživatelé</label>
                        <?php $this->app_components->getSelect2Users(['input_name' => 'rooms_users','id' => 'rooms_users_edit', "multiple" => TRUE]); ?>
                        <small>Povolte přístup jen určitým osobám, tento výběr má přednost před skupinami!</small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" id="roomSave" class="btn btn-primary">Uložit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- SOLARIUM MODALS -->
<div class="modal fade" id="editSolariumModal" role="dialog" aria-labelledby="editSolariumModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content b-0">
            <div class="modal-header r-0 bg-primary">
                <h6 class="modal-title text-white" id="exampleModalLabel">Úprava solária</h6>
                <a href="#" data-dismiss="modal" aria-label="Close" class="paper-nav-toggle paper-nav-white active"><i></i></a>
            </div>
            <form id="solariumEditForm" novalidate>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="name">Název <span class="required">*</span></label>
                            <input type="text" class="form-control" name="name" required />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="usage_minutes_limit">Maximální provozní doba trubic (v hodinách) <span class="required">*</span></label>
                            <input type="number" class="form-control" name="usage_minutes_limit" required />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="roomSave" class="btn btn-primary">Uložit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="solariumMaintenanceModal" role="dialog" aria-labelledby="editSolariumModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content b-0">
            <div class="modal-header r-0 bg-primary">
                <h6 class="modal-title text-white" id="exampleModalLabel">Přidání záznamu o údržbě</h6>
                <a href="#" data-dismiss="modal" aria-label="Close" class="paper-nav-toggle paper-nav-white active"><i></i></a>
            </div>
            <form id="solariumMaintenanceForm" novalidate>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    Výměna trubic
                                    <div class="material-switch float-right" data-children-count="1">
                                        <input id="change_pipes" name="change_pipes" type="checkbox">
                                        <label for="change_pipes" class="bg-primary"></label>
                                    </div>
                                </li>
                            </ul>
                        </div>                   
                        <div class="form-group col-md-12">
                            <label for="name">Poznámka</label>
                            <textarea class="form-control" name="note" placeholder="Poznámka.."></textarea>
                        </div>  
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="roomSave" class="btn btn-primary">Přidat záznam</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END SOLARIUM MODALS -->
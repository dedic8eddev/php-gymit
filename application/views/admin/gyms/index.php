<div class="page has-sidebar-left has-sidebar-tabs height-full">
    <header class="white relative nav-sticky">
        <div class="container-fluid text-primary">
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li>
                        <a class="nav-link active switch-to-users" id="v-pills-all-tab" data-toggle="pill" href="#v-pills-all" role="tab" aria-controls="v-pills-all"><i class="icon icon-users"></i>Přehled</a>
                    </li>
                    <li>
                        <a class="nav-link" id="v-pills-add-tab" data-toggle="pill" href="#v-pills-add" role="tab" aria-controls="v-pills-add"><i class="icon icon-plus-circle"></i> Nová provozovna</a>
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
                                <h6 class="pull-left">Seznam provozoven</h6>
                                <button class="btn btn-danger btn-xs float-right" id="js_depot_home_clear_filter">Zrušit filtr</button>
                            </div>
                            <div class="table-responsive">
                                    <table id="gymsTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $gymsUrl; ?>">
                                    </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane animated fadeInUpShort go" id="v-pills-add" role="tabpanel" aria-labelledby="v-pills-add-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header white">
                                <h6>Vytvoření nové provozovny</h6>
                            </div>
                            <div class="card-body b-b">
                                <form id="addGymForm">

                                    <div class="form-row">
                                        <div class="form-group focused col-md-4" data-children-count="1">
                                            <label for="name">Název</label>
                                            <input class="form-control" id="gymname_input" type="text" name="name" placeholder="Název provozovny" required>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="slug">Zkratka</label>
                                            <input class="form-control" id="slug_input" type="text" name="slug" placeholder="Zkratka názvu">
                                            <small>Využíváno v URL a interně, pouze malá písmena a pomlčky místo mezer.</small>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="dbname">Název DB</label>
                                            <input class="form-control" id="dbname_input" type="text" name="dbname" placeholder="Název databáze" disabled>
                                            <small>Pro interní účely, bude vyplněno automaticky.</small>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-sm btn-primary add-gym-submit" data-ajax="<?php echo $addUrl; ?>">Přidat provozovnu</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
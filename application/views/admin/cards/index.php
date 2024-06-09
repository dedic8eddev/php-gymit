<div class="page has-sidebar-left has-sidebar-tabs height-full">
    <header class="white relative nav-sticky">
        <div class="container-fluid text-primary">
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li>
                        <a class="nav-link active switch-to-users" id="v-pills-all-tab" data-toggle="pill" href="#v-pills-all" role="tab" aria-controls="v-pills-all"><i class="icon icon-users"></i>Přehled</a>
                    </li>
                    <li>
                        <a class="nav-link" href="#pairNewCardModal" data-toggle="modal" data-target="#pairNewCardModal"><i class="icon icon-plus-circle"></i> Nová karta</a>
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
                                <h6 class="pull-left">Seznam karet</h6>
                                <button class="btn btn-danger btn-xs float-right" id="js_depot_home_clear_filter">Zrušit filtr</button>
                            </div>
                            <div class="table-responsive">
                                    <table id="cardsTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $cardsUrl; ?>">
                                    </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<div class="modal fade" id="pairNewCardModal" role="dialog" aria-labelledby="pairNewCardModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content b-0">
            <div class="modal-header r-0 bg-primary">
                <h6 class="modal-title text-white" id="exampleModalLabel">Spárování karty</h6>
                <a href="#" data-dismiss="modal" aria-label="Close"
                   class="paper-nav-toggle paper-nav-white active"><i></i></a>
            </div>
            <form id="pairNewCardForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="client_id">Uživatel</label>
                        <?php $this->app_components->getSelect2Users(['input_name' => 'client_id','id' => 'client_id']); ?>
                    </div>
                    <div class="form-group">
                        <label for="card_id">ID Karty</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <?php $this->app_components->getSelectPersonificators(['input_name' => 'reader_id','id' => 'inputGroupSelect01']); ?>
                                </span>
                            </div>
                            <input class="form-control" name="card_id" id="readerInput">

                            <div id="cardLoader"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" data-ajax="<?php echo $pairSubmit; ?>" id="pairNewCardSubmit" class="btn btn-primary">Uložit</button>
                </div>
            </form>
        </div>
    </div>
</div>
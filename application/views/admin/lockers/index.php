<div class="page has-sidebar-left has-sidebar-tabs height-full">
    <?php $this->load->view('layout/alerts'); ?>

    <div class="container-fluid relative">
        <div class="row">
            <div class="col-md-12 mt-3">
                <div class="row no-m">
                    <div class="col-md-6 locker-room-header">
                        Pánské šatny
                    </div>
                    <div class="col-md-6 locker-room-header fem">
                        Dámské šatny
                    </div>
                </div>

                <div class="row no-m text-white">
                    <div class="col-md-3 locker-room-numbers green unlocked-total-male">
                        0
                    </div>
                    <div class="col-md-3 locker-room-numbers strawberry locked-total-male">
                        0
                    </div>

                    <div class="col-md-3 locker-room-numbers green unlocked-total-female">
                        0
                    </div>
                    <div class="col-md-3 locker-room-numbers strawberry locked-total-female">
                        0
                    </div>
                </div>

                <div class="row no-m" id="lockerContainer">
                    <div class="col-md-6 no-p room-row" data-room="male"></div>
                    <div class="col-md-6 no-p room-row" data-room="female"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="lockerModal" tabindex="-1" role="dialog" aria-labelledby="lockerModal" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="modal-title" style="font-weight: bold;">Skřínka #<span></span></div>
                    <div class="modal-separator"></div>
                </div>

                <div class="col-md-12">
                    <label for="">VIP Karty</label>
                    <?php $this->app_components->getSelect2Users(['input_name' => 'vipCards','id' => 'vipCards', "multiple" => TRUE, "onlyCards" => TRUE]); ?>
                </div>
                <div class="col-md-12 mt-2">
                    <label for="">Master karty (max. 2)</label>
                    <?php $this->app_components->getSelect2Users(['input_name' => 'masterCards','id' => 'masterCards', "multiple" => TRUE, "onlyCards" => TRUE]); ?>
                </div>

                <div class="col-md-12 mt-2">
                    <a class="btn btn-primary save-locker-cards mt-3" style="width:100%;">Uložit</a>

                    <div class="modal-separator"></div>

                    <a class="btn btn-success remote-open-locker mb-1" style="width:100%;">Odemknout dálkově</a>
                    <!--<a class="btn btn-warning remote-unblock-locker mb-1" style="width:100%;">Vymazat VIP blokaci</a>
                    <a class="btn btn-danger remote-reset-locker" style="width:100%;">Resetovat skřínku</a>-->
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
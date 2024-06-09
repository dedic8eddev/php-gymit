<div id="dashboardPage" class="page has-sidebar-left has-sidebar-tabs height-full">
    <?php $this->load->view('layout/alerts'); ?>

    <div class="container-fluid relative">
        <div class="row">
            <div class="col-md-12">
                <div class="row mt-3">

                    <div class="col-md-12">
                        <small class="pull-right mt-2">Data se obnovují každé 3 sekundy</small>
                    </div>
                    <div class="col-md-12">
                        <a href="javascript:;" data-toggle="modal" data-remote="<?php echo base_url('admin/dashboard/client-modal/21'); ?>" data-target="#modal" title="Zobrazit detail" data-modal-title="Detail zákazníka" data-modal-submit="">
                            <?php echo 'Test user'; ?>
                        </a>                    
                        <?php //print_r($rooms); ?>
                        <?php if(!empty($rooms)): ?>
                            <?php foreach($rooms["data"] as $room): ?>
                                <div class="card my-3 no-b ">
                                    <div class="card-header white b-0 p-3">
                                            <div class="card-handle">
                                                <a class="room-expand" data-toggle="collapse" href="#room_<?php echo $room->id; ?>" aria-expanded="<?php if(!empty($room->occupation)): ?>true<?php else: ?>false<?php endif; ?>" aria-controls="room_<?php echo $room->id; ?>" class="">
                                                <i class="<?php if(!empty($room->occupation)){ echo 'icon-keyboard_arrow_down'; }else{ echo 'icon-keyboard_arrow_up'; } ?>"></i>
                                            </a>
                                        </div>
                                        <h4 class="card-title"><?php echo $room->name; ?></h4>
                                        <small class="card-subtitle mb-2 text-muted">
                                            <?php 
                                            if(!empty($room->occupation)){ 
                                                if(count($room->occupation) == 1) echo '1 zákazník';
                                                else if (count($room->occupation) > 1 && count($room->occupation) < 5) echo count($room->occupation) . ' zákazníci';
                                                else if (count($room->occupation) > 4) echo count($room->occupation) . ' zákazníků';
                                            } else echo 'Místnost je prázdná';
                                            ?>
                                        </small>
                                    </div>
                                    <div class="collapse <?php if(!empty($room->occupation)){ echo 'show'; } ?>" id="room_<?php echo $room->id; ?>" style="">
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead class="bg-light">
                                                    <tr>
                                                        <th>Jméno</th>
                                                        <th>Čas</th>
                                                        <th>Vstup</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if(!empty($room->occupation)): ?>
                                                        <?php foreach($room->occupation as $o): ?>
                                                            <?php echo $o; ?>
                                                        <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>    

            </div>
        </div>
    </div>
    <!-- AJAX MODAL -->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header r-0">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button id="btn-dismiss-modal" type="button" class="btn btn-default" data-dismiss="modal">Zrušit</button>
                    <button type="button" class="btn btn-primary" id="modalSubmit">Přidat</button>                    
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalOverModal" tabindex="-1" role="dialog" aria-labelledby="modalOverModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:650px;">
            <div class="modal-content">
                <div class="modal-header r-0">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button id="btn-dismiss-modal" type="button" class="btn btn-default" data-dismiss="modal">Zrušit</button>
                    <button type="button" class="btn btn-primary" id="modalOverModalSubmit">Přidat</button>                    
                </div>
            </div>
        </div>
    </div>      
</div>

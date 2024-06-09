<div id="vocuhersPage" class="page has-sidebar-left has-sidebar-tabs height-full">
    <header class="white relative nav-sticky">
        <div class="container-fluid text-primary">
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li>
                        <a class="nav-link active" id="v-pills-table-tab" data-toggle="pill" href="#v-pills-table" role="tab" aria-controls="v-pills-table"><i class="icon icon-list2"></i>Přehled</a>
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
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Vouchery</h6>
                                <button class="btn btn-danger btn-xs float-right js-clear-filter" data-table="vouchers_table">Zrušit filtr</button>
                            </div>
                            <div class="table-responsive">
                                <table id="vouchersTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $vouchersUrl; ?>">
                                </table>
                            </div>                            
                        </div>
                    </div>
                </div> 
            </div>
        </div> 
    </div>
    <!-- INVOICE ITEMS MODAL -->
    <div class="modal fade" id="invoiceItemsModal" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header r-0 bg-primary">
                    <h5 class="modal-title text-white">Přidat vouchery</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <table id="addVoucherItemsTable" class="table table-striped" data-identification-type="invoice" data-identification-id="<?php echo @$invoiceId; ?>">
                        <thead>
                            <tr><th>Položka</th>
                            <th style="width:100px;">Počet</th></tr>
                        </thead>
                        <tbody>
                            <?php if(isset($invoiceItems) && !empty($invoiceItems)): ?>
                            <?php foreach($invoiceItems as $i): ?>
                            <tr>
                                <td class="p-1"><input class="form-control" value="<?php echo $i['name']; ?>" readonly /></td>
                                <td class="p-1"><input type="number" class="form-control voucher-item" data-item-type="<?php echo $i['type']; ?>" data-item-id="<?php echo $i['id']; ?>" value="<?php echo $i['amount']; ?>" readonly /></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan='2' class="text-center">V této faktuře nejsou žádné položky, ze kterých by šly vytvořit vouchery</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <?php if(isset($createdVouchers) && $createdVouchers>0): ?>
                        <div role="alert" class="alert alert-warning text-center"><i class="icon-warning mr-2"></i><strong>Vouchery byly již vytvořeny</strong></div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Zrušit</button>
                    <?php if(isset($createdVouchers) && $createdVouchers>0): ?>
                    <button id="showCreatedInvoiceVouchers" data-invoice-number="<?php echo $invoiceId; ?>" type="button" class="btn btn-primary">Zobrazit vouchery</button>                    
                    <?php else: ?>
                        <?php if(!isset($createdVouchers) || (isset($createdVouchers) && $createdVouchers==0)): ?>
                        <button id="addVouchers" type="button" class="btn btn-primary">Přidat</button> 
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>       
    <!-- AJAX MODAL -->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header r-0">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
<div class="page has-sidebar-left has-sidebar-tabs height-full">
    <!--<header class="white relative nav-sticky">
        <div class="container-fluid text-primary">
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li>
                        <a class="nav-link active" id="v-pills-all-tab" data-toggle="pill" href="#v-pills-all" role="tab" aria-controls="v-pills-all"><i class="icon icon-users"></i>Reporty</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>-->

    <?php $this->load->view('layout/alerts'); ?>

    <div class="container-fluid animatedParent animateOnce">
        <div class="tab-content my-3" id="v-pills-tabContent">
            
            <div class="tab-pane animated fadeInUpShort show active go" id="v-pills-all" role="tabpanel" aria-labelledby="v-pills-all-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header white">
                                <h6 class="pull-left">Přehled reportů</h6>                            
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover r-0">
                                    <thead>
                                        <tr>
                                            <th style="width:30%;">Název</th>
                                            <th>Popis</th>
                                        </tr>
                                    </thead>
                                    <tbody style="cursor:pointer;">
                                        <tr data-report="daily_report" data-toggle="modal" data-target="#dateFilterModal">
                                            <td>Denní report pro rozsah</td>
                                            <td>....</td>
                                        </tr>
                                        <tr data-report="manager_report" data-toggle="modal" data-target="#dateFilterModal">
                                            <td>Manažerská výsledovka</td>
                                            <td>....</td>
                                        </tr>
                                        <tr data-report="checkouts_report" data-toggle="modal" data-target="#dateFilterModal">
                                            <td>Report pokladen</td>
                                            <td>Informace o otevírání a zavírání jednotlivých pokladen</td>
                                        </tr>                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>     
            </div>

        </div>
    </div>

</div>
<!-- MODAL -->
<div class="modal fade" id="dateFilterModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Vyberte rozmezí</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form class="form-row">
                    <div class="form-group col-md-6">
                        <input type="date" placeholder="Datum od" name="from" class="form-control" />
                    </div>
                    <div class="form-group col-md-6">
                        <input type="date" placeholder="Datum do" name="to" class="form-control" />
                    </div>   
                    <div class="form-group col-md-12">
                        <a class="generateReport btn btn-primary btn-block">Vygenerovat</a>                 
                    </div>
                </form>            
                
            </div>
        </div>
    </div>
</div>
<div id="gymSolariumLogsPage" class="page has-sidebar-left has-sidebar-tabs height-full">
    <header class="white relative nav-sticky">
        <div class="container-fluid text-primary">
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li><a class="nav-link active" id="v-pills-usage-tab" data-toggle="pill" href="#v-pills-usage" role="tab" aria-controls="v-pills-usage"><i class="icon icon-building2"></i>Použití</a></li> 
                    <li><a class="nav-link" id="v-pills-maintenance-tab" data-toggle="pill" href="#v-pills-maintenance" role="tab" aria-controls="v-pills-maintenance"><i class="icon icon-box6"></i>Údržba</a></li> 
                </ul>
            </div>
        </div>
    </header>
    <div class="container-fluid animatedParent animateOnce">
        <div class="tab-content my-3" id="v-pills-tabContent">

            <div class="tab-pane animated fadeInUpShort show active go" id="v-pills-usage" role="tabpanel" aria-labelledby="v-pills-usage">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Logy použití solárií</h6>
                                <button class="btn btn-danger btn-xs float-right js-clear-filter" data-table="usage_table">Zrušit filtr</button>
                            </div>
                            <div class="table-responsive">
                                <table id="usageTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $usageUrl; ?>">
                                </table>
                            </div>                            
                        </div>
                    </div>
                </div>     
            </div>
            
            <div class="tab-pane animated fadeInUpShort go" id="v-pills-maintenance" role="tabpanel" aria-labelledby="v-pills-depots">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Logy údržby</h6>
                                <button class="btn btn-danger btn-xs float-right js-clear-filter" data-table="maintenance_table">Zrušit filtr</button>
                            </div>
                            <div class="table-responsive">
                                <table id="maintenanceTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $maintenanceUrl; ?>">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>     
            </div>         
                        
        </div>
    </div>       
</div>                       
<div class="row justify-content-between">
    <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
        <li>
            <a class="nav-link active" id="v-pills-general-tab" data-toggle="pill" href="#v-pills-general" role="tab" aria-controls="v-pills-general"><i class="icon icon-th-list"></i>Základní údaje</a>
        </li>                    
        <li>
            <a class="nav-link" id="v-pills-opening-hours-tab" data-toggle="pill" href="#v-pills-opening-hours" role="tab" aria-controls="v-pills-opening-hours"><i class="icon icon-clock-o"></i>Otevírací hodiny</a>
        </li>                                                               
    </ul>
</div>
<div class="tab-content my-3" id="v-pills-tabContent">

    <div class="tab-pane animated fadeInUpShort show active go" id="v-pills-general" role="tabpanel" aria-labelledby="v-pills-general-tab">
        <div class="row my-3">
            <div class="col-md-12">
                <div class="card r-0 shadow">
                    <div class="card-header white">
                        <h6 class="pull-left">Základní údaje</h6>
                    </div>
                    <div class="card-body">
                        <?php $this->load->view('admin/cms/general_info', @$general_info); ?>
                    </div>                            
                </div>
            </div>
        </div>                
    </div>           

    <div class="tab-pane animated fadeInUpShort go" id="v-pills-opening-hours" role="tabpanel" aria-labelledby="v-pills-opening-hours-tab">
        <div class="row my-3">
            <div class="col-md-12">
                <div class="card r-0 shadow">
                    <div class="card-header white">
                        <h6 class="pull-left">Otevírací hodiny</h6>
                    </div>
                    <div class="card-body">
                        <?php $this->load->view('admin/cms/opening_hours', @$opening_hours); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>  
                        
</div>
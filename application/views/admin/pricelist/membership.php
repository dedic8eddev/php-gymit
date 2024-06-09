
<form id="membershipForm" data-ajax="<?php echo $saveUrl; ?>">
    <input type="hidden" name="id" value="<?php echo $membership->id; ?>" />
    <input type="hidden" name="type_id" value="<?php echo $membership->type_id; ?>" />
    <div class="row justify-content-between">
        <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
            <li><a class="nav-link active" id="v-pills-general-tab" data-toggle="pill" href="#v-pills-general" role="tab" aria-controls="v-pills-general"><i class="icon icon-th-list"></i>Obsah</a></li>                    
            <li><a class="nav-link" id="v-pills-servicesPrices-tab" data-toggle="pill" href="#v-pills-servicesPrices" role="tab" aria-controls="v-pills-servicesPrices"><i class="icon icon-dollar"></i>Ceník</a></li>  
            <!--<li><a class="nav-link" id="v-pills-benefits-tab" data-toggle="pill" href="#v-pills-benefits" role="tab" aria-controls="v-pills-benefits"><i class="icon icon-star-3"></i>Výhody</a></li>-->
        </ul>
    </div>

    <div class="tab-content my-3" id="v-pills-tabContent">
        <div class="tab-pane animated fadeInUpShort show active go" id="v-pills-general" role="tabpanel" aria-labelledby="v-pills-general-tab">
            <div class="row my-3">
                <div class="col-md-12">
                    <div class="card r-0 shadow">
                        <div class="card-header white">
                            <h6 class="pull-left">Obsah</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="name">Název <small>(Pro účely administrace)</small> <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $membership->name; ?>" placeholder="Název členství" required />
                                </div>  
                                <div class="form-group col-md-6">
                                    <label for="name">Název typu členství <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="type_name" value="<?php echo $membership->type_name; ?>" placeholder="Název typu členství" required />
                                </div>                                  
                            </div>  
                            <?php $data=json_decode($membership->front_data); ?>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="title_1">Název v hlavičce <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="front_data[header_title]" value="<?php echo @$data->header_title; ?>" placeholder="Název v hlavičce" required />
                                </div>                            
                                <div class="form-group col-md-12">
                                    <label for="title_1">Titulek 1. textu <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="front_data[title_1]" value="<?php echo @$data->title_1; ?>" placeholder="Titulek 1. textu" required />
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="text_1">Text 1 <span class="required">*</span></label>
                                    <textarea class="form-control js-trumbowyg-editor" name="front_data[text_1]" placeholder="Text 1" required><?php echo @$data->text_1; ?></textarea>
                                </div>           
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="title_2">Titulek 2. textu</label>
                                    <input type="text" class="form-control" name="front_data[title_2]" value="<?php echo @$data->title_1; ?>" placeholder="Titulek 2. textu" />
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="subtitle_2">Podtitulek 2. textu</label>
                                    <input type="text" class="form-control" name="front_data[subtitle_2]" value="<?php echo @$data->subtitle_2; ?>" placeholder="Podtitulek 2. textu" />
                                </div>        
                                <div class="form-group col-md-12">
                                    <label for="text_2">Text 2</label>
                                    <textarea class="form-control js-trumbowyg-editor" name="front_data[text_2]" placeholder="Text 2"><?php echo @$data->text_2; ?></textarea>
                                </div>           
                            </div>                          
                        </div>
                    </div>
                </div>
            </div>        
        </div> 

        <div class="tab-pane animated fadeInUpShort go" id="v-pills-servicesPrices" role="tabpanel" aria-labelledby="v-pills-servicesPrices-tab">
            <div class="row my-3">
                <div class="col-md-12">
                    <div class="card r-0 shadow">
                        <div class="card-header white">
                            <h6 class="pull-left">Cena členství</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <?php foreach ($membership->prices as $p):
                                    if($p->period_type=='month') $period_type = 'měsíc'; 
                                    else if ($p->period_type=='quarter') $period_type = 'čtvrtletí';
                                    else $period_type = 'rok'; 
                                ?>
                                <?php $label = $p->period_type?'Cena za '.$period_type : 'Cena'; ?>
                                <div class="form-group col-md-6">
                                    <label><?php echo $label; ?></label>
                                    <input type="number" name="prices[<?php echo $p->id;?>][price]" class="form-control" value="<?php echo $p->price;?>" />
                                    <input type="hidden" name="prices[<?php echo $p->id;?>][period_type]" value="<?php echo $p->period_type;?>" />
                                </div>
                                <?php endforeach; ?>
                            </div>   
                    
                        </div>
                    </div>
                </div>
            </div> 
            <div class="row my-3">
                <div class="col-md-12">
                    <div class="card r-0 shadow">
                        <div class="card-header white">
                            <h6 class="pull-left">Ceník služeb <small>(Ceny editujte klikem do buňky)</small></h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="servicesPrices_table" class="table table-striped table-hover r-0" data-ajax="<?php echo $servicesPricesUrl; ?>"></table>
                            </div>     
                        </div>
                    </div>
                </div>
            </div>                        
        </div>

        <div class="tab-pane animated fadeInUpShort go" id="v-pills-benefits" role="tabpanel" aria-labelledby="v-pills-benefits-tab">
            <div class="row my-3">
                <div class="col-md-12">
                    <div class="card r-0 shadow">
                        <div class="card-header white">Výhody členství</div>
                        <div class="card-body p-0">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Typ</th>
                                        <th>Položka</th>
                                        <th>Sleva (%)</th>
                                        <th>Počet</th>
                                        <th>Perioda</th>
                                        <th>Čas od</th>
                                        <th>Čas do</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($membership_benefits as $mb): ?>
                                    <tr>
                                        <td><?php echo $mb->depot ? 'Sklad' : 'Služba'; ?></td>
                                        <td><?php echo $mb->item_name; ?></td>
                                        <td class="text-right"><?php echo $mb->discount; ?></td>
                                        <td class="text-right"><?php echo $mb->forever ? 'Neomezeně' : $mb->quantity.' x'; ?></td>
                                        <td><?php echo $mb->period_type ? paymentPeriodToHuman($mb->period_type) : ''; ?></td>
                                        <td><?php echo $mb->specific_hour_start; ?></td>
                                        <td><?php echo $mb->specific_hour_end; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

</form>              
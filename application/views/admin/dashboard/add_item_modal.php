<div class="row">
    <div class="col-2 mb-3" style="border-right:1px solid #e1e8ee;">
        <ul class="nav nav-pills flex-column" id="myTab" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="service-tab" data-toggle="tab" href="#service" role="tab" aria-controls="service" aria-selected="true">Služby</a></li>
            <li class="nav-item"><a class="nav-link" id="depot-tab" data-toggle="tab" href="#depot" role="tab" aria-controls="depot" aria-selected="false">Sklad</a></li>
        </ul>
    </div>
    <div class="col-10">
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="service" role="tabpanel" aria-labelledby="service-tab">                                   
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label for="service_item">Položka</label>
                        <select id="service_item" name="service_item" class="select2">
                            <?php foreach($price_list as $item): ?>
                                <?php if(is_object($item)): // Membership price ?>
                                    <option value="<?php echo $item->price_id; ?>" data-price="<?php echo $item->vat_price; ?>" data-vat="<?php echo $item->vat; ?>"><?php echo $item->name; ?></option>
                                <?php else: // Classis price without membership ?>
                                    <option value="<?php echo $item['id']; ?>" data-price="<?php echo $item['vat_price']; ?>" data-vat="<?php echo $item['vat']; ?>"><?php echo $item['name']; ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>                                                                                                                                                   
                </div>     
                <button id="btnAddServiceItem" class="btn btn-sm btn-primary">Přidat položku</button>&nbsp;                                                
            </div>
            <div class="tab-pane fade" id="depot" role="tabpanel" aria-labelledby="depot-tab">
                <div class="form-row">
                    <div class="form-group col-md-5">
                        <label for="depot_id">Sklad</label>
                        <select id="depot_id" name="depot_id" class="select2">
                            <?php foreach($depots as $depot): ?>
                                <option value="<?php echo $depot->id; ?>"><?php echo $depot->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-7">
                        <label for="depot_item">Položka</label>
                        <select id="depot_item" name="depot_item" class="select2"></select>
                    </div>
                </div>   
                <button id="btnAddDepotItem" class="btn btn-sm btn-primary">Přidat položku</button>              
            </div>
        </div>      
    </div>
</div>  
<hr class="text-primary bg-primary">
<div class="row">
    <div class="col-md-12 table-responsive">
        <table id="addListTable" class="table table-hover">
            <thead class="bg-light">
            <tr>
                <th>Položka</th>
                <th class="text-right" style="width:100px;">Množství</th>
                <th class="text-right" style="width:100px;">Sleva (%)</th>
                <th class="text-right" style="width:120px;">Cena celkem</th>
                <th style="width:40px;"></th>
            </tr>
            </thead>
            <tbody>                                                           
            </tbody>
        </table>
    </div>    
</div>
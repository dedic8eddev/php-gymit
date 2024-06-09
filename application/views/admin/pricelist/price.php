
<form id="priceForm" data-ajax="<?php echo $saveUrl; ?>">
    <input type="hidden" name="id" value="<?php echo @$price['id']; ?>" />
    <div class="form-row">
        <div class="col-md-6 mb-3">
            <label for="service_type">Typ služby <span class="required">*</span></label>
            <select name="service_type" id="service_type" class="form-control" required>
                <?php if (!isset($price['service_type'])): ?> 
                <option disabled selected>Vyberte typ služby</option>
                <?php endif; ?>
                <?php foreach(config_item('app')['services'] as $value => $name): ?>
                    <option value="<?php echo $value; ?>" <?php echo @$price['service_type']==$value ? 'selected':''; ?>><?php echo $name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6 mb-3 js-duration-col <?php echo in_array(@$price['service_type'],[3,5]) ? '':'d-none';?>">
            <label for="client_limit">Trvání <span class="required">*</span></label>
            <select name="duration" id="duration" class="form-control">
            <?php foreach(config_item('app')['lessons_duration'] as $key => $val): ?>
                <option value="<?php echo $key; ?>" <?php echo @$price['duration']==$key ? 'selected':''; ?>><?php echo $val; ?></option>
            <?php endforeach; ?>
            </select>
        </div>         
    </div>      

    <div class="form-row">
        <div class="form-group col-md-12">
            <label for="name">Druh účtu <span class="required">*</span></label>
            <?php $this->app_components->getSelect2AutocontAccounts(['input_name' => 'account_number','id' => 'account_number', 'required' => true]); ?>
        </div>  
    </div>

    <div class="form-row">
        <div class="form-group col-md-12">
            <label for="name">Název <span class="required">*</span></label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo @$price['name']; ?>" <?php echo @$price['service_type']==3 ? '':'required';?> placeholder="Název položky" />
        </div>  
    </div>    
    <div class="form-row">
        <div class="col-md-4 mb-3">
            <label for="price">Prodejní cena za jednotku (bez DPH) <span class="required">*</span></label>
            <input type="number" min="0" step="0.01" name="price" id="price_edit" value="<?php echo @$price['price']; ?>" class="form-control" placeholder="Prodejní cena bez DPH" required>
        </div>
        <div class="col-md-4 mb-3">
            <label for="vat">Hodnota DPH (21% bez výběru)</label>
            <select name="vat" id="vat_value_edit" class="form-control">
                <option disabled selected>Vyberte % DPH</option>
                <?php foreach(config_item('app')['vat_values'] as $value => $name): ?>
                    <option value="<?php echo $value; ?>" <?php echo @$price['vat']==$value ? 'selected' :''; ?>><?php echo $name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label for="vat_price">Prodejní cena za jednotku s DPH <span class="required">*</span></label>
            <input type="number" min="0" step="0.01" name="vat_price" id="vat_price_edit" value="<?php echo @$price['vat_price']; ?>" class="form-control" placeholder="Prodejní cena s DPH" required>
        </div>
    </div>       
    <div class="form-row">
        <div class="col-md-12 mb-3">
            <label for="description">Popisek</label>
            <textarea name="description" id="description_edit" class="form-control js-trumbowyg-editor" placeholder="Popis položky.."><?php echo @$price['description']; ?></textarea>
        </div>                                                                                                
    </div>
    <div class="form-row">
        <div class="col-md-3 mb-3">
          <label class="customCheckbox">
              <?php
              echo form_checkbox([
                  'name' => 'visible',
                  'value' => empty($price['visible']) ? 1 : 0,
                  'checked' => ! empty($price['visible']),
              ]); ?>
              <span class="title">Zobrazit na webu</span>
          </label>
        </div>
    </div>
</form>              
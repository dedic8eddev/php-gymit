<input type="hidden" id="user_id" name="user_id" value="<?php echo @$coach->id; ?>" />
<input type="hidden" name="coach_data_id" value="<?php echo @$coach_data->id; ?>" />
<div class="row">
    <div class="col-md-6">
        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="role">Instruktor / trenér <span class="required">*</span></label>
              <?php echo form_dropdown([ 'name' => 'role', 'class' => 'form-control', 'required' => 'required'], [
                      PERSONAL_TRAINER  => 'Trenér',
                      MASTER_TRAINER    => 'Master trenér',
                      INSTRUCTOR        => 'Instruktor',
                  ], $coach->group_id ?? PERSONAL_TRAINER);
              ?>
            </div>                                    
            <div class="form-group col-md-12">
                <label for="phone">IČ <span class="required">*</span></label>
                <input class="form-control" type="number" name="company_id" value="<?php echo @$users_data->company_id; ?>" placeholder="IČ" required>
            </div>
            <div class="form-group col-md-6">
                <label for="phone">DIČ</label>
                <input class="form-control" type="text" name="vat_id" value="<?php echo @$users_data->vat_id; ?>" placeholder="DIČ">
            </div>  
            <div class="form-group col-md-6">
                <label for="phone">Plátce DPH <span class="required">*</span></label>
                <select class="form-control" name="vat_enabled" required>
                    <option value="" selected disabled>Vyberte</option>
                    <option value="1" <?php echo (@$users_data->vat_enabled == 1) ? "selected" : ""; ?>>Ano</option>
                    <option value="0" <?php echo (@$users_data->vat_enabled == 0) ? "selected" : ""; ?>>NE</option>
                </select>
            </div>                                                                                                              
        </div> 
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="identification_type">Typ identifikačního průkazu <span class="required">*</span></label>
                <?php $this->app_components->getSelect2IdentificationTypes(['input_name' => 'identification_type', 'id' => 'identification_type', 'selected' => @$users_data->identification_type, 'required' => true]); ?>
            </div>
            <div class="form-group col-md-6">
                <label for="identification">Číslo identifikačního průkazu <span class="required">*</span></label>
                <input class="form-control" type="text" name="identification" value="<?php echo @$users_data->identification; ?>" placeholder="Číslo identifikačního průkazu" required>
            </div>                             
        </div>  
        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="quote">Citát</label>
                <input type="text" name="coach_data[quote]" class="form-control" placeholder="Citát" value="<?php echo @$coach_data->quote; ?>" />
            </div>                                      
            <div class="form-group col-md-12">
                <label for="about">Popis</label>
                <textarea name="coach_data[about]" id="about" class="form-control js-trumbowyg-editor" placeholder="O Sobě .."><?php echo @$coach_data->about; ?></textarea>
            </div>                                    
        </div>                                                                                                         
    </div>
    <div class="col-md-6">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="birth_date">Datum narození <span class="required">*</span></label>
                <input type="text" name="birth_date" class="form-control" id="birth_date" value="<?php echo @$users_data->birth_date; ?>" placeholder="Datum narození" required />
            </div>   
            <div class="form-group col-md-6">
                <label for="personal_identification_number">Rodné číslo <span class="required">*</span></label>
                <input type="text" name="personal_identification_number" class="form-control" value="<?php echo @$users_data->personal_identification_number; ?>" placeholder="Rodné číslo" required />
            </div>                 
        </div>  
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="first_name">Křestní Jméno <span class="required">*</span></label>
                <input class="form-control" type="text" name="first_name" value="<?php echo @$users_data->first_name; ?>" placeholder="Jméno" required>
            </div>
            <div class="form-group col-md-6">
                <label for="last_name">Příjmení <span class="required">*</span></label>
                <input class="form-control" type="text" name="last_name" value="<?php echo @$users_data->last_name; ?>" placeholder="Příjmení" required>
            </div>                                    
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="address">Ulice <span class="required">*</span></label>
                <input class="form-control" type="text" name="street" value="<?php echo @$users_data->street; ?>" placeholder="Ulice" required>
            </div>
            <div class="form-group col-md-6">
                <label for="address">Město <span class="required">*</span></label>
                <input class="form-control" type="text" name="city" value="<?php echo @$users_data->city; ?>" placeholder="Město" required>  
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="address">PSČ <span class="required">*</span></label>
                <input class="form-control" type="text" name="zip" value="<?php echo @$users_data->zip; ?>" placeholder="PSČ" required>     
            </div>
            <div class="form-group col-md-6">
                <label for="country">Země <span class="required">*</span></label>
                <?php $this->app_components->getSelect2Country(['input_name' => 'country','id' => 'country_id', 'selected' => @$users_data->country, 'required' => true]); ?>
            </div>
        </div>  
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="email">Email <span class="required">*</span></label>
                <input class="form-control" type="email" name="email" value="<?php echo @$coach->email; ?>" placeholder="E-mailová adresa" required>
            </div>
            <div class="form-group col-md-6">
                <label for="phone">Telefon <span class="required">*</span></label>
                <input class="form-control" type="number" name="phone" value="<?php echo @$users_data->phone; ?>" placeholder="Telefonní číslo" required>
            </div>
        </div> 
        <div class="form-row mb-3">   
            <div class="col-md-4 js-media-input-container mb-2">
                <div class="js-media-open-modal-btn">
                    <label for="photo">Foto uživatele</label>
                    <div class="aspect16_8 image-preview<?php echo strlen(@$users_data->photo)>0 ? ' uploaded':''; ?>" style="<?php echo strlen(@$users_data->photo)>0 ? "background-image:url('".$this->app->getMedia($users_data->photo_src,$users_data->photo_meta,true)['src']."')" : ''; ?>" data-placeholder="Klikněte pro otevření galerie."></div>
                    <input type="hidden" name="photo" value='<?php echo @$users_data->photo; ?>' class="js-media-input-target-id">
                </div>  
            </div>
            <div class="col-md-8">
                <label for="specializations">Specializace</label>
                <a id="btnSpecializationsModal" class="ml-1" href="javascript:;" data-toggle="modal" data-remote="/admin/coaches/specializations" data-target="#modal" data-modal-title="Správa specializací" data-modal-submit="Uložit" title="Správa specializací"><i class="icon-pencil"></i></a>
                <select id="specializations" class="select2" name="specializations[]" multiple>
                    <?php foreach(@$specializations as $s): ?>
                        <option value="<?php echo $s['id']; ?>" <?php echo (is_array(@$coach_specializations) && in_array($s['id'],$coach_specializations)) ? 'selected' : ''; ?>><?php echo $s['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>                
        </div>                                                                                                                                              
    </div>
</div>                                   
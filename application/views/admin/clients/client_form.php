<input type="hidden" id="user_id" name="user_id" value="<?php echo @$user->id; ?>" />
<input type="hidden" name="client_data_id" value="<?php echo @$client_data->id; ?>" />
<div class="row">
    <div class="col-md-6">
        <div class="form-row">
            <div class="form-group col-md-12">
                <ul class="list-group">
                    <li class="list-group-item">
                        Jednorázový uživatel
                        <div class="material-switch float-right">
                            <input class="lessRequired" id="disposable_user" name="disposable_user" type="checkbox" <?php echo @$user->group_id==21 ? 'checked' : ''; ?>>
                            <label for="disposable_user" class="bg-primary"></label>
                        </div>
                    </li>
                    <li class="list-group-item">
                        VIP
                        <div class="material-switch float-right">
                            <input class="lessRequired" id="client_data[vip]" name="client_data[vip]" type="checkbox" <?php echo @$client_data->vip ? 'checked' : ''; ?>>
                            <label for="client_data[vip]" class="bg-success"></label>
                        </div>
                    </li>
                    <li class="list-group-item">
                        Dailypass
                        <div class="material-switch float-right">
                            <input class="lessRequired" id="client_data[dailypass]" name="client_data[dailypass]" type="checkbox" <?php echo @$client_data->dailypass ? 'checked' : ''; ?>>
                            <label for="client_data[dailypass]" class="bg-secondary"></label>
                        </div>
                    </li>                                        
                </ul>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6 disposable normal">
                <label for="first_name">Křestní Jméno <span class="required">*</span></label>
                <input class="form-control" type="text" name="first_name" value="<?php echo @$user_data->first_name; ?>" placeholder="Jméno" required>
            </div>
            <div class="form-group col-md-6 disposable normal">
                <label for="last_name">Příjmení <span class="required">*</span></label>
                <input class="form-control" type="text" name="last_name" value="<?php echo @$user_data->last_name; ?>" placeholder="Příjmení" required>
            </div>                                    
        </div>    
        <div class="form-row">                                
            <div class="form-group col-md-4">
                <label for="company_id">IČ</label>
                <input class="form-control" type="number" name="company_id" value="<?php echo @$user_data->company_id; ?>" placeholder="IČ">
            </div>
            <div class="form-group col-md-4">
                <label for="vat_id">DIČ</label>
                <input class="form-control" type="text" name="vat_id" value="<?php echo @$user_data->vat_id; ?>" placeholder="DIČ">
            </div>
            <div class="form-group col-md-4">
                <label for="vat_enabled">Plátce DPH</label>
                <select class="form-control" name="vat_enabled">
                    <option value="1" <?php echo @$user_data->vat_enabled == 1 ? 'selected' :''; ?>>Ano</option>
                    <option value="0" <?php echo @$user_data->vat_enabled == 1 ? '' :'selected'; ?>>NE</option>
                </select>   
            </div>                                                                                                                  
        </div> 
        <div class="form-row">
            <div class="form-group col-md-6 normal">
                <label for="identification_type">Typ identifikačního průkazu <span class="required">*</span></label>
                <?php $this->app_components->getSelect2IdentificationTypes(['input_name' => 'identification_type', 'id' => 'identification_type', 'selected' => @$user_data->identification_type, 'required' => true]); ?>
            </div>
            <div class="form-group col-md-6 normal">
                <label for="identification">Číslo identifikačního průkazu <span class="required">*</span></label>
                <input class="form-control" type="text" name="identification" value="<?php echo @$user_data->identification; ?>" placeholder="Číslo identifikačního průkazu" required>
            </div>                             
        </div>
        <div class="form-row mb-3">   
            <div class="col-md-4 js-media-input-container mb-2">
                <div class="js-media-open-modal-btn">
                    <label for="photo">Foto uživatele</label>
                    <div class="aspect16_8 image-preview<?php echo strlen(@$user_data->photo)>0 ? ' uploaded':''; ?>" style="<?php echo strlen(@$user_data->photo)>0 ? "background-image:url('".$this->app->getMedia($user_data->photo_src,$user_data->photo_meta,true)['src']."')" : ''; ?>" data-placeholder="Klikněte pro otevření galerie."></div>
                    <input type="hidden" name="photo" value='<?php echo @$user_data->photo; ?>' class="js-media-input-target-id">
                </div>  
            </div>     
            <div class="col-md-8">
                <label for="internal_note">Interní poznámka</label>
                <textarea class="form-control" name="internal_note" rows="3" placeholder="Interní poznámka.."><?php echo @$user_data->internal_note; ?></textarea>
            </div>        
        </div>                                                                                                                   
    </div>
    <div class="col-md-6">
        <div class="form-row">
            <div class="form-group col-md-6 normal">
                <label for="birth_date">Datum narození <span class="required">*</span></label>
                <input type="text" name="birth_date" class="form-control" id="birth_date" value="<?php echo @$user_data->birth_date; ?>" placeholder="Datum narození" required />
            </div>   
            <div class="form-group col-md-6 normal">
                <label for="personal_identification_number">Rodné číslo <span class="required">*</span></label>
                <input type="text" name="personal_identification_number" class="form-control" value="<?php echo @$user_data->personal_identification_number; ?>" placeholder="Rodné číslo" required />
            </div>                 
        </div>  
        <div class="form-row">
            <div class="form-group col-md-6 disposable normal">
                <label for="email">Email <span class="required">*</span></label>
                <input class="form-control" type="email" name="email" value="<?php echo @$user->email; ?>" placeholder="E-mailová adresa" required>
            </div>
            <div class="form-group col-md-6 normal">
                <label for="phone">Telefon <span class="required">*</span></label>
                <input class="form-control" type="number" name="phone" value="<?php echo @$user_data->phone; ?>" placeholder="Telefonní číslo" required>
            </div>
        </div>          
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="address">Ulice</label>
                <input class="form-control" type="text" name="street" value="<?php echo @$user_data->street; ?>" placeholder="Ulice">
            </div>
            <div class="form-group col-md-6">
                <label for="address">Město</label>
                <input class="form-control" type="text" name="city" value="<?php echo @$user_data->city; ?>" placeholder="Město">  
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="address">PSČ</label>
                <input class="form-control" type="text" name="zip" value="<?php echo @$user_data->zip; ?>" placeholder="PSČ">     
            </div>
            <div class="form-group col-md-6">
                <label for="country">Země</label>
                <?php $this->app_components->getSelect2Country(['input_name' => 'country','id' => 'country_id', 'selected' => @$user_data->country]); ?>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="multisport_id">Číslo multisport karty</label>
                <input class="form-control" type="text" name="client_data[multisport_id]" value="<?php echo @$client_data->multisport_id; ?>" placeholder="Číslo multisport karty">     
            </div>
        </div>          
        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="card_id">ID Karty</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <?php $this->app_components->getSelectPersonificators(['input_name' => 'reader_id','id' => 'inputGroupSelect01']); ?>
                        </span>
                    </div>
                    <input class="form-control" name="card_id" id="readerInput">
                    <div id="cardLoader"></div>
                </div>
            </div>
        </div>                                                                                                                        
    </div>
</div>                                   
<form id="generalInfoForm" data-ajax="<?php echo $saveGeneralInfoUrl; ?>">
    <input type="hidden" name="id" value="<?php echo @$id; ?>">
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="address">Ulice <span class="required">*</span></label>
            <input class="form-control" type="text" name="street" placeholder="Ulice" value="<?php echo @$data['street']; ?>" required>
        </div>
        <div class="form-group col-md-6">
            <label for="address">Město <span class="required">*</span></label>
            <input class="form-control" type="text" name="city" placeholder="Město" value="<?php echo @$data['city']; ?>" required>
        </div>                                        
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="address">PSČ <span class="required">*</span></label>
            <input class="form-control" type="text" name="zip" placeholder="PSČ" value="<?php echo @$data['zip']; ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="country">Země <span class="required">*</span></label>
            <?php $this->app_components->getSelect2Country(['input_name' => 'country','id' => 'country_id', 'selected' => empty(@$data['country']) ? 'CZ' : @$data['country'], 'required' => true]); ?>
            </div>                                          
    </div>  
    <div class="form-row">
        <div class="form-group focused col-md-6" data-children-count="1">
            <label for="email">Email <span class="required">*</span></label>
            <input class="form-control" type="email" name="email" placeholder="E-mailová adresa" value="<?php echo @$data['email']; ?>" required>
        </div>
        <div class="form-group col-md-6">
            <label for="phone">Telefon <span class="required">*</span></label>
            <input class="form-control" type="text" name="phone" placeholder="Telefonní číslo" value="<?php echo @$data['phone']; ?>" required>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group focused col-md-6" data-children-count="1">
            <label for="email">Facebook link</label>
            <input class="form-control" type="text" name="fb_link" placeholder="Facebook link" value="<?php echo @$data['fb_link']; ?>">
        </div>
        <div class="form-group col-md-6">
            <label for="phone">Instagram link</label>
            <input class="form-control" type="text" name="ig_link" placeholder="Instagram link" value="<?php echo @$data['ig_link']; ?>">
        </div>
    </div>                                                                                 
</form>
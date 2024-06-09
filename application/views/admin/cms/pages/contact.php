<form id="pageForm" data-ajax="<?php echo $saveUrl; ?>">
    <input type="hidden" name="page_contact[id]" value="<?php echo $page_contact['id']; ?>" />
    <input type="hidden" name="page_contact[name]" value="<?php echo $page_contact['name']; ?>" />
    <?php foreach ($page_contact['blocks'] as $b): ?>
        <input type="hidden" name="page_contact[blocks][]" value="<?php echo $b; ?>">
    <?php endforeach; ?>    
    <div class="row">
        <div class="col-md-6">
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="header_title">Hlavička - nadpis <span class="required">*</span></label>
                    <input type="text" class="form-control" name="page_contact[header_title]" value="<?php echo $page_contact['header_title']; ?>" required />
                </div>                                                                                                                                             
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="header_contact_title">Hlavička (kontaktujte nás) - nadpis <span class="required">*</span></label>
                    <input type="text" class="form-control" name="page_contact[header_contact_title]" value="<?php echo $page_contact['header_contact_title']; ?>" required />
                </div>                                                                                                                                             
            </div>       
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="header_location_title">Hlavička (kde nás najdete) - nadpis <span class="required">*</span></label>
                    <input type="text" class="form-control" name="page_contact[header_location_title]" value="<?php echo $page_contact['header_location_title']; ?>" required />
                </div>                                                                                                                                             
            </div>       
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="header_opening_hours_title">Hlavička (otevírací hodiny) - nadpis <span class="required">*</span></label>
                    <input type="text" class="form-control" name="page_contact[header_opening_hours_title]" value="<?php echo $page_contact['header_opening_hours_title']; ?>" required />
                </div>                                                                                                                                             
            </div>                                                     
        </div>
        <div class="col-md-6">
            <div class="form-row js-media-input-container">
                <div class="col-md-12 mb-3 js-media-open-modal-btn">
                    <label for="header_image">Hlavička - obrázek na pozadí</label>
                    <div class="aspect16_8 image-preview <?php echo strlen($page_contact['header_image'])>0 ? ' uploaded':''; ?>" style="background-image:url('<?php echo $page_contact['header_image']; ?>')" data-placeholder="Klikněte pro otevření galerie."></div>
                    <input type="hidden" name="page_contact[header_image]" value='<?php echo $page_contact['header_image']; ?>' class="js-media-input-target-id">
                </div>
            </div>            
        </div>        
    </div> 
    <h3>Kontaktní informace</h3><hr class="bg-primary text-primary"> 
    <div class="form-row mb-3">
        <div class="col-md-12">
            <label for="find_us_title">Nadpis (Kde nás najdete) <span class="required">*</></label>
            <input type="text" name="page_contact[find_us_title]" class="form-control" placeholder="Nadpis sekce" value="<?php echo $page_contact['find_us_title']; ?>" required />
        </div>
    </div>
    <div class="form-row mb-3">
        <div class="col-md-12">
            <label for="find_us_text">Text (Kde nás najdete) <span class="required">*</span></label>
            <textarea name="page_contact[find_us_text]" id="text" class="form-control js-trumbowyg-editor" placeholder="Obsah .." required><?php echo $page_contact['find_us_text']; ?></textarea>
        </div>
    </div>
    <div class="form-row mb-3">        
        <div class="col-md-6">
            <label for="find_us_bus">Cesta autobusem <span class="required">*</></label>
            <input type="text" name="page_contact[find_us_bus]" class="form-control" placeholder="Cesta autobusem" value="<?php echo $page_contact['find_us_bus']; ?>" required />
        </div> 
        <div class="col-md-6">
            <label for="find_us_bus_walk">Cesta pěšky <span class="required">*</></label>
            <input type="text" name="page_contact[find_us_bus_walk]" class="form-control" placeholder="Cesta pěšky po vystoupení z autobusu" value="<?php echo $page_contact['find_us_bus_walk']; ?>" required />
        </div>
    </div>
    <div class="form-row mb-3">        
        <div class="col-md-6">
            <label for="find_us_car">Cesta autem <span class="required">*</></label>
            <input type="text" name="page_contact[find_us_car]" class="form-control" placeholder="Cesta autem" value="<?php echo $page_contact['find_us_car']; ?>" required />
        </div> 
        <div class="col-md-6">
            <label for="find_us_car_walk">Cesta pěšky <span class="required">*</></label>
            <input type="text" name="page_contact[find_us_car_walk]" class="form-control" placeholder="Cesta pěšky po vystoupení z auta" value="<?php echo $page_contact['find_us_car_walk']; ?>" required />
        </div>
    </div>    
    <div class="form-row mb-3">           
        <div class="col-md-12">
            <label for="operator_title">Nadpis (Provozovatel) <span class="required">*</></label>
            <input type="text" name="page_contact[operator_title]" class="form-control" placeholder="Nadpis sekce" value="<?php echo $page_contact['operator_title']; ?>" required />
        </div>
    </div>
    <div class="form-row mb-3">
        <div class="col-md-12">
            <label for="operator_text">Text (Provozovatel) <span class="required">*</span></label>
            <textarea name="page_contact[operator_text]" id="text" class="form-control js-trumbowyg-editor" placeholder="Obsah .." required><?php echo $page_contact['operator_text']; ?></textarea>
        </div>                       
    </div>
    <h3>Kontaktní formulář</h3><hr class="bg-primary text-primary"> 
    <div class="form-row mb-3">
        <div class="col-md-6">
            <label for="text">Nadpis sekce <span class="required">*</span></label>
            <input type="text" name="page_contact[contact_form_title]" class="form-control" placeholder="Nadpis sekce" value="<?php echo $page_contact['contact_form_title']; ?>" required />
        </div>
        <div class="col-md-6">
            <label for="text">Podnadpis sekce <span class="required">*</span></label>
            <input type="text" name="page_contact[contact_form_subtitle]" class="form-control" placeholder="Podnadpis sekce" value="<?php echo $page_contact['contact_form_subtitle']; ?>" required />
        </div>
    </div> 
    <h3>Newsletter</h3><hr class="bg-primary text-primary">      
    <?php $this->load->view('admin/cms/blocks/newsletter', $block_newsletter); ?>         
</form>
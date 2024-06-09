<form id="gymServiceForm" data-ajax="<?php echo $saveUrl; ?>">
    <input type="hidden" name="id" value="<?php echo @$service['id']; ?>" />
    <div class="form-row mb-3">
        <div class="col-md-6">
            <label for="name">Název <span class="required">*</span></label>
            <input type="text" name="name" class="form-control" id="name" placeholder="Název (pro administraci)" value="<?php echo @$service['name']; ?>" required>
        </div>                                        
    </div>
    <div class="form-row mb-3">
        <div class="col-md-12">
            <label for="perex">Perex (<small>max 40 slov</small>) <span class="required">*</span></label>
            <input type="text" name="perex" class="form-control" id="perex" placeholder="Perex" value="<?php echo @$service['perex']; ?>" required>
        </div>
    </div>    
    <div class="form-row mb-3">
        <div class="col-md-12">
            <label for="text">Text <span class="required">*</span></label>
            <textarea name="text" id="text" class="form-control js-trumbowyg-editor" placeholder="Obsah příspěvku .." required><?php echo @$service['text']; ?></textarea>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-3 js-media-input-container mb-3">
            <label for="icon_image">Náhledová ikona</label>
            <select class="select2 form-control" name="icon_image" id="icon_image" style="height:50px;">
                <option value="ico_services_01" <?php if (@$service['icon_image']=='ico_services_01') echo 'selected'; ?> data-thumbnail="<?php echo base_url(config_item('app')['img_folder'].'svg/ico_services_01.svg'); ?>">Fitness</option>
                <option value="ico_services_02" <?php if (@$service['icon_image']=='ico_services_02') echo 'selected'; ?> data-thumbnail="<?php echo base_url(config_item('app')['img_folder'].'svg/ico_services_02.svg'); ?>">Trainer</option>
                <option value="ico_services_03" <?php if (@$service['icon_image']=='ico_services_03') echo 'selected'; ?> data-thumbnail="<?php echo base_url(config_item('app')['img_folder'].'svg/ico_services_03.svg'); ?>">Lectures</option>
                <option value="ico_services_04" <?php if (@$service['icon_image']=='ico_services_04') echo 'selected'; ?> data-thumbnail="<?php echo base_url(config_item('app')['img_folder'].'svg/ico_services_04.svg'); ?>">Wellness</option>                                                
            </select>
            <div class="js-media-open-modal-btn mt-2">
                <label for="photo">Cover Obrázek <span class="required">*</span></label>
                <div class="aspect6_4 image-preview<?php echo strlen(@$service['cover_image'])>0 ? ' uploaded':''; ?>" style="<?php echo strlen(@$service['cover_image'])>0 ? "background-image:url('".$this->app->getMedia($service['cover_img_src'],$service['cover_img_meta'],true)['src']."')" : ''; ?>" data-placeholder="Klikněte pro otevření galerie."></div>
                <input type="hidden" name="cover_image" value=='<?php echo @$service['cover_image']; ?>' class="js-media-input-target-id">
            </div>            
        </div>   
        <div class="col-md-9 js-media-input-container mb-2">
            <div class="js-media-open-modal-btn">
                <label for="photo">Header Obrázek <span class="required">*</span></label>
                <div class="aspect16_8 image-preview<?php echo strlen(@$service['header_image'])>0 ? ' uploaded':''; ?>" style="<?php echo strlen(@$service['header_image'])>0 ? "background-image:url('".$this->app->getMedia($service['header_img_src'],$service['header_img_meta'],true)['src']."')" : ''; ?>" data-placeholder="Klikněte pro otevření galerie."></div>
                <input type="hidden" name="header_image" value='<?php echo @$service['header_image']; ?>' class="js-media-input-target-id">
            </div>  
        </div>        
    </div>                                                                            
    <div class="form-row mb-3">
        <div class="col-md-6">
            <label for="state">Status <span class="required">*</span></label>
            <select name="state" id="state" class="form-control" required>
                <option value="1" <?php echo (@$service['state'] == 1) ? 'selected' : ''; ?>>Aktivní</option>
                <option value="2" <?php echo (@$service['state'] == 2) ? 'selected' : ''; ?>>Neaktivní</option>
            </select>
        </div>
    </div> 
</form>
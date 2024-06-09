<form id="pageForm" data-ajax="<?php echo $saveUrl; ?>">
    <input type="hidden" name="page_coaches[id]" value="<?php echo $page_coaches['id']; ?>" />
    <input type="hidden" name="page_coaches[name]" value="<?php echo $page_coaches['name']; ?>" />
    <?php foreach ($page_coaches['blocks'] as $b): ?>
        <input type="hidden" name="page_coaches[blocks][]" value="<?php echo $b; ?>">
    <?php endforeach; ?>    
    <div class="form-row mb-3">
        <div class="col-md-12">
            <label for="header_title">Hlavička - nadpis <span class="required">*</span></label>
            <input type="text" name="page_coaches[header_title]" class="form-control" id="name" placeholder="Název" value="<?php echo $page_coaches['header_title']; ?>" required>
        </div>                                        
    </div>
    <div class="form-row mb-3">
        <div class="col-md-6">
            <label for="text">Nadpis sekce <span class="required">*</span></label>
            <input type="text" name="page_coaches[coaches_title]" class="form-control" placeholder="Nadpis sekce" value="<?php echo $page_coaches['coaches_title']; ?>" required />
        </div>
        <div class="col-md-6">
            <label for="text">Podnadpis sekce <span class="required">*</span></label>
            <input type="text" name="page_coaches[coaches_subtitle]" class="form-control" placeholder="Podnadpis sekce" value="<?php echo $page_coaches['coaches_subtitle']; ?>" required />
        </div>
    </div>       
    <div class="form-row mb-3">
        <div class="col-md-12">
            <label for="perex">Perex (<small>max 40 slov</small>) <span class="required">*</span></label>
            <input type="text" name="page_coaches[perex]" class="form-control" id="perex" placeholder="Perex" value="<?php echo $page_coaches['perex']; ?>" required>
        </div>
    </div>   
    <!--<div class="form-row">
        <div class="form-group col-md-6">
            <label for="header_subtitle">Hlavička - text tlačítka <span class="required">*</span></label>
            <input type="text" class="form-control" name="page_coaches[header_btn_text]" value="<?php echo $page_coaches['header_btn_text']; ?>" required />
        </div>
        <div class="form-group col-md-6">
            <label for="header_subtitle">Hlavička - URL tlačítka <span class="required">*</span></label>
            <input type="text" class="form-control" name="page_coaches[header_btn_url]" value="<?php echo $page_coaches['header_btn_url']; ?>" required />
        </div>                                                                                                                  
    </div>-->           
    <div class="form-row row">
        <div class="col-md-3 js-media-input-container mb-3">
            <label for="icon_image">Náhledová ikona</label>        
            <button type="button" class="btn btn-icon shadow-none dropdown-toggle d-block" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="<?php echo base_url(config_item('app')['img_folder']."svg/ico_services_0".$page_coaches['icon_image'].".svg"); ?>" /></button>
            <ul class="dropdown-menu">
                <li data-id="1"><img src="<?php echo base_url(config_item('app')['img_folder'].'svg/ico_services_01.svg'); ?>"></li>
                <li data-id="2"><img src="<?php echo base_url(config_item('app')['img_folder'].'svg/ico_services_02.svg'); ?>"></li>
                <li data-id="3"><img src="<?php echo base_url(config_item('app')['img_folder'].'svg/ico_services_03.svg'); ?>"></li>
                <li data-id="4"><img src="<?php echo base_url(config_item('app')['img_folder'].'svg/ico_services_04.svg'); ?>"></li>
            </ul>
            <input name="page_coaches[icon_image]" value="<?php echo $page_coaches['icon_image']; ?>" type="hidden" class="selectedDropDownItem" />
            <div class="js-media-open-modal-btn mt-4">
                <label for="photo" style="margin-top:.45rem !important;">Cover Obrázek <span class="required">*</span></label>
                <div class="aspect6_4 image-preview<?php echo strlen($page_coaches['cover_image'])>0 ? ' uploaded':''; ?>" style="<?php echo strlen($page_coaches['cover_image'])>0 ? "background-image:url('".$page_coaches['cover_image']."')" : ''; ?>" data-placeholder="Klikněte pro otevření galerie."></div>
                <input type="hidden" name="page_coaches[cover_image]" value='<?php echo $page_coaches['cover_image']; ?>' class="js-media-input-target-id">
            </div>            
        </div>   
        <div class="col-md-9 js-media-input-container mb-2">
            <div class="js-media-open-modal-btn">
                <label for="photo">Header Obrázek <span class="required">*</span></label>
                <div class="aspect16_8 image-preview<?php echo strlen($page_coaches['header_image'])>0 ? ' uploaded':''; ?>" style="<?php echo strlen($page_coaches['header_image'])>0 ? "background-image:url('".$page_coaches['header_image']."')" : ''; ?>" data-placeholder="Klikněte pro otevření galerie."></div>
                <input type="hidden" name="page_coaches[header_image]" value='<?php echo $page_coaches['header_image']; ?>' class="js-media-input-target-id">
            </div>  
        </div>        
    </div>   
    <h3>Newsletter</h3><hr class="bg-primary text-primary">      
    <?php $this->load->view('admin/cms/blocks/newsletter', $block_newsletter); ?>                            
</form>  
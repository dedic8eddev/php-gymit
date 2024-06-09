<form id="pageForm" data-ajax="<?php echo $saveUrl; ?>">
    <input type="hidden" name="page_calendar[id]" value="<?php echo $page_calendar['id']; ?>" />
    <input type="hidden" name="page_calendar[name]" value="<?php echo $page_calendar['name']; ?>" />
    <?php foreach ($page_calendar['blocks'] as $b): ?>
        <input type="hidden" name="page_calendar[blocks][]" value="<?php echo $b; ?>">
    <?php endforeach; ?>
    <div class="row">
        <div class="col-md-6">
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="header_title">Hlavička - nadpis <span class="required">*</span></label>
                    <input type="text" class="form-control" name="page_calendar[header_title]" value="<?php echo $page_calendar['header_title']; ?>" required />
                </div>                                                                                                                                             
            </div>                                                    
        </div>
        <div class="col-md-6">
            <div class="form-row js-media-input-container">
                <div class="col-md-12 mb-3 js-media-open-modal-btn">
                    <label for="header_image">Hlavička - obrázek na pozadí</label>
                    <div class="aspect16_8 image-preview <?php echo strlen($page_calendar['header_image'])>0 ? ' uploaded':''; ?>" style="background-image:url('<?php echo $page_calendar['header_image']; ?>')" data-placeholder="Klikněte pro otevření galerie."></div>
                    <input type="hidden" name="page_calendar[header_image]" value='<?php echo $page_calendar['header_image']; ?>' class="js-media-input-target-id">
                </div>
            </div>            
        </div>        
    </div> 
    <h3>Newsletter</h3><hr class="bg-primary text-primary">      
    <?php $this->load->view('admin/cms/blocks/newsletter', $block_newsletter); ?>        
</form>
<form id="pageForm" data-ajax="<?php echo $saveUrl; ?>">
    <input type="hidden" name="page_pricelist[id]" value="<?php echo $page_pricelist['id']; ?>" />
    <input type="hidden" name="page_pricelist[name]" value="<?php echo $page_pricelist['name']; ?>" />
    <?php foreach ($page_pricelist['blocks'] as $b): ?>
        <input type="hidden" name="page_pricelist[blocks][]" value="<?php echo $b; ?>">
    <?php endforeach; ?>
    <div class="row">
        <div class="col-md-6">
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="header_title">Hlavička - nadpis <span class="required">*</span></label>
                    <input type="text" class="form-control" name="page_pricelist[header_title]" value="<?php echo $page_pricelist['header_title']; ?>" required />
                </div>                                                                                                                                             
            </div> 
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="header_title">Sekce členství - nadpis <span class="required">*</span></label>
                    <input type="text" class="form-control" name="page_pricelist[membership_title]" value="<?php echo $page_pricelist['membership_title']; ?>" required />
                </div>   
                <div class="form-group col-md-6">
                    <label for="header_title">Sekce ceník - nadpis <span class="required">*</span></label>
                    <input type="text" class="form-control" name="page_pricelist[pricelist_title]" value="<?php echo $page_pricelist['pricelist_title']; ?>" required />
                </div>                                                                                                                                                            
            </div>                      
        </div>
        <div class="col-md-6">
            <div class="form-row js-media-input-container">
                <div class="col-md-12 mb-3 js-media-open-modal-btn">
                    <label for="header_img">Hlavička - obrázek na pozadí</label>
                    <div class="aspect16_8 image-preview<?php echo strlen($page_pricelist['header_image'])>0 ? ' uploaded':''; ?>" style="background-image:url('<?php echo $page_pricelist['header_image']; ?>')" data-placeholder="Klikněte pro otevření galerie."></div>
                    <input type="hidden" name="page_pricelist[header_image]" value='<?php echo $page_pricelist['header_image']; ?>' class="js-media-input-target-id">
                </div>
            </div>           
        </div>
    </div>   
    <h3>Členství</h3><hr class="bg-primary text-primary">      
    <?php $this->load->view('admin/cms/blocks/membership', $block_membership); ?>  
    <h3>Ceník</h3><hr class="bg-primary text-primary">  
    <?php $this->load->view('admin/cms/blocks/pricelist', $block_pricelist); ?>  
    <h3>Newsletter</h3><hr class="bg-primary text-primary">      
    <?php $this->load->view('admin/cms/blocks/newsletter', $block_newsletter); ?>                
</form>
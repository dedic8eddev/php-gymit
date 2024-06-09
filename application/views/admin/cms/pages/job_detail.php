<form id="pageForm" data-ajax="<?php echo $saveUrl; ?>">
    <input type="hidden" name="page_job_detail[id]" value="<?php echo $page_job_detail['id']; ?>" />
    <input type="hidden" name="page_job_detail[name]" value="<?php echo $page_job_detail['name']; ?>" />
    <div class="row">
        <div class="col-md-6">
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="hire_title">Text (Přidej se k nám) <span class="required">*</span></label>
                    <input type="text" class="form-control" name="page_job_detail[hire_title]" value="<?php echo $page_job_detail['hire_title']; ?>" required />
                </div>                                                                                                                                             
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="hire_email">E-mail (Přidej se k nám) <span class="required">*</span></label>
                    <input type="email" class="form-control" name="page_job_detail[hire_email]" value="<?php echo $page_job_detail['hire_email']; ?>" required />
                </div>                                                                                                                                             
            </div>                   
        </div>
        <div class="col-md-6">
            <div class="form-row js-media-input-container">
                <div class="col-md-12 mb-3 js-media-open-modal-btn">
                    <label for="header_image">Hlavička - obrázek na pozadí</label>
                    <div class="aspect16_8 image-preview <?php echo strlen($page_job_detail['header_image'])>0 ? ' uploaded':''; ?>" style="background-image:url('<?php echo $page_job_detail['header_image']; ?>')" data-placeholder="Klikněte pro otevření galerie."></div>
                    <input type="hidden" name="page_job_detail[header_image]" value='<?php echo $page_job_detail['header_image']; ?>' class="js-media-input-target-id">
                </div>
            </div>            
        </div>  
    </div>   

</form>
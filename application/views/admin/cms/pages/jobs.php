<form id="pageForm" data-ajax="<?php echo $saveUrl; ?>">
    <input type="hidden" name="page_jobs[id]" value="<?php echo $page_jobs['id']; ?>" />
    <input type="hidden" name="page_jobs[name]" value="<?php echo $page_jobs['name']; ?>" />
    <div class="row">
        <div class="col-md-6">
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="header_title">Hlavička - nadpis <span class="required">*</span></label>
                    <input type="text" class="form-control" name="page_jobs[header_title]" value="<?php echo $page_jobs['header_title']; ?>" required />
                </div>                                                                                                                                             
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="jobs_title">Sekce (Přidej se k nám) - nadpis <span class="required">*</span></label>
                    <input type="text" class="form-control" name="page_jobs[jobs_title]" value="<?php echo $page_jobs['jobs_title']; ?>" required />
                </div>                                                                                                                                             
            </div>       
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="jobs_subtitle">Sekce (Přidej se k nám) - podnadpis <span class="required">*</span></label>
                    <input type="text" class="form-control" name="page_jobs[jobs_subtitle]" value="<?php echo $page_jobs['jobs_subtitle']; ?>" required />
                </div>                                                                                                                                             
            </div>              
        </div>
        <div class="col-md-6">
            <div class="form-row js-media-input-container">
                <div class="col-md-12 mb-3 js-media-open-modal-btn">
                    <label for="header_image">Hlavička - obrázek na pozadí</label>
                    <div class="aspect16_8 image-preview <?php echo strlen($page_jobs['header_image'])>0 ? ' uploaded':''; ?>" style="background-image:url('<?php echo $page_jobs['header_image']; ?>')" data-placeholder="Klikněte pro otevření galerie."></div>
                    <input type="hidden" name="page_jobs[header_image]" value='<?php echo $page_jobs['header_image']; ?>' class="js-media-input-target-id">
                </div>
            </div>            
        </div>  
    </div>  
    <h3>Další nabídky práce</h3><hr class="bg-primary text-primary">  
    <div class="row">
        <div class="col-md-6">
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="another_job_title">Nadpi sekce <span class="required">*</span></label>
                    <input type="text" class="form-control" name="page_jobs[another_job_title]" value="<?php echo $page_jobs['another_job_title']; ?>" required />
                </div>                                                                                                                                             
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="another_job_subtitle">Podnadpis sekce <span class="required">*</span></label>
                    <input type="text" class="form-control" name="page_jobs[another_job_subtitle]" value="<?php echo $page_jobs['another_job_subtitle']; ?>" required />
                </div>                                                                                                                                             
            </div>       
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="another_job_email">HR email <span class="required">*</span></label>
                    <input type="email" class="form-control" name="page_jobs[another_job_email]" value="<?php echo $page_jobs['another_job_email']; ?>" required />
                </div>                                                                                                                                             
            </div>  
        </div>
        <div class="col-md-6">
            <div class="form-row mb-3">
                <div class="col-md-12">
                    <label for="another_job_text">Text <span class="required">*</span></label>
                    <textarea name="page_jobs[another_job_text]" id="text" class="form-control js-trumbowyg-editor" placeholder="Obsah .." required><?php echo $page_jobs['another_job_text']; ?></textarea>
                </div>                       
            </div>
        </div>
    </div>     

</form>
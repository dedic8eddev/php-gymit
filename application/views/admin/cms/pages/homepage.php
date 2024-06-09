<form id="pageForm" data-ajax="<?php echo $saveUrl; ?>">
    <input type="hidden" name="page_homepage[id]" value="<?php echo $page_homepage['id']; ?>" />
    <input type="hidden" name="page_homepage[name]" value="<?php echo $page_homepage['name']; ?>" />
    <?php foreach ($page_homepage['blocks'] as $b): ?>
        <input type="hidden" name="page_homepage[blocks][]" value="<?php echo $b; ?>">
    <?php endforeach; ?>
    <div class="row">
        <div class="col-md-6">
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="header_title">Hlavička - nadpis <span class="required">*</span></label>
                    <input type="text" class="form-control" name="page_homepage[header_title]" value="<?php echo $page_homepage['header_title']; ?>" required />
                </div>                                                                                                                                             
            </div> 
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="header_subtitle">Hlavička - podnadpis <span class="required">*</span></label>
                    <input type="text" class="form-control" name="page_homepage[header_subtitle]" value="<?php echo $page_homepage['header_subtitle']; ?>" required />
                </div>                                                                                                        
            </div>       
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="header_subtitle">Hlavička - text tlačítka <span class="required">*</span></label>
                    <input type="text" class="form-control" name="page_homepage[header_btn_text]" value="<?php echo $page_homepage['header_btn_text']; ?>" required />
                </div>
                <div class="form-group col-md-6">
                    <label for="header_subtitle">Hlavička - URL tlačítka <span class="required">*</span></label>
                    <input type="text" class="form-control" name="page_homepage[header_btn_url]" value="<?php echo $page_homepage['header_btn_url']; ?>" required />
                </div>                                                                                                                  
            </div>         
        </div>
        <div class="col-md-6">
            <div class="form-row js-media-input-container">
                <div class="col-md-12 mb-3 js-media-open-modal-btn">
                    <label for="header_img">Hlavička - obrázek na pozadí</label>
                    <div class="aspect16_8 image-preview <?php echo strlen($page_homepage['header_img'])>0 ? ' uploaded':''; ?>" style="<?php echo strlen($page_homepage['header_img'])>0 ? "background-image:url('".$page_homepage['header_img']."')" : ''; ?>" data-placeholder="Klikněte pro otevření galerie."></div>
                    <input type="hidden" name="page_homepage[header_img]" value='<?php echo $page_homepage['header_img']; ?>' class="js-media-input-target-id">
                </div>
            </div>            
        </div>        
    </div> 
    <h3>Skupinové lekce</h3><hr class="bg-primary text-primary"> 
    <div class="form-row mb-3">
        <div class="col-md-6">
            <label for="text">Nadpis sekce <span class="required">*</span></label>
            <input type="text" name="page_homepage[lessons_title]" class="form-control" placeholder="Nadpis sekce" value="<?php echo $page_homepage['lessons_title']; ?>" required />
        </div>
        <div class="col-md-6">
            <label for="text">Podnadpis sekce <span class="required">*</span></label>
            <input type="text" name="page_homepage[lessons_subtitle]" class="form-control" placeholder="Podnadpis sekce" value="<?php echo $page_homepage['lessons_subtitle']; ?>" required />
        </div>
    </div>
    <div class="form-row mb-3">
        <div class="col-md-6">
            <label for="text">Text tlačítka (přechod do kalendáře) <span class="required">*</span></label>
            <input type="text" name="page_homepage[lessons_btn_text]" class="form-control" placeholder="Text tlačítka" value="<?php echo $page_homepage['lessons_btn_text']; ?>" required />
        </div>
    </div>    
    <h3>První vstup zdarma</h3><hr class="bg-primary text-primary">
    <?php $this->load->view('admin/cms/blocks/free_entry', $block_free_entry); ?>           
    <h3>Trenéři</h3><hr class="bg-primary text-primary"> 
    <div class="form-row mb-3">
        <div class="col-md-6">
            <label for="text">Nadpis sekce <span class="required">*</span></label>
            <input type="text" name="page_homepage[coaches_title]" class="form-control" placeholder="Nadpis sekce" value="<?php echo $page_homepage['coaches_title']; ?>" required />
        </div>
        <div class="col-md-6">
            <label for="text">Podnadpis sekce <span class="required">*</span></label>
            <input type="text" name="page_homepage[coaches_subtitle]" class="form-control" placeholder="Podnadpis sekce" value="<?php echo $page_homepage['coaches_subtitle']; ?>" required />
        </div>
    </div>      
    <h3>Členství</h3><hr class="bg-primary text-primary">      
    <?php $this->load->view('admin/cms/blocks/membership', $block_membership); ?>
    <h3>Služby</h3><hr class="bg-primary text-primary"> 
    <div class="form-row mb-3">
        <div class="col-md-6">
            <label for="text">Nadpis sekce <span class="required">*</span></label>
            <input type="text" name="page_homepage[services_title]" class="form-control" placeholder="Nadpis sekce" value="<?php echo $page_homepage['services_title']; ?>" required />
        </div>
        <div class="col-md-6">
            <label for="text">Podnadpis sekce <span class="required">*</span></label>
            <input type="text" name="page_homepage[services_subtitle]" class="form-control" placeholder="Podnadpis sekce" value="<?php echo $page_homepage['services_subtitle']; ?>" required />
        </div>
    </div>      
    <h3>Aktuality</h3><hr class="bg-primary text-primary"> 
    <div class="form-row mb-3">
        <div class="col-md-6">
            <label for="text">Nadpis sekce <span class="required">*</span></label>
            <input type="text" name="page_homepage[news_title]" class="form-control" placeholder="Nadpis sekce" value="<?php echo $page_homepage['news_title']; ?>" required />
        </div>
        <div class="col-md-6">
            <label for="text">Podnadpis sekce <span class="required">*</span></label>
            <input type="text" name="page_homepage[news_subtitle]" class="form-control" placeholder="Podnadpis sekce" value="<?php echo $page_homepage['news_subtitle']; ?>" required />
        </div>
    </div>
    <h3>Newsletter</h3><hr class="bg-primary text-primary">      
    <?php $this->load->view('admin/cms/blocks/newsletter', $block_newsletter); ?>            

</form>  
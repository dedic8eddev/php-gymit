<form id="gymJobForm" data-ajax="<?php echo $saveUrl; ?>">
    <input type="hidden" name="id" value="<?php echo @$job['id']; ?>" />
    <div class="form-row mb-3">   
        <div class="col-md-12">
            <label for="name">Název <span class="required">*</span></label>
            <input type="text" name="name" class="form-control" id="name" placeholder="Název (pro administraci)" value="<?php echo @$job['name']; ?>" required>
        </div>                                        
    </div>
    <div class="form-row mb-3">
        <div class="col-md-2">
            <div class="row">
                <div class="col-md-12">
                    <label for="icon_image">Náhledová ikona</label>        
                    <button type="button" class="btn btn-icon shadow-none dropdown-toggle d-block" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="<?php echo base_url(config_item('app')['img_folder']."svg/ico_job_01.svg"); ?>" /></button>
                    <ul class="dropdown-menu">
                        <li data-id="1"><img src="<?php echo base_url(config_item('app')['img_folder'].'svg/ico_job_01.svg'); ?>"></li>
                        <li data-id="2"><img src="<?php echo base_url(config_item('app')['img_folder'].'svg/ico_job_02.svg'); ?>"></li>
                        <li data-id="3"><img src="<?php echo base_url(config_item('app')['img_folder'].'svg/ico_job_03.svg'); ?>"></li>
                    </ul>
                    <input name="icon_image" value="<?php echo @$job['icon_image']; ?>" type="hidden" class="selectedDropDownItem" />    
                </div>                  
            </div>
        </div>
        <div class="col-md-10">
            <div class="row">
                <div class="col-md-12">
                    <label for="title">Titulek <span class="required">*</span></label>
                    <input type="text" name="title" class="form-control" id="title" placeholder="Titulek" value="<?php echo @$job['title']; ?>" required>
                </div>                  
                <div class="col-md-12">
                    <label for="perex">Perex (<small>max 40 slov</small>) <span class="required">*</span></label>
                    <input type="text" name="perex" class="form-control" id="perex" placeholder="Perex" value="<?php echo @$job['perex']; ?>" required>
                </div>                  
            </div>          
        </div> 
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-row mb-3">
                <div class="col-md-12">
                    <label for="requirement_education">Vzdělání</label>
                    <a class="ml-1" href="javascript:;" data-toggle="modal" data-remote="/admin/cms/gym_jobs_requirements/education" data-target="#requirementModal" data-modal-title="Správa požadavků na vzdělání" data-modal-submit="Uložit" title="Správa požadavků na vzdělání"><i class="icon-pencil"></i></a>
                    <select id="requirement_education" class="select2" name="requirements[]" multiple>
                        <?php foreach($requirements['education'] as $k => $v): ?>
                            <option value="<?php echo $k; ?>" <?php echo in_array($k,$requirements) ? 'selected' : ''; ?>><?php echo $v; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-12">
                    <label for="requirement_practice">Praxe</label>
                    <a class="ml-1" href="javascript:;" data-toggle="modal" data-remote="/admin/cms/gym_jobs_requirements/practice" data-target="#requirementModal" data-modal-title="Správa požadavků na praxi" data-modal-submit="Uložit" title="Správa požadavků na praxi"><i class="icon-pencil"></i></a>
                    <select id="requirement_practice" class="select2" name="requirements[]" multiple>
                        <?php foreach($requirements['practice'] as $k => $v): ?>
                            <option value="<?php echo $k; ?>" <?php echo in_array($k,$requirements) ? 'selected' : ''; ?>><?php echo $v; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-12">
                    <label for="requirement_obligation">Úvazek</label>
                    <a class="ml-1" href="javascript:;" data-toggle="modal" data-remote="/admin/cms/gym_jobs_requirements/obligation" data-target="#requirementModal" data-modal-title="Správa požadavků na úvazek" data-modal-submit="Uložit" title="Správa požadavků na úvazek"><i class="icon-pencil"></i></a>
                    <select id="requirement_obligation" class="select2" name="requirements[]" multiple>
                        <?php foreach($requirements['obligation'] as $k => $v): ?>
                            <option value="<?php echo $k; ?>" <?php echo in_array($k,$requirements) ? 'selected' : ''; ?>><?php echo $v; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>  
                <div class="col-md-12">
                    <label for="requirement_income">Plat</label>
                    <a class="ml-1" href="javascript:;" data-toggle="modal" data-remote="/admin/cms/gym_jobs_requirements/income" data-target="#requirementModal" data-modal-title="Správa platů" data-modal-submit="Uložit" title="Správa platů"><i class="icon-pencil"></i></a>
                    <select id="requirement_income" class="select2" name="requirements[]" multiple>
                        <?php foreach($requirements['income'] as $k => $v): ?>
                            <option value="<?php echo $k; ?>" <?php echo in_array($k,$requirements) ? 'selected' : ''; ?>><?php echo $v; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>                                   
            </div>                  
        </div>
        <div class="col-md-9">
            <div class="form-row mb-3">
                <div class="col-md-12">
                    <label for="text">Text <span class="required">*</span></label>
                    <textarea name="text" id="text" class="form-control js-trumbowyg-editor" placeholder="Obsah pozice .." required><?php echo @$job['text']; ?></textarea>
                </div>
            </div>
        </div>            
    </div>                                                                       
    <div class="form-row mb-3">
        <div class="col-md-6">
            <label for="state">Status <span class="required">*</span></label>
            <select name="state" id="state" class="form-control" required>
                <option value="1" <?php echo (@$job['state'] == 1) ? 'selected' : ''; ?>>Aktivní</option>
                <option value="2" <?php echo (@$job['state'] == 2) ? 'selected' : ''; ?>>Neaktivní</option>
            </select>
        </div>
    </div> 
</form>
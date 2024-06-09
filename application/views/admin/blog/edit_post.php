<div class="page has-sidebar-left has-sidebar-tabs height-full">
    <div class="container-fluid animatedParent animateOnce">
        <div class="row my-3">
            <div class="col-md-12">
                <div class="card r-0 shadow">
                    <div class="card-header white">
                        <h6 class="pull-left">Úprava položky</h6>
                    </div>
                    <div class="card-body b-b">
                        <form id="savePostForm" data-ajax="<?php echo $saveUrl; ?>">
                            <input type="hidden" name="id" value="<?php echo $post['id']; ?>" />
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label for="name">Název <small>(pro administraci)*</small></label>
                                    <input type="text" name="name" class="form-control" id="name" placeholder="Název (pro administraci)" value="<?php echo $post['name']; ?>" required>
                                </div>
                                <div class="form-group col-md-6 mb-3">
                                    <label for="gym_id">Gym</label>
                                    <?php $this->app_components->getSelect2Gyms(['input_name' => 'gym_id[]','id' => 'js_select2_gyms','selected' => $post['gyms'], 'required' => true, 'multiple'=>true]); ?>
                                </div>                                           
                            </div>
                            <div class="form-row">
                                <div class="col-md-3 mb-3">
                                    <label for="publish_date_from">Publikovat od  <span class="required">*</span></label>
                                    <div class="input-group">
                                        <input type="text" name="publish_date_from" class="form-control js-flatpickr-date" id="publish_date_from" placeholder="Publikovat od" value="<?php echo $post['publish_from']; ?>" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="icon icon-calendar"></i></span>
                                        </div> 
                                    </div>                                               
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="publish_time_from">&nbsp;</label>
                                    <div class="input-group">
                                        <input type="text" name="publish_time_from" class="form-control js-flatpickr-time" id="publish_time_from" value="<?php echo date('H:i', strtotime($post['publish_from'])); ?>" placeholder="Čas" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="icon icon-clock-o"></i></span>
                                        </div>                                             
                                    </div>                                            
                                </div>                                           
                                <div class="col-md-3 mb-3">
                                    <label for="publish_date_to">Publikovat do</label>
                                    <div class="input-group">
                                        <input type="text" name="publish_date_to" class="form-control js-flatpickr-date" id="publish_date_to" value="<?php echo $post['publish_to']; ?>" placeholder="Publikovat do">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="icon icon-calendar"></i></span>
                                        </div> 
                                    </div>                                               
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="publish_time_to">&nbsp;</label>
                                    <div class="input-group">
                                        <input type="text" name="publish_time_to" class="form-control js-flatpickr-time" id="publish_time_to" value="<?php echo date('H:i', strtotime($post['publish_to'])); ?>" placeholder="Čas">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="icon icon-clock-o"></i></span>
                                        </div>                                             
                                    </div>                                            
                                </div>                                           
                            </div>

                            <div class="form-row js-media-input-container mb-3">
                                <div class="col-md-6 js-media-open-modal-btn">
                                    <label for="photo">Obrázek <span class="required">*</span></label>
                                    <div class="aspect16_9 image-preview<?php echo strlen($post['image'])>0 ? ' uploaded':''; ?>" style="<?php echo strlen($post['image'])>0 ? "background-image:url('".$this->app->getMedia($post['photo_src'],$post['photo_meta'],true)['src']."')" : ''; ?>" data-placeholder="Klikněte pro otevření galerie."></div>
                                    <input type="hidden" name="image" value='<?php echo $post['image']; ?>' class="js-media-input-target-id">
                                </div>
                            </div>                              

                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label for="title">Titulek <span class="required">*</span></label>
                                    <input type="text" name="title" class="form-control js-title-input" id="title" placeholder="Titulek" value="<?php echo $post['title']; ?>" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label for="perex">Perex (<small>max 40 slov</small>) <span class="required">*</span></label>
                                    <input type="text" name="perex" class="form-control" id="perex" placeholder="Perex" value="<?php echo $post['perex']; ?>" required>
                                </div>
                            </div>    
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label for="text">Text <span class="required">*</span></label>
                                    <textarea name="text" id="text" class="form-control js-trumbowyg-editor" placeholder="Obsah příspěvku .." required><?php echo $post['text']; ?></textarea>
                                </div>
                            </div>                                                                    
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label for="state">Status <span class="required">*</span></label>
                                    <select name="state" id="state" class="form-control" required>
                                        <option value="1" <?php echo ($post['state'] == 1) ? 'selected' : '' ?>>Aktivní</option>
                                        <option value="2" <?php echo ($post['state'] == 2) ? 'selected' : '' ?>>Neaktivní</option>
                                    </select>
                                </div>
                            </div>
                            <a href="/admin/blog/" class="btn btn-sm btn-secondary px-2">Zpět</a>  
                            <button type="submit" class="btn btn-sm btn-primary">Uložit příspěvek</button>&nbsp;
                        </form>  
                    </div>                          
                </div>
            </div>
        </div>               
    </div>  
</div>                       
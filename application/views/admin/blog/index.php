<div class="page has-sidebar-left has-sidebar-tabs height-full">
    <header class="white relative nav-sticky">
        <div class="container-fluid text-primary">
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pills-tab" role="tablist">
                    <li>
                        <a class="nav-link active" id="v-pills-list-tab" data-toggle="pill" href="#v-pills-list" role="tab" aria-controls="v-pills-list"><i class="icon icon-list-alt"></i>Seznam příspěvků</a>
                    </li>                               
                    <li>
                        <a class="nav-link" id="v-pills-add-tab" data-toggle="pill" href="#v-pills-new" role="tab" aria-controls="v-pills-new"><i class="icon icon-plus-circle"></i>Nový příspěvěk</a>
                    </li>                    
                </ul>
            </div>
        </div>
    </header>
    <div class="container-fluid animatedParent animateOnce">
        <div class="tab-content my-3" id="v-pills-tabContent">

            <div class="tab-pane animated fadeInUpShort show active go" id="v-pills-list" role="tabpanel" aria-labelledby="v-pills-list-tab">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Seznam příspěvků</h6>
                                <button class="btn btn-danger btn-xs float-right" id="js_posts_clear_filter">Zrušit filtr</button>
                            </div>
                            <div class="table-responsive">
                                <table id="postsTable" class="table table-striped table-hover r-0" data-ajax="<?php echo $getAllUrl; ?>"></table>
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>   

            <div class="tab-pane animated fadeInUpShort go" id="v-pills-new" role="tabpanel" aria-labelledby="v-pills-new-tab">
                <div class="row my-3">
                    <div class="col-md-12">
                        <div class="card r-0 shadow">
                            <div class="card-header white">
                                <h6 class="pull-left">Nový příspěvek</h6>
                            </div>
                            <div class="card-body">
                                <form id="addPostForm" data-ajax="<?php echo $addUrl; ?>">
                                    <div class="form-row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name">Název <small>(pro administraci)*</small></label>
                                            <input type="text" name="name" class="form-control" id="name" placeholder="Název (pro administraci)" required>
                                        </div>
                                        <div class="form-group col-md-6 mb-3">
                                            <label for="gym_id">Gym <span class="required">*</span></label>
                                            <?php $this->app_components->getSelect2Gyms(['input_name' => 'gym_id[]','id' => 'js_select2_gyms','required' => true, 'multiple'=>true]); ?>
                                        </div>                                           
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-3 mb-3">
                                            <label for="publish_date_from">Publikovat od  <span class="required">*</span></label>
                                            <div class="input-group">
                                                <input type="text" name="publish_date_from" class="form-control js-flatpickr-date" id="publish_date_from" placeholder="Publikovat od" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="icon icon-calendar"></i></span>
                                                </div> 
                                            </div>                                               
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="publish_time_from">&nbsp;</label>
                                            <div class="input-group">
                                                <input type="text" name="publish_time_from" class="form-control js-flatpickr-time" id="publish_time_from" placeholder="Čas" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="icon icon-clock-o"></i></span>
                                                </div>                                             
                                            </div>                                            
                                        </div>                                           
                                        <div class="col-md-3 mb-3">
                                            <label for="publish_date_to">Publikovat do</label>
                                            <div class="input-group">
                                                <input type="text" name="publish_date_to" class="form-control js-flatpickr-date" id="publish_date_to" placeholder="Publikovat do">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="icon icon-calendar"></i></span>
                                                </div> 
                                            </div>                                               
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="publish_time_to">&nbsp;</label>
                                            <div class="input-group">
                                                <input type="text" name="publish_time_to" class="form-control js-flatpickr-time" id="publish_time_to" placeholder="Čas">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="icon icon-clock-o"></i></span>
                                                </div>                                             
                                            </div>                                            
                                        </div>                                           
                                    </div>

                                    <div class="form-row js-media-input-container mb-3">
                                        <div class="col-md-6 js-media-open-modal-btn">
                                            <label for="photo">Obrázek <span class="required">*</span></label>
                                            <div class="aspect16_9 image-preview" data-placeholder="Klikněte pro otevření galerie."></div>
                                            <input type="hidden" id="image" name="image" value='' class="js-media-input-target-id">
                                        </div>
                                    </div>                                     

                                    <div class="form-row">
                                        <div class="col-md-12 mb-3">
                                            <label for="title">Titulek <span class="required">*</span></label>
                                            <input type="text" name="title" class="form-control js-title-input" id="title" placeholder="Titulek" required>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-12 mb-3">
                                            <label for="perex">Perex (<small>max 40 slov</small>) <span class="required">*</span></label>
                                            <input type="text" name="perex" class="form-control" id="perex" placeholder="Perex" required>
                                        </div>
                                    </div>    
                                    <div class="form-row">
                                        <div class="col-md-12 mb-3">
                                            <label for="text">Text <span class="required">*</span></label>
                                            <textarea name="text" id="text" class="form-control js-trumbowyg-editor" placeholder="Obsah příspěvku .." required></textarea>
                                        </div>
                                    </div>                                                                    
                                    <div class="form-row">
                                        <div class="col-md-6 mb-3">
                                            <label for="state">Status <span class="required">*</span></label>
                                            <select name="state" id="state" class="form-control" required>
                                                <option value="1">Aktivní</option>
                                                <option value="2">Neaktivní</option>
                                            </select>
                                        </div>                                     
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary">Vytvořit příspěvek</button>&nbsp;
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>                
        </div>
    </div>  
</div>                       
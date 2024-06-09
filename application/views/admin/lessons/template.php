<form method="post" id="editTemplateForm" data-ajax="<?php echo $saveUrl; ?>">

    <div class="form-row mb-3">
        <div class="col-md-6">
            <label for="name">Název <span class="required">*</span></label>
            <input type="text" required name="name" id="name_edit" class="form-control" value="<?php echo @$lesson->name; ?>">
        </div>
        <div class="col-md-6">
            <label for="room_id">Sál <span class="required">*</span></label>
            <?php $this->app_components->getSelect2Rooms(['required' => TRUE, 'input_name' => 'room_id','id' => 'room_id', 'selected' => [@$lesson->room_id]]); ?>
        </div>        
    </div>

    <div class="form-row mb-3">
        <div class="col-md-12">
            <label for="tags">Tagy lekce <span class="required">*</span></label>
            <a class="ml-1" href="javascript:;" data-toggle="modal" data-remote="/admin/lessons/templates_tags_modal" data-target="#tagsModal" data-modal-title="Správa tagů" data-modal-submit="Uložit" title="Správa tagů"><i class="icon-pencil"></i></a>
            <select id="tags" class="select2" name="tags[]" multiple required>
                <?php foreach(@$tags as $t): ?>
                    <option value="<?php echo $t->id; ?>" <?php echo (is_array(@$lesson_tags) && in_array($t->id,$lesson_tags)) ? 'selected' : ''; ?>><?php echo $t->name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>    
    </div>

    <div class="form-row mb-3"> 
        <div class="col-md-4">
            <label for="pricelist_id">Ceník <span class="required">*</span></label>
            <!-- pricelist lekce -->
            <?php $this->app_components->getSelect2PricelistItems(3, ['required' => TRUE, 'input_name' => 'pricelist_id','id' => 'pricelist_id', 'selected' => [@$lesson->pricelist_id]]); ?>
        </div>
        <div class="col-md-4">
            <label for="lesson_duration">Trvání lekce</label>
            <input id="lesson_duration" name="duration" class="form-control" value='<?php echo @$lesson->duration; ?>' readonly />
        </div>
        <div class="col-md-4">
            <label for="client_limit">Limit klientů na lekci <span class="required">*</span></label>
            <input type="number" name="client_limit" value='<?php echo @$lesson->client_limit; ?>' class="form-control" required />
        </div> 
    </div>    

    <div class="form-group js-media-input-container">
        <div class="js-media-open-modal-btn">
            <label for="photo">Obrázek</label>
            <div class="image-preview<?php echo strlen(@$lesson->photo)>0 ? ' uploaded':''; ?>" style="<?php echo strlen(@$lesson->photo)>0 ? "background-image:url('".$this->app->getMedia(@$lesson->photo_src,@$lesson->photo_meta,true)['src']."')" : ''; ?>" data-placeholder="Klikněte pro otevření galerie."></div>
            <input type="hidden" name="photo" value='<?php echo @$lesson->photo; ?>' class="js-media-input-target-id">
        </div>
    </div>                          

    <div class="form-group">
        <label for="description">Popisek (V hlavičce)</label>
        <textarea name="description" id="description_edit" class="form-control"><?php echo @$lesson->description; ?></textarea>
    </div>

    <div class="form-group">
        <label for="text_title">Obsah (nadpis)</label>
        <input type="text" name="text_title" class="form-control" value="<?php echo @$lesson->text_title; ?>" placeholder="Titulek" />
    </div>     

    <div class="form-group">
        <label for="text">Obsah (text)</label>
        <textarea name="text" id="text_edit" class="form-control js-trumbowyg-editor" placeholder="O Lekci.."><?php echo @$lesson->text; ?></textarea>
    </div>

    <div class="form-group">
        <label for="teachers">Trenéři / Instruktoři</label>
        <?php $this->app_components->getSelect2Teachers(['input_name' => 'teachers[]','id' => 'teachers_edit', 'multiple' => true, 'selected' => @$lesson->teachers]); ?>
    </div>

    <?php if(isset($lesson->id)): ?>
    <input type="hidden" name="template_id" value="<?php echo $lesson->id; ?>" />
    <?php endif; ?>

</form>
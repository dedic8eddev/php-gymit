<input type="hidden" name="block_exercise_zones_equipment[id]" value="<?php echo $block_exercise_zones_equipment['id']; ?>" />
<input type="hidden" name="block_exercise_zones_equipment[name]" value="<?php echo $block_exercise_zones_equipment['name']; ?>" />
<div class="row px-3">
    <?php if(isset($block_exercise_zones_equipment['images'])) foreach ($block_exercise_zones_equipment['images'] as $e): ?>
        <div class='imgCol col-md-2'><div class="aspect16_15 image-preview uploaded" style="min-height:0; cursor:default; background-image:url('<?php echo $e; ?>');"></div>
            <div class="equipmentRemoveIcon"><i onclick="PAGES.rmEquipmentImg(this);" class="icon-remove s-18"></i></div>
            <input type="hidden" name="block_exercise_zones_equipment[images][]" value="<?php echo $e; ?>" />
        </div>
    <?php endforeach; ?>
    <div class="imgCol col-md-2 js-media-input-container mb-2">
        <div class="js-media-open-modal-btn">
            <div class="aspect16_15 image-preview equipment<?php echo strlen(@$service['header_image'])>0 ? ' uploaded':''; ?>" style="min-height:0; <?php echo strlen(@$service['header_image'])>0 ? "background-image:url('".$this->app->getMedia($service['header_img_src'],$service['header_img_meta'],true)['src']."')" : ''; ?>" data-placeholder="Klikněte pro přidání obrázku."></div>
            <input type="hidden" value='<?php echo @$service['header_image']; ?>' class="js-media-input-target-id" data-input-name="block_exercise_zones_equipment">
        </div>  
    </div>         
</div>
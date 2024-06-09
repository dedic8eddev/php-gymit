<input type="hidden" name="block_free_entry[id]" value="<?php echo $block_free_entry['id']; ?>" />
<input type="hidden" name="block_free_entry[name]" value="<?php echo $block_free_entry['name']; ?>" />
<div class="form-row mb-3">
    <div class="col-md-6">
        <label for="text">Nadpis sekce<span class="required">*</span></label>
        <input type="text" name="block_free_entry[title]" class="form-control" placeholder="Nadpis sekce" value="<?php echo $block_free_entry['title']; ?>" required />
    </div>
    <div class="col-md-6">
        <label for="text">Podnadpis sekce <span class="required">*</span></label>
        <input type="text" name="block_free_entry[subtitle]" class="form-control" placeholder="Podnadpis sekce" value="<?php echo $block_free_entry['subtitle']; ?>" required />
    </div>
</div>     
<div class="form-row mb-3">     
    <div class="col-md-12">
        <label for="text">Popis <span class="required">*</span></label>
        <textarea name="block_free_entry[text]" class="form-control" placeholder="Popis" required><?php echo $block_free_entry['text']; ?></textarea>
    </div>    
</div>     
<div class="form-row mb-3">     
    <div class="col-md-6">
        <label for="text">Text tlačítka <span class="required">*</span></label>
        <input type="text" name="block_free_entry[btn_text]" class="form-control" placeholder="Text tlačítka" value="<?php echo $block_free_entry['btn_text']; ?>" required />
    </div>   
    <div class="col-md-6">
        <label for="text">URL tlačítka <span class="required">*</span></label>
        <input type="text" name="block_free_entry[btn_url]" class="form-control" placeholder="URL tlačítka" value="<?php echo $block_free_entry['btn_url']; ?>" required />
    </div>       
</div>   
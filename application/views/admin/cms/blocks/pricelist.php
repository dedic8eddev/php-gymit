<input type="hidden" name="block_pricelist[id]" value="<?php echo $block_pricelist['id']; ?>" />
<input type="hidden" name="block_pricelist[name]" value="<?php echo $block_pricelist['name']; ?>" />
<div class="form-row mb-3">
    <div class="col-md-6">
        <label for="text">Nadpis sekce<span class="required">*</span></label>
        <input type="text" name="block_pricelist[title]" class="form-control" placeholder="Nadpis sekce" value="<?php echo $block_pricelist['title']; ?>" required />
    </div>
    <div class="col-md-6">
        <label for="text">Podnadpis sekce <span class="required">*</span></label>
        <input type="text" name="block_pricelist[subtitle]" class="form-control" placeholder="Podnadpis sekce" value="<?php echo $block_pricelist['subtitle']; ?>" required />
    </div>
</div>
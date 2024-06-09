<input type="hidden" name="block_newsletter[id]" value="<?php echo $block_newsletter['id']; ?>" />
<input type="hidden" name="block_newsletter[name]" value="<?php echo $block_newsletter['name']; ?>" />
<div class="form-row mb-3">
    <div class="col-md-6">
        <label for="text">Titulek newsletteru <span class="required">*</span></label>
        <input type="text" name="block_newsletter[title]" class="form-control" placeholder="Titulek" value="<?php echo $block_newsletter['title']; ?>" required />
    </div>
    <div class="col-md-1">
        <label for="text">Prefix ceny <span class="required">*</span></label>
        <input type="text" name="block_newsletter[priceFromText]" class="form-control" placeholder="Od .." value="<?php echo $block_newsletter['priceFromText']; ?>" required />
    </div>
    <div class="col-md-2">
        <label for="text">Cena <span class="required">*</span></label>
        <input type="text" name="block_newsletter[price]" class="form-control" placeholder="Cena.." value="<?php echo $block_newsletter['price']; ?>" required />
    </div>
    <div class="col-md-3">
        <label for="text">Postfix ceny <span class="required">*</span></label>
        <input type="text" name="block_newsletter[priceDescText]" class="form-control" placeholder="Kč ...." value="<?php echo $block_newsletter['priceDescText']; ?>" required />
    </div>      
</div>
<div class="form-row mb-3"> 
    <div class="col-md-6">
        <label for="text">Podtitulek newsletteru <span class="required">*</span></label>
        <input type="text" name="block_newsletter[subtitle]" class="form-control" placeholder="Text tlačítka" value="<?php echo $block_newsletter['subtitle']; ?>" required />
    </div>      
    <div class="col-md-3">
        <label for="text">Text tlačítka <span class="required">*</span></label>
        <input type="text" name="block_newsletter[btn_text]" class="form-control" placeholder="Text tlačítka" value="<?php echo $block_newsletter['btn_text']; ?>" required />
    </div>   
    <div class="col-md-3">
        <label for="text">URL tlačítka <span class="required">*</span></label>
        <input type="text" name="block_newsletter[btn_url]" class="form-control" placeholder="URL tlačítka" value="<?php echo $block_newsletter['btn_url']; ?>" required />
    </div>       
</div>      
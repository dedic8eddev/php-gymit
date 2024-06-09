<form id="checkoutForm" data-ajax="<?php echo $saveUrl; ?>">
    <input type="hidden" name="id" value="<?php echo @$checkout['id']; ?>" />
    <input type="hidden" name="checkout_id" value="<?php echo @$checkout['checkout_id']; ?>" />
    <div class="form-row mb-3">   
        <div class="col-md-12">
            <label for="name">Název <span class="required">*</span></label>
            <input type="text" name="name" class="form-control" id="name" placeholder="Název (pro administraci)" value="<?php echo @$checkout['name']; ?>" required>
        </div>                                        
    </div>
</form>
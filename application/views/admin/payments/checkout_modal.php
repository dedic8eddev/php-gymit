<!-- CHECKOUT MODAL -->
<div class="modal fade" id="chooseCheckoutModal" tabindex="-1" role="dialog" aria-labelledby="chooseCheckoutModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">Volba pokladny</div>
      <form id="chooseCheckoutForm">
      <div class="modal-body">
        <?php if(empty($checkouts)): ?>
          <h4 class="text-center">Dosud nebyla vytvořena žádná pokladna</h4>
          <div class="text-center"><a class="btn btn-primary" href="/admin/eetapp">Vytvořit pokladnu</a></div>
        <?php else: ?>      
          <div class="form-row">
            <label for="checkout_id">Pokladna <span class="required">*</span></label>
            <select class="form-control" name="checkout_id" id="checkout_id_select" required>
                <option disabled selected>Vyberte pokladnu..</option>
                <?php foreach ($checkouts as $ch): ?>
                <option data-id="<?php echo $ch->id; ?>" data-state="<?php echo $ch->state; ?>" value="<?php echo $ch->checkout_id;?>"><?php echo $ch->name;?></option>
                <?php endforeach; ?>
            </select>
          </div>            
        <?php endif; ?>
        <div class="form-row mt-2">
          <label for="terminal_id">Terminál <span class="required">*</span></label>
          <select class="form-control" name="checkout_id" id="terminal_id_select" required>
              <option disabled selected>Vyberte terminál..</option>
              <?php foreach ($terminals as $t): ?>
              <option data-ip="<?php echo $t->microservice_ip; ?>" value="<?php echo $t->id;?>"><?php echo $t->terminal_name;?></option>
              <?php endforeach; ?>
          </select>
        </div> 
      </div>
      <?php if(!empty($checkouts)): ?>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Potvrdit</button>
      </div>
      <?php endif; ?>
      </form>
    </div>
  </div>
</div>
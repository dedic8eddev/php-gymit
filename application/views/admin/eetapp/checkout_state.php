<h5 class="mb-2">Od posledního <?php echo $checkout['state']==0 ? 'zavření' : 'otevření'; ?> pokladny <i>(<?php echo ($lastLog) ? humanDate($lastLog->date_created,true) : humanDate(date("Y-m-d H:i:s"),true); ?>)</i></h5>
<table>
<?php foreach($lastPayments as $k=>$v): ?>
    <tr>
        <th><?php echo $transCategories[$k]['value']; ?></th>
        <td class="pl-4 text-right"><?php echo number_format($v,2,'.',' '); ?> Kč</td>
    </tr>
<?php endforeach; ?>
</table>
<hr class="bg-primary text-primary" />
<form id="checkoutStateForm" data-ajax="<?php echo $saveUrl; ?>">
    <input type="hidden" name="id" value="<?php echo $checkout['id']; ?>" />
    <input type="hidden" name="checkout_id" value="<?php echo $checkout['checkout_id']; ?>" />
    <input type="hidden" name="state" value="<?php echo $checkout['state']; ?>" />
    <div class="form-row mb-3">   
        <div class="col-md-12">
            <label for="amount">Zůstatek v pokladně <span class="required">*</span></label>
            <input type="number" name="amount" class="form-control" placeholder="Zůstatek v pokladně" required>
        </div>                                        
    </div>
    <div class="form-row mb-3">   
        <div class="col-md-12">
            <label for="note">Poznámka</label>
            <textarea name="note" class="form-control" placeholder="Poznámka.."></textarea>
        </div>                                        
    </div>    
</form>
<div id="queItems">
    <table id="clientQueTable" class="table table-hover">
        <thead class="bg-light">
        <tr>
            <th>Položka</th>
            <th class="text-right" style="width:100px;">Množství</th>
            <th class="text-right" style="width:100px;">Sleva (%)</th>
            <th class="text-right" style="width:120px;">Cena celkem</th>
            <th style="width:40px;"></th>    
        </tr>
        </thead>
        <?php if(isset($que->rows)): $multisportItemSet=false; ?>
        <tbody>
            <?php foreach($que->rows as $item): ?>
            <tr>
                <td><?php echo $item->itemInfo->name; ?><?php if($que->multisportCard && !$multisportItemSet && $item->itemId==$que->multisportItemId) echo '<br/><small>Multisport</small>'; ?><?php echo isset($item->overtimeFee) ? "<br /><small>Přesčas (Minuty: $item->overtimeMinutes)</small>":''; ?></td>
                <td class="text-right"><?php echo $item->amount; ?></td>
                <td class="text-right"><?php echo $item->discount ?? 0; ?></td>
                <?php if($que->multisportCard && !$multisportItemSet && $item->itemId==$que->multisportItemId): // item cannot have discount ?> 
                <td class="text-right"><?php echo ($item->itemInfo->vat_price * $item->amount); ?> Kč<br /><small>- <?php echo number_format($item->itemInfo->vat_price,0); ?> Kč</small><?php echo isset($item->overtimeFee) ? "<br /><small>$item->overtimeFee Kč</small>":''; ?></td>
                <?php $multisportItemSet=true; ?>
                <?php else: ?>
                <td class="text-right"><?php echo ($item->itemInfo->vat_price - ($item->itemInfo->vat_price * ($item->discount ?? 0) / 100)) * $item->amount; ?> Kč<?php echo isset($item->overtimeFee) ? "<br /><small>$item->overtimeFee Kč</small>":''; ?></td>
                <?php endif; ?>
                <td>
                <?php if(hasDeletePermission()): ?> 
                    <a href="javascript:;" class="float-right rm-que-item text-danger" title="Odstranit položku" onclick="QUE.removeQueItem(this,'<?php echo $que->cardId.'\',\''.$item->_id.'\','; echo isset($item->depotId) ? $item->depotId:'null'; echo ','.$item->itemId.','.$item->amount; ?>);"><i class="icon-close"></i></a>
                <?php endif; ?>
                </td>
            </tr>            
            <?php endforeach; ?>                                                            
        </tbody>
        <tfoot class="bg-light" style="font-size:12px;">
            <tr>
                <th style="font-weight:400;" colspan="3">Celkem</th>
                <th style="font-weight:400;" class="text-right"><?php echo $que->totalPrice; ?> Kč</th>
                <th></th>
            </tr>
        </tfoot>
        <?php endif; ?>        
    </table>
    <?php if(isset($que->note) && !empty($que->note)): ?>
    <div class="form-group mt-3">
        <h5>Poznámka</h5>
        <?php echo nl2br($que->note); ?>
    </div>
    <?php endif; ?>
</div>
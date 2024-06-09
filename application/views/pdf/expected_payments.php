<div class="float-left w-50">
    <img width="150px" src="<?php echo config_item('app')['img_folder'].'logo.png';?>">
</div>
<div class="float-right w-50 text-right">
    Číslo smlouvy: <b><?php echo $subPayments->contractNumber; ?></Číslo>
</div>
<div id="gymitInfo" class="mt-4 clear-both">
    <p>CS Fitness, s.r.o.</p>
    <p>Elišky Peškové 735/15</p>
    <p>Smíchov, 150 00 Praha</p>
    <p>IČ: 05142067</p>
</div>
<p style="font-size:16px;">Typ členství: <b><?php echo $subInfo->name; ?></b></p>
<table id="subPayments" class="mt-4">
    <thead>
        <tr>
            <th>Začátek</th>
            <th>Konec</th>
            <th>Cena s DPH</th>
            <th>Stav platby</th>
            <th>Splatnost</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($subPayments->transactions as $t): ?>
            <tr>
                <td><?php echo date('d.m.Y', strtotime($t->start)); ?></td>
                <td><?php echo date('d.m.Y', strtotime($t->end)); ?></td>
                <td class="text-right"><?php echo $t->value+$t->vat_value; ?> Kč</td>
                <td class="text-center" style="width:150px;"><?php echo $t->paid ? 'Zaplaceno' : 'Očekávaná platba'; ?></td>
                <td>????</td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
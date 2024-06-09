<p>Dobrý den,<br/>
na dnes objednávám:</p>
<br /><br />
<?php foreach($tasks as $task): ?>
    <?php $store_data = json_decode($task->stores);
        if(is_string($store_data)) $store_data = json_decode($store_data); ?>

    <?php foreach($store_data as $store_trans): ?>
        <?php echo $task->id; ?> - <?php echo $store_trans->store_name; ?>, připraveno <?php echo $store_trans->store_time_ready; ?>, <?php echo $store_trans->store_delivery_type; ?>
    <?php endforeach; ?>
<?php endforeach; ?>

Prosím o potvrzení.

Moc děkuji
-- 
Veronika Pechová
asistentka
Gymit s.r.o.
V Lužích 818/23
14200 Praha 4

+420 702 192 584

-- 
Petr Prchlík
Gymit
V lužích 818/23 
142 00 Praha 4
email:petr@gymit.cz
tel.: 777 152 191 


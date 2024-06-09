<?php if(isset($prev)): ?>
<table id="clientMovingHistoryTable" class="table table-hover">
    <thead class="bg-light">
    <tr>
        <th>Místnost</th>
        <th>Příchod</th>
        <th>Odchod</th>
        <th>Strávený čas</th>    
    </tr>
    </thead>
    <tbody>
        <tr class="cyan lighten-5">
            <td><?php echo $now['room']; ?></td>
            <td><?php echo date('d.m.Y (H:i)', strtotime($now['checked_in'])); ?></td>
            <td></td>
            <td><?php echo $now['time_diff']; ?></td>
        </tr>
        <?php foreach ($prev as $move): ?>
        <tr>
            <td><?php echo $move['room']; ?></td>
            <td><?php echo date('d.m.Y (H:i)', strtotime($move['checked_in'])); ?></td>
            <td><?php echo date('d.m.Y (H:i)', strtotime($move['checked_out'])); ?></td>
            <td><?php echo $move['time_diff']; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
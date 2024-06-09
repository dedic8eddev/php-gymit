<tr>
    <td>
        <a href="javascript:;" data-toggle="modal" data-remote="<?php echo base_url('admin/dashboard/client-modal/'.$user_data->user_id); ?>" data-target="#modal" title="Zobrazit detail" data-modal-title="Detail zákazníka" data-modal-submit="">
            <?php echo $user_data->first_name.' '.$user_data->last_name; ?>
        </a>
    </td>
    <td><?php echo humanTime($time_diff); ?></td>
    <td><?php echo date('d.m.Y (H:i)', strtotime($checked_in)); ?></td>
</tr>
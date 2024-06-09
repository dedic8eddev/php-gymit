<div class="row">
    <div class="col-lg-12">
        <table>
            <tr>
                <th style="width:120px;">Kód voucheru</th>
                <td><?php echo $voucher->code; ?></td>
            </tr>
            <tr>
                <th>Položka</th>
                <td><?php echo $voucher->name; ?></td>
            </tr>
            <tr>
                <th>Cena položky</th>
                <td><?php echo number_format($voucher->vat_price,2,'.',' ')." Kč"; ?></td>
            </tr>
            <tr>
                <th>Založeno</th>
                <td><?php echo date('j.n.Y H:m:s',strtotime($voucher->date_created))." ($voucher->created_by_name - $voucher->created_by_email)"; ?></td>
            </tr>
            <tr>
                <th>Typ platby</th>
                <td><?php echo $voucher->identification_name." ($voucher->identification_id)"; ?></td>
            </tr>
            <tr>
                <th>Deaktivováno</th>
                <td><?php echo $voucher->date_disabled ? date('j.n.Y H:m:s',strtotime($voucher->date_disabled))." ($voucher->disabled_by_name - $voucher->disabled_by_email)" : '<button type="button" class="btn btn-xs btn-danger js-disable-voucher" data-code="'.$voucher->code.'" data-ajax="/admin/vouchers/disable-voucher-ajax">Deaktivovat</button>'; ?></td>
            </tr>
            <tr>
                <th>Obdarovaný</th>
                <td><?php echo $voucher->gifted_user_name ? "$voucher->gifted_user_name - $voucher->gifted_user_email" : ''; ?></td>
            </tr>
            <tr>
                <th>Poznámka</th>
                <td><?php echo $voucher->note; ?></td>
            </tr>
        </table>
    </div>
</div>
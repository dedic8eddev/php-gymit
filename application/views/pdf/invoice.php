<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
	</head>
	<body style="margin:0;padding:0;">
		<table style="width:100%;font-family:'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;font-size:12px;color:#222;margin:0;padding:0px;" cellpadding="0" cellspacing="0">
			<tbody>
				<tr>
					<td style="padding:0px;width:50%;vertical-align:top;border-bottom:1px dotted #444;">
						<table style="margin:0;padding:0;width:100%;" cellpadding="0" cellspacing="0">
							<tbody>
								<tr>
									<td style="line-height:20px;padding:22px 21px 21px 21px;border-bottom:1px dotted #444; text-align: center;">
										<div class="float-left w-50">
											<img width="150px" src="<?php echo config_item('app')['img_folder'].'logo.png';?>">
										</div>
									</td>
								</tr>
								<tr>
									<td style="line-height:20px;padding:20px 20px 0px 20px;">
                                        <h3>Odběratel</h3>
                                        <span><?php echo $invoice['client_name']; ?></span> <br>
                                        <span><?php echo $invoice['client_street']; ?></span> <br>
                                        <span><?php echo $invoice['client_zip']; ?> <?php echo $invoice['client_city']; ?></span> <br>
                                        <span><?php echo $invoice['country_name']; ?></span>
									</td>
								</tr>
								<tr>
									<td style="line-height:20px;padding:20px 20px 20px 20px;border-bottom:1px dotted #444;">
                                        <?php if(!empty($invoice['client_company_id'])): ?>
                                        <span><b>IČ:</b> <?php echo $invoice['client_company_id']; ?></span><br>
                                        <?php endif; ?>

                                        <?php if(!empty($invoice['client_vat_id'])): ?>
                                        <span><b>DIČ:</b> <?php echo $invoice['client_vat_id']; ?></span><br>
                                        <?php endif; ?>
									</td>
								</tr>
								<tr>
									<td style="line-height:20px;padding:20px;">

                                        <span>Variabilní číslo: <?php echo $invoice['invoice_number']; ?></span><br>
                                        <span>Metoda platby: <?php $pt = $this->payments->returnTransCategories(); echo $pt[$invoice['payment_method']]['value']; ?>
                                        </span><br>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
					<td style="padding:0px;width:50%;vertical-align:top;border-bottom:1px dotted #444;">
						<table style="margin:0;padding:0;width:100%;border-left:1px dotted #444;" cellpadding="0" cellspacing="0">
							<tbody>
								<tr>
									<td style="padding:30px 20px 20px 40px;font-size:30px;border-bottom:1px dotted #444;" colspan="2">
										<b>Faktura</b>&nbsp;<?php echo $invoice['invoice_number']; ?>
									</td>
								</tr>
								<tr>
									<td style="padding:20px 20px 0px 40px;line-height:20px;" colspan="2">
										<b>Vystavil:</b><br>
										<?php echo $subject_info->name; ?><br>
										<?php echo $subject_info->street; ?><br>							
                                        <?php echo $subject_info->zip; ?>, <?php echo $subject_info->town; ?>
									</td>
								</tr>
								<tr>
									<td style="line-height:20px;padding:40px 0px 20px 40px;vertical-align:top;">
										<span><b>IČ:</b> 05142067</span><br>
										<!--<span><b>DIČ:</b> CZ24850161</span>-->
									</td>
									<td style="line-height:20px;padding:40px 20px 20px 20px;">
										<b>Datum vystavení:</b> <?php echo date('d.m.Y',strtotime($invoice['issue_date'])); ?><br>
										<b>Datum splatnosti:</b> <?php echo date('d.m.Y',strtotime($invoice['due_date'])); ?><br>
										<b>DUZP: </b><?php echo date('d.m.Y',strtotime($invoice['issue_date'])); ?><br>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>


		<table style="width:100%;font-family:'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;font-size:10px;color:#222;margin:0;padding:20px 0px 10px 0px;text-align:left" cellpadding="0" cellspacing="0">
			<tbody>
				
                <tr>
					<th style="padding:10px 20px;border-bottom:1px solid #222">Označení dodávky</th>
					<th style="padding:10px 20px;border-bottom:1px solid #222">Množství</th>
					<th style="padding:10px 20px;border-bottom:1px solid #222">Cena za jednotku</th>
					<th style="padding:10px 20px;border-bottom:1px solid #222">Sleva</th>
					<th style="padding:10px 20px;border-bottom:1px solid #222">Celkem</th>
				</tr>

                <?php foreach(json_decode($invoice['items']) as $item): ?>
					<tr>
						<td style="padding:10px 20px;border-bottom:1px solid #ddd"><?php echo $item->item_name; ?></td>
						<td style="padding:10px 20px;border-bottom:1px solid #ddd;text-align:right;"><?php echo $item->item_amount; ?></td>
						<td style="padding:10px 20px;border-bottom:1px solid #ddd;text-align:right;"><?php echo number_format((int)$item->item_value, 0, ',', ' '); ?> Kč</td>

						<td style="padding:10px 20px;border-bottom:1px solid #ddd;text-align:right;"><?php echo (int)$item->item_discount; ?>%</td>

						<td style="padding:10px 20px;border-bottom:1px solid #ddd;text-align:right;"><?php echo number_format(($item->item_value - ($item->item_value * (int)$item->item_discount / 100)) * $item->item_amount, 0, ',', ' '); ?> Kč</td>
					</tr>
				<?php endforeach; ?>

					<tr>
						<td colspan="8" style="padding:20px;text-align:right;">
							<table>
								<tr>
                                    <td style="font-size:18px;"><b>Celkem: <?php echo number_format($invoice['vat_value'], 0, ',', ' '); ?> Kč</b></td>
                                </tr>
                                <?php if($invoice['payment_date'] != NULL): ?>
                                <tr>
                                    <td style="font-size:18px;color: green;">
                                        <hr>
                                        <b>ZAPLACENO: <?php echo date('d. m. Y', strtotime($invoice['payment_date'])); ?></b>
                                    </td>
								</tr>
                                <?php endif; ?>
							</table>
						</td>
					</tr>
			</tbody>
		</table>
	</body>
</html>
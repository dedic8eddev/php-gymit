<input type="hidden" name="block_membership[id]" value="<?php echo $block_membership['id']; ?>" />
<input type="hidden" name="block_membership[name]" value="<?php echo $block_membership['name']; ?>" />
<div class="form-row mb-3">
    <div class="col-md-6">
        <label for="text">Nadpis sekce<span class="required">*</span></label>
        <input type="text" name="block_membership[membership_title]" class="form-control" placeholder="Nadpis sekce" value="<?php echo $block_membership['membership_title']; ?>" required />
    </div>
    <div class="col-md-6">
        <label for="text">Podnadpis sekce <span class="required">*</span></label>
        <input type="text" name="block_membership[membership_subtitle]" class="form-control" placeholder="Podnadpis sekce" value="<?php echo $block_membership['membership_subtitle']; ?>" required />
    </div>
</div>    
<div class="priceList text-center">
    <div class="priceListRow">
        <?php $i=0; foreach($block_membership['membership'] as $m): $i++; ?>
        <input type="hidden" name="block_membership[membership][<?php echo $i;?>][code]" value="<?php echo $m['code']; ?>" />
        <?php if($i==1) $colClass=' first'; 
                else if ($i==4) $colClass=' premium';
                else $colClass=''; ?>
        <div class="col<?php echo $colClass; ?>">
            <input name="block_membership[membership][<?php echo $i;?>][header]" value="<?php echo $m['header']; ?>" type="text" class="form-control h3 colTitle text-center" placeholder="Název" />
            <div class="text mb-3"><textarea name="block_membership[membership][<?php echo $i;?>][memDescription]" class="form-control text" style="background:transparent; text-align:center;" placeholder="popis členství"><?php echo $m['memDescription']; ?></textarea></div>
            <div class="priceLabel form-inline justify-content-center">
                <span class="from"><input name="block_membership[membership][<?php echo $i;?>][priceFromText]" value="<?php echo $m['priceFromText']; ?>" type="text" class="form-control from"/></span>
                <span class="price"><input name="block_membership[membership][<?php echo $i;?>][price]" value="<?php echo $membership_prices[$m['code']]; ?>" type="text" class="form-control price text-center" readonly /></span><small class="currency">Kč</small>
            </div>
            <div class="period form-inline justify-content-center"><input name="block_membership[membership][<?php echo $i;?>][periodText]" value="<?php echo $m['periodText']; ?>" type="text" class="form-control period text-center"/></div>
            <div class="divider"></div>
            <div class="colDescription">
                <p><strong><input name="block_membership[membership][<?php echo $i;?>][description]" value="<?php echo $m['description']; ?>" type="text" class="form-control text-center colDesc"/></strong></p>
                <ul>
                    <?php foreach ($m['icon'] as $k => $icon): ?>
                    <li>
                        <div class="row">
                            <div class="col-md-4">
                                <button type="button" class="btn btn-icon shadow-none dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $icon=='' ? '<div class="px-1 py-1 d-inline-block text-dark">-----</div>' : "<img src=".base_url(config_item('app')['img_folder']."svg/ico_services_0$icon.svg").">"; ?></button>
                                <ul class="dropdown-menu">
                                    <li data-id="1"><img src="<?php echo base_url(config_item('app')['img_folder'].'svg/ico_services_01.svg'); ?>"></li>
                                    <li data-id="2"><img src="<?php echo base_url(config_item('app')['img_folder'].'svg/ico_services_02.svg'); ?>"></li>
                                    <li data-id="3"><img src="<?php echo base_url(config_item('app')['img_folder'].'svg/ico_services_03.svg'); ?>"></li>
                                    <li data-id="4"><img src="<?php echo base_url(config_item('app')['img_folder'].'svg/ico_services_04.svg'); ?>"></li>
                                    <li data-id=""><div class="px-1 py-1 d-inline-block text-dark">-----</div></li>
                                </ul>
                            </div>
                            <div class="col-md-8 pl-0">
                                <input name="block_membership[membership][<?php echo $i;?>][icon][<?php echo $k; ?>]" value="<?php echo $icon; ?>" type="hidden" class="selectedDropDownItem" />
                                <input name="block_membership[membership][<?php echo $i;?>][iconText][<?php echo $k; ?>]" value="<?php echo @$m['iconText'][$k]; ?>" type="text" class="form-control form-control-sm itemText text ml-1" <?php echo isset($m['iconText'][$k]) ? '' : 'disabled'; ?> />
                                <input name="block_membership[membership][<?php echo $i;?>][iconPriceText][<?php echo $k; ?>]" value="<?php echo @$m['iconPriceText'][$k]; ?>" type="text" class="form-control form-control-xs itemText text ml-1" <?php echo isset($m['iconPriceText'][$k]) ? '' : 'disabled'; ?> />
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="btnContainer">
                <div class="btn btn--brown">
                    <input name="block_membership[membership][<?php echo $i;?>][btnText]" value="<?php echo $m['btnText']; ?>" type="text" class="form-control" placeholder="text tlačítka" />
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
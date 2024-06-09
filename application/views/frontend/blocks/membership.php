<section class="priceList">
  <div class="container">
    <h2 class="sectionTitle"><?php echo $block_membership['membership_title'] ;?><span class="smallText"><?php echo $block_membership['membership_subtitle'] ;?></span></h2>
    <div class="priceListRow four">
    <?php $i=0; foreach($block_membership['membership'] as $m): $i++; ?>
      <?php if($i==1){ $colClass=' first'; $iconColor="_white"; }
            else if ($i==4){ $colClass=' premium'; $iconColor=""; }
            else { $colClass=''; $iconColor="_white"; } ?>
      <div class="col<?php echo $colClass; ?>">
        <h3 class="colTitle"><?php echo $m['header']; ?></h3>
        <span class="text"><?php echo $m['memDescription']; ?></span>
        <div class="priceLabel margin__top--l">
          <span class="from"><?php echo $m['priceFromText']; ?></span>
          <span class="price"><?php echo $membership_prices[$m['code']]; ?></span><small class="currency">Kč</small>
          <div class="period"><?php echo $m['periodText']; ?></div>
        </div>
        <div class="divider"></div>
        <div class="colDescription">
          <p><strong><?php echo $m['description']; ?></strong></p>
          <ul>
            <?php foreach ($m['iconText'] as $k => $iconText): ?>
            <li>
              <span class="ico a1"><img src="<?php echo base_url(config_item('app')['img_folder']."svg/ico_services_0".$m['icon'][$k].$iconColor.".svg"); ?>"></span> 
              <span class="text"><?php echo $m['iconText'][$k]; ?></span>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="btnContainer"><a href="<?php echo '/membership/detail/'.$m['code']; ?>" class="btn btn--brown">
            <div class="bg"></div><span><?php echo $m['btnText']; ?></span>
          </a></div>
      </div>
    <?php endforeach; ?>
    </div>
  </div>
</section>
<section class="priceListTable">
  <h2 class="sectionTitle"><?php echo $block_pricelist['title']; ?><span class="smallText"><?php echo $block_pricelist['subtitle']; ?></span></h2>
  <?php foreach($single_entry_prices as $p): ?>
  <div class="row">
    <div class="title">
      <h3><?php echo $p->name; ?></h3>
    </div>
    <div class="price"><?php echo $p->vat_price == 0.0 ? 'Zdarma' : number_format($p->vat_price, 0, '', '.') . '<span>Kč</span>'; ?></div>
    <div class="btnContainer"><a href="#" class="btn btn--brown defaultTooltip"
        data-tippy-content="Denní vstup do cvičebních zón si můžete zakoupit na recepeci Gymit Premium Fitness na 85 Hall Street, Ústí nad Labem">
        <div class="bg"></div><span>Info</span>
      </a></div>
  </div>
  <?php endforeach; ?>
</section>
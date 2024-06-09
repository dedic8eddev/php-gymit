<?php if(!isset($site_settings) OR ( isset($site_settings) && is_null($site_settings->current_site) ) ){ ?>

<footer id="pageFooter">
  <div class="container">
    <div class="row">
      <?php $prev=""; foreach ($this->gymSettings['footer']['data'] as $colIndex => $col): ?>
          <?php $colClass = $colIndex == 0 ? 'block' : $colIndex == 1 ? 'block middle' : 'block last'; ?>
          <div class="<?php echo $colClass; ?>">
            <?php foreach (@$col as $k => $v): ?>              
              <?php if ($k=='logo'): ?>
                <img class="logo" src="<?php echo base_url(); ?>public/assets/img/svg/logo_gymit_premium.svg" alt="Gymit" width="150" />
              <?php elseif ($k=='text'): ?>
                <p><?php echo @$v; ?></p>
              <?php elseif ($k=='links'): ?>
                <ul class="footerMenu">
                <?php foreach ($this->gymSettings['footer']['data'][$colIndex][$k] as $linkIndex => $link): ?>
                  <li><a href="<?php echo $link['link']; ?>"><?php echo $link['text']; ?></a></li>                          
                <?php endforeach; ?>                                        
                </ul>
              <?php elseif ($k=='address'): ?>
                <div class="addressLine map">
                  <?php echo $this->gymSettings['general_info']['data']['street']; ?>, <?php echo $this->gymSettings['general_info']['data']['city']; ?>
                  <small> 
                      Po - Pá <?php echo $this->gymSettings['opening_hours']['data']['monday']['from']; ?> - 
                      <?php echo $this->gymSettings['opening_hours']['data']['monday']['to']; ?>, So - Ne 
                      <?php echo $this->gymSettings['opening_hours']['data']['saturday']['from']; ?> - 
                      <?php echo $this->gymSettings['opening_hours']['data']['saturday']['to']; ?>
                  </small>
                </div> 
              <?php elseif ($k=='phone'): ?>
                <div class="addressLine phone">
                  <a href="tel:<?php echo $this->gymSettings['general_info']['data']['phone']; ?>"><?php echo $this->gymSettings['general_info']['data']['phone']; ?></a>
                </div>  
              <?php elseif ($k=='email'): ?>
                <div class="addressLine email">
                  <a href="mailto:<?php echo $this->gymSettings['general_info']['data']['email']; ?>"><?php echo $this->gymSettings['general_info']['data']['email']; ?></a>
                </div>                                                                                                     
              <?php elseif ($k=='social_icons'): ?>  
                <div class="socialIcons">     
                  <a href="<?php echo $this->gymSettings['general_info']['data']['fb_link']; ?>" target="_blank">                        
                    <svg version="1.1" id="Vrstva_1" width="30px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 18 18" style="enable-background:new 0 0 18 18;" xml:space="preserve">
                        <g>
                            <defs><rect id="SVGID_1_" width="18" height="18" /></defs>
                            <path class="st0" d="M17,0H1C0.4,0,0,0.4,0,1v16c0,0.6,0.4,1,1,1h8.6v-7H7.3V8.3h2.3v-2c0-2.3,1.4-3.6,3.5-3.6c0.7,0,1.4,0,2.1,0.1v2.4h-1.4c-1.1,0-1.3,0.5-1.3,1.3v1.7h2.7L14.8,11h-2.3v7H17c0.6,0,1-0.4,1-1V1C18,0.4,17.6,0,17,0" />
                        </g>
                    </svg>
                  </a>
                  <a href="<?php echo $this->gymSettings['general_info']['data']['ig_link']; ?>" target="_blank">
                    <svg class="ml-2" version="1.1" id="Vrstva_1" width="30px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 18 18" style="enable-background:new 0 0 18 18;" xml:space="preserve">
                        <g>
                            <defs><rect id="SVGID_1_" y="0" width="18" height="18" /></defs>
                            <path class="st0" d="M9,4.4C6.4,4.4,4.4,6.4,4.4,9c0,2.6,2.1,4.6,4.6,4.6c2.6,0,4.6-2.1,4.6-4.6C13.6,6.4,11.5,4.4,9,4.4 M9,12c-1.7,0-3-1.3-3-3c0-1.7,1.3-3,3-3s3,1.3,3,3C12,10.7,10.7,12,9,12" />
                            <path class="st0" d="M13.8,3.1c0.6,0,1.1,0.5,1.1,1.1c0,0.6-0.5,1.1-1.1,1.1c-0.6,0-1.1-0.5-1.1-1.1C12.7,3.6,13.2,3.1,13.8,3.1" />
                            <path class="st0" d="M17.5,3.1c-0.5-1.2-1.4-2.2-2.6-2.6c-0.7-0.3-1.4-0.4-2.2-0.4C11.7,0,11.4,0,9,0C6.6,0,6.2,0,5.3,0.1 c-0.7,0-1.5,0.2-2.2,0.4C1.9,0.9,0.9,1.9,0.5,3.1C0.2,3.8,0.1,4.5,0.1,5.3C0,6.3,0,6.6,0,9s0,2.8,0.1,3.7c0,0.7,0.2,1.5,0.4,2.2c0.5,1.2,1.4,2.2,2.6,2.6C3.8,17.8,4.5,18,5.3,18C6.3,18,6.6,18,9,18c2.4,0,2.8,0,3.7-0.1c0.7,0,1.5-0.2,2.2-0.4c1.2-0.5,2.2-1.4,2.6-2.6c0.3-0.7,0.4-1.4,0.4-2.2c0-1,0.1-1.3,0.1-3.7s0-2.8-0.1-3.7C17.9,4.6,17.8,3.8,17.5,3.1 M16.3,12.6c0,0.6-0.1,1.1-0.3,1.7c-0.3,0.8-0.9,1.4-1.7,1.7c-0.5,0.2-1.1,0.3-1.7,0.3c-1,0-1.2,0.1-3.7,0.1c-2.4,0-2.7,0-3.7-0.1c-0.6,0-1.1-0.1-1.7-0.3c-0.8-0.3-1.4-0.9-1.7-1.7c-0.2-0.5-0.3-1.1-0.3-1.7c0-1-0.1-1.2-0.1-3.7c0-2.4,0-2.7,0.1-3.7c0-0.6,0.1-1.1,0.3-1.7c0.3-0.8,0.9-1.4,1.7-1.7c0.5-0.2,1.1-0.3,1.7-0.3c1,0,1.2-0.1,3.7-0.1c2.4,0,2.7,0,3.7,0.1c0.6,0,1.1,0.1,1.7,0.3c0.8,0.3,1.4,0.9,1.7,1.7c0.2,0.5,0.3,1.1,0.3,1.7c0,1,0.1,1.2,0.1,3.7C16.4,11.4,16.4,11.7,16.3,12.6L16.3,12.6L16.3,12.6z" />
                        </g>
                    </svg> 
                  </a>                                                                              
                </div>                                                                                 
              <?php endif; ?>
            <?php $prev=$k; endforeach; ?>
          </div>                                
      <?php endforeach; ?>     
    </div>
  </div>
</footer>

<div class="goToTop js-gotop"></div>


<div class="popUpOverlay js-submitPopup closed" data-popup="footer-newsletter">
  <div class="popUp js-submitPopup newsletter closed" data-popup="footer-newsletter">
    <div class="close js-popUp-close"></div>
    <div>
      <p class="use">Footer newsletter</p>
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam sed vehicula orci, a egestas lectus.
        Suspendisse
        eget
        purus feugiat, suscipit leo sed.</p>
      <p><label for="footer-newsletter"><input type="checkbox" name="footer-newsletter" id="footer-newsletter"
            class="js-submitPopupInput" required />
          Lorem
          ipsum dolor
          sit</label></p>
      <a href="#" class="btn js-submitPopupConfirm"><span>i agree</span></a>
      <div class="errorLine">Musíte souhlasit s ochranou osobních údajů pro odeslání newsletteru.</div>
    </div>
  </div>
</div>
<div class="popUpOverlay js-submitPopup closed" data-popup="hpForm01">
  <div class="popUp js-submitPopup newsletter closed" data-popup="hpForm01">
    <div class="close js-popUp-close"></div>
    <div>
      <p class="use">Lorem ipsum dolor sit</p>
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam sed vehicula orci, a egestas lectus. Suspendisse
        eget
        purus feugiat, suscipit leo sed.</p>
      <p><label for="hpForm01"><input type="checkbox" name="hpForm01" id="hpForm01" class="js-submitPopupInput"
            required />
          Lorem
          ipsum dolor
          sit</label></p>
      <a href="#" class="btn js-submitPopupConfirm"><span>i agree</span></a>
      <div class="errorLine">Musíte souhlasit s ochranou osobních údajů pro odeslání newsletteru.</div>
    </div>
  </div>
</div>

<?php } ?>

</div>
<!--/#app -->
<?php $this->app->loadAssets('js'); ?>

</body>

</html>
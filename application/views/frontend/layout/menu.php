<?php if(!isset($site_settings) OR ( isset($site_settings) && is_null($site_settings->current_site) ) ){ ?>
<div id="mainMenu" class="<?php echo (isset($menuClass)) ? $menuClass : ''; ?>">
  <div class="menuLogo">
    <a href="<?php echo base_url(); ?>">
      <svg version="1.1" id="Main" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px"
        y="0px" viewBox="0 0 640 229" style="enable-background:new 0 0 640 229;" xml:space="preserve">
        <g>
          <path class="st0" d="M106.6,60.3c-23,0-40.5,14.9-40.5,41.5c0,23.2,13.5,28.5,28.3,28.5c7.5,0,14.5-1.9,21.7-6.5l2.2-10.8H89.1
        l4.1-23.6H152l-7.2,45.6c-10.2,15.2-34,23.7-57,23.7c-35.9,0-60.1-22.9-55.7-61.5c4.8-41.7,37.2-65.7,79.6-65.7
        c18.3,0,33,4.8,44.2,18.1l-5.1,29h-28.3l1.5-14.7C118.5,61,111.9,60.3,106.6,60.3z" />
          <path class="st1" d="M199.8,111.9c-1.7,9.4-0.5,18.6,10.2,18.6c10.4,0,14.5-10.2,15.9-18.1l3.2-18.8h-11.3l4.1-23.2h42.9L250,154.5
        c-5.3,30.2-24.2,42.9-52.6,42.9c-26.3,0-40.6-11.4-37.4-35.9l31.8-0.2c-0.5,7,2.7,10.4,10.1,10.4c10.2,0,16-6,17.9-16.6l1.7-9.9
        c-7.3,7.7-13.1,9.6-22.9,9.6c-26.8,0-34.5-17.8-30.2-42.5l3.2-18.8h-11.3l4.1-23.2h42.7L199.8,111.9z" />
          <path class="st2" d="M386.7,108.6c1.4-8.7-2.7-12.5-7.7-12.5c-6.1,0-11.6,4.8-13.1,12.8c-2.6,15.5-5.6,30.9-8.4,46.3
        c-15.7,0-16,0-31.6,0c2.7-15.4,5.6-30.9,8.4-46.6c1.5-9.7-2.4-13.1-6.8-13.1c-8.4,0-13.8,6.3-14.9,13.8l-8.4,45.9h-31.6l10.9-61.3
        h-12l4.4-23.4h38.1l1.2,9.6c7.5-7.9,16.4-11.3,26.3-10.9c8.9,0,16.4,4.1,19.3,12.1C368.7,72.6,380.5,69,392,69
        c20,0,31.2,11.4,26.1,39.3c0,0-5.5,31.6-8.2,47c-15.7,0-16,0-31.6,0C381,139.9,386.7,108.6,386.7,108.6z" />
        </g>
        <g>
          <path class="st3"
            d="M441.5,93.9h-11.8l4.1-23.4h43l-9,51.9c-1.7,10.2,5,9.9,12.6,8.4l-4.1,23c-28,7.5-45.9,2-40.1-30.9L441.5,93.9
        z M468.8,31.1c7.7,0,14.9,4.6,14.9,13.7c0,12-11.1,18.6-20.5,18.6c-8.4,0-15.5-4.8-15.5-13.7C447.7,37.6,458.6,31.1,468.8,31.1z" />
          <path class="st4" d="M576.3,122.6c24.2,0,20.7,33.5-6,33.5C546.4,156.1,552.4,122.6,576.3,122.6z M561.6,116l13.7-80.2h33.1
        L593.5,116H561.6z" />
          <path class="st5"
            d="M527.1,95.5h19.1l4.3-24.1h-19l4.3-24.4l-31.8,3.4l-3.7,21h-10.6l-4.3,24.1H496l-4.9,27.7
        c-5.1,26.5,8.5,34.3,25.3,34.3c8.7,0,17.4-1.7,25.3-5.8l-2.7-23.2c-2.9,1.7-6.7,3.1-9.7,3.1c-4.8,0-7.9-2.4-7-8.4L527.1,95.5z" />
        </g>
      </svg>
    </a>
  </div>
  <div class="open-menu">
    <div class="nav-icon3">
      <span></span>
      <span></span>
      <span></span>
      <span></span>
    </div>
  </div>
  <div class="menuInfo">
    <svg version="1.1" id="Vrstva_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
      x="0px" y="0px" viewBox="0 0 20 20" style="enable-background:new 0 0 20 20;" xml:space="preserve">
      <g>
        <defs>
          <rect id="SVGID_1_" width="20" height="20" />
        </defs>
        <path class="st0" d="M10,0C4.5,0,0,4.5,0,10s4.5,10,10,10s10-4.5,10-10S15.5,0,10,0 M10,18c-4.4,0-8-3.6-8-8s3.6-8,8-8s8,3.6,8,8
       S14.4,18,10,18" />
        <polygon class="st0" points="11,5 9,5 9,10.4 12.3,13.7 13.7,12.3 11,9.6 	" />
      </g>
    </svg>
    Po - Pá: <?php echo $this->gymSettings['opening_hours']['data']['monday']['from']; ?> - <?php echo $this->gymSettings['opening_hours']['data']['monday']['to']; ?>
    So - Ne: <?php echo $this->gymSettings['opening_hours']['data']['saturday']['from']; ?> - <?php echo $this->gymSettings['opening_hours']['data']['saturday']['to']; ?>
    <span class="openStatus <?php echo $this->openStatus['class']; ?>"><?php echo $this->openStatus['text']; ?></span> 
    <?php if (gym_userid()>0): ?>
      <?php if ($this->router->fetch_class()=='account'): ?>
      <a href="/logout" class="login btn btn--xs"><div class="bg"></div><span>Odhlásit</span></a>
      <?php else: ?>
      <a href="/account" class="login btn btn--xs"><div class="bg"></div><span>Uživatelská sekce</span></a>
      <?php endif; ?>
    <?php else: ?>
      <a href="/login" class="login btn btn--xs"><div class="bg"></div><span>Přihlásit se</span></a>
    <?php endif; ?>
  </div>
  <nav class="menuContent">
    <ul>
      <?php foreach($this->gymSettings['front_menu_items']['data']['items'] as $item): ?>
      <?php if(!isset($item['show'])) continue; ?>
      <li><a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a></li>
      <?php endforeach; ?>
    </ul>
  </nav>
</div>
<?php } ?>
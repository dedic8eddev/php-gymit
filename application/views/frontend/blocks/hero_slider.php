<section class="hpIntro" style="background-image:url('<?php echo strlen($page_homepage['header_img'])>0 ? $page_homepage['header_img'] : config_item('app')['img_folder'].'temp/image_temp_00.png'; ?>');">
  <div class="container">
    <h1 class="sectionTitle"><?php echo $page_homepage['header_title']; ?></h1>
    <div class="divider logo"><img src="public/assets/img/svg/logo_gymit_white.svg" alt="Gymit" /></div>
    <h2 class="sectionSubTitle"><?php echo $page_homepage['header_subtitle']; ?></h2>
    <a href="<?php echo $page_homepage['header_btn_url']; ?>" class="btn btn--big">
      <div class="bg"></div><span><?php echo $page_homepage['header_btn_text']; ?></span>
    </a>
  </div>
</section>
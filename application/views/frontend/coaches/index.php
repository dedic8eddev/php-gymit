<div class="subpage lecture personal_coach">
  <section class="header header_v1" style="background-image:url('<?php echo $page_coaches['header_image']; ?>');">
    <div class="headerTopOverlay">
      <div class="container">
        <div class="headerContainer">
          <a href="/services" class="navigationBack">Služby</a>
          <h2 class="headerTitle"><?php echo $page_coaches['header_title']; ?></h2>
          <p class="headerDescription"><?php echo $page_coaches['perex']; ?></p>

          <!--<div class="headerPrice">
            1 trénink
            <span class="price">od 390,- Kč</span>
          </div>
          <div class="headerButtons txt-right">
            <a href="<?php echo $page_coaches['header_btn_url']; ?>" class="btn btn--big">
              <div class="bg"></div><span><?php echo $page_coaches['header_btn_text']; ?></span>
            </a>
          </div>-->
        </div>
      </div>
    </div>
  </section>
  <?php $this->app_blocks->coaches($coaches,$page_coaches); ?>
</div>

<?php $this->app_blocks->newsletter([]); ?>
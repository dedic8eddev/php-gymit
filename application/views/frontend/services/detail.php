<div class="subpage lecture cvicebni_zony">
  <section class="header header_v1" style="<?php echo "background-image:url('".$service['header_image']."')"; ?>">
    <div class="headerTopOverlay">
      <div class="container">
        <div class="headerContainer">
          <a href="/services" class="navigationBack">Služby</a>
          <h2 class="headerTitle"><?php echo $service['name']; ?></h2>
          <p class="headerDescription"><?php echo $service['perex']; ?></p>

          <!--<div class="headerPrice">
            Vstup
            <span class="price">od 140,- Kč</span>
          </div>
          <div class="headerButtons txt-right">pinned_articles
            <a href="#" class="btn btn--big">
              <div class="bg"></div><span>Více info</span>
            </a>
          </div>-->
        </div>
      </div>
    </div>
  </section>

  <div class="lectureHalf">
    <div class="container">
      <div class="aboutLectureFrame">
        <section class="aboutLecture">
          <h3 class="sectionTitle">Vybavení</h3>
          <div class="block">
            <p><?php echo $service['text']; ?></p>

            <ul class="dotList half">
              <?php foreach ($equipment as $k=>$v): ?>
                <li><?php echo $v['equipment_name']; ?></li>
              <?php endforeach; ?>
            </ul>
          </div>

        </section>
      </div>
      <div class="nextEventsFrame">
        <section class="nextEvents">
          <h3 class="sectionTitle">Nabídka vstupů</h3>
          <div class="eventList">
            <?php foreach($prices as $k => $v): ?>
            <?php $label = $k == 'regular' ? 'Jednorázový vstup' : "Členství $k"; ?>
            <div class="event full">
              <div class="eventContent">
                <a href="#" class="btn">
                  <div class="bg"></div><span><img src="<?php echo site_url(config_item('app')['img_folder'].'svg/bx-chevron-right.svg'); ?>" alt=""
                      height="15" /></span>
                </a>
                <h4 class="title"><?php echo $label; ?></h4>
                <div class="priceContainer m">
                  <?php if($k != 'regular') echo '<span class="l from">od</span>'; ?>
                  <span class="l price"><?php echo $v; ?></span>
                  <span class="l currency">Kč</span>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <div class="showCalendar">
            <a href="#" class="btn btn--transparent">
              <div class="bg"></div><span>členství a ceník</span>
            </a>
          </div>
        </section>
      </div>
    </div>
  </div>

  <?php $this->app_blocks->equipment($block_equipment); ?>
  <section class="servicesList hideSquer">
    <div class="container">
        <h2 class="sectionTitle"><?php echo $service['news_title']; ?><span class="smallText"><?php echo $service['news_subtitle']; ?></span></h2>
        <div class="row">
          <?php $this->app_blocks->pinnedNews($pinned_articles); ?>
        </div>
    </div>
  </section>

</div>
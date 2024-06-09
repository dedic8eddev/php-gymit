<div class="subpage lecture group_lessons">
  <section class="header header_v1" style="background-image:url('<?php echo $page_lessons['header_image']; ?>');">
    <div class="headerTopOverlay">
      <div class="container">
        <div class="headerContainer">
          <a href="/services" class="navigationBack">Služby</a>
          <h2 class="headerTitle"><?php echo $page_lessons['header_title']; ?></h2>
          <p class="headerDescription"><?php echo $page_lessons['perex']; ?></p>
        </div>
      </div>
    </div>
    <div class="headerBottomOverlay">
      <div class="container">
        <?php foreach ($lessons_templates_tags as $t): ?>
        <label class="customCheckbox">
          <input type="checkbox" name="reason" value=".t<?php echo $t->id; ?>" checked>
          <span class="checkmark"></span>
          <span class="title"><?php echo $t->name; ?></span>
        </label>        
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="group_lessons_body ">
    <div class="container">
      <div class="flexRow four">
        <?php foreach ($lessons as $lesson): ?>
        <?php $tags = array_map(function($t){ return 't'.$t; },empty(json_decode($lesson->tags)) ? [] : json_decode($lesson->tags)); ?>
        <div class="col <?php echo join(' ',$tags); ?>">
          <a class="item" href="/lessons/detail/<?php echo slugify($lesson->name)."-".$lesson->id; ?>" style="background-image:url('<?php echo $this->app->getMedia($lesson->photo_src,$lesson->photo_meta,true)['src']; ?>');">
            <h2 class="title"><?php echo $lesson->name; ?></h2>
            <!--<div class="priceContainer">
              <span class="l from">od</span>
              <span class="l price">140</span>
              <span class="l currency">Kč</span>
            </div>-->
            <span href="#" class="btn">
              <div class="bg"></div><span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                  viewBox="0 0 24 24">
                  <path d="M10.707 17.707L16.414 12 10.707 6.293 9.293 7.707 13.586 12 9.293 16.293z" /></svg></span>
            </span>
          </a>
        </div>
        <?php endforeach; ?>
        <!--<div class="col kondicni">
          <a class="item" href="/lectures/detail/1" style="background-image:url(<?php echo config_item('app')['img_folder']; ?>temp/froup_lessons_01.png);">
            <h2 class="title">Boxing</h2>
            <div class="priceContainer">
              <span class="l from">od</span>
              <span class="l price">140</span>
              <span class="l currency">Kč</span>
            </div>
            <span href="#" class="btn">
              <div class="bg"></div><span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                  viewBox="0 0 24 24">
                  <path d="M10.707 17.707L16.414 12 10.707 6.293 9.293 7.707 13.586 12 9.293 16.293z" /></svg></span>
            </span>
          </a>
        </div>-->
      </div>
    </div>
  </section>
</div>
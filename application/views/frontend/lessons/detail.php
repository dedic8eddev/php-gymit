<div class="subpage lecture">
  <section class="header header_v1" style="background-image:url('<?php echo $this->app->getMedia($lesson->photo_src,$lesson->photo_meta,true)['src']; ?>');">
    <div class="headerTopOverlay">
      <div class="container">
        <div class="headerContainer">
          <a href="/lessons" class="navigationBack">Skupinové lekce</a>
          <h2 class="headerTitle"><?php echo $lesson->name; ?></h2>
          <p class="headerDescription"><?php echo $lesson->description; ?></p>

          <!--<div class="headerPrice">
            Cena za lekci
            <span class="price">od 89,- Kč</span>
          </div>-->
          <div class="headerButtons">
            <a href="#" class="btn btn--big btn--transparent">
              <div class="bg"></div><span>Více o lekci</span>
            </a>
            <a href="#" class="btn btn--big">
              <div class="bg"></div><span>Zapsat se</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="lectureHalf">
    <div class="container">
      <div class="aboutLectureFrame">
        <section class="aboutLecture">
          <h3 class="sectionTitle">O lekci</h3>
          <div class="block">
            <h4 class="blockTitle">Kdy?</h4>
            <?php foreach ($comingLessons['period'] as $l): ?>
              <p><strong><?php echo config_item('app')['weekdaysCZ'][$l->weekday]; ?></strong> (<?php echo date("H:i", strtotime($l->starting_on)); ?> - <?php echo date("H:i", strtotime($l->ending_on)); ?>)</p>
            <?php endforeach; ?>
          </div>
          <div class="block">
            <h4 class="blockTitle"><?php echo $lesson->text_title; ?></h4>
            <?php echo $lesson->text; ?>
          </div>
        </section>
      </div>
      <div class="nextEventsFrame">
        <section class="nextEvents">
          <h3 class="sectionTitle">Nadcházející lekce</h3>
          <?php if(!empty($comingLessons['coming'])): ?>
          <div class="eventList">
            <?php foreach ($comingLessons['coming'] as $l): ?>
            <div class="event">
              <div class="eventDate">
                <span class="day"><?php echo date("j", strtotime($l->starting_on)); ?>.</span>
                <span class="month"><?php echo config_item('app')['monthsCZ'][date("n", strtotime($l->starting_on))]; ?></span>
                <span class="year"><?php echo date("Y", strtotime($l->starting_on)); ?></span>
              </div>  
              <div class="eventContent">
                <a href="#" class="btn">
                  <div class="bg"></div><span>Zeptat se</span>
                </a>
                <h4 class="title"><?php echo $l->name; ?></h4>
                <span class="hour"><?php echo date("H:i", strtotime($l->starting_on)); ?> - <?php echo date("H:i", strtotime($l->ending_on)); ?></span>
              </div>                        
            </div>
            <?php endforeach; ?>
          </div>
          <div class="showCalendar">
            <a href="#" class="btn btn--transparent">
              <div class="bg"></div><span>zobrazit Kalendář</span>
            </a>
          </div>
          <?php else: ?>
            <p>Momentálně nejsou naplánovány žádné lekce.  Registrujte se a mějte přehled lekcí vždy po ruce. </p>
            <div class="txt-center">
              <a href="/login" class="btn btn--transparent">
                <div class="bg"></div><span>Registrovat</span>
              </a>
            </div>
          <?php endif; ?>
        </section>
      </div>
    </div>
  </div>

  <section class="ourCoaches">
    <div class="container">
      <h2 class="sectionTitle">Trenéři lekce<span class="smallText">Effective forms advertising ninternet web
          site</span></h2>
      <div class="flexRow center third">
        <?php foreach($lessonTeachers as $t): ?>
        <div class="col">
          <div class="coachImg">
            <a href="osobni_trener_detail.html"><img <?php echo $this->app->getMedia($t->photo_src,$t->photo_meta); ?> /></a></div>
            <h3 class="coachName"><a href="osobni_trener_detail.html"><?php echo $t->first_name." ".$t->last_name; ?></a></h3>
            <p class="coachText"><?php echo $t->quote; ?></p>
        </div>
        <?php endforeach; ?>        
      </div>
    </div>
  </section>  
</div>
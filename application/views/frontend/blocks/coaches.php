<section class="ourCoaches">
    <div class="container">
      <h2 class="sectionTitle"><?php echo $options['coaches_title']; ?><span class="smallText"><?php echo $options['coaches_subtitle']; ?></span></h2>
      <div class="flexRow third">
        <?php foreach ($coaches['data'] as $coach): ?>
        <?php if (! $coach->visible) { continue; } ?>
        <div class="col">
          <div class="coachImg">
            <a href="/coaches/detail/<?php echo strtolower($coach->first_name)."-".strtolower($coach->last_name)."-".$coach->id; ?>"><img <?php echo $this->app->getMedia($coach->photo_src,$coach->photo_meta); ?> /></a>
          </div>
          <h3 class="coachName">
            <a href="/coaches/detail/<?php echo strtolower($coach->first_name)."-".strtolower($coach->last_name)."-".$coach->id; ?>">
              <?php echo $coach->first_name." ".$coach->last_name; ?>
            </a>
          </h3>
          <h4 class="coachPost"><?php echo $coach->quote; ?></h4>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
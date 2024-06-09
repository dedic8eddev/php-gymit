<div class="container">
  <?php if(!empty($data)): ?>
  <div class="calendarHead">
    <div class="row">
      <div class="col label"></div>
      <?php if(@$hp==1): ?>
      <div class="col today"><?php echo config_item('app')['weekdaysCZ'][date('N')-1]; ?>, <?php echo date('j.n.'); ?></div>
      <?php else:?>
      <div class="col">Pondělí, <?php echo date('j.n.', strtotime($from)); ?></div>
      <div class="col">Úterý, <?php echo date('j.n.', strtotime($from.' + 1 days')); ?></div>
      <div class="col">Středa, <?php echo date('j.n.', strtotime($from.' + 2 days')); ?></div>
      <div class="col">Čtvrtek, <?php echo date('j.n.', strtotime($from.' + 3 days')); ?></div>
      <div class="col">Pátek, <?php echo date('j.n.', strtotime($from.' + 4 days')); ?></div>
      <div class="col">Sobota, <?php echo date('j.n.', strtotime($from.' + 5 days')); ?></div>
      <div class="col">Neděle, <?php echo date('j.n.', strtotime($from.' + 6 days')); ?></div>
      <?php endif; ?>
    </div>
  </div>

  <div class="calendarBody">
    <?php foreach ($data as $hour => $days): ?>
    <div class="hour">
      <div class="row" data-hour="<?php echo $hour; ?>" data-min="0">
        <div class="col label"><?php echo $hour.":00"; ?></div>            
        <?php foreach ($days as $day => $lessons): ?>
        <div class="col day <?php echo @$hp==1?'today':'';?>" data-day="<?php echo $day; ?>">
          <?php if(isset($lessons)): ?>
          <?php foreach ($lessons as $id => $l): ?>
            <div class="box <?php echo $l['box_class']; ?>" 
              data-id="<?php echo $id; ?>"
              data-hour="<?php echo $hour; ?>" 
              data-min="<?php echo date('i', strtotime($l['starting_on'])); ?>" 
              data-coaches="<?php echo isset($l['coaches']) ? join(', ',$l['coaches']):''; ?>"
              data-img="<img width='150' height='200' <?php echo $this->app->getMedia($l['photo_src'],$l['photo_meta']); ?>>"
              data-clients="<?php echo $l['lesson_clients']; ?>"
              data-limit="<?php echo $l['client_limit']; ?>"
              data-start="<?php echo $l['starting_on'] ;?>"
              data-end="<?php echo $l['ending_on'] ;?>"
              data-registered="<?php echo $l['registered'] ?? 0; ?>">
              <div class="boxTitle"><?php echo $l['name']; ?></div>
              <div class="boxTime"><?php echo date('H:i', strtotime($l['starting_on'])).' - '.date('H:i', strtotime($l['ending_on'])); ?></div>
              <div class="boxCoach"><?php echo isset($l['coaches']) ? join(', ',$l['coaches']):''; ?></div>
            </div>                
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <h4 class="text-center margin__top--l">Bohužel neexistují žádné lekce s tímto filtrem</h4>
  <?php endif; ?>
</div>

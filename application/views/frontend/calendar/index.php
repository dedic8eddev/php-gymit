<div class="subpage calendar">
  <section class="header header_v2" style="background-image:url('<?php echo $page_calendar['header_image']; ?>');">
    <div class="headerTopOverlay">
      <div class="container">
        <h1 class="headerTitle"><?php echo $page_calendar['header_title']; ?></h1>
        <div class="headerFilter">
          <form>
            <div class="col">
              <label>Lekce</label>
              <select id="lessonSelect" class="customSelect">
                <option value="">Všechny</option>
                <?php foreach ($lessons as $l): ?>
                  <option value="<?php echo $l->id; ?>"><?php echo $l->name; ?></option> 
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col">
              <label>Trenér</label>
              <select id="coachSelect" class="customSelect">
                <option value="">Všichni</option>
                <?php foreach ($coaches['data'] as $c): ?>
                  <option value="<?php echo $c->id; ?>"><?php echo $c->first_name." ".$c->last_name; ?></option> 
                <?php endforeach; ?>
              </select>
            </div>

          </form>
        </div>
      </div>
    </div>
    <div class="headerFilterSecond">
      <div class="container">
        <div class="tabSwitcher">
          <ul>
            <li class="btnDayView">Den</li>
            <li class="btnWeekView active">Týden</li>
          </ul>
        </div>
        <div class="rangeSwitcher">
          <select id="weekSelect" class="customSelect">
            <?php for($i=0;$i<5;$i++): ?>
              <?php if(date('Y', strtotime('monday this week + '.($i*7).' days')) == date('Y', strtotime('sunday this week + '.($i*7).' days'))): ?>
              <option value="<?php echo date('Y-m-d', strtotime('monday this week + '.($i*7).' days'));?>"><?php echo date('j. n.', strtotime('monday this week + '.($i*7).' days')).' - '.date('j. n. Y', strtotime('sunday this week + '.($i*7).' days')); ?></option>
              <?php else: ?>
              <option value="<?php echo date('Y-m-d', strtotime('monday this week + '.($i*7).' days'));?>"><?php echo date('j. n.Y', strtotime('monday this week + '.($i*7).' days')).' - '.date('j. n. Y', strtotime('sunday this week + '.($i*7).' days')); ?></option>
              <?php endif; ?>
            <?php endfor; ?>
          </select>
        </div>
        <div class="statusFilter">
          <label class="customCheckbox available">
            <input type="checkbox" class="available" name="available" value="0" checked>
            <span class="checkmark"></span>
            <span class="title">Volná</span>
          </label>
          <label class="customCheckbox full">
            <input type="checkbox" class="full" name="available" value="2" checked>
            <span class="checkmark"></span>
            <span class="title">Obsazená</span>
          </label>
          <label class="customCheckbox finished">
            <input type="checkbox" class="finished" name="available" value="1" checked>
            <span class="checkmark"></span>
            <span class="title">Skončená / probíhající</span>
          </label>
        </div>
      </div>
    </div>
  </section>
  <section class="calendarSection">
    <?php $this->load->view('frontend/calendar/calendar',$calendar); ?>
  </section>
</div>

<?php $this->app_blocks->newsletter([]); ?>
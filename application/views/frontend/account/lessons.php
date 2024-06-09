<div class="subpage userPanel">
  <div class="container-custom flex">

  <?php $this->load->view('frontend/layout/account-menu'); ?>

    <section class="content">
      <h2 class="mobileTitle">Moje lekce</h2>
      <div class="block">
        <div class="blockIcon">
          <img src="public/assets/img/svg/ico_lecture_white.svg" alt="" />
        </div>
        <div class="blockTitle">
          <span>Vaše nadcházející lekce</span>
        </div>
        <div class="blockContent">
          <div class="eventList">
            <?php foreach($upcoming as $event): ?>
            <div class="event flex-middle">
              <div class="eventDate">
                <span class="day"><?php echo dateFromString($event->starting_on, 'j.'); ?></span>
                <span class="month"><?php echo config_item('app')['monthsCZ'][dateFromString($event->starting_on, 'n')]; ?></span>
                <span class="year"><?php echo dateFromString($event->starting_on, 'Y'); ?></span>
              </div>
              <div class="eventContent">
                <h3 class="title">
                    <?php echoEscapedHtml($event->name) ?>
                    <span class="">
                        10/<?php echoEscapedHtml($event->client_limit); ?>
                    </span>
                </h3>
                <ul class="meta">
                  <li>
                      <strong>Čas:</strong>
                      <?php echoEscapedHtml(timeFromString($event->starting_on)) ?> -
                      <?php echoEscapedHtml(timeFromString($event->ending_on)) ?>
                  </li>
                  <li><strong>Místnost:</strong> <?php echoEscapedHtml($event->room); ?></li>
                  <li><strong>Trenér:</strong> <?php echoEscapedHtml($event->first_name . ' ' . $event->last_name); ?></li>
                </ul>
                <ul class="links">
                  <li><a href="<?php echo url('lessons/detail', $event->name, $event->id)?>">Detail lekce</a></li>
                    <li>
                        <a href="#" data-start='<?php echo $event->staring_on; ?>' data-id='<?php echo $event->id; ?>' class='cancelReservation'>
                            Odhlásit se z lekce
                        </a>
                    </li>
                </ul>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="dBlock">
        <h3>Historie vašich lekcí</h3>
        <table class="lectures">
          <thead></thead>
          <tbody>
          <?php foreach($history['data'] as $historyEvent): ?>
            <tr>
              <td><?php echoEscapedHtml(dateFromString($historyEvent->starting_on)) ?>,<br />
                  <?php echoEscapedHtml(timeFromString($historyEvent->starting_on)) ?> -
                  <?php echoEscapedHtml(timeFromString($historyEvent->ending_on)) ?>
              </td>
              <td><?php echoEscapedHtml($historyEvent->name); ?></td>
              <td><a href="<?php echo url('lessons/detail', $historyEvent->name, $historyEvent->id)?>">Detail lekce</a></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>

        <?php $this->app_components->getPagination($history); ?>

        <div class="showAll">
          <a href="#" class="text">Celá historie (<?php echoEscapedHtml($history['count']); ?>)</a>
        </div>

      </div>

    </section>
  </div>
</div>
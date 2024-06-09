<div class="eventList">
    <?php foreach($events as $event): ?>
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
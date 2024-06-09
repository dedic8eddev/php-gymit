<div class="subpage userPanel">
  <div class="container-custom flex">

    <?php $this->load->view('frontend/layout/account-menu'); ?>

    <section class="content">
      <h2 class="mobileTitle">Platby</h2>
      <div class="block">
        <div class="blockIcon">
          <img src="public/assets/img/svg/ico_payment_white.svg" alt="" />
        </div>
        <div class="blockTitle">
          <span>Platby</span>
        </div>
        <div class="blockContent">
          <h3 class="border">Aktuální stav: <?php echo ($currentStateOfPayments > 0.0) ? echoPriceWithCurrency(-$currentStateOfPayments) : 'vše uhrazeno'; ?></h3>
          </p>
          <div class="hr"></div>
          <div class="paymentTable">
            <h4 class="smallGoldTitle">Nejbližší platby</h4>
            <table>
              <thead></thead>
              <tbody>
              <?php foreach($upcomingPayments as $payment): ?>
                <tr>
                  <td><?php echoEscapedHtml(dateFromString($payment->start)) ?></td>
                  <td><?php echoEscapedHtml($payment->text); ?></td>
                  <td><?php echoPriceWithCurrency($payment->value, $payment->currency); ?></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="dBlock">
        <h3>Historie vašich plateb</h3>
        <table class="payments">
          <thead></thead>
          <tbody>
          <?php foreach($historyPayments['data'] as $payment): ?>
              <tr>
                  <td><?php echoEscapedHtml(dateFromString($payment->paidOn)) ?></td>
                  <td><?php echoEscapedHtml($payment->text); ?></td>
                  <td><?php echoPriceWithCurrency($payment->value, $payment->currency); ?></td>
                  <td><a href="#">Faktura</a></td>
              </tr>
          <?php endforeach; ?>
          </tbody>
        </table>

        <?php $this->app_components->getPagination($historyPayments); ?>

        <div class="showAll">
          <a href="#" class="text">Celá historie (<?php echoEscapedHtml($historyPayments['count']); ?>)</a>
        </div>

      </div>


    </section>
  </div>
</div>
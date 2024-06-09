
<div class="subpage userPanel">
  <div class="container-custom flex">
    
    <?php $this->load->view('frontend/layout/account-menu'); ?>

    <section class="content">
      <h2 class="mobileTitle">Přehled</h2>
      <div class="block">
        <div class="blockIcon">
          <img src="assets/img/svg/ico_membership_white.svg" alt="" />
        </div>
        <div class="blockTitle">
          <span>Členství</span>
        </div>
        <div class="blockContent">
          <?php if ($subscription): ?>
          <h3>
              <?php echoEscapedHtml($subscription->name); ?> - platné do <?php echoEscapedHtml(dateFromString($subscription->end)); ?>
          </h3>
          <p><strong>Začátek členství:</strong> <?php echoEscapedHtml(dateFromString($subscription->createdOn)); ?></p>
          <?php endif; ?>

        </div>
        <div class="blockFooter">
          <a href="<?php echo url('account/membership'); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
              <path
                d="M7,17.013l4.413-0.015l9.632-9.54c0.378-0.378,0.586-0.88,0.586-1.414s-0.208-1.036-0.586-1.414l-1.586-1.586 c-0.756-0.756-2.075-0.752-2.825-0.003L7,12.583V17.013z M18.045,4.458l1.589,1.583l-1.597,1.582l-1.586-1.585L18.045,4.458z M9,13.417l6.03-5.973l1.586,1.586l-6.029,5.971L9,15.006V13.417z" />
              <path
                d="M5,21h14c1.103,0,2-0.897,2-2v-8.668l-2,2V19H8.158c-0.026,0-0.053,0.01-0.079,0.01c-0.033,0-0.066-0.009-0.1-0.01H5V5 h6.847l2-2H5C3.897,3,3,3.897,3,5v14C3,20.103,3.897,21,5,21z" />
            </svg> Nastavení členství</a>
        </div>
      </div>

      <div class="block">
        <div class="blockIcon">
          <img src="assets/img/svg/ico_lecture_white.svg" alt="" />
        </div>
        <div class="blockTitle">
          <span>Vaše nadcházející lekce</span>
        </div>
        <div class="blockContent">
            <?php $this->app_components->getEventList($upcomingLessons); ?>
        </div>
        <div class="blockFooter">
          <a href="<?php echo url('account/lessons'); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
              <path d="M12,9c-1.642,0-3,1.359-3,3c0,1.642,1.358,3,3,3c1.641,0,3-1.358,3-3C15,10.359,13.641,9,12,9z" />
              <path
                d="M12,5c-7.633,0-9.927,6.617-9.948,6.684L1.946,12l0.105,0.316C2.073,12.383,4.367,19,12,19s9.927-6.617,9.948-6.684 L22.054,12l-0.105-0.316C21.927,11.617,19.633,5,12,5z M12,17c-5.351,0-7.424-3.846-7.926-5C4.578,10.842,6.652,7,12,7 c5.351,0,7.424,3.846,7.926,5C19.422,13.158,17.348,17,12,17z" />
            </svg> Zobrazit moje lekce</a>
        </div>
      </div>

      <div class="block">
        <div class="blockIcon">
          <img src="assets/img/svg/ico_payment_white.svg" alt="" />
        </div>
        <div class="blockTitle">
          <span>Platby</span>
        </div>
        <div class="blockContent">
          <h3 class="border">
            Aktuální stav: <?php echo ($currentStateOfPayments > 0.0) ? echoPriceWithCurrency(-$currentStateOfPayments) : 'vše uhrazeno'; ?>
          </h3>
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
        <div class="blockFooter">
          <a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
              <path
                d="M7,17.013l4.413-0.015l9.632-9.54c0.378-0.378,0.586-0.88,0.586-1.414s-0.208-1.036-0.586-1.414l-1.586-1.586 c-0.756-0.756-2.075-0.752-2.825-0.003L7,12.583V17.013z M18.045,4.458l1.589,1.583l-1.597,1.582l-1.586-1.585L18.045,4.458z M9,13.417l6.03-5.973l1.586,1.586l-6.029,5.971L9,15.006V13.417z" />
              <path
                d="M5,21h14c1.103,0,2-0.897,2-2v-8.668l-2,2V19H8.158c-0.026,0-0.053,0.01-0.079,0.01c-0.033,0-0.066-0.009-0.1-0.01H5V5 h6.847l2-2H5C3.897,3,3,3.897,3,5v14C3,20.103,3.897,21,5,21z" />
            </svg> Zobrazit platby</a>
        </div>
      </div>
    </section>
  </div>
</div>

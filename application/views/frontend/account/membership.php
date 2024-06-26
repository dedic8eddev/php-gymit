<div class="subpage userPanel">
  <div class="container-custom flex">

    <?php $this->load->view('frontend/layout/account-menu'); ?>

    <section class="content">
      <h2 class="mobileTitle">Členství</h2>
      <div class="block">
        <div class="blockIcon">
          <img src="public/assets/img/svg/ico_membership_white.svg" alt="" />
        </div>
        <div class="blockTitle">
          <span>Členství <?php echoEscapedHtml($subscription ? $subscription->name: ''); ?></span>
        </div>
        <?php if ($subscription): ?>
        <div class="blockContent">
          <h3>Platné do <?php echoEscapedHtml(dateFromString($subscription->end)); ?></h3>
          <p><strong>Začátek členství:</strong> <?php echoEscapedHtml(dateFromString($subscription->createdOn)); ?></p>
          <div class="hr"></div>
          <h4 class="smallGoldTitle">Stav Vašeho účtu:</h4>
          <p class="pRow"><strong>Volné vstupy do wellness:</strong> 2 z 3<br>
            <strong>Volné vstupy do fitness zóny:</strong> neomezeně<br>
            <strong>Sleva na skupinové lekce:</strong> 10%</p>
          <div class="hr"></div>
          <a href="#" class="showContract"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
              viewBox="0 0 24 24">
              <path
                d="M8.267 14.68c-.184 0-.308.018-.372.036v1.178c.076.018.171.023.302.023.479 0 .774-.242.774-.651C8.971 14.9 8.717 14.68 8.267 14.68zM11.754 14.692c-.2 0-.33.018-.407.036v2.61c.077.018.201.018.313.018.817.006 1.349-.444 1.349-1.396C13.015 15.13 12.53 14.692 11.754 14.692z" />
              <path
                d="M14,2H6C4.896,2,4,2.896,4,4v16c0,1.104,0.896,2,2,2h12c1.104,0,2-0.896,2-2V8L14,2z M9.498,16.19 c-0.309,0.29-0.765,0.42-1.296,0.42c-0.119,0-0.226-0.006-0.308-0.018v1.426H7v-3.936C7.278,14.036,7.669,14,8.219,14 c0.557,0,0.953,0.106,1.22,0.319c0.254,0.202,0.426,0.533,0.426,0.923C9.864,15.634,9.734,15.965,9.498,16.19z M13.305,17.545 c-0.42,0.349-1.059,0.515-1.84,0.515c-0.468,0-0.799-0.03-1.024-0.06v-3.917C10.772,14.029,11.204,14,11.66,14 c0.757,0,1.249,0.136,1.633,0.426c0.415,0.308,0.675,0.799,0.675,1.504C13.968,16.693,13.689,17.22,13.305,17.545z M17,14.77 h-1.532v0.911h1.432v0.734h-1.432v1.604h-0.906v-3.989H17V14.77z M14,9c-0.553,0-1,0-1,0V4l5,5H14z" />
            </svg> Ukázat smlouvu</a>
        </div>
        <?php endif; ?>
      </div>
      <?php if ($subscription == null || strpos($subscription->subType, 'premium') === false): ?>
      <div class="dBlock">
        <div class="membershipHalf">
          <div class="half first">
            <h3>Získejte<br>premium členství se slevou 15%</h3>
            <p>Jako náš dlouhodobý člen máte možnost získat <strong>členství PREMIUM se všemi výhodami na příštích 12
                měsíců se
                slevou 15%.</strong></p>
          </div>
          <div class="half second gradientColor01">
            <div class="col premium">
              <h3 class="colTitle">Premium</h3>
              <div class="priceLabel">
                <span class="oldPrice">1.890 Kč</span>
                <span class="price">1.600</span><small class="currency">Kč</small>
                <div class="period">Měsíčně</div>
              </div>
              <div class="divider"></div>
              <div class="colDescription">
                <p><strong>50% sleva</strong> na skupinové lekce</p>
                <p><strong>Neomezený vstup</strong> do fitness zóny</p>
                <p><strong>Zdarma 10 vstupů</strong> měsíčně do wellness</p>
                <p>Registrační poplatek <strong>0 Kč</strong></p>
              </div>
              <div class="btnContainer"><a href="<?php echo url('membership/detail/platinum') ?>" class="btn btn--brown">
                  <div class="bg"></div><span>To chci</span>
                </a></div>
            </div>
          </div>
        </div>

      </div>
      <?php endif; ?>
    </section>
  </div>
</div>
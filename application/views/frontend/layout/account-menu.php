<section class="navigation">
      <div class="navigationContent">
        <h1 class="sectionTitle"><?php echo $this->__userData->first_name . ' ' . $this->__userData->last_name; ?></h1>
        <span class="userID"><strong>Členské ID:</strong> <?php echo '#'.$this->__userData->id; ?></span>
        <span class="openUserMenu js-openUserMenu">Menu</span>
        <nav class="menu">
          <ul>
            <li class="<?php echo ($this->router->fetch_method() == 'index') ? 'active' : '' ; ?>"><a href="/account">Přehled <img src="/public/assets/img/svg/ico_dashboard.svg" alt=""
                  width="34px" /></a></li>
            <li class="<?php echo ($this->router->fetch_method() == 'lessons') ? 'active' : '' ; ?>"><a href="/account/lessons">Moje lekce <img src="/public/assets/img/svg/ico_lecture.svg" alt=""
                  width="34px" /></a></li>
            <li class="<?php echo ($this->router->fetch_method() == 'membership') ? 'active' : '' ; ?>"><a href="/account/membership">Členství <img src="/public/assets/img/svg/ico_membership.svg" alt=""
                  width="34px" /></a></li>
            <li class="payment <?php echo ($this->router->fetch_method() == 'payments') ? 'active' : '' ; ?>"><a href="/account/payments">Platby <img src="/public/assets/img/svg/ico_payment.svg" alt=""
                  width="34px" /></a></li>
            <li class="<?php echo ($this->router->fetch_method() == 'settings') ? 'active' : '' ; ?>"><a href="/account/settings">Nastavení účtu <img src="/public/assets/img/svg/ico_settings.svg" alt=""
                  width="34px" /></a></li>
            <li class="logout"><a href="/logout"><img src="/public/assets/img/svg/bx-log-out-circle.svg" alt=""
                  width="25px" />
                Odhlásit
                se</a></li>
          </ul>
        </nav>
        <div class="infoBlock">
          V případě dotazů ohledně vašeho účtu nás kontaktujte na <a href="tel:+420123456789">+420 123 456 789</a> nebo
          na <a href="mailto:hello@csfitness.cz">hello@csfitness.cz</a>.
        </div>
      </div>
    </section>
<div class="subpage userPanel login">
  <div class="container-custom">
    <section class="content">
      <div class="block">
        <div class="blockIcon">
          <img src="public/assets/img/svg/ico_membership_white.svg" alt="" />
        </div>
        <div class="blockTitle">
          <span>Přihlášení do účtu</span>
        </div>
        <div class="blockContent">

            <div class="login-errors"><?php echo $this->session->flashdata('error'); ?></div>

            <?php echo form_open(); ?>
                <div class="fLine">
                    <label><strong>ID nebo přihlašovací E-mail:</strong><input type="text" name="email" value="" /></label>
                </div>
                <div class="fLine">
                    <label><strong>Heslo:</strong><input type="password" name="password" value="Vaše heslo" /></label>
                </div>
                <div class="fLine">
                <label class="customCheckbox">
                    <input type="checkbox" name="remember" value="0">
                    <span class="checkmark"></span>
                    Zapamatovat heslo
                </label>
                </div>
                <div class="fLine">
                    <div class="loginBtn">
                        <button class="btn btn--transparent" type="submit">
                        <div class="bg"></div><span>Přihlásit se</span>
                        </button>
                    </div>
                </div>
                <div class="fLine resetPassword">
                    <a href="/login/reset">Zapomněli jste heslo?</a>
                </div>
            <?php echo form_close(); ?>
        </div>
      </div>
      <?php if(!isset($site_settings) OR ( isset($site_settings) && is_null($site_settings->current_site) ) ){ ?>
      <div class="infoBlock">
          V případě dotazů ohledně vašeho účtu nebo v případě zájmu o přihlášení na lekci nás kontaktujte na<br><a href="tel:+420123456789">+420 123 456 789</a> nebo
        na <a href="mailto:hello@csfitness.cz">hello@csfitness.cz</a>.
      </div>
      <?php } ?>
    </section>
  </div>
</div>
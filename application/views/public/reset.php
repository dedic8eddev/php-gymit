<div class="subpage userPanel login">
  <div class="container-custom">
    <section class="content">
      <div class="block">
        <div class="blockIcon">
          <img src="public/assets/img/svg/ico_membership_white.svg" alt="" />
        </div>
        <div class="blockTitle">
          <span>Reset hesla k účtu</span>
        </div>
        <div class="blockContent">

            <div class="login-errors"><?php echo $this->session->flashdata('error'); ?></div>

            <form id="resetForm">
                <div class="fLine">
                    <label><strong>ID nebo přihlašovací E-mail:</strong><input type="text" name="email" value="" required /></label>
                </div>

                <div class="fLine">
                    <div class="loginBtn">
                        <button class="btn btn--transparent" id="submitReset" type="submit" data-ajax="<?php echo $sendResetLink; ?>">
                        <div class="bg"></div><span>Resetovat heslo</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
      </div>
      <div class="infoBlock">
        V případě dotazů ohledně vašeho účtu nás kontaktujte na<br><a href="tel:+420123456789">+420 123 456 789</a> nebo
        na <a href="mailto:hello@csfitness.cz">hello@csfitness.cz</a>.
      </div>
    </section>
  </div>
</div>
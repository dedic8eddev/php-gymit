<div class="subpage userPanel">
  <div class="container-custom flex">

    <?php $this->load->view('frontend/layout/account-menu'); ?>

    <section class="content">
      <h2 class="mobileTitle">Nastavení účtu</h2>
      <div class="block">
        <div class="blockIcon">
          <img src="public/assets/img/svg/ico_settings_white.svg" alt="" />
        </div>
        <div class="blockTitle">
          <span>Nastavení účtu</span>
        </div>
        <div class="blockContent">
          <span class="userID"><strong>Členské ID:</strong> 156</span>

          <h4 class="smallGoldTitle">Osobní a kontaktní údaje</h4>

          <div class="pRow settingRow js-containerSettings">
            <form id="updatePersonalInfo">
                <div class="lds-roller">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
                <div class="half">
                    <div class="fLine"><strong>Jméno:</strong>
                        <?php echo form_input('first_name', $userData->first_name, ['required' => true, 'disabled' => true]); ?>
                    </div>
                    <div class="fLine"><strong>Příjmení:</strong>
                        <?php echo form_input('last_name', $userData->last_name, ['required' => true, 'disabled' => true]); ?>
                    </div>
                    <div class="fLine"><strong>Datum narození:</strong>
                        <?php echo form_input('birth_date', $userData->birth_date, ['required' => false, 'disabled' => true]); ?>
                    </div>
                </div>
                <div class="half">
                    <div class="fLine"><strong>E-mail:</strong>
                        <?php echo form_input(['type' => 'email', 'name'=> 'email'], $userData->email, ['required' => false, 'disabled' => true]); ?>
                    </div>
                    <div class="fLine"><strong>Tel.č:</strong>
                        <?php echo form_input(['type' => 'phone', 'name' => 'phone',], $userData->phone, ['required' => false, 'disabled' => true]); ?>
                    </div>
                    <div class="fLine"><strong>Ulice:</strong>
                        <?php echo form_input('street', $userData->street, ['required' => false, 'disabled' => true]); ?>
                    </div>
                    <div class="fLine"><strong>Město:</strong>
                        <?php echo form_input('city', $userData->city, ['required' => false, 'disabled' => true]); ?>
                    </div>
                    <div class="fLine"><strong>PSČ:</strong>
                        <?php echo form_input('zip', $userData->zip, ['required' => false, 'disabled' => true]); ?>
                    </div>
                    <div class="fLine"><strong>Stát:</strong>
                        <?php $this->app_components->getSelect2Country(['input_name' => 'country','id' => 'country_id', 'selected' => $userData->country, 'required' => true, 'disabled' => true,]); ?>
                    </div>
                </div>

                <div class="enableEditSettings js-enableEditSettings"><a href="#"> Upravit údaje</a></div>

                <div class="saveEditSettings js-saveEditSettings" data-ajax="<?php echo $updatePersonalInfo; ?>">
                    <button type="submit" class="btn btn--transparent">
                        <div class="bg"></div>
                        <span> Potvrdit změny</span>
                    </button>
                </div>
            </form>
            </div>
          <div class="hr hr--small hr--transparent"></div>

          <h4 class="smallGoldTitle">Upozornění</h4>

          <div class="pRow settingRow js-containerSettings">
            <form>
              <div class="lds-roller">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
              </div>
              <div class="">
                <div class="fLine">
                  <label class="customCheckbox">
                    <input type="checkbox" name="41243" value="0" disabled>
                    <span class="checkmark"></span>
                    Zasílat upozornění o platbách
                  </label></div>
                <div class="fLine">
                  <label class="customCheckbox">
                    <input type="checkbox" name="0005" value="0" disabled>
                    <span class="checkmark"></span>
                    Zasílat novinky z oblasti fitness a zdravého životního stylu
                  </label></div>
                <div class="fLine">
                  <label class="customCheckbox">
                    <input type="checkbox" name="0005" value="0" disabled>
                    <span class="checkmark"></span>
                    Zasílat speciální akční nabídky v Gymit premium fitness určené pro Vás
                  </label></div>
              </div>

              <div class="enableEditSettings js-enableEditSettings"><a href="#"> Změnit nastavení</a></div>

              <div class="saveEditSettings js-saveEditSettings" data-ajax="<?php echo $updateNotifications; ?>">
                  <button type="submit" class="btn btn--transparent">
                      <div class="bg"></div>
                      <span> Potvrdit změny</span>
                  </button>
              </div>
            </form>
          </div>


          <div class="hr hr--small hr--transparent"></div>

          <h4 class="smallGoldTitle">Zabezpečení</h4>

          <div class="pRow settingRow js-containerSettings">
            <form>
              <div class="lds-roller">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
                <div></div>
              </div>
              <div class="">
                <div class="fLine x"><strong>Přihlašovací E-mail:</strong>
                    <?php echo form_input('username', $user->username, ['disabled' => true]); ?>
                    <div class="hit">Na tomto emaily Vás budeme informovat o změnách citlivých údajů na Vašem
                    účtu.</div>
                </div>
                <div class="fLine x"><strong>Aktuální heslo:</strong>
                    <?php echo form_input(['type' => 'password', 'name'=> 'current_password'], '', ['disabled' => true]); ?>
                </div>
                <div class="fLine x"><strong>Nové heslo:</strong>
                    <?php echo form_input(['type' => 'password', 'name'=> 'new_password'], '', ['disabled' => true]); ?>
                </div>
              </div>

              <div class="enableEditSettings js-enableEditSettings"><a href="#"> Upravit údaje</a></div>

              <div class="saveEditSettings js-saveEditSettings" data-ajax="<?php echo $updateSecurity; ?>">
                  <button type="submit" class="btn btn--transparent">
                      <div class="bg"></div>
                      <span> Potvrdit změny</span>
                  </button>
              </div>
            </form>
          </div>

        </div>
      </div>
    </section>
  </div>
</div>
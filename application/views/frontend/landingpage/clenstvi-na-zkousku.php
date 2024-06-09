<div class="subpage contact membership">
  <section class="header header_v2" style="background-image:url('/public/landingpage/img/header_default.png');">
    <div class="headerTopOverlay small">
      <div class="container">
        <div class="headerContainer">
          <a href="<?php echo base_url(); ?>p/predprodej/clenstvi-a-cenik" class="navigationBack">Členství a ceník</a>
          <h1 class="headerTitle">Členství Na zkoušku</h1>
        </div>
      </div>
    </div>
    <div class="headerBottomOverlay">
      <div class="container">
        <div class="flexRow four mb0">
          <div class="col anim1-150 js-tabTitle active" data-tab="0">
            <h3 class="cTitle">Na zkoušku</h3>
            <div class="cPrice">954<span><strong>Kč</strong> na měsíc</span></div>
            <div class="arrow anim1-150"></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="contact_body">
    <div class="container">
        <div class="left">
          <div class="js-tabContent" data-tab="0">
            <div class="wysiwygContent">
              <h3 class="cTitle">Na zkoušku</h3>
              <ul>
                <li>Neomezený vstup do posilovny, na skupinové lekce a wellness</li>
                <li>V ceně členství nabízíme ručníkový servis</li>
                <li>Platnost členství od zakoupení 30 dní. Pokud klient členství neprodlouží z karty se automaticky stává dobíjecí karta.</li>
              </ul>
              <h3 class="cTitle">U VŠECH DRUHŮ ČLENSTVÍ SI ČLEN MŮŽE URČIT POČÁTEK ČLENSTVÍ </h3>
              <img src="<?php echo base_url(); ?>public/landingpage/img/darek_detail_zkouska.jpg" alt="" />
            </div>
          </div>
        </div>
        <div class="right">
        <form class="contactForm" method="post" action="<?php echo $subSubmitUrl; ?>" data-url="<?php echo $subSubmitUrl; ?>">
          <div class="row">
            <div class="col label">Platba za členství</div>
              <div class="col">
                <div class="input">
                  <select name="membership_id" class="styleSelect" id="membershipSelect" aria-invalid="false">
                    <option data-price="954" value="15">Trial (Zkušební členství)</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col label">Osobní údaje</div>
              <div class="col">
                <div class="input">
                  <span class="placeholder"><span class="name">Jméno</span></span>
                  <input type="text" name="first_name" placeholder="Jméno" required />
                </div>
              </div>
              <div class="col">
                <div class="input">
                  <span class="placeholder"><span class="name">Příjmení</span></span>
                  <input type="text" name="last_name" placeholder="Příjmení" required />
                </div>
              </div>                
              <div class="col">
                <div class="input">
                  <span class="placeholder"><span class="name">E-mail</span></span>
                  <input type="email" name="email" placeholder="E-mail" required />
                </div>
              </div>
              <div class="col">
                <div class="input">
                  <span class="placeholder"><span class="name">Telefon</span></span>
                  <input type="tel" name="phone" placeholder="Telefon" required />
                </div>
              </div>        
            </div>

            <div class="row">
              <div class="col label">K úhradě</div>
              <div class="col">
                <table class="summary">
                  <tfoot>
                    <tr>
                      <td>Celkově</td>
                      <td id="totalPrice">2 850 Kč</td>
                    </tr>
                  </tfoot>
                </table>
                <div class="summaryInfo">
                  <p>Poplatky za předprodej členství je možné nyní hradit platební či kreditní kartou. Po zakoupení členství Vám na e-mail zašleme bližší informace ohledně dalšího postupu a aktivace členství.</p>
                  <p>V případě dotazů nás kontaktujte na <a href="tel:++420702051943">+420 702 051 943</a></p>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col col1">
                <label class="customCheckbox">
                  <input type="checkbox" name="reason" value="0" required>
                  <span class="checkmark"></span>
                  <span class="title">Souhlasím se <a href="<?php echo base_url(); ?>public/assets/files/Gymit_GDPR.pdf" target="_blank" style="color:#604e2f;">zpracováním osobních údajů</a></span>                
                </label>
              </div>
              <div class="col col1">
                <label class="customCheckbox">
                  <input type="checkbox" name="reason" value="0" required>
                  <span class="checkmark"></span>
                  <span class="title">Potvrzuji že jsem se seznámil se <a href="<?php echo base_url(); ?>public/assets/files/Gymit_SmluvniPodminky.pdf" target="_blank" style="color:#604e2f;">smluvními podmínkami</a> týkajících se členství v
                    Gymit premium fitness</span>
                </label>
              </div>
            </div>

            <div class="row">
              <div class="col col1">
                <div class="form-errors"></div>
                <div class="message-alert-ok"></div>
                <button type="submit" class="btn btn--brown submitBtn">
                  <div class="bg"></div><span>Zaplatit online</span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>
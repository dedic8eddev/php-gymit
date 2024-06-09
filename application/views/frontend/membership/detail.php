<div class="subpage contact membership">
  <section class="header header_v2" style="background-image:url('<?php echo site_url(config_item('app')['img_folder'].'header_default.png'); ?>');">
    <div class="headerTopOverlay small">
      <div class="container">
        <div class="headerContainer">
          <a href="/membership" class="navigationBack">Členství a ceník</a>
          <h1 class="headerTitle"><?php echo $memberships[0]->type_name; ?></h1>
        </div>
      </div>
    </div>
    <div class="headerBottomOverlay">
      <div class="container">
        <?php if(count($memberships)>1): ?>
        <?php $flexClass = count($memberships)==3 ? 'third' : 'four'; ?>
        <div class="flexRow <?php echo $flexClass; ?> mb0">
        <?php $i=0; foreach ($memberships as $m): $i++; ?>
          <div class="col anim1-150 js-tabTitle <?php echo $i==1 ? 'active':''; ?>" data-tab="<?php echo $m->id; ?>">
            <h3 class="cTitle"><?php echo @$m->data->header_title; ?></h3>
            <div class="cPrice"><?php echo $m->price; ?><span><strong>Kč</strong> měsíčně</span></div>
            <div class="arrow anim1-150"></div>
          </div>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <section class="contact_body">
    <div class="container">
      <?php $i=0; foreach ($memberships as $m): $i++; ?>
      <div class="js-tabContent" data-tab="<?php echo $m->id; ?>" <?php if($i>1) echo 'style="display:none;"'; ?>>
        <div class="left">
          <div class="wysiwygContent">
            <h3 class="cTitle"><?php echo @$m->data->title_1; ?></h3>
            <?php echo @$m->data->text_1; ?>
            <h3 class="cTitle"><?php echo @$m->data->title_2; ?></h3>
            <h4 class="cTitleFree"><?php echo @$m->data->subtitle_2; ?></h4>
            <?php echo @$m->data->text_2; ?>
          </div>
        </div>
        <div class="right">
          <form class="contactForm">
            <h2 class="big">Zakupte si členství</h2>
            <h3 class="small">z pohodlí vašeho domova</h3>

            <div class="row">
              <div class="col label">Platba za členství</div>
              <div class="col">
                <div class="input">
                  <div class="selectedPayment"><strong>Měsíční platba</strong> (900 Kč měsíčně)</div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col label">Osobní údaje</div>
              <div class="col col1-2">
                <div class="input">
                  <span class="placeholder"><span class="name">Jméno</span></span>
                  <input type="text" name="firstName" placeholder="Jméno" required />
                </div>
              </div>
              <div class="col col1-2">
                <div class="input">
                  <span class="placeholder"><span class="name">Příjmení</span></span>
                  <input type="text" name="lastName" placeholder="Příjmení" required />
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col col1-2">
                <div class="input">
                  <span class="placeholder"><span class="name">E-mail</span></span>
                  <input type="email" name="email" placeholder="E-mail" required />
                </div>
              </div>
              <div class="col col1-2">
                <div class="input">
                  <span class="placeholder"><span class="name">Tel. č</span></span>
                  <input type="text" name="phone" placeholder="Tel. č" />
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col label">Adresa</div>
              <div class="col">
                <div class="input">
                  <span class="placeholder"><span class="name">Ulice, číslo</span></span>
                  <input type="text" name="firstName" placeholder="Ulice, číslo" required />
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col col1-2">
                <div class="input">
                  <span class="placeholder"><span class="name">Jméno</span></span>
                  <input type="text" name="firstName" placeholder="Jméno" required />
                </div>
              </div>
              <div class="col col1-2">
                <div class="input">
                  <span class="placeholder"><span class="name">Příjmení</span></span>
                  <input type="text" name="lastName" placeholder="Příjmení" required />
                </div>
              </div>
            </div>


            <div class="row">
              <div class="col label">K úhradě</div>
              <div class="col">
                <table class="summary">
                  <tbody>
                    <tr>
                      <td>Členský poplatek 1. měsíc</td>
                      <td>990 Kč</td>
                    </tr>
                    <tr>
                      <td>Členský poplatek 1. měsíc</td>
                      <td>990 Kč</td>
                    </tr>
                    <tr>
                      <td>Členský poplatek 1. měsíc</td>
                      <td>990 Kč</td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td>Celkově</td>
                      <td>2 850 Kč</td>
                    </tr>
                  </tfoot>
                </table>
                <div class="summaryInfo">
                  <p>Poplatky za členství je možné hradit v hotovosti, bankovním převodem nebo kreditní kartou. Po
                    odeslání
                    objednávky Vám ná e-mail zašleme bližší informace ohledně nastavení plateb a Vašeho účtu.</p>
                  <p>V případě dotazů nás kontaktujte na <a href="tel:+421123456789">+421 123 456 789</a></p>
                </div>
              </div>
            </div>



            <div class="row">
              <div class="col col1">
                <label class="customCheckbox">
                  <input type="checkbox" name="reason" value="0" required>
                  <span class="checkmark"></span>
                  <span class="title">Souhlasím se zpracováním osobních údajů</span>
                </label>
              </div>
              <div class="col col1">
                <label class="customCheckbox">
                  <input type="checkbox" name="reason" value="0" required>
                  <span class="checkmark"></span>
                  <span class="title">Potvrzuji že jsem se seznámil se smluvními podmínkami týkajících se členství v
                    Gymit premium fitness</span>
                </label>
              </div>
            </div>

            <div class="row">
              <div class="col col1">
                <div class="form-errors"></div>
                <div class="message-alert-ok"></div>
                <button class="btn btn--brown submitBtn">
                  <div class="bg"></div><span>Odeslat</span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
      <div class="js-tabContent" data-tab="1" style="display:none;">contact 01</div>
      <div class="js-tabContent" data-tab="2" style="display:none;">contact 02</div>
      <div class="js-tabContent" data-tab="3" style="display:none;">contact 03</div>
    </div>
  </section>
</div>
<section class="footerNewsletter">
  <div class="container">
    <a href="#" class="btn btn--brown">
      <div class="bg"></div><span>Chci být členem</span>
    </a>
    <div class="sectionTitle">Stante se členem <span class="smallText">od <strong>890</strong> Kč měsíčně</span></div>
    <div class="newsletterDivider">Gymit premium fitness</div>
  </div>
</section>
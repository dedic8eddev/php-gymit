<div class="subpage lecture contact">
  <section class="header header_v2" style="background-image:url('<?php echo $page_contact['header_image']; ?>');">
    <div class="headerTopOverlay">
      <div class="container">
        <div class="headerContainer">
          <h1 class="headerTitle"><?php echo $page_contact['header_title']; ?></h1>
        </div>
      </div>
    </div>
    <div class="headerBottomOverlay">
      <div class="container">
        <div class="flexRow third mb0">
          <div class="col">
            <h3 class="cTitle"><?php echo $page_contact['header_contact_title']; ?></h3>
            <ul class="iconList">
              <li class="phone"><a href="tel:<?php echo $this->gymSettings['general_info']['data']['phone']; ?>"><?php echo $this->gymSettings['general_info']['data']['phone']; ?></a></li>
              <li class="email"><a href="mailto:<?php echo $this->gymSettings['general_info']['data']['email']; ?>"><?php echo $this->gymSettings['general_info']['data']['email']; ?></a></li>
              <li class="messanger"><a href="#">Napište nám</a></li>
            </ul>
          </div>
          <div class="col">
            <h3 class="cTitle"><?php echo $page_contact['header_location_title']; ?></h3>
            <ul class="iconList">
              <li class="map">
                Gymit Premium Fitness<br><?php echo $this->gymSettings['general_info']['data']['street']; ?><br><?php echo $this->gymSettings['general_info']['data']['zip']." ".$this->gymSettings['general_info']['data']['city']; ?><br><br>
                <a href="https://www.google.com/maps?q=<?php echo $this->gymSettings['general_info']['data']['street'].", ".$this->gymSettings['general_info']['data']['zip']." ".$this->gymSettings['general_info']['data']['city']; ?>">Ukázat na mapě</a>
              </li>
            </ul>
          </div>
          <div class="col">
            <h3 class="cTitle"><?php echo $page_contact['header_opening_hours_title']; ?></h3>
            <ul class="iconList">
              <li class="clock"><strong>Po - Pá:</strong> <?php echo $this->gymSettings['opening_hours']['data']['monday']['from']; ?> - <?php echo $this->gymSettings['opening_hours']['data']['monday']['to']; ?>
              <br><strong>So - Ne:</strong> <?php echo $this->gymSettings['opening_hours']['data']['saturday']['from']; ?> - <?php echo $this->gymSettings['opening_hours']['data']['saturday']['to']; ?></li>
            </ul>

          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="contact_body">
    <div class="container">
      <div class="left">
        <h3 class="cTitle"><?php echo $page_contact['find_us_title']; ?></h3>
        <?php echo $page_contact['find_us_text']; ?>
        <p>
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path d="M21.004,7.975V6c0-2.206-1.794-4-4-4h-10c-2.206,0-4,1.794-4,4v1.998C2.961,8,2.928,8.002,2.928,8.002 C2.404,8.04,2,8.475,2,9v2c0,0.552,0.447,1,1,1h0.004v6c0,0.735,0.403,1.372,0.996,1.72V21c0,0.553,0.447,1,1,1h1 c0.553,0,1-0.447,1-1v-1h10v1c0,0.553,0.447,1,1,1h1c0.553,0,1-0.447,1-1v-1.276c0.597-0.347,1.004-0.985,1.004-1.724v-6 c0.553,0,1-0.448,1-1V9.062c0.011-0.153-0.012-0.309-0.072-0.455C21.729,8.12,21.297,8.003,21.004,7.975z M19.006,18H19h-1H6H5.004 v-4h14.001L19.006,18z M5.004,12V8h6v4H5.004z M13.004,12V8h6v0.998c0,0.001,0,0.001,0,0.002v2c0,0.001,0.001,0.002,0.001,0.004V12 H13.004z M7.004,4h10c1.103,0,2,0.897,2,2h-14C5.004,4.897,5.901,4,7.004,4z" /><path d="M6.004 15H8.004V17H6.004zM16.004 15H18.004V17H16.004z" /></svg> 
          <?php echo $page_contact['find_us_bus']; ?> 
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><circle cx="13" cy="4" r="2" /><path d="M13.978,12.27c0.245,0.368,0.611,0.647,1.031,0.787l2.675,0.892l0.633-1.896l-2.675-0.892l-1.663-2.495 c-0.192-0.288-0.457-0.521-0.769-0.679L11.776,7.27c-0.425-0.212-0.913-0.267-1.378-0.149L7.205,7.918 C6.639,8.059,6.163,8.439,5.899,8.964l-1.794,3.589l1.789,0.895l1.794-3.589l2.223-0.556L8.09,17.726l-2.766,2.537l1.352,1.475 L9.441,19.2c0.307-0.281,0.515-0.645,0.604-1.052l0.533-2.465l2.517,1.888l0.925,4.625l1.961-0.393l-0.925-4.627 c-0.099-0.484-0.369-0.913-0.762-1.206l-2.171-1.628l0.647-3.885L13.978,12.27z" /></svg> 
          <?php echo $page_contact['find_us_bus_walk']; ?>
        </p>
        <p>
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path d="M20.772,10.156l-1.368-4.105C18.995,4.824,17.852,4,16.559,4H7.441C6.148,4,5.005,4.824,4.596,6.051l-1.368,4.105 C2.508,10.459,2,11.171,2,12v5c0,0.753,0.423,1.402,1.039,1.743C3.026,18.809,3,18.869,3,18.938V21c0,0.553,0.447,1,1,1h1 c0.553,0,1-0.447,1-1v-2h12v2c0,0.553,0.447,1,1,1h1c0.553,0,1-0.447,1-1v-2.062c0-0.069-0.026-0.13-0.039-0.195 C21.577,18.402,22,17.753,22,17v-5C22,11.171,21.492,10.459,20.772,10.156z M4,17v-5h16l0.002,5H4z M7.441,6h9.117 c0.431,0,0.813,0.274,0.949,0.684L18.613,10H5.387l1.105-3.316C6.629,6.274,7.011,6,7.441,6z"/><circle cx="6.5" cy="14.5" r="1.5"/><circle cx="17.5" cy="14.5" r="1.5"/></svg>
          <?php echo $page_contact['find_us_car']; ?> 
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><circle cx="13" cy="4" r="2" /><path d="M13.978,12.27c0.245,0.368,0.611,0.647,1.031,0.787l2.675,0.892l0.633-1.896l-2.675-0.892l-1.663-2.495 c-0.192-0.288-0.457-0.521-0.769-0.679L11.776,7.27c-0.425-0.212-0.913-0.267-1.378-0.149L7.205,7.918 C6.639,8.059,6.163,8.439,5.899,8.964l-1.794,3.589l1.789,0.895l1.794-3.589l2.223-0.556L8.09,17.726l-2.766,2.537l1.352,1.475 L9.441,19.2c0.307-0.281,0.515-0.645,0.604-1.052l0.533-2.465l2.517,1.888l0.925,4.625l1.961-0.393l-0.925-4.627 c-0.099-0.484-0.369-0.913-0.762-1.206l-2.171-1.628l0.647-3.885L13.978,12.27z" /></svg> 
          <?php echo $page_contact['find_us_car_walk']; ?>
        </p>        
        <br>
        <h3 class="cTitle"><?php echo $page_contact['operator_title']; ?></h3>
        <?php echo $page_contact['operator_text']; ?>
      </div>
      <div class="right">
        <form class="contactForm">
          <h2 class="big"><?php echo $page_contact['contact_form_title']; ?></h2>
          <h3 class="small"><?php echo $page_contact['contact_form_subtitle']; ?></h3>

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
            <div class="col col1">
              <div class="textarea">
                <textarea type="text" name="price" placeholder="Jak Vám mužeme pomoci?"></textarea>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col col1">
              <label class="customCheckbox">
                <input type="checkbox" name="reason" value="0" required>
                <span class="checkmark"></span>
                <span class="title">Odesláním souhlasím se zpracováním svých výše uvedených kontaktních údajů pro účely
                  mého
                  zpětného oslovení, dle podmínek zpracování osobních údajů a GDPR.</span>
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
  </section>


</div>

<?php $this->app_blocks->newsletter([]); ?>
<div class="subpage personal_coach_detail">
  <section class="header header_v2" style="background-image:url('<?php echo base_url(config_item('app')['img_folder'].'header_default.png');?>">
    <div class="headerTopOverlay">
      <div class="container">
        <a href="#" class="navigationBack">osobní trenéři</a>
        <h1 class="headerTitle"><?php echo $users_data->first_name." ".$users_data->last_name; ?></h1>
      </div>
    </div>
  </section>

  <section class="coach_body">
    <div class="container">
      <div class="left">
        <div class="coachImg"><img <?php echo $this->app->getMedia($users_data->photo_src,$users_data->photo_meta); ?> /></div>
        <div class="coachContact">
          <a href="tel:<?php echo $users_data->phone; ?>" class="box">
            <div class="icon">
              <svg version="1.1" id="Layer_2" xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 365.5 362.5"
                style="enable-background:new 0 0 365.5 362.5;" xml:space="preserve">
                <path d="M355,293l-68.4-68.4c-3.3-3.3-7.7-5.1-12.4-5.1c-4.7,0-9.1,1.8-12.4,5.1l-32,32c-2.6,2.6-6.5,3.7-10.1,2.7
               c-24.9-6.6-52.7-23-72.8-42.8c-19.8-20.1-36.2-47.9-42.8-72.8c-1-3.6,0.1-7.5,2.7-10.1l32-32c3.3-3.3,5.1-7.7,5.1-12.4
               c0-4.7-1.8-9.1-5.1-12.4L70.5,8.5c-3.3-3.3-7.7-5.1-12.4-5.1c-4.7,0-9.1,1.8-12.4,5.1L19.1,35.2C8.5,45.8,3.2,60.1,4.6,74.5
               c5.1,55.6,47.2,135.8,97.9,186.5c-0.2-0.2-0.3-0.4-0.4-0.5l2.9-2l-2.3,2.7l0.2,0.2C153.5,312,233.6,353.9,289,359
               c1.4,0.1,2.9,0.2,4.3,0.2c12.9,0,25.6-5.4,35-14.7l26.7-26.7c3.3-3.3,5.1-7.7,5.1-12.4C360.1,300.7,358.3,296.3,355,293z M350,312.7
               l-26.7,26.7c-8.9,8.9-21.5,13.6-33.6,12.5c-54-4.9-132.1-46-181.8-95.5l-0.3-0.3l-0.1-0.1c-49.7-49.7-90.9-128-95.8-182.2
               C10.5,61.6,15,49.3,24.1,40.2l26.7-26.7c2-2,4.6-3,7.4-3c2.8,0,5.4,1.1,7.4,3l68.4,68.4c2,2,3,4.6,3,7.4c0,2.8-1.1,5.4-3,7.4l-32,32
               c-4.4,4.4-6.1,10.9-4.5,17c6.9,26,24,55.1,44.6,76c20.9,20.6,50,37.7,76,44.6c1.5,0.4,3,0.6,4.5,0.6c4.7,0,9.1-1.8,12.4-5.1l32-32
               c2-2,4.6-3,7.4-3c2.8,0,5.4,1.1,7.4,3L350,298c2,2,3,4.6,3,7.4C353,308.1,352,310.8,350,312.7z" />
              </svg>
            </div>
            <span class="text"><?php echo $users_data->phone; ?></span>
          </a>
          <a href="mailto:<?php echo $coach->email; ?>" class="box">
            <div class="icon">
              <svg version="1.1" id="Layer_2" xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 370.3 260.7"
                style="enable-background:new 0 0 370.3 260.7;" xml:space="preserve">
                <path d="M361.8,3.1H8.1H2.6v254.8h365.3V3.1H361.8z M353.2,10.2L184.9,137.6L16.6,10.2H353.2z M9.6,250.8V13.7
             l175.3,132.7L360.8,13.4v237.5H9.6z" />
              </svg>
            </div>
            <span class="text"><?php echo $coach->email; ?></span>
          </a>
        </div>
        <div class="coachReviewsSlider">
          <div>
            <p class="text"><?php echo $coach_data->quote; ?></p>
          </div>
          <div>test</div>
          <div>test</div>
        </div>    
      </div>
      <div class="right">
        <?php echo @$coach_data->about; ?>
        <h3>Specializace</h3>
        <ul class="dotList">
          <?php foreach ($coach_specializations as $s): ?>
            <li><?php echo $s['specialization_name'];?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </section>
</div>
<?php $this->app_blocks->newsletter([]); ?>
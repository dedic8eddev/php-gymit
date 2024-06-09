<div class="subpage work_in_gymit">
  <section class="header header_v2" style="background-image:url('<?php echo $page_job_detail['header_image']; ?>');">
    <div class="headerTopOverlay small">
      <div class="container">
        <a href="/jobs" class="navigationBack">Práce v gymitu</a>
        <h1 class="headerTitle"><?php echo $job['title']; ?></h1>
      </div>
    </div>
  </section>

  <section class="work_in_gymit_body detail">
    <div class="container">
      <div class="flexRow half">
        <div class="col">
          <div class="jobRequirements">
            <ul>
              <?php if(!empty($requirements['education'])): ?>
              <li><span class="ico school">
                  <img src="<?php echo site_url(config_item('app')['img_folder'].'svg/ico_job_req_school.svg'); ?>" alt="" />
                </span>
                <span class="text"><span>
                    <div class="title">Požadované vzdělání</div>
                    <div class="desc"><?php echo join(', ',$requirements['education']); ?></div>
                  </span></span>
              </li>
              <?php endif; ?>
              <li>
              <?php if(!empty($requirements['practice'])): ?>
              <li><span class="ico practice">
                  <img src="<?php echo site_url(config_item('app')['img_folder'].'svg/ico_job_req_practice.svg'); ?>" alt="" />
                </span>
                <span class="text"><span>
                    <div class="title">Požadovaná praxe</div>
                    <div class="desc"><?php echo join(', ',$requirements['practice']); ?></div>
                  </span></span>
              </li>
              <?php endif; ?>
              <li>
              <?php if(!empty($requirements['obligation'])): ?>
              <li><span class="ico workingtime">
                  <img src="<?php echo site_url(config_item('app')['img_folder'].'svg/ico_job_req_workingtime.svg'); ?>" alt="" />
                </span>
                <span class="text"><span>
                    <div class="title">Úvazek</div>
                    <div class="desc"><?php echo join(', ',$requirements['obligation']); ?></div>
                  </span></span>
              </li>
              <?php endif; ?>
              <li>
              <?php if(!empty($requirements['income'])): ?>
              <li><span class="ico pay">
                  <img src="<?php echo site_url(config_item('app')['img_folder'].'svg/ico_job_req_pay.svg'); ?>" alt="" />
                </span>
                <span class="text"><span>
                    <div class="title">Plat</div>
                    <div class="desc"><?php echo join(', ',$requirements['income']); ?></div>
                  </span></span>
              </li>
              <?php endif; ?>
              <li>
            </ul>
          </div>
        </div>
        <div class="col last">
          <div class="wysiwygContent"><?php echo $job['text']; ?></div>
        </div>
      </div>
      <div class="jobFooter detail">
        <h2 class="sectionTitle"><?php echo $page_job_detail['hire_title']; ?></h2>
        <a href="mailto:<?php echo $page_job_detail['hire_email']; ?>" class="contact"><?php echo $page_job_detail['hire_email']; ?></a>
      </div>
    </div>
  </section>
</div>
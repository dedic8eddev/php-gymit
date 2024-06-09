<div class="subpage work_in_gymit">
    <section class="header header_v2" style="background-image:url('<?php echo $page_jobs['header_image']; ?>');">
        <div class="headerTopOverlay small">
            <div class="container">
                <h1 class="headerTitle"><?php echo $page_jobs['header_title']; ?></h1>
            </div>
        </div>
    </section>

    <section class="work_in_gymit_body">
        <h2 class="sectionTitle"><?php echo $page_jobs['jobs_title']; ?><span class="smallText"><?php echo $page_jobs['jobs_subtitle']; ?></span></h2>
        <div class="container">
            <div class="flexRow default third">
                <?php if (!empty($jobs['data'])): ?>
                    <?php foreach ($jobs['data'] as $j): ?>
                    <div class="col serviceItem">
                        <div class="jobBox">
                            <div class="ico">
                                <img src="<?php echo config_item('app')['img_folder'].'svg/ico_job_0'.$j->icon_image.'_white.svg'; ?>" alt="">
                            </div>
                            <h3 class="boxTitle"><?php echo $j->title; ?></h3>
                            <p><?php echo $j->perex; ?></p>
                            <a href="/jobs/detail/<?php echo slugify($j->title)."-".$j->id; ?>" class="btn btn--brown"><div class="bg"></div><span>Mám zájem</span></a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Momentálně nejsou k dispozici žádné pracovní nabídky</p>
                <?php endif; ?>
            </div>
            <div class="jobFooter">
                <h2 class="sectionTitle"><?php echo $page_jobs['another_job_title']; ?>
                    <span class="smallText"><?php echo $page_jobs['another_job_subtitle']; ?></span>
                </h2>
                <div class="desc"><?php echo $page_jobs['another_job_text']; ?></div>
                <a href="mailto:<?php echo $page_jobs['another_job_email']; ?>" class="contact"><?php echo $page_jobs['another_job_email']; ?></a>
            </div>
        </div>
    </section>
</div>
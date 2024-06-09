<section class="servicesList">
  <div class="container">
    <h2 class="sectionTitle"><?php echo $page_homepage['services_title']; ?><span class="smallText"><?php echo $page_homepage['services_subtitle']; ?></span></h2>
    <div class="row">
      <div class="servicesListSlider">
        <div class="sliderContainer">
        <?php foreach (['page_exercise_zones','page_coaches','page_lessons','page_wellness'] as $p): ?>   
          <div><img src="<?php echo ${$p}['cover_image']; ?>" /></div>
        <?php endforeach; ?> 
        </div>
        <div class="sliderProgress">
          <div class="progress"></div>
        </div>
      </div>
      <div class="servicesListText">
        <?php $i=0; foreach (['page_exercise_zones','page_coaches','page_lessons','page_wellness'] as $p): $i++;?>
        <?php if($p=='page_coaches') $link='/coaches';
              else if ($p=='page_lessons') $link='/lessons';
              else $link='/services/detail/'.slugify(preg_replace('/^page_/','',$p)); ?>        
        <div class="block <?php if($i==1) echo 'active'; ?>">
          <img src="<?php echo base_url(config_item('app')['img_folder'].'icons/ico_services_0'.${$p}['icon_image'].'.png');?>" alt="" />
          <h3 class="blockTitle"><?php echo ${$p}['header_title']; ?></h3>
          <p class="blockDescription"><?php echo ${$p}['perex']; ?></p>
          <a href="<?php echo $link; ?>" class="showMore">Více informací</a>
        </div>
        <?php endforeach; ?> 
      </div>
    </div>
    <div class="divider"></div>
  </div>
</section>
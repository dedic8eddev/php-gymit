<div class="subpage services">
  <section class="header header_v2" style="background-image:url('<?php echo $data['page_services']['header_image']; ?>');">
    <div class="headerTopOverlay">
      <div class="container">
        <h1 class="headerTitle">Služby</h1>
      </div>
    </div>
  </section>
  <section class="services_body">
    <div class="container">
      <div class="flexRow half">
        <?php foreach ($data as $k=>$v): if($k=='page_services') continue; ?>
        <?php if($k=='page_coaches') $link='/coaches';
              else if ($k=='page_lessons') $link='/lessons';
              else $link='/services/detail/'.slugify(preg_replace('/^page_/','',$k)); ?>
        <div class="col serviceItem">
          <div class="ico">
            <img src="<?php echo base_url(config_item('app')['img_folder'].'svg/ico_services_0'.$v['icon_image'].'_white.svg');?>" alt="" />
          </div>
          <h2 class="title"><a href="<?php echo $link ?>"><?php echo $v['header_title']; ?></a></h2>
          <p class="desc"><?php echo $v['perex']; ?></p>
          <a href="<?php echo $link ?>">Více informací</a>
          <div class="img"><img src="<?php echo $v['cover_image'];?>" /></div>
        </div>
        <?php endforeach; ?>      
      </div>      
    </div>
  </section>
</div>

<?php $this->app_blocks->newsletter([]); ?>
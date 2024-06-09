<div class="subpage article">

  <div class="container">
    <article class="post">
      <header>
        <div class="moveLeft">
          <div class="breadcrumb"><a href="<?php echo base_url('blog/'); ?>">Aktuality</a></div>
          <h1 class="postTitle"><?php echo $article['title']; ?></h1>
        </div>
        <div class="img"> <img <?php echo $this->app->getMedia($article['photo_src'],$article['photo_meta']); ?> /></div>
        <p class="description"><?php echo $article['perex']; ?></p>
      </header>
      <div class="entry-content twoCols"><?php echo $article['text']; ?></div>
      <footer>
        <div class="preTitle">Líbil se Vám článek?</div>
        <span class="title">Sdílejte ho s Vašimi známými</span>
        <div class="socials">
          <ul>
            <li><a href="#"><img src="<?php echo base_url(); ?>public/assets/img/svg/social_facebook.svg" alt="Facebook" width="34px" /></a></li>
            <li><a href="#"><img src="<?php echo base_url(); ?>public/assets/img/svg/social_insta.svg" alt="Instagram" width="34px" /></a></li>
            <li><a href="#"><img src="<?php echo base_url(); ?>public/assets/img/svg/social_twitter.svg" alt="Twitter" width="34px" /></a></li>
            <li><a href="#"><img src="<?php echo base_url(); ?>public/assets/img/svg/social_linkedin.svg" alt="Linkedin" width="34px" /></a></li>
          </ul>
        </div>
        </span>
      </footer>
    </article>
  </div>
</div>

<?php $this->app_blocks->newsletter([]); ?>
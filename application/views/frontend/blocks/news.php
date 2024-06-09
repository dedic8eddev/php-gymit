<section class="newsList">
  <div class="container">
    <h2 class="sectionTitle"><?php echo $page_homepage['news_title']; ?><span class="smallText"><?php echo $page_homepage['news_subtitle']; ?></span></h2>
    <div class="flexRow four">
    <?php if(!empty($articles['data'])): ?>
      <?php foreach($articles['data'] as $article): ?>
        <div class="col">
          <div class="newsImg"><a href="<?php echo blogArticleLink($article); ?>"><img <?php echo $this->app->getMedia($article->photo_src,$article->photo_meta); ?> /></a></div>
          <span class="newsMeta"><?php echo date('d.m.Y', strtotime($article->publish_from)); ?>, <?php echo $article->author_name; ?></span>
          <h3 class="newsTitle"><a href="<?php echo blogArticleLink($article); ?>"><?php echo $article->name; ?></a></h3>
          <a href="<?php echo blogArticleLink($article); ?>" class="showMore"><span>Přečíst článek</span></a>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col text-center">
        <p class="muted">V systému nejsou žádné články.</p>
      </div>
    <?php endif; ?>

    </div>
  </div>
</section>

<div class="progressBarSlider">
    <div class="sliderContainer">
    <?php foreach($pinned_articles['data'] as $article): ?>
        <div><img <?php echo $this->app->getMedia($article->photo_src,$article->photo_meta); ?> /></div>
    <?php endforeach; ?>
    </div>
    <div class="sliderProgress">
        <div class="progress"></div>
    </div>
</div>
<div class="servicesListText">
    <?php foreach($pinned_articles['data'] as $article): ?>
        <div class="block">
            <h3 class="blockTitle"><a href="<?php echo blogArticleLink($article); ?>"><?php echo $article->title; ?></a></h3>
            <p class="blockDescription"><?php echo $article->perex; ?></p>
        </div>
    <?php endforeach; ?>
</div>

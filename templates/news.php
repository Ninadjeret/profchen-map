<?php
/*
 * Template Name: News
 */
get_header();
?>
<div class="page-content container mt-60 mb-60" id="timeline">

    <?php foreach( POGO_helpers::getNews() as $news ) { ?>
    <div class="news__wrapper <?php echo ($news->isImportant()) ? 'important' : '' ;?>">
        <div class="news__left">
            <span class="news__marker">
                <?php echo $news->getPublishedDate()->format('d/m'); ?>
                <?php echo ($news->isImportant()) ? '<i class="material-icons">announcement</i>' : '' ;?>
            </span>
        </div>
        <div class="news__right">
            <div class="news__content">
                <h3><?php echo $news->getNameFr(); ?></h3>
                <?php echo $news->getContentForMap(); ?>
            </div>
        </div>
    </div>
    <?php } ?>
    
</div>
<?php
get_footer();
?>
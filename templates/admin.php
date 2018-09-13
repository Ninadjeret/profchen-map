<?php
/*
 * Template Name: Admin
 */
$user = new POGO_user( get_current_user_id() );
get_header();
?>
<div class="page-content mt-60 mb-60">
<?php if( $user->getAdminCommunities() ) { ?>
    <?php foreach( $user->getAdminCommunities() as $community ) { ?>
        <div class="settings-section">
            <div class="section__title"><?php echo $community->getNameFr(); ?></div> 
            <div class="settings-section__wrapper">
                <div class="setting__wrapper">
                    <a href="<?php echo get_permalink( POGO_routes::getAdminCommunitySettingsPageId() ).'?communityId='.$community->wpId; ?>" class="setting-link">Modifier la communauté</a>
                </div>
                <?php if( $community->getType() == 'discord' ) { ?>
                <div class="setting__wrapper">
                    <a href="<?php echo get_permalink( POGO_routes::getAdminConnectorsPageId() ).'?communityId='.$community->wpId; ?>" class="setting-link">Gérer les connecteurs</a>
                </div> 
                <?php } ?>
            </div>
        </div>
    <?php } ?> 
    <div class="settings-section">
        <div class="section__title">Outils</div> 
        <div class="settings-section__wrapper">
            <div class="setting__wrapper">
                <a href="<?php echo get_permalink( POGO_routes::getNewsPageId() ); ?>" class="setting-link">News</a>
            </div>
        </div>
    </div>
<?php } ?>    
</div>


<?php
get_footer();
?>
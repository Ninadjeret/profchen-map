<?php
/*
 * Template Name: Admin > Connecteurs
 */
$user = new POGO_user(get_current_user_id());
$communityId = ( isset($_GET['communityId']) && !empty($_GET['communityId']) ) ? $_GET['communityId'] : false;
acf_form_head();
get_header();
?>

<?php if( $communityId && $user->canEditCommunity($communityId) ) { ?>
    <?php $community = new POGO_community($communityId); ?>
    <div class="page-content mt-60 mb-60">
            <div class="settings-section">
                <div class="settings-section__wrapper">
                    <?php foreach( $community->getConnectors() as $connector ) { ?>                    
                        <div class="setting__wrapper">
                            <a href="<?php echo get_permalink( POGO_routes::getAdminConnectorSettingsPageId() ).'?connectorId='.$connector->wpId.'&communityId='.$communityId; ?>" class="setting-link"><?php echo $connector->getNameFr(); ?></a>
                        </div>
                    <?php } ?>                                  
                </div>
            </div>
            <div class="form__save">
                <a href="<?php echo get_permalink( POGO_routes::getAdminNewConnectorPageId() ).'?communityId='.$communityId; ?>" class="bt-round"><i class="material-icons">add</i></a>
            </div> 
    </div>
<?php } else { ?>
    <div class="page-content-full container">
        <p>Vous n'êtes administrateur de cette communauté</p>
    </div>
<?php } ?>
       

<?php
get_footer();
?>
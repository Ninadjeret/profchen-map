<?php
/*
 * Template Name: Admin > Nouveau connecteur
 */
$user = new POGO_user(get_current_user_id());
$communityId = ( isset($_GET['communityId']) && !empty($_GET['communityId']) ) ? $_GET['communityId'] : false;
acf_form_head();
get_header();
?>

<?php if( $communityId && $user->canEditCommunity($communityId) ) { ?>
    <div class="page-content mt-60 mb-60">
        <div class="section">
            <div class="section__wrapper">
                <?php acf_form(array(
                    'post_id'               => 'new_post',
                    'new_post'              => array(
			'post_type'	=> 'connector',
			'post_status'	=> 'publish'
                    ),
                    'post_title'            => false,
                    'return'                => get_permalink( POGO_routes::getAdminConnectorsPageId() ).'?communityId='.$communityId,
                    'fields'                => array( 'field_5b67f35e4dfc1', 'name_fr', 'url', 'connector_display_source', 'field_5b7baf833e1bd', 'connector_filter_gyms', 'connector_cities', 'connector_gyms', 'field_5b7baf9a3e1be', 'connector_filter_pokemon', 'connector_egglevels', 'connector_pokemon' ),
                    'html_submit_button'    => '<div class="form__save"><button class="bt-round"><i class="material-icons">save</i></button></div>',
                    'html_submit_spinner'   => '<div class="form__save acf-spinner"><button class="bt-round"><div class="mdl-spinner mdl-spinner--single-color mdl-js-spinner is-active"></div></button></div>'
                    )); ?>
            </div> 
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
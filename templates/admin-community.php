<?php
/*
 * Template Name: Admin > Communauté
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
                    'post_id'               => $communityId,
                    'post_title'            => false,
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
<?php
/*
All the functions are in the PHP files in the `functions/` folder.
*/
define('PROFCHEN_VERSION', '1.3.25');
setlocale(LC_TIME, 'fr_FR.utf8', 'fra');

require get_stylesheet_directory() . '/config/config.php';
require get_template_directory() . '/includes/class.network.php';
//require get_template_directory() . '/admin/bot_update.php';
//require get_template_directory() . '/admin/quest.php';

require get_template_directory() . '/admin/connector.php';
require get_template_directory() . '/admin/gym.php';
require get_template_directory() . '/admin/options.php';
require get_template_directory() . '/admin/raid.php';
require get_template_directory() . '/admin/community.php';  

if( POGO_network::isMainSite() ) {
    require get_template_directory() . '/admin/pokemon.php';    
}

require get_template_directory() . '/includes/class.acf.php';
require get_template_directory() . '/includes/class.actions.php';
require get_template_directory() . '/includes/class.ajax.php';
require get_template_directory() . '/includes/class.announce.php';
require get_template_directory() . '/includes/class.community.php';
require get_template_directory() . '/includes/class.connector.php';
require get_template_directory() . '/includes/class.filters.php';
require get_template_directory() . '/includes/class.helpers.php';
//require get_template_directory() . '/includes/class.image_analyzer.php';
require get_template_directory() . '/includes/class.pokemon.php';
require get_template_directory() . '/includes/class.gym.php';
require get_template_directory() . '/includes/class.quest.php';
require get_template_directory() . '/includes/class.message_decoder.php';
require get_template_directory() . '/includes/class.news.php';
require get_template_directory() . '/includes/class.phptoshop.php';
require get_template_directory() . '/includes/class.pokemon_match_engine.php';
require get_template_directory() . '/includes/class.query.php';
require get_template_directory() . '/includes/class.raid.php';
require get_template_directory() . '/includes/class.routes.php';
require get_template_directory() . '/includes/class.setting.php';
require get_template_directory() . '/includes/class.user.php';

require get_template_directory() . '/includes/imageAnalizer/class.ia.engine.php';
require get_template_directory() . '/includes/imageAnalizer/class.ia.color_picker.php';
require get_template_directory() . '/includes/imageAnalizer/class.ia.coordinates.php';
require get_template_directory() . '/includes/imageAnalizer/class.ia.gym_search.php';
require get_template_directory() . '/includes/imageAnalizer/class.ia.pokemon_search.php';
require get_template_directory() . '/includes/imageAnalizer/class.ia.microsoft_ocr.php';

require get_template_directory() . '/api/class.api.php';
require get_template_directory() . '/api/class.dijon-api.php';

require get_template_directory() . '/includes/functions/setup.php';
require get_template_directory() . '/includes/functions/enqueues.php';
require get_template_directory() . '/includes/functions/navbar.php';

require get_template_directory() . '/includes/import/helpers.php';
 

add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );
function custom_excerpt_length( $length ) {
	return 100;
}



add_action( 'transition_post_status', 'raid_content_publish', 10, 3 );
function raid_content_publish( $new_status, $old_status, $post ) {

    if( get_post_type($post->ID) != 'raid' ) return;
    if ( $new_status != 'publish' || $old_status != 'future' ) return;
    
    do_action('pogo/raid/active', $post->ID);
    
}

add_action('after_setup_theme', 'remove_admin_bar'); 
function remove_admin_bar() {
    if (!is_admin()) {
        show_admin_bar(false);
    }
}

add_filter('body_class', 'multisite_body_classes');
function multisite_body_classes($classes) {
    if( !is_user_logged_in() && !in_array( get_the_ID(), POGO_routes::getNonLoggedPages() ) ) {
        $classes[] = 'login-form';
    }
    return $classes;
}

add_action( 'admin_init', 'wpse_74018_enable_draft_comments' );
function wpse_74018_enable_draft_comments()
{
    if( isset( $_GET['post'] ) ) 
    {
        $post_id = absint( $_GET['post'] ); 
        $post = get_post( $post_id ); 
        if ( 'future' == $post->post_status )
            add_meta_box(
                'commentsdiv', 
                __('Comments'), 
                'post_comment_meta_box', 
                'raid', // CHANGE FOR YOUR CPT
                'normal', 
                'core'
            );
    }
}

add_action( 'widgets_init', 'b4st_widgets_init' );
function b4st_widgets_init() {
    register_sidebar(array(
        'name' => __('Login', 'b4st'),
        'id' => 'login',
        'description' => __('Login Area', 'b4st'),
        'before_widget' => '<section class="%1$s %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="h4">',
        'after_title' => '</h2>',
    ));
}

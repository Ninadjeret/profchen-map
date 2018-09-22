<?php

/* * !
 * Enqueues
 */

if (!function_exists('b4st_enqueues')) {

    function b4st_enqueues() {

        // Styles
        
        $version = PROFCHEN_VERSION;
        $config = array(
            'assetsUrl' => POGO_config::ASSETS_URL,
            'siteUrl'   => POGO_config::APP_URL,
            'futureRaidDuration' => POGO_config::get('futureRaidDuration'),
            'activeRaidDuration' => POGO_config::get('activeRaidDuration'),
        );

        wp_register_style('bootstrap-css', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.0/css/bootstrap.min.css', false, '4.1.0', null);
        wp_enqueue_style('bootstrap-css');

        wp_register_style('materialize-css', 'https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css', false, $version);
        //wp_enqueue_style('materialize-css');               
        
        wp_register_style('dialog-polyfill-css', get_template_directory_uri() . '/theme/css/dialog-polyfill.css', false, $version);
        wp_enqueue_style('dialog-polyfill-css');
        
        wp_register_style('rangeslider-css', get_template_directory_uri() . '/theme/css/rangeslider.css', false, $version);
        wp_enqueue_style('rangeslider-css');
        
        wp_register_style('b4st-css', get_template_directory_uri() . '/theme/css/b4st.css', false, $version);
        wp_enqueue_style('b4st-css');
        

        // Scripts

        wp_register_script('font-awesome-config-js', get_template_directory_uri() . '/theme/js/font-awesome-config.js', false, null, null);
        wp_enqueue_script('font-awesome-config-js');

        wp_register_script('font-awesome', 'https://use.fontawesome.com/releases/v5.0.10/js/all.js', false, '5.0.10', null);
        wp_enqueue_script('font-awesome');

        wp_register_script('modernizr', 'https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js', false, '2.8.3', true);
        wp_enqueue_script('modernizr');

        wp_register_script('jquery-3.3.1', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js', false, '3.3.1', true);
        wp_enqueue_script('jquery-3.3.1');

        wp_register_script('popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js', false, '1.14.0', true);
        wp_enqueue_script('popper');

        wp_register_script('bootstrap-js', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.0/js/bootstrap.min.js', false, '4.1.0', true);
        wp_enqueue_script('bootstrap-js');
        
        wp_register_script('materialize-js', 'https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js', false, '1.0.0', true);
        //wp_enqueue_script('materialize-js');

        wp_register_script('jquerycountdown-js', get_template_directory_uri() . '/theme/js/jquery-countdown.js', array('jquery'), $version, true);
        wp_enqueue_script('jquerycountdown-js');
        
        wp_register_script('b4st-js', get_template_directory_uri() . '/theme/js/b4st.js', false, $version, true);
        wp_enqueue_script('b4st-js');               
        
        wp_register_script('cookie-js', 'https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js', false, $version, true);
        wp_enqueue_script('cookie-js');
        
        wp_register_script('settings-js', get_template_directory_uri() . '/theme/js/settings.js', array('cookie-js'), $version, true);
        wp_localize_script('settings-js', 'siteConfig', $config );
        wp_enqueue_script('settings-js');
        
        wp_register_script('dialog-polyfill-js', get_template_directory_uri() . '/theme/js/dialog-polyfill.js', false, null, true);
        wp_enqueue_script('dialog-polyfill-js');
        
        wp_register_script('moment-js', get_template_directory_uri() . '/theme/js/moment.js', false, null, true);
        wp_enqueue_script('moment-js');
        
        wp_register_script('rangeslider-js', get_template_directory_uri() . '/theme/js/rangeslider.js', array('jquery'), null, true);
        wp_enqueue_script('rangeslider-js');
        
        wp_register_script('app-js', get_template_directory_uri() . '/theme/js/app.js', array('jquery', 'moment-js'), $version, true);
        wp_localize_script('app-js', 'siteConfig', $config );
        wp_enqueue_script('app-js');

        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
    }

}
add_action('wp_enqueue_scripts', 'b4st_enqueues', 100);

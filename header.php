<?php
$user = ( is_user_logged_in() ) ? new POGO_user( get_current_user_id() ) : false ;
?>
<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
    <head>
        <meta name="description" content="<?php
        if (is_single()) {
            single_post_title('', true);
        } else {
            bloginfo('name');
            echo " - ";
            bloginfo('description');
        }
        ?>" />
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php wp_head(); ?>
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-pink.min.css">
        <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
        <link href="https://fonts.googleapis.com/css?family=Teko" rel="stylesheet">
        <meta name="description" content="Profitez des annonces du Prof Chen en temps réel et sur une map">
        
        <!-- WebApp compatibility -->
        <meta name="theme-color" content="#1c8796">
        <meta name="mobile-web-app-capable" content="yes">
        <link rel="icon" sizes="192x192" href="<?php echo POGO_config::get('AssetsUrl'); ?>/img/profchen_logo_192.png">
        <link rel="manifest" href="<?php echo get_stylesheet_directory_uri(); ?>/manifest.json?ver=<?php echo PROFCHEN_VERSION; ?>">
        <!-- ./ WebApp compatibility -->
        
        <!-- Apple compatibility -->
        <link rel="apple-touch-icon" href="<?php echo POGO_config::get('AssetsUrl'); ?>/img/profchen_logo_128.png">
        <link rel="apple-touch-icon" sizes="152x152" href="<?php echo POGO_config::get('AssetsUrl'); ?>/img/profchen_logo_152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="<?php echo POGO_config::get('AssetsUrl'); ?>/img/profchen_logo_180.png">
        <link rel="apple-touch-icon" sizes="167x167" href="<?php echo POGO_config::get('AssetsUrl'); ?>/img/profchen_logo_167.png">
        <link rel="apple-touch-startup-image" href="<?php echo POGO_config::get('AssetsUrl'); ?>/img/bg_loading.jpg">
        <meta name="apple-mobile-web-app-title" content="<?php echo POGO_config::get('appName'); ?>">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <!-- ./ Apple compatibility -->
        
        <script src="https://unpkg.com/vue"></script>
        
    </head>

<body <?php body_class(); ?>>


<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
    <header class="mdl-layout__header">
        <?php if( $post && $post->post_parent ) { ?>
            <?php
            $url = get_permalink($post->post_parent);
            if( ( get_the_ID() == POGO_routes::getAdminConnectorSettingsPageId() || get_the_ID() == POGO_routes::getAdminNewConnectorPageId() ) && isset($_GET['communityId']) ) $url.= '?communityId='.$_GET['communityId'];
            ?>
            <div role="button" class="mdl-layout__drawer-button"><a href="<?php echo $url; ?>"><i class="material-icons">arrow_back</i></a></div>
        <?php } ?>        
        <div class="mdl-layout__header-row mdl-typography--text-center">
            <?php if (is_front_page()) { ?>
                <div class="mdl-layout-spacer"></div>
                <span class="mdl-layout-title"><img src="https://assets.profchen.fr/img/logo_main_400.png"> PROF CHEN<small>&nbsp;<?php echo ( POGO_network::isMainSite() ) ? 'map' : get_bloginfo('name') ; ?></small></span>
                <div class="mdl-layout-spacer"></div>
            <?php } else { ?>
                <span class="mdl-layout-title"><?php the_title(); ?></span>
            <?php } ?>
    </header>
    <div class="main-menu <?php if( $user && $user->getAdminCommunities() ) echo 'admin'; ?>">
        <div class="item <?php echo ( is_page(POGO_routes::getMapsPageId()) ) ? 'active' : '' ; ?>">
            <a href="<?php echo get_permalink(POGO_routes::getMapsPageId()); ?>">
                <i class="material-icons">map</i><span>Carte</span>
            </a>
        </div>
        <div class="item <?php echo ( is_page(POGO_routes::getAlertsPageId()) ) ? 'active' : '' ; ?>">
            <a href="<?php echo get_permalink(POGO_routes::getAlertsPageId()); ?>">
                <i class="material-icons">notifications_active</i><span>Alertes</span>
            </a>
        </div>   
        <div class="item <?php echo ( is_page(POGO_routes::getSettingsPageId()) ) ? 'active' : '' ; ?>">
            <a href="<?php echo get_permalink(POGO_routes::getSettingsPageId()); ?>">
                <i class="material-icons">settings</i><span>Réglages</span>
            </a>
        </div> 
        <?php if( $user && $user->getAdminCommunities() ) { ?>
        <div class="item <?php echo ( is_page( POGO_routes::getAdminPageId() ) ) ? 'active' : '' ; ?>">
                <a href="<?php echo get_permalink(POGO_routes::getAdminPageId()); ?>">
                    <i class="material-icons">settings_input_component</i><span>Admin</span>
                </a>
            </div>        
        <?php } ?>
    </div>
  <main class="mdl-layout__content">
      <?php
        /*wp_nav_menu( array(
          'theme_location'  => 'navbar',
          'container'       => false,
          'menu_class'      => '',
          'fallback_cb'     => '__return_false',
          'items_wrap'      => '<ul id="%1$s" class="navbar-nav mr-auto mt-2 mt-lg-0 %2$s">%3$s</ul>',
          'depth'           => 2,
          'walker'          => new b4st_walker_nav_menu()
        ) );*/
      ?>

    


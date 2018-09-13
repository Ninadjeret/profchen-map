<?php
/*
 * Template Name: Login
 */
get_header(); ?>
<div class="page-content-full container">
    <div class="branding">
        <img src="https://assets.profchen.fr/img/logo_main_400.png">
        <h1>PROF CHEN<small>&nbsp;map</small></h1>
        <?php 
        //wp_login_form( array( 'redirect' => get_permalink( POGO_routes::getLoadingPageId() ).'' ) ); 
        ?>  
        <?php dynamic_sidebar('login'); ?>
    </div>               
</div
<?php 
get_footer();
?>
<?php
/*
 * Template Name: Settings
 */
$user = new POGO_user( get_current_user_id() );
get_header();
?>
<div class="page-content mt-60 mb-60">
    <div class="settings-section connected">
        <div class="section__title">Connecté en tant que</div>
        <div class="settings-section__wrapper">
            <div class="setting__wrapper user">
                <div class="user__img">
                    <img src="<?php echo $user->getAvatarUrl();?>" />
                </div>
                <div class="user__info">
                    <h3><?php echo $user->getUsername(); ?> <small>//via Discord</small></h3>
                    <a href="<?php echo wp_logout_url( get_permalink( POGO_routes::getLogoutPageId() ) ); ?>" class="logout">SE DECONNECTER</a>
                </div>
            </div>
        </div>
    </div>

    <div class="settings-section map">
        <div class="section__title">Map</div>
        <div class="settings-section__wrapper">
            <?php 
            POGO_settings::displayToggleSetting( array(
                'id'            => 'mapHideEmpty',
                'title'         => 'Masquer les arênes vides',
                'description'   => 'N\'afficher sur la carte que les arênes avec un raid en cours ou à venir'
            ) );
            ?>
            <?php 
            POGO_settings::displaySelectSetting( array(
                'id'            => 'mapDefaultPosition',
                'title'         => 'Position par défaut',
                'description'   => 'Cet emplacement sera affiché par défaut à l\'ouverture de la carte',
                'choices'       => array(
                    'discord_chartres' => 'Chartres et alentours',
                    'discord_vern'  => 'Vern et alentours',
                    //'last_position' => 'Dernière position utilisée'
                )
            ) );
            ?>
        </div>
    </div>
    <!--
    <div class="settings-section alerts">
        <div class="section__title">Alertes</div> 
        <div class="settings-section__wrapper">
            <div class="setting__wrapper">
                <a href="#" class="setting-link">Politique de confidentialité</a>
            </div>
            <div class="setting__wrapper">
                <a href="<?php echo get_permalink( POGO_routes::getLoadingPageId() ); ?>" class="setting-link">Re-télécharger les données</a>
            </div>
        </div>
    </div>
    -->
    <div class="settings-section help">
        <div class="section__title">Assistance</div> 
        <div class="settings-section__wrapper">
            <div class="setting__wrapper">
                <a href="<?php echo get_permalink( POGO_routes::getPolicyPageId() ); ?>" class="setting-link">Politique de confidentialité</a>
            </div>
            <div class="setting__wrapper">
                <a href="<?php echo get_permalink( POGO_routes::getLoadingPageId() ); ?>" class="setting-link">Re-télécharger les données</a>
            </div>
        </div>
    </div>
    
    <div class="settings-section about">
        <div class="section__title">A propos</div> 
        <p class="credit">
            Prof Chen map, créé pour vous avec <i class="material-icons">favorite</i><br>
            Version <span id="version"></span>
        </p>
    </div>
    
</div>

<script>
(function ($) {
    'use strict';
    $(document).ready(function () {
        //loadSettings()       
    });
}(jQuery));
</script>

<?php
get_footer();
?>
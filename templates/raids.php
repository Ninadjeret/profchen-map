<?php
/*
 * Template Name: Raids
 */
get_header();
?>
<div class="mt-60 mb-60">

    <div class="raids__active">
        <div class="section__title">Raids en cours</div>  
        <div class="raids__wrapper"></div>        
    </div>

    <div class="raids__future">
        <div class="section__title">Raids à venir</div>
        <div class="raids__wrapper"></div> 
    </div>
    
    <div class="raids__empty hide">
        <img src="https://assets.profchen.fr/img/empty_raids.png" />
        <h3>Aucun raid pour le moment...</h3>
    </div>
    
</div>

<div class="map__actions"> 
    <div class="map__overlay"></div>
    <div class="actions">
        <button class="action" id="refresh">                    
            <span>Actualiser</span>
            <div class="mdl-spinner mdl-spinner--single-color mdl-js-spinner is-active"></div>
            <i class="material-icons">refresh</i>
        </button>        
    </div>
    <div class="launcher">
        <button id="launcher">
            <i class="material-icons menu-on">menu</i>
            <i class="material-icons menu-off">close</i>
        </button>        
    </div>
</div>

<script>
(function ($) {
    'use strict';
    $(document).ready(function () {
        loadRaids()       
    });
}(jQuery));
</script>
<?php
get_footer();
?>
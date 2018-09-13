<?php
/* 
 * Template Name: Map
 */
get_header();
?>
<div class="acf-map"></div>
<div class="map__actions"> 
    <div class="map__overlay"></div>
    <div class="actions">
        <button class="action" id="findme">        
            <span>Localiser</span><i class="material-icons">gps_fixed</i>
        </button>
        <button class="button__action1" id="refresh">                    
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

<?php get_footer(); ?>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD3t3mvxE6L2z_8XCGwZnbMIldhYtUwkd4"></script>
<script type="text/javascript">
(function($) {
    //var map = null;
    $(document).ready(function(){
        $('.acf-map').each(function(){
            // create map
            window.map = createMap( $(this) );
        });
        loadMarkers();
    });

})(jQuery);
</script>


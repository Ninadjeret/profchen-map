<?php
/* 
 * Template Name: Map
 */
get_header();
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.4/dist/leaflet.css"
   integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA=="
   crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.3.4/dist/leaflet.js"
   integrity="sha512-nMMmRyTVoLYqjP9hrbed9S+FzjZHW5gY1TWCHA5ckwXZBadntCNs8kEqAWdrb9O7rxbCaA4lKTIWjDXZxflOcA=="
   crossorigin=""></script>
   
<div id="mapid"></div>
   
<div class="acf-map2"></div>
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

<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo POGO_config::get('GoogleMapsApiKey'); ?>"></script>
<script type="text/javascript">
(function($) {
    //var map = null;
    $(document).ready(function(){
        /*$('.acf-map').each(function(){
            // create map
            window.map = createMap( $(this) );
        });
        loadMarkers();*/
    });

})(jQuery);
</script>

<script>
    window.map = L.map('mapid').setView([48.0395517, -1.6039126,], 13);
    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
        maxZoom: 20,
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
                '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
                'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
        id: 'mapbox.streets',
        zoomControl:false
    }).addTo(window.map);
    centerMapToPlayer();
</script>


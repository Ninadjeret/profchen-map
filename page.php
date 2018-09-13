<?php get_header(); ?>
<div id="map"></div>
    <script>
      function initMap() {
        // Styles a map in night mode.
        var map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: 48.045379, lng: -1.604150}, 
          zoom: 15,
          disableDefaultUI: true
        });
      }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD3t3mvxE6L2z_8XCGwZnbMIldhYtUwkd4&callback=initMap"
    async defer></script>
<?php get_footer(); ?>

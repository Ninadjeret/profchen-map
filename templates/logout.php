<?php
/*
 * Template Name: Logout
 */
get_header(); ?>
<div class="page-content-full container">
    <div class="loading__wrapper">
        <p>Déconnexion en cours...</p>
        <div id="p2" class="mdl-progress mdl-js-progress mdl-progress__indeterminate"></div>
    </div>
    
</div>
<script>
    

    
(function ($) {
    'use strict';
    $(document).ready(function () {    


    var CACHE_VERSION = "<?php echo PROFCHEN_VERSION; ?>";
    var CACHE_NAME = "profchen-v"+CACHE_VERSION;

    var pageHome = new Request('/', { credentials: 'include' });
    var pageRaids = new Request('/alerts/', { credentials: 'include' });
    var pageNews = new Request('/news/', { credentials: 'include' });
    var pageSettings = new Request('/settings/', { credentials: 'include' });

    caches.keys().then(function(cacheNames) {
        cacheNames.map(function(cacheName) {
            if (cacheName.startsWith("profchen")) {
                caches.delete(cacheName);
            }
        })
    });     
    
    setTimeout(function(){
        document.location.href="<?php echo home_url(); ?>"
    }, 3000);
    
    });

}(jQuery));    
</script>
<?php 
get_footer();
?>
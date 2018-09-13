<?php
/*
 * Template Name: Loading
 */
get_header(); ?>
<div class="page-content-full container">
    <div class="loading__wrapper">
        <p>Chargement en cours...</p>
        <div id="p2" class="mdl-progress mdl-js-progress mdl-progress__indeterminate"></div>
    </div>
    
</div>
<script>
    

    
(function ($) {
    'use strict';
    $(document).ready(function () {    

    var CACHE_VERSION = "<?php echo PROFCHEN_VERSION; ?>";
    var CACHE_NAME = "profchen-v"+CACHE_VERSION;
    var USER_ID = <?php echo get_current_user_id(); ?>;

    var pageHome = new Request('/', { credentials: 'include' });
    var pageRaids = new Request('/alerts/', { credentials: 'include' });
    var pageNews = new Request('/admin/news/', { credentials: 'include' });
    var pageSettings = new Request('/settings/', { credentials: 'include' });
    var pagePolicy = new Request('/settings/policy/', { credentials: 'include' });
    var pageAdmin = new Request('/admin/', { credentials: 'include' });

    caches.keys().then(function(cacheNames) {
        cacheNames.map(function(cacheName) {
            if (cacheName.startsWith("profchen")) {
                caches.delete(cacheName);
            }
        })
    });
    
    caches.keys().then(function(cacheNames) {
        caches.open(CACHE_NAME).then(function(cache) {
            localStorage.setItem('profchen_version', CACHE_VERSION)
            localStorage.setItem('profchen_user_id', USER_ID)
            cache.addAll([
                pageHome,
                pageRaids,
                pageNews,
                pageAdmin,
                pagePolicy,
                '/wp-content/themes/site/theme/css/b4st.css?ver='+CACHE_VERSION,
                '/wp-content/themes/site/theme/js/b4st.js?ver='+CACHE_VERSION,
                '/wp-content/themes/site/theme/js/app.js?ver='+CACHE_VERSION,
                '/wp-content/themes/site/theme/js/settings.js?ver='+CACHE_VERSION
            ]);
        });        
    }); 
    
    downloadSettings();
    
    setTimeout(function(){
        document.location.href="<?php echo home_url(); ?>"
    }, 5000);
    
    });

}(jQuery));    
</script>
<?php 
get_footer();
?>
importScripts('/cache-polyfill.js');

var CACHE_VERSION = "0.1.25";
var CACHE_NAME = "profchen-v"+CACHE_VERSION;

self.addEventListener('install', function(e) {
    e.waitUntil(
        caches.open(CACHE_NAME).then(function(cache) {
            return cache.addAll([
                '/',
                '/settings/',
                '/alerts/',
                '/news/',
                '/wp-content/themes/b4st-master/theme/css/b4st.css?ver='+CACHE_VERSION,
                '/wp-content/themes/b4st-master/theme/js/b4st.js?ver='+CACHE_VERSION
            ]);
        })
    );
});

self.addEventListener('fetch', function(event) {
    //console.log(event.request.url);
    event.respondWith(
        caches.match(event.request).then(function(response) {
            return response || fetch(event.request);
        })

    );
}); 

self.addEventListener("activate", function(event) {
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.map(function(cacheName) {
                    if (CACHE_NAME !== cacheName &&  cacheName.startsWith("profchen")) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});
/**!
 * b4st JS
 */

(function ($) {

    'use strict';

    $(document).ready(function () {

        // Comments

        $('.commentlist li').addClass('card');
        $('.comment-reply-link').addClass('btn btn-secondary');

        // Forms

        $('select, input[type=text], input[type=email], input[type=password], textarea').addClass('form-control');
        $('input[type=submit]').addClass('btn btn-primary');

        // Pagination fix for ellipsis

        $('.pagination .dots').addClass('page-link').parent().addClass('disabled');
           
    });

}(jQuery));
/*
(function () {
    'use strict';
    var dialogButton = document.querySelector('.dialog-button');
    var dialog = document.querySelector('#dialog');
    if (!dialog.showModal) {
        dialogPolyfill.registerDialog(dialog);
    }
    dialogButton.addEventListener('click', function () {
        dialog.showModal();
    });
    dialog.querySelector('button:not([disabled])')
            .addEventListener('click', function () {
                dialog.close();
            });
}());
*/
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function initMapSync() {
    DisplayPlayerOnMap();
    loadGyms();
    //setInterval( loadGyms, 60000 );
}














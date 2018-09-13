(function ($) {
    'use strict';
    $(document).ready(function () {
        
        $('#mapHideEmpty').change( function() {
            saveToggleSetting( $(this) );
        });
        
        $('#mapDefaultPosition').change( function() {
            saveSelectSetting( $(this) );
        });
        
    });
}(jQuery));

function saveToggleSetting( input ) {
    setTimeout(function(){
        var settingId = input.attr('data-id');
        var settingLabelParent = input.parents('label');
        var settingVal = 0;
        if( settingLabelParent.hasClass('is-checked') ) {
            settingVal = 1;
        }
        updateToggleSetting( settingId, settingVal );
    }, 200);
}

function saveSelectSetting( input ) {
    var settingId = input.attr('data-id');
    var settingVal = $(input).find(":selected").val();
    updateSelectSetting( settingId, settingVal );
}

/*function setToggleSetting( input ) {
    var settingId = input.attr('data-id');
    var appSettings = Cookies.getJSON('pogo_settings');
    if( appSettings[settingId] === 1 ) {
        input.prop('checked', true);
    } else {
        input.prop('checked', false);
    }
}
*/

function updateToggleSetting(settingId, settingVal) {
    console.log('Saving '+settingVal+' for '+settingId);
    $('#message').html('Enregistrement en cours');
    $('#message').addClass('display');
    $.ajax({
        url: siteConfig.siteUrl+'/wp-admin/admin-ajax.php', 
        method: 'POST',
        data: {
            action: 'saveToggleSetting',
            settingId: settingId,
            settingVal: settingVal,
        }, 
        success: function (data) { 
            downloadSettings();
            displayUpdateSettingMessage('Enregistrement réussi');
        },
        error: function (data) {
            displayUpdateSettingMessage('Erreur :-/');
        }
    });
}

function updateSelectSetting(settingId, settingVal) {
    $('#message').html('Enregistrement en cours');
    $('#message').addClass('display');
    $.ajax({
        url: siteConfig.siteUrl+'/wp-admin/admin-ajax.php', 
        method: 'POST',
        data: {
            action: 'saveSelectSetting',
            settingId: settingId,
            settingVal: settingVal,
        }, 
        success: function (data) { 
            downloadSettings();
            displayUpdateSettingMessage('Enregistrement réussi');
        },
        error: function (data) {
            displayUpdateSettingMessage('Erreur :-/');
        }
    });
}

function displayUpdateSettingMessage( message ) {    
    $('#message').html(message);
    setTimeout(function(){
        $('#message').removeClass('display');
    }, 1000);     
}
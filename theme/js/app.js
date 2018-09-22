//======================================================================
// ON READY
//======================================================================
(function ($) {
    'use strict';   
    $(document).ready(function () {    
        //Init Synchronization();
        initSync();
        
        //Actions from buttons
        $('#launcher').click( function(){
            $('.map__actions').toggleClass('open');
        });
        $('.map__actions #refresh').click( function() {
            $('.map__actions').toggleClass('open');
            syncData('manual');
        });
        $('.map__actions #findme').click( function() {
            $('.map__actions').toggleClass('open');
            centerMapToPlayer();
        });
        
        //Connexion et Déconnexion
        $('.widget_mo_oauth_widget a').click( function() {
            $(this).html('Connexion...');
        });
        $('.logout').click( function() {
            $(this).html('DECONNEXION...');
        });
        
        
        //Settings
         $('#version').html( getCachedVersion() );
         
         //Range
         $(document).on('input', '.range', function() {
            updateTimeRange( $(this).val() );
        });
              
        
    });
}(jQuery));

//======================================================================
// SYNC FUNCTIONS
//======================================================================

function initSync() {
    syncData( 'auto' );
    //setInterval( downloadGyms, 60000 ); 
    setInterval( syncData, 60000, 'auto' ); 
}

function syncData( mode ) {
    
    if( mode == 'manual' ) {
        displayPermanentMessage('Synchronisation en cours...');
    }   
    
    var button = $('.map__actions #refresh');
    button.attr('disabled', 'disabled');
    button.find('.mdl-spinner').addClass('is-active');
    
    downloadGyms().then( function(){
        loadRaids();
        loadMarkers();
    });
    downloadNews().then( function(){
        loadNews();
    });
    downloadRaidBosses();
    downloadSettings();

    
    setTimeout(function(){
        button.removeAttr('disabled');
        button.find('.mdl-spinner').removeClass('is-active');
        if( mode == 'manual' ) {
            displayDeleteMessage('Synchronisation terminée');
        }
    }, 1500);

}

function downloadGyms() {
    var gyms = [];   
    var result = $.getJSON(siteConfig.siteUrl+"/api/v1/gyms/?token=AsdxZRqPkrst67utwHVM2w4rt4HjxGNcX8XVJDryMtffBFZk3VGM47HkvnF9&userId="+getCachedUserId())
        .done(function (data) {
            $.each( data, function( i, item ) {
                gyms.push(item);
            });
            localStorage.setItem('profchen_gyms', JSON.stringify(gyms));
        });  
    //console.log(result);
    return result;
}

function downloadNews() {
    var news = [];   
    var result = $.getJSON(siteConfig.siteUrl+"/api/v1/news/?token=AsdxZRqPkrst67utwHVM2w4rt4HjxGNcX8XVJDryMtffBFZk3VGM47HkvnF9")
        .done(function (data) {
            $.each( data, function( i, item ) {
                news.push(item);
            });
            localStorage.setItem('profchen_news', JSON.stringify(news));
            //return true;
        });   
    return result;
}

function downloadRaidBosses() {
    var bosses = [];   
    var result = $.getJSON(siteConfig.siteUrl+"/api/v1/raidbosses/?token=AsdxZRqPkrst67utwHVM2w4rt4HjxGNcX8XVJDryMtffBFZk3VGM47HkvnF9")
        .done(function (data) {
            $.each( data, function( i, item ) {
                bosses.push(item);
            });
            localStorage.setItem('profchen_raidBoss', JSON.stringify(bosses));
            //return true;
        });   
    return result;
}

function downloadSettings() {
    var result = $.ajax({
        url: siteConfig.siteUrl+'/wp-admin/admin-ajax.php', 
        method: 'POST',
        data: {
            action: 'getUserSettings'
        }, 
        success: function (data) {
            var settings = JSON.parse(data);
            localStorage.setItem( 'profchen_settings', JSON.stringify(settings) );
        }
    });
    return result;
}

function getCachedGyms() {
    return JSON.parse( localStorage.getItem("profchen_gyms") ); 
}

function getCachedNews() {
    return JSON.parse( localStorage.getItem("profchen_news") ); 
}

function getCachedRaidBosses() {
    return JSON.parse( localStorage.getItem("profchen_raidBoss") ); 
}

function getCachedSettings() {
    return JSON.parse( localStorage.getItem("profchen_settings") ); 
}

function getCachedVersion() {
    return localStorage.getItem("profchen_version");
}

function getCachedUserId() {
    return localStorage.getItem("profchen_user_id");
}

//======================================================================
// MAP FUNCTIONS
//======================================================================

/**
 * 
 * @param {type} $el
 * @returns {google.maps.Map|createMap.map}
 */
function createMap( $el ) {

    var zoom = 12;
    var settings = getCachedSettings();
    var lat = 48.033353;
    var lng = -1.601819;
    if( settings.mapDefaultPosition == 'discord_chartres' ) {
        lng = -1.699008744618699;
    }

    var args = {
            zoom                : zoom,
            streetViewControl   : false,
            center              : new google.maps.LatLng(lat, lng),
            mapTypeId           : google.maps.MapTypeId.ROADMAP,
            disableDefaultUI    : true
    };	        	
    var map = new google.maps.Map( $el[0], args);
    map.markers = [];	

    /*map.addListener('zoom_changed', function() {
        //setCookie('mapZoom', map.getZoom(), 30);
        //updateSetting( 'mapZoom', map.getZoom() );
    });*/
    
    /*map.addListener('center_changed', function () {
        console.log( map.getCenter().lng() );
    });*/    

    // return
    return map;	
}

/**
 * 
 * @returns {undefined}
 */
function loadMarkers() {   
    
    if ( $( ".acf-map" ).length === 0 ) {
        return;
    }
    
    var gyms = getCachedGyms(); 
    var settings = getCachedSettings();
    deleteGymMarkers();
    window.gymMarkers = [];   
    gyms.forEach(function(gym) {       
        if( (settings.mapHideEmpty === false && gym.raid === false) || gym.raid !== false ) {
            var mapMarker = addGymMarker( gym, window.map );
            window.gymMarkers.push(mapMarker); 
        }              
    });   
    displayPlayerOnMap();
}

/**
 * 
 * @param {type} item
 * @param {type} map
 * @returns {google.maps.Marker|addGymMarker.mapMarker}
 */
function addGymMarker( item, map ) {

	// var
	var latlng = new google.maps.LatLng( item.GPSCoordinates.lat, item.GPSCoordinates.lng );
        
        //Create image
        var image = 'https://assets.profchen.fr/img/map/map_marker_default.png';
        var label = false;
        var zindex = 1;
        var raidId = false;
        var url = false;
        if( item.raid !== false ) {
            var now = moment();
            raidId = item.raid.id;
            if( item.raid.status === 'active' ) {
                var raidEndTime = moment( item.raid.endTime );
                label = raidEndTime.diff(now, 'minutes') + ' min';                
                url = 'https://assets.profchen.fr/img/map/map_marker_future_'+item.raid.eggLevel+'.png';
                if( item.raid.pokemon != false && typeof item.raid.pokemon.pokedexId != 'undefined' ) {
                    url = 'https://assets.profchen.fr/img/map/map_marker_pokemon_'+item.raid.pokemon.pokedexId+'.png';    
                }
            } else {
                var raidStartTime = moment( item.raid.startTime );
                label = raidStartTime.diff(now, 'minutes') + ' min';
                url = 'https://assets.profchen.fr/img/map/map_marker_future_'+item.raid.eggLevel+'.png';
            }
            zindex = item.raid.eggLevel * 100;
            image = {
                 url: url,
                 size: new google.maps.Size(46, 49),
                 scaledSize: new google.maps.Size(46, 49),
                 labelOrigin: new google.maps.Point(23, 39)
            };             
        }       
	// create marker
	var mapMarker = new google.maps.Marker({
		position	: latlng,
		map		: map,
                title: 'Hello World!',
                label: {
                    text: label,
                    color: 'white',
                    fontSize: '11px'
                },
                labelClass: "labels",
                icon: image,
                raidId: raidId,
                gymId: item.id,
                gymNameFr: item.nameFr,
                gymCity:  item.city,
                zIndex: zindex
                
	});
        
	// add to array
	//map.markers.push( mapMarker );
        google.maps.event.addListener(mapMarker, 'click', function() {
                openGymModal( mapMarker.gymId );

        });
        
        return mapMarker;

}

/**
 * 
 * @returns {undefined}
 */
function centerMapToPlayer() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            var pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };

            window.map.setCenter(pos);
        });
    }
}

/**
 * 
 * @returns {undefined}
 */
function displayPlayerOnMap() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            
            var pos = new google.maps.LatLng( position.coords.latitude, position.coords.longitude )

            var mapMarker = new google.maps.Marker({
                    position	: pos,
                    map		: map,
                    title: 'It\'s me, Mario',
                    icon: 'https://assets.profchen.fr/img/map/map_marker_player.png',
                    zIndex: 9999
            });
            window.gymMarkers.push(mapMarker); 
            return mapMarker;
        });
    }    
}

/**
 * 
 * @returns {undefined}
 */
function deleteGymMarkers() {
    if( !window.gymMarkers ) {
        return;
    }
    for (var i = 0; i < window.gymMarkers.length; i++) {
        window.gymMarkers[i].setMap(null);
    }
    window.gymMarkers = [];
}

//======================================================================
// ALERTS FUNCTIONS
//======================================================================

/**
 * 
 * @returns {undefined}
 */
function loadRaids() {
    console.log('_____ loadRaids _____');
    var gyms = getCachedGyms();
    var futureRaids = [];
    var activeRaids = [];
    var now = moment();
    gyms.forEach(function(gym) {

        if( gym.raid != false && now.isBefore(gym.raid.startTime) ) {
            futureRaids.push(gym);
        } else if( gym.raid != false && now.isBefore(gym.raid.endTime) ) {
            activeRaids.push(gym);
        }

    });
    
    $('.raids__future .raids__wrapper').html( '' );
    if( futureRaids.length > 0 ) {
        $('.raids__future').removeClass('hide');
        futureRaids.forEach(function(gym) {
            $('.raids__future .raids__wrapper').append( htmlRaid(gym) );
        });
    } else {
        $('.raids__future').addClass('hide');
    }
    
    $('.raids__active .raids__wrapper').html( '' );
    if( activeRaids.length > 0 ) {
        $('.raids__active').removeClass('hide');
        activeRaids.forEach(function(gym) {
            $('.raids__active .raids__wrapper').append( htmlRaid(gym) );
        });
    } else {
        $('.raids__active').addClass('hide');
    }
    
    if( activeRaids.length === 0 && futureRaids.length === 0 ) {
        $('.raids__empty').removeClass('hide');
    } else {
        $('.raids__empty').addClass('hide');
    }
    
    activeTimers();
    $('.raid__wrapper').click( function() {
        openGymModal( $(this).attr('data-gym-id') );
    });     

}

/**
 * 
 * @param {type} gym
 * @returns {String}
 */
function htmlRaid( gym ) {
    var html = '';
    var now = moment();
    var raidStatus = 'future';
    if( now.isAfter(gym.raid.startTime) ) {
        raidStatus = 'active';
    }
    var startTime = moment(gym.raid.startTime);
    var endTime = moment(gym.raid.endTime);
    html += '<div class="raid__wrapper" data-gym-id="'+gym.id+'">';
        html += '<div class="raid__img">';
            if( gym.raid.pokemon == false ) {
                html += '<img src="https://assets.profchen.fr/img/eggs/egg_'+gym.raid.eggLevel+'.png" />';
            } else {
                html += '<img src="'+gym.raid.pokemon.thumbnailUrl+'" />';
            }        
        html += '</div>';
        html += '<div class="raid__content">';
            html += '<h3>'+gym.raid.eggLevel+'T de '+startTime.format('HH[h]mm')+' à '+endTime.format('HH[h]mm');
            html += '<span class="raid__timer '+raidStatus+'" data-start="'+gym.raid.startTime+'" data-end="'+gym.raid.endTime+'">';
            html += '</h3>';
            html += '<div class="raid__gym"><img src="https://d30y9cdsu7xlg0.cloudfront.net/png/4096-200.png" />';
            html += gym.city + ' - ' + gym.nameFr;
            html += '</div>';
        html += '</div>';
    html += '</div>';
    return html;
}

/**
 * 
 * @returns {undefined}
 */
function activeTimers() {
    
    $('.raid__timer.future').each(function( index ) {
        var startTime = $(this).attr('data-start');
        $(this).countdown(startTime, {defer: null})
            .on('update.countdown', function(event) {
                $(this).text(
                    event.strftime('Dans %N min')
                );
            })
            .on('finish.countdown', function(event) {
                    loadRaids();
            })
            .countdown('start');
    });
    
    $('.raid__timer.active').each(function( index ) {
        var endTime = $(this).attr('data-end');
        $(this).countdown(endTime, {defer: null})
            .on('update.countdown', function(event) {
                $(this).text(
                    event.strftime('Reste %N min')
                );
            })
            .on('finish.countdown', function(event) {
                    loadRaids();
            })
            .countdown('start');
    });
    
}

//======================================================================
// NEWS FUNCTIONS
//======================================================================

/**
 * 
 * @returns {undefined}
 */
function loadNews() {
    var news = getCachedNews();
    
    $('#timeline').html( '' );
    news.forEach(function(newsItem) {
        $('#timeline').append( htmlNews(newsItem) );
    });
    
}

/**
 * 
 * @param {type} gym
 * @returns {String}
 */
function htmlNews( news ) {
    var html = '';
    var publishDate = moment(news.publishDate);
    var importantClass = '';
    if( news.isImportant === true ) {
        importantClass = 'important';
    }
    html += '<div class="news__wrapper '+importantClass+'">';
        html += '<div class="news__left">';
            html += '<div class="news__marker">';
                html += publishDate.format('DD/MM');
                if( news.isImportant === true ) {
                    html += '<i class="material-icons">announcement</i>';
                }                
            html += '</div>';
        html += '</div>';
        html += '<div class="news__right">';
            html += '<div class="news__content">';
                html += '<h3>'+news.nameFr+'</h3>';
                if( news.contentMap !== false ) {
                    html += news.contentMap;
                }
            html += '</div>';
        html += '</div>';
    html += '</div>';
    return html;
}

//======================================================================
// GENERAL HTML FUNCTIONS
//======================================================================

function openGymModal( gymId ) {
    
    var cachedGyms = getCachedGyms();
    var gym = false;
    cachedGyms.forEach(function(cachedGym) {
        if( cachedGym.id == gymId ) {
            gym = cachedGym;
        }
    });
    
    var dialog = document.querySelector('#dialog');
    if (!dialog.showModal) {
        dialogPolyfill.registerDialog(dialog);
    }
    
    //
    loadModalGymData( gym );
    loadModalChoosePokemon( gym );
    loadPokemonChoices( gym );
    
    //Gestion de la fermeture du dialog
    dialog.showModal();
    dialog.querySelector('button:not([disabled])')
            .addEventListener('click', function () {
                dialog.close();
            });
    $('a.modal__action.update-raid').click(function() {
        modalActiveScreen('update-raid');
        event.preventDefault();   
    });
    $('a.modal__action.create-raid').click(function() {
        modalActiveScreen('create-raid');
        updateTimeRange( $('.range').val() );
        event.preventDefault();   
    });
    $('a.modal__action.delete-raid').click(function() {
        deleteRaid( gym );
        event.preventDefault();   
    });
    $('a.modal__action.cancel').click(function() {
        modalActiveScreen('gym');
        event.preventDefault();   
    });
    $('.update-raid__wrapper ul li a').click( function(){
        updateRaidBoss( gym, $(this).data('pokemon-id'), $(this).data('pokemon-namefr'), dialog );
        event.preventDefault();
    });
    
    $('.modal__screen [data-step-name="level"] button').click( function(){
        var eggLevel = $(this).data('level');
        console.log(eggLevel);
        $('.modal__screen [data-step-name="level"] button').removeClass('active');
        $(this).addClass('active');
        $('.modal__screen [data-step-name="boss"] li').each( function() {
            if( $(this).attr('data-pokemon-level') == eggLevel ) {
                $(this).show();
            } else {
                $(this).hide();
            }           
        });
        if( $('.modal__screen [data-step-name="level"]').attr('data-validate') == 'oui' ) {
            var startTime = $('.step__timer').attr('data-starttime');
            sendNewFutureRaid( gym, $(this).data('level'), startTime, dialog );
            event.preventDefault();
        } else {
            console.log('raid en cours');
        }
        event.preventDefault();
    });
    $('.modal__screen [data-step-name="boss"] ul li').click( function(){
        var startTime = $('.step__timer').attr('data-starttime');
        sendNewActiveRaid( gym, $(this).data('pokemon-id'), $(this).data('pokemon-namefr'), $(this).data('pokemon-level'), startTime, dialog );
        event.preventDefault();
    });
    /*$('.modal__screen [data-validate="true"] button').click( function(){
        var startTime = $('.step__timer').data('starttime');
        sendNewFutureRaid( gym, $(this).data('level'), startTime, dialog );
        event.preventDefault();
    });*/
    
}

function loadModalGymData( gym ) {

    modalActiveScreen('gym');
    var userSettings = getCachedSettings(); 
    
    //Préparation des valeurs
    var now = moment();
    $('#dialog .mdl-dialog__title').html(gym.nameFr);
    $('#dialog .mdl-dialog__city').html(gym.city);
    $('.mdl-dialog__egg').removeClass('active');
    $('.mdl-dialog__egg').removeClass('future');
    $('.mdl-dialog__egg').removeClass('empty');
    $('.mdl-dialog__egg').removeClass('hide');

    //Si il y a un raid à venir
    if( gym.raid != false && now.isBefore(gym.raid.startTime) ) {

        $('.mdl-dialog__egg').addClass('future');
        $('.mdl-dialog__egg .annonce').html('Un oeuf <strong>'+gym.raid.eggLevel+' têtes</strong> va bientot éclore...');
        $('.mdl-dialog__egg img').attr( 'src', 'https://assets.profchen.fr/img/eggs/egg_'+gym.raid.eggLevel+'.png');
        $('#dialog .mdl-dialog__counter').countdown(gym.raid.startTime, {defer: null})
            .on('update.countdown', function(event) {
                $('#dialog .mdl-dialog__counter').text( event.strftime('%N:%S'));
            })
            .on('finish.countdown', function(event) {
                    console.log('Début du raid');
                    loadModalGymData( gym );
            })
            .countdown('start');
    } else if( gym.raid != false && now.isBefore(gym.raid.endTime) ) {
        if( gym.raid.pokemon == false || typeof gym.raid.pokemon.nameFr == 'undefined' ) {
            $('.mdl-dialog__egg').addClass('active');
            $('.mdl-dialog__egg .annonce').html('Un raid <strong>'+gym.raid.eggLevel+' têtes</strong> est en cours...');
            $('.mdl-dialog__egg img').attr( 'src', 'https://assets.profchen.fr/img/eggs/egg_'+gym.raid.eggLevel+'.png');                       
            $('#dialog .mdl-dialog__counter').countdown(gym.raid.endTime, {defer: null})
                .on('update.countdown', function(event) {
                    $('#dialog .mdl-dialog__counter').text( event.strftime('%N:%S'));
                })
                .on('finish.countdown', function(event) {
                        console.log('Raid terminé');
                        loadModalGymData( gym );
                })
                .countdown('start');
        } else {
            $('.mdl-dialog__egg').addClass('active');
            $('.mdl-dialog__egg .annonce').html('Un raid <strong>'+gym.raid.pokemon.nameFr+'</strong> est en cours...');
            $('.mdl-dialog__egg img').attr( 'src', gym.raid.pokemon.thumbnailUrl);
            $("#dialog .mdl-dialog__counter").countdown(gym.raid.endTime, function (event) {
                $(this).text(
                    event.strftime('%N:%S')
                );
            }); 
            $('#dialog .mdl-dialog__counter').countdown(gym.raid.endTime, {defer: null})
                .on('update.countdown', function(event) {
                    $('#dialog .mdl-dialog__counter').text( event.strftime('%N:%S'));
                })
                .on('finish.countdown', function(event) {
                        console.log('Raid terminé');
                        loadModalGymData( gym );
                })
                .countdown('start');
        }
    } else {
        $('.mdl-dialog__egg').addClass('empty');
        $('.mdl-dialog__egg .annonce').html('Rien en ce moment...');
        $('.mdl-dialog__egg .source').html('');
        $('.mdl-dialog__egg img').attr( 'src', 'https://assets.profchen.fr/img/eggs/egg_0.png');
    }

    
    //Gestion de la source à afficher en haut
    if( gym.raid != false && now.isBefore(gym.raid.endTime) && gym.raid.source != false && gym.raid.source.type == 'map' ) {
        $('.mdl-dialog__egg .source').html('Annoncé depuis la map ');
    } else if( gym.raid != false && now.isBefore(gym.raid.endTime) && gym.raid.source != false && gym.raid.source.community != false ) {
        $('.mdl-dialog__egg .source').html('Annoncé depuis le '+ gym.raid.source.community.nameFr );
    }

    
    //Gestion des actions à afficher
    $('.mdl-dialog__content ul').html('');
    if( gym.raid == false || now.isAfter(gym.raid.endTime) ) {
        $('.mdl-dialog__content ul').append('<li><a class="modal__action create-raid" href="#"><i class="material-icons">add_alert</i><span>Annoncer un raid</span></li>');
    }
    if( gym.raid != false && !now.isBefore(gym.raid.startTime) && now.isBefore(gym.raid.endTime) && typeof gym.raid.pokemon.nameFr == 'undefined' ) {
        $('.mdl-dialog__content ul').append('<li><a class="modal__action update-raid" href="#"><i class="material-icons">fingerprint</i><span>Préciser le Pokémon</span></li>');
    }
    if( gym.raid != false && now.isBefore(gym.raid.endTime) && userSettings.user.role == 'communityAdmin' ) {
        $('.mdl-dialog__content ul').append('<li><a class="modal__action delete-raid" href="#"><i class="material-icons">delete</i><span>Supprimer le raid</span></li>');
    }
    $('.mdl-dialog__content ul').append('<li><a href="'+gym.GoogleMapsUrl+'"><i class="material-icons">navigation</i><span>Itinéraire vers l\'arène</span></li>');
    if( gym.raid != false && now.isBefore(gym.raid.endTime) && gym.raid.source != false && gym.raid.source.url != false ) {
        $('.mdl-dialog__content ul').append('<li><a href="'+gym.raid.source.url+'"><i class="material-icons">message</i><span>Rejoindre la conversation</span></li>');
    } 
}

function loadModalChoosePokemon( gym ) {
    var html = '<ul>';
    var raidBosses = getCachedRaidBosses();
    var gym = gym;
    $.each( raidBosses, function( i, pokemon ) {
        if( pokemon.eggLevel == gym.raid.eggLevel ) {
            html += '<li ><a data-raid-id="'+gym.raid.id+'" data-pokemon-id="'+pokemon.id+'" data-pokemon-namefr="'+pokemon.nameFr+'"><img src="'+pokemon.thumbnailUrl+'"></a></li>';
        }
    });
    html += '</ul>';
    $('.update-raid__wrapper').html( html );
}

function updateRaidBoss( gym, bossId, bossNameFr, dialog ) {
    var result = confirm('Confirmer '+bossNameFr+' comme boss de raid actuellement dans l\'arène '+gym.nameFr+' ('+gym.city+') ?');
    if (result) {
        dialog.close();
        displayPermanentMessage('Enregistrement en cours...');
        var result = $.ajax({
            url: siteConfig.siteUrl+'/api/v1/raid/'+gym.raid.id+'/update?token=AsdxZRqPkrst67utwHVM2w4rt4HjxGNcX8XVJDryMtffBFZk3VGM47HkvnF9&pokemonId='+bossId+'&userId='+getCachedUserId(), 
            method: 'POST', 
            success: function (data) {
                downloadGyms().then( function(){
                        loadRaids();
                        loadMarkers();
                        displayDeleteMessage('Enregistrement réussi');
                    });
            }
        });
        return result;        
    }    
}

function modalActiveScreen( name ) {
    $('.modal__screen').each( function( screen ){
        var screenName = $(this).data('screen');
        if( screenName == name ) {
            console.log(screenName+' show');
            $(this).show();
        } else {
            console.log(screenName+' hide');
            $(this).hide();
        }
    });
}

function displayPermanentMessage( message ) {
    $('#message').addClass('display');
    $('#message').html(message);  
}

function displayDeleteMessage( message ) { 
    $('#message').addClass('display');
    $('#message').html(message);
    setTimeout(function(){
        $('#message').removeClass('display');
    }, 1000);     
}

//======================================================================
// MODAL CREATE RAID
//======================================================================
function updateTimeRange( val ) {
    var raidStartTime = moment();
    var raidEndTime = moment();
    if( val >= 0 ) {
        var timeLeft = siteConfig.activeRaidDuration - val;
        raidStartTime.subtract(val, 'minutes').minutes();
        raidEndTime.add(timeLeft, 'minutes').minutes();
        $('.step__timer' ).attr( 'data-starttime', raidStartTime.format('YYYY-MM-DD HH:mm:ss') );
        $('.step__timer--delai').html('Le raid est en cours. Il reste <strong>' + timeLeft + ' min</strong> '); 
        $('.step__timer--horaires').html( 'De <strong>' + raidStartTime.format('HH[h]mm') + '</strong>  à <strong>' + raidEndTime.format('HH[h]mm') + '</strong> ' );
        $('.modal__screen [data-step-name="boss"]').show();
        $('.modal__screen [data-step-name="level"]').attr('data-validate', 'non');
    } else {
        var timeLeft = Math.abs(val);
        raidStartTime.add(timeLeft, 'minutes').minutes();
        raidEndTime.add(timeLeft + siteConfig.activeRaidDuration, 'minutes').minutes();
        $('.step__timer').attr( 'data-starttime', raidStartTime.format('YYYY-MM-DD HH:mm:ss') );
        $('.step__timer--delai').html('Le raid débute dans <strong>' + timeLeft + ' min</strong> ');
        $('.step__timer--horaires').html( 'De <strong>' + raidStartTime.format('HH[h]mm') + '</strong>  à <strong>' + raidEndTime.format('HH[h]mm') + '</strong> ' );
        $('.modal__screen [data-step-name="boss"]').hide();
        $('.modal__screen [data-step-name="level"]').attr('data-validate', 'oui');
    }
}

function loadPokemonChoices( gym ) {
    $('[data-step-name="level"] .step__wrapper').html('<ul><li><button data-level="1">1T</button></li><li><button data-level="2">2T</button></li><li><button data-level="3">3T</button></li><li><button data-level="4">4T</button></li><li><button data-level="5">5T</button></li></ul>');
    var html = '<ul>';
    var raidBosses = getCachedRaidBosses();
    var gym = gym;
    $.each( raidBosses, function( i, pokemon ) {
        html += '<li data-pokemon-level="'+pokemon.eggLevel+'" data-pokemon-id="'+pokemon.id+'" data-pokemon-namefr="'+pokemon.nameFr+'"><a><img src="'+pokemon.thumbnailUrl+'"></a></li>';
    });
    html += '</ul>';
    $('.modal__screen [data-step-name="boss"] .step__wrapper').html( html );
}

function sendNewActiveRaid( gym, bossId, bossNameFr, eggLevel, startTime, dialog ) {
    var result = confirm('Confirmer '+bossNameFr+' comme boss de raid actuellement dans l\'arène '+gym.nameFr+' ('+gym.city+') ?');
    if (result) {
        dialog.close();
        displayPermanentMessage('Enregistrement en cours...');
        var result = $.ajax({
            url: siteConfig.siteUrl+'/api/v1/raids/add?token=AsdxZRqPkrst67utwHVM2w4rt4HjxGNcX8XVJDryMtffBFZk3VGM47HkvnF9&gymId='+gym.id+'&eggLevel='+eggLevel+'&pokemonId='+bossId+'&date='+encodeURI(startTime)+'&sourceType=map&userId='+getCachedUserId(), 
            method: 'GET', 
            success: function (data) {
                downloadGyms().then( function(){
                        loadRaids();
                        loadMarkers();
                        displayDeleteMessage('Enregistrement réussi');
                    });
            }
        });
        return result;        
    }      
}

function sendNewFutureRaid( gym, eggLevel, startTime, dialog ) {
    console.log(startTime);
    var result = confirm('Confirmer un raid '+eggLevel+'T à venir à l\'arène '+gym.nameFr+' ('+gym.city+') ?');
    if (result) {
        dialog.close();
        displayPermanentMessage('Enregistrement en cours...');
        var result = $.ajax({
            url: siteConfig.siteUrl+'/api/v1/raids/add?token=AsdxZRqPkrst67utwHVM2w4rt4HjxGNcX8XVJDryMtffBFZk3VGM47HkvnF9&gymId='+gym.id+'&eggLevel='+eggLevel+'&date='+encodeURI(startTime)+'&sourceType=map&userId='+getCachedUserId(), 
            method: 'GET', 
            success: function (data) {
                downloadGyms().then( function(){
                        loadRaids();
                        loadMarkers();
                        displayDeleteMessage('Enregistrement réussi');
                    });
            }
        });
        return result;        
    }      
}

function deleteRaid( gym ) {
    var userSettings = getCachedSettings(); 
    var result = confirm('Supprimer le raid à l\'arène '+gym.nameFr+' ('+gym.city+') ?');
    if (result) {
        dialog.close();
        displayPermanentMessage('Suppression en cours...');
        var result = $.ajax({
            url: siteConfig.siteUrl+'/api/v1/raid/'+gym.raid.id+'/delete?token='+userSettings.user.secretKey, 
            method: 'POST', 
            success: function (data) {
                downloadGyms().then( function(){
                        loadRaids();
                        loadMarkers();
                        displayDeleteMessage('Le raid a été supprmimé');
                    });               
            }
        });
        return result;        
    }    
}
//======================================================================
// SETTINGS FUNCTIONS
//======================================================================



<?php

class POGO_ajax {
    
    function __construct() {
        add_action( 'wp_ajax_getUserSettings', array('POGO_settings', 'getUserSettings') );
        add_action( 'wp_ajax_saveToggleSetting', array('POGO_settings', 'saveToggleSetting') );
        add_action( 'wp_ajax_saveSelectSetting', array('POGO_settings', 'saveSelectSetting') );
    }
    
}

new POGO_ajax();

<?php

class POGO_config {
    
    const APP_NAME = 'Prof Chen';
    const ASSETS_URL =  'https://assets.profchen.fr';
    const APP_URL = 'https://yourdomain.com';
    
    const APP_API_PUBLIC_KEY = 'YOUR_APP_API_PUBLIC_KEY';
    const APP_API_PRIVATE_KEY = 'YOUR_APP_API_PRIVATE_KEY';
    
    const SETTINGS = array(
        
        //External API keys
        'GoogleMapsApiKey'  => 'YOUR_GOOGLE_MAPS_API_KEY',
        'MicrosoftApiKey'   => 'YOUR_MICROSOFT_API_KEY',
        'MicrosoftServer'   => 'YOUR_MICROSOFT_SERVER',
        
        //Time for raids
        'futureRaidDuration' => 60,
        'activeRaidDuration' => 45,
    );
    
    public static function get( $setting ) {
        
        if( !isset( POGO_config::SETTINGS[$setting] ) ) {
            return false;
        }
        
        return apply_filters( 'pogo/config/'.$setting, POGO_config::SETTINGS[$setting] );
        
    }
    
}


<?php

class POGO_routes {
    
    function __construct() {
        add_action( 'template_redirect', array( $this, 'checkAcces' ), 0 );
        add_action( 'template_redirect', array( $this, 'profWillDo' ), 30 );
        add_action( 'wp_logout ', array( $this, 'logoutRedirect' ), 20, 3 );
        add_filter( 'login_redirect ', array( $this, 'loginRedirect' ), 20, 3 );
    }
    
    public static function getLoginPageId() {
        return 1530;
    }
    
    public static function getLoadingPageId() {
        return 1635;
    }
    
    public static function getLogoutPageId() {
        return 1650;
    }
    
    public static function getPolicyPageId() {
        return 1716;
    }
    
    public static function getNewsPageId() {
        return 1081;
    }
    public static function getAdminPageId() {
        return 1959;
    }
    public static function getAdminConnectorsPageId() {
        return 1961;
    }
    public static function getAdminNewConnectorPageId() {
        return 2122;
    }
    public static function getAdminCommunitySettingsPageId() {
        return 2070;
    }
    public static function getAdminConnectorSettingsPageId() {
        return 2078;
    }
    
    public static function getNonLoggedPages() {
        return array( POGO_routes::getLoginPageId(), POGO_routes::getLogoutPageId(), 1791 );
    }
    
    public function checkAcces() {
        if( !in_array( get_the_ID(), POGO_routes::getNonLoggedPages() ) && !is_user_logged_in() ) {
            include( get_stylesheet_directory() . '/templates/login.php' );
            exit;
        }
    }
    
    public function loginRedirect( $redirect_to, $request, $user ) {
        var_dump( POGO_routes::getLoadingPageId() );
        return get_permalink( POGO_routes::getLoadingPageId() );
    } 
    
    public function logoutRedirect() {
        wp_redirect( home_url() );
        exit;
    }
    
    public function profWillDo() {
        if( !isset( $_GET['profWillDo'] ) || empty( $_GET['profWillDo'] ) ) return;
        
        $action = $_GET['profWillDo'];
        $user = new POGO_user( get_current_user_id() );
        $pid = ( isset( $_GET['pid'] ) && !empty( $_GET['pid'] ) ) ? $_GET['pid'] : false;        
        
        switch ($action) {
            case 'delete':                
                if( !$pid ) return;
                if( get_post_type($pid) == 'connector' ) {
                    $connector = new POGO_connector($pid);
                    if( !$connector->getCommunity() ) return;
                    if( !$user->canEditCommunity( $connector->getCommunity()->wpId ) ) return;                    
                    $connector->delete();
                    error_log( print_r($connector, true) );
                    $redirect = get_permalink( POGO_routes::getAdminConnectorsPageId() ).'?communityId='.$connector->getCommunity()->wpId;
                }
                break;
        }
        
        if ($redirect) {
            wp_redirect($redirect);
            die();
        }
    }

}

new POGO_routes();


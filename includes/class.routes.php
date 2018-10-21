<?php

class POGO_routes {
    
    function __construct() {
        add_action( 'template_redirect', array( $this, 'checkAcces' ), 0 );
        add_action( 'template_redirect', array( $this, 'profWillDo' ), 30 );
        add_action( 'wp_logout ', array( $this, 'logoutRedirect' ), 20, 3 );
        add_filter( 'login_redirect ', array( $this, 'loginRedirect' ), 20, 3 );
    }
    
    public static function getMapsPageId() {
        return get_option( 'page_on_front' );
    }
    
    public static function getAlertsPageId() {
        $page = get_page_by_path('alerts');
        if( $page ) {
            return $page->ID;
        }
        return false;
    }
    
    public static function getSettingsPageId() {
        $page = get_page_by_path('settings');
        if( $page ) {
            return $page->ID;
        }
        return false;
    }
    
    public static function getLoginPageId() {
        $page = get_page_by_path('login');
        if( $page ) {
            return $page->ID;
        }
        return false;
    }
    
    public static function getLoadingPageId() {
        $page = get_page_by_path('loading');
        if( $page ) {
            return $page->ID;
        }
        return false;
    }
    
    public static function getLogoutPageId() {
        $page = get_page_by_path('logout');
        if( $page ) {
            return $page->ID;
        }
        return false;
    }
    
    public static function getPolicyPageId() {
        $page = get_page_by_path('settings/policy');
        if( $page ) {
            return $page->ID;
        }
        return false;
    }
    
    public static function getNewsPageId() {
        $page = get_page_by_path('admin/news');
        if( $page ) {
            return $page->ID;
        }
        return false;
    }
    
    public static function getAdminPageId() {
        $page = get_page_by_path('admin');
        if( $page ) {
            return $page->ID;
        }
        return false;
    }
    
    public static function getAdminConnectorsPageId() {
        $page = get_page_by_path('admin/connectors');
        if( $page ) {
            return $page->ID;
        }
        return false;
    }
    public static function getAdminNewConnectorPageId() {
        $page = get_page_by_path('admin/connectors/new');
        if( $page ) {
            return $page->ID;
        }
        return false;
    }
    public static function getAdminCommunitySettingsPageId() {
        $page = get_page_by_path('admin/community');
        if( $page ) {
            return $page->ID;
        }
        return false;
    }
    public static function getAdminConnectorSettingsPageId() {
        $page = get_page_by_path('admin/connectors/edit');
        if( $page ) {
            return $page->ID;
        }
        return false;
    }
    
    public static function getNonLoggedPages() {
        return array( POGO_routes::getLoginPageId(), POGO_routes::getLogoutPageId(), 1791 );
    }
    
    public function checkAcces() {
        if( !in_array( get_the_ID(), POGO_routes::getNonLoggedPages() ) && !is_user_logged_in() ) {
            include( get_template_directory() . '/templates/login.php' );
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


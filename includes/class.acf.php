<?php

class POGO_acf {
    
    function __construct() {
        add_filter('acf/settings/save_json', array( $this, 'saveFolder') );
        add_filter('acf/settings/load_json', array( $this, 'loadFolder') );
        add_action('acf/init', array( $this, 'setGoogleApiKey' ) );
        /*
        add_filter('acf/fields/relationship/result/name=connector_gyms', array( $this, 'displayGymName'), 10, 4);
        add_filter('acf/fields/post_object/result/name=raid_gym', array( $this, 'displayGymName'), 10, 4);
        */
        add_filter('acf/load_field/name=community_type', array( $this, 'readOnly' ) );
        add_filter('acf/load_field/name=community_discordid', array( $this, 'readOnly' ) );
        
        add_filter('acf/load_field/name=connector_pokemon', array( $this, 'loadRaidBosses' ) );
        
        add_filter('acf/load_field/name=connector_gyms', array( $this, 'loadGyms' ) );
        add_filter('acf/load_field/name=connector_cities', array( $this, 'loadCities' ) );
        //add_filter('acf/load_field/name=connector_community', array( $this, 'loadCommunities' ) ); 
        
        //add_filter('acf/load_field/name=admin_communities', array( $this, 'loadCommunities' ) );
        
        add_filter('acf/load_field/name=raid_boss', array( $this, 'loadRaidBosses' ) );
    }
    
    function saveFolder( $path ) {
        $path = get_template_directory() . '/admin/fields';
        return $path;        
    }
    
    function loadFolder( $paths ) {
        unset($paths[0]);
        $paths[] = get_template_directory() . '/admin/fields';
        return $paths;
    }
    
    function displayGymName( $title, $post, $field, $post_id ) {
        $gym = new POGO_gym($post->ID);
        return $gym->getCity().' - '.$title;
    }
    
    function setGoogleApiKey( $api ) {
        acf_update_setting('google_api_key', POGO_config::get('GoogleMapsApiKey') );
    }
    
    function readOnly( $field ) {
        if( is_admin() ) return $field;
        $field['disabled'] = true;
        return $field;
    }
    
    function loadRaidBosses( $field ) {
        switch_to_blog( POGO_network::MAIN_BLOG_ID );
        $choices = array();
        foreach( array(1,2,3,4,5) as $raid_level ) {
            $opt_group = $raid_level.' têtes'; 
            $choices[$opt_group] = array();
            $bosses = POGO_query::getRaidBosses($raid_level);
            foreach( $bosses as $boss ) {
                $choices[$opt_group][$boss->wpId] = $boss->getNameFr();
            }
        }
        $field['choices'] = $choices;
        restore_current_blog();
        return $field;
    }
    
    function loadCommunities($field) {
        switch_to_blog( POGO_network::MAIN_BLOG_ID );
        $choices = array();
        $communities = POGO_query::getCommunities();
        foreach( $communities as $community ) {
            $choices[$community->wpId] = $community->getNameFr();
        }
        $field['choices'] = $choices;
        restore_current_blog();
        return $field;
    }
    
    function loadGyms( $field ) {
        $choices = array();
        foreach( POGO_helpers::getCities() as $city ) {
            $opt_group = $city; 
            $choices[$opt_group] = array();
            $gyms = POGO_query::getGyms($city);
            if($gyms) {
                foreach( $gyms as $gym ) {
                    $choices[$opt_group][$gym->wpId] = $gym->getNameFr();
                }                
            }
        }
        $field['choices'] = $choices;
        return $field;
    }
    
    function loadCities( $field ) {
        $choices = array();
        foreach( POGO_helpers::getCities() as $city ) {
            $choices[$city] = $city;
        }
        $field['choices'] = $choices;
        return $field;
    }
    
}

new POGO_acf();



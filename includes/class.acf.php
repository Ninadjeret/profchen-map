<?php

class POGO_acf {
    
    function __construct() {
        add_filter('acf/settings/save_json', array( $this, 'saveFolder') );
        add_filter('acf/settings/load_json', array( $this, 'loadFolder') );
        
        add_filter('acf/fields/relationship/result/name=connector_gyms', array( $this, 'displayGymName'), 10, 4);
        add_filter('acf/fields/post_object/result/name=raid_gym', array( $this, 'displayGymName'), 10, 4);
        add_action('acf/init', array( $this, 'setGoogleApiKey' ) );
        
        add_filter('acf/load_field/name=community_type', array( $this, 'readOnly' ) );
        add_filter('acf/load_field/name=community_discordid', array( $this, 'readOnly' ) );
        
        add_filter('acf/load_field/name=connector_pokemon', array( $this, 'loadRaidBosses' ) );
        add_filter('acf/load_field/name=connector_gyms', array( $this, 'loadGyms' ) );
        add_filter('acf/load_field/name=connector_cities', array( $this, 'loadCities' ) );
    }
    
    function saveFolder( $path ) {
        $path = get_stylesheet_directory() . '/admin/fields';
        return $path;        
    }
    
    function loadFolder( $paths ) {
        unset($paths[0]);
        $paths[] = get_stylesheet_directory() . '/admin/fields';
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
        $choices = array();
        foreach( array(1,2,3,4,5) as $raid_level ) {
            $opt_group = $raid_level.' têtes'; 
            $choices[$opt_group] = array();
            $bosses = POGO_helpers::getRaidBosses($raid_level);
            foreach( $bosses as $boss_id ) {
                $boss = new POGO_pokemon($boss_id);
                $choices[$opt_group][$boss->wpId] = $boss->getNameFr();
            }
        }
        $field['choices'] = $choices;
        return $field;
    }
    
    function loadGyms( $field ) {
        $choices = array();
        foreach( POGO_helpers::getCities() as $city ) {
            $opt_group = $city; 
            $choices[$opt_group] = array();
            $gyms = POGO_query::getGyms($city);
            foreach( $gyms as $gym ) {
                $choices[$opt_group][$gym->wpId] = $gym->getNameFr();
            }
        }
        $field['choices'] = $choices;
        return $field;
    }
    
    function loadCities( $field ) {
        $choices = array();
        foreach( POGO_helpers::getCities() as $city ) {
            $choices[] = $city;
        }
        $field['choices'] = $choices;
        return $field;
    }
    
}

new POGO_acf();


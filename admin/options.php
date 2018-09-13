<?php

class POGO_admin_options {
    
    function __construct() {
	acf_add_options_sub_page(array(
		'page_title' 	=> 'Boss de raid',
		'menu_title' 	=> 'Boss de raid',
		'parent_slug' 	=> 'edit.php?post_type=pokemon',
	));

        /*add_filter('acf/load_value/name=boss_1t', array( $this,'load_1t' ), 10, 3);
        add_filter('acf/load_value/name=boss_2t', array( $this,'load_2t' ), 10, 3);
        add_filter('acf/load_value/name=boss_3t', array( $this,'load_3t' ), 10, 3);
        add_filter('acf/load_value/name=boss_4t', array( $this,'load_4t' ), 10, 3);
        add_filter('acf/load_value/name=boss_5t', array( $this,'load_5t' ), 10, 3);*/
        
        add_action( 'acf/save_post', array( $this, 'deleteRaidBosses' ), 1 );
        add_action( 'acf/save_post', array( $this, 'updateRaidBosses' ), 20 );
    }
    
    function load_1t( $value, $post_id, $field ) {
        return POGO_helpers::getRaidBosses(1);
    }
    
    function load_2t( $value, $post_id, $field ) {
        return POGO_helpers::getRaidBosses(2);
    }
    
    function load_3t( $value, $post_id, $field ) {
        return POGO_helpers::getRaidBosses(3);
    }
    
    function load_4t( $value, $post_id, $field ) {
        return POGO_helpers::getRaidBosses(4);
    }
    
    function load_5t( $value, $post_id, $field ) {
        return POGO_helpers::getRaidBosses(5);
    }
    
    function deleteRaidBosses( $post_id  ) {
        if( $post_id != 'option' ) return;
       
        foreach( array(1,2,3,4,5) as $boss_level ) {
            $bosses = get_field('boss_'.$boss_level.'t', 'option');
            foreach( $bosses as $boss_id ) {
                update_field('raidboss', 0, $boss_id);
                update_field('raidboss_egg', '', $boss_id);
            }
        }
        
    }
    
    function updateRaidBosses( $post_id  ) {
        
        error_log($post_id);
        
        if( $post_id != 'options' ) return;
        
        $news_bosses = array();
        foreach( array(1,2,3,4,5) as $boss_level ) {
            $bosses = get_field('boss_'.$boss_level.'t', 'option');
            foreach( $bosses as $boss_id ) {
                $news_bosses[] = $boss_id; 
                update_field('raidboss', 1, $boss_id);
                update_field('raidboss_egg', $boss_level, $boss_id);
            }
        }
        
        $bosses = POGO_helpers::getRaidBosses();
        foreach( $bosses as $boss_id ) {
            if( !in_array($boss_id, $news_bosses) ) {
                update_field('raidboss', 0, $boss_id);
                update_field('raidboss_egg', '', $boss_id);                 
            }           
        }
        
    }
    
}

new POGO_admin_options();


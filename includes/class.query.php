<?php

class POGO_query {
    
    
    /**
     * 
     * @param type $city
     * @return boolean|\POGO_gym
     */
    public static function getGyms( $city = false ) {
        $args =  array(
            'post_type'         => 'gym',
            'posts_per_page'    => -1,
            'fields'            => 'ids',
            'orderby'           => 'name',
            'order'             => 'ASC',
        ); 
        
        if( !empty( $city ) ) {
            $args['meta_query'] = array();
            $args['meta_query'][] = array(
                'key'       => 'city',
                'value'     => $city,
                'compare'   => '=',
            );
        }
        
        $gyms = get_posts( $args );
        if( empty( $gyms ) ) {
            return false;
        }
        
        $return = array();
        foreach($gyms as $gym_id) {
            $return[] = new POGO_gym($gym_id);
        }

        return $return;         
    }
    
    
    /**
     * 
     * @return boolean
     */
    public static function getRaidBosses( $egglevel = false ) {
        switch_to_blog( POGO_network::MAIN_BLOG_ID );
        $args = array(
            'post_type'         => 'pokemon',
            'posts_per_page'    => -1,
            'fields'            => 'ids',
            'orderby'           => 'meta_value_num',
            'meta_key'          => 'id_pokedex',
            'order'             => 'ASC',
            'meta_query'        => array(
                array(
                    'key'       => 'raidboss',
                    'value'     => 1,
                    'compare'   => '='
                )
            )
        );
        
        if( !empty( $egglevel ) ) {
            $args['meta_query'][] = array(
                'key'       => 'raidboss_egg',
                'value'     => $egglevel,
                'compare'   => '=',
            );
        }
        
        $pokemon = get_posts( $args ); 
        
        if( empty( $pokemon ) ) {
            restore_current_blog();
            return false;
        }
        
        $return = array();
        foreach($pokemon as $pokemon_id) {
            $return[] = new POGO_pokemon($pokemon_id);
        }

        restore_current_blog();
        return $return;       
        
    }
    
    public static function getUserFromSecretKey($secret) {
        $args = array(
            'meta_query' => array(
                array(
                    'key' => 'secret_key',
                    'value' => $secret,
                    'compare' => '='
                ),
            )
        );
        $user_query = new WP_User_Query($args);
        $authors = $user_query->get_results();
        if (!empty($authors)) { 
            $user_id = $authors[0]->ID;
            return new POGO_user($user_id);
        }
        return false;
    }
    
    public static function getCommunities() {
        $communities = get_posts( array(
            'post_type'         => 'community',
            'posts_per_page'    => -1,
            'fields'            => 'ids',
            'orderby'           => 'name',
            'order'             => 'ASC',
        ) ); 

        if( empty( $communities ) ) {
            return false;
        }
        
        $return = array();
        foreach($communities as $community_id) {
            $return[] = new POGO_community($community_id);
        }
        return $return;          
    }

}

new POGO_query();


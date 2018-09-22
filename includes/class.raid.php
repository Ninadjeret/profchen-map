<?php

class POGO_raid {
    
    /**
     * 
     * @param type $wp_id
     * @return boolean
     */
    function __construct( $wp_id ) {
        
        if( get_post_type($wp_id) != 'raid' ) {
            return false;
        }
        
        $this->wpId = $wp_id;        
    }
    
    /**
     * 
     * @param type $args
     * @return boolean|\POGO_raid
     */
    public static function create( $args, $source = false ) {
        if( !isset($args['gym']) || empty($args['gym']) ) return false;
        if( !isset($args['date']) || empty($args['date']) ) return false;
        $pokemon = ( !empty($args['pokemon']) ) ? new POGO_pokemon($args['pokemon']) : false ; 
        $egglevel = ( isset($args['egglevel']) || empty($args['egglevel']) ) ? $args['egglevel'] : false ; 
        $gym_id = $args['gym'];
        
        //Gestion de la date et du statut
        $raiddate = new DateTime($args['date']); 
        $date_auj = new DateTime();
        $raidstatus = ( $raiddate->getTimestamp() > $date_auj->getTimestamp() ) ? 'future' : 'publish' ; 
        
        if( !$pokemon && !$egglevel ) {
            return false;
        }
               
        $raid_id = wp_insert_post( array(
            'post_title'    => 'Raid '.$egglevel.' à l\'arêne '.get_the_title($gym_id),
            'post_type'     => 'raid',
            'post_status'   => $raidstatus,
            'post_date'     => $args['date']
        ) );
        update_field('raid_gym', $gym_id, $raid_id );
        update_field('raid_start_time', $args['date'], $raid_id );
        if( !empty($pokemon) ) {
            update_field('raid_boss', $pokemon->wpId, $raid_id );
            update_field('raid_egglevel', $pokemon->getRaidLevel(), $raid_id );
        } else {
            update_field('raid_egglevel', $egglevel, $raid_id );
        }
        
        $raid = new POGO_raid($raid_id);
        
        $source_type = false;
        error_log( print_r($source, true) );
        if( $source ) {            
            $source_type = $source['type'];            
            $raid->addAnnounce($source);
        }
        
        /**
         * 
         */
        do_action('pogo/raid/create', $raid_id, $source_type);
        
        return $raid;
        
    }
    
    public function delete() {
        wp_trash_post( $this->wpId );
        return true;
    }
    
    /**
     * 
     * @return boolean|\POGO_gym
     */
    public function getGym() {
        if( get_field('raid_gym', $this->wpId) ) {
            return new POGO_gym( get_field('raid_gym', $this->wpId) );
        }
        return false;
    }
    
    public function isFuture() {
        if( get_post_status( $this->wpId ) == 'future' ) {
            return true;
        }
        return false;
    }
    
    /**
     * 
     * @return type
     */
    public function getEggLevel() {
        return get_field('raid_egglevel', $this->wpId);
    }
    
    public function getMatchingConnectors() {
        
        $pokemon_id = ( $this->getPokemon() ) ? $this->getPokemon()->wpId : '000' ;
        
        $args = array(
            'post_type' => 'connector',
            'posts_per_page' => -1,
            'fields' => 'ids',
        );
        $results = get_posts( $args );
        
        if( empty( $results ) ) {
            return false;
        }
        
        $connectors = array();
        $city = ( $this->getGym() && $this->getGym()->getCity() ) ? $this->getGym()->getCity() : false ;
        $gym_id = ( $this->getGym() ) ? $this->getGym()->wpId : false ;
        $pokemon_id = ( $this->getPokemon() ) ? $this->getPokemon()->wpId : false;
        foreach( $results as $connector_id ) {
            $connector = new POGO_connector($connector_id);
            
            if( $connector->filterGyms() == 'city' ) {
                if( !$city ) continue;
                if( !in_array( $city, $connector->getFilteredCities() ) ) continue;
            }
            
            if( $connector->filterGyms() == 'gym' ) {
                if( !$gym_id ) continue;
                if( !in_array( $gym_id, $connector->getFilteredGyms() ) ) continue;
            }
            
            if( $connector->filterPokemon() == 'level' ) {
                if( !$this->getEggLevel() ) continue;
                if( !in_array( $this->getEggLevel(), $connector->getFilteredLevels() ) ) continue;
            }
            
            if( $connector->filterPokemon() == 'pokemon' ) {
                if( !$pokemon_id ) continue;
                if( !in_array( $pokemon_id, $connector->getFilteredPokemon() ) ) continue;
            }
            
            $connectors[] = $connector;
        }
        
        return $connectors;
    }
    
    public function getStartTime() {
        $dbtime = get_the_date('Y-m-d H:i:s', $this->wpId);
        if( empty($dbtime) ) return false;
        
        return new DateTime($dbtime);
    }
    
    public function getEndTime() {
        $dbtime = get_the_date('Y-m-d H:i:s', $this->wpId);
        if( empty($dbtime) ) return false;
        
        $end_time = new DateTime($dbtime); 
        $end_time->modify( '+ '.POGO_config::get('activeRaidDuration').' minutes' );
        return $end_time;
    }
    
    public function getRemainingTime() {
        $now = new DateTime();
        if( $this->isFuture() ) {
            return $now->diff($this->getStartTime());
        }

        return $this->getEndTime()->diff($now);
    }
    
    public function getPokemon() {        
        $pokemon_id = get_field('raid_boss', $this->wpId);
        if( !empty( $pokemon_id ) ) {
            return new POGO_pokemon($pokemon_id);
        }
        return false;
    }
    
    public function updateRaid( $pokemon_id, $source ) {
        if( $this->getPokemon() ) {
            return;
        }
        
        //On prévient une erreur d'analyse en autorisant que si le pokemon correspond bien au niveau du raid annoncé.
        $pokemon = new POGO_pokemon( $pokemon_id );
        if( $pokemon->getRaidLevel() != $this->getEggLevel() ) {
            return;
        }
        
        update_field('raid_boss', $pokemon_id, $this->wpId);
        $this->addAnnounce($source);
        do_action('pogo/raid/update', $this->wpId);
    }
    
    public function getStatus() {
        return get_post_status( $this->wpId );
    }
    
    public function addAnnounce( $args ) {
        error_log( '__________ Creation du commentaire ________' );       
        $author = $args['user'];
        $content = $args['content'];
        $type = $args['type'];
        $community = ( !empty($args['community']) ) ? POGO_community::initFromExternalId($args['community']) : false ;
        $channelid = ( !empty($args['channel']) ) ? $args['channel'] : false ;
        $commentId = wp_insert_comment( array(
            'comment_post_ID' => $this->wpId,
            'comment_author' => $author,
            'comment_author_email' => 'bot@profchen.fr',
            'comment_content' => $content,
            'comment_approved' => 1,
        ) );
        error_log( print_r( $commentId, true ) );
        
        update_field('sourceType', $type, 'comment_'.$commentId);
        if( $community ) update_field('sourceCommunity', $community->wpId, 'comment_'.$commentId);
        if( $channelid ) update_field('sourceChannel', $channelid, 'comment_'.$commentId);
        
    }
    
    public function getFirstAnnounce() {
        $announces = get_comments( array(
            'post_id'   => $this->wpId,
            'order'     => 'ASC',
            'number'    => 1,
        ) );
        
        if( !empty( $announces ) ) {
            return new POGO_announce( $announces[0]->comment_ID );
        } 
        
        return false;
    }
    
    public function getLastAnnounce() {
        $announces = get_comments( array(
            'post_id'   => $this->wpId,
            'order'     => 'DESC',
            'number'    => 1,
        ) );
        
        if( !empty( $announces ) ) {
            return new POGO_announce( $announces[0]->comment_ID );
        } 
        
        return false;
    }

}


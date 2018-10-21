<?php

class POGO_gym {
    
    function __construct( $id, $niantic_id = false ) {
        $this->wpId = $id;
        $this->nianticId = ( $niantic_id ) ? $niantic_id : $this->_getNianticId() ;
    }
    
    private function _getNianticId() {
        return get_field('id_niantic', $this->wpId );
    }
    
    public function getNameFr() {
        return html_entity_decode( get_the_title($this->wpId) );
    }
    
    public function getCity() {
        $value = get_field('city', $this->wpId);
        if( empty($value) ) {
            return false;
        }
        return $value;         
    }
    
    public function getDiscordChannel() {
        $value = get_field('discord_channel', $this->wpId);
        if( empty($value) ) {
            return false;
        }
        return $value;         
    }
    
    public function getSearhPatterns() {
        $return = array( strtolower( POGO_helpers::deleteAccents( $this->nianticId ) ) );
        $patterns = get_field('search_patterns', $this->wpId);
        if( empty( $patterns ) ) {
            return $return;
        }
        foreach( $patterns as $pattern ) {
            $return[] = strtolower( POGO_helpers::deleteAccents( $pattern['pattern'] ) );
        }
        return $return;
    }  
    
    public function getCommunitySearchPatterns() {
        $return = array();
        $patterns = get_field('search_patterns', $this->wpId);
        if( empty( $patterns ) ) {
            return false;
        }
        foreach( $patterns as $pattern ) {
            $sanitized_query = strtolower( POGO_helpers::deleteAccents( $pattern['pattern'] ) );
            
            if( !empty($pattern['communaute']) ) {
                foreach( $pattern['communaute'] as $community_id ) {
                    if( !isset( $return[$sanitized_query] ) ) $return[$sanitized_query] = array();
                    $return[$sanitized_query][] = new POGO_community($community_id);
                }
            }
        }
        return $return;
    }
    
    
    public function getGPSCoordinates() {
        $value = get_field('gps_coordinates', $this->wpId);
        if( empty($value) ) {
            return false;
        }
        return $value;         
    }
    
    public function getGoogleMapsUrl() {
        if( !$this->getGPSCoordinates() ) return false;
        $location = $this->getGPSCoordinates();
        return 'https://www.google.com/maps/search/?api=1&query='.$location['lat'].','.$location['lng'];
    }
    
    public function getCurrentRaid() {
        
        $end = new DateTime();
        $end->setTimezone(new DateTimeZone('Europe/Paris'));
        
        $begin = clone $end;         
        $begin->modify('- '.POGO_config::get('activeRaidDuration').' minutes');

        $args = array(
            'post_type' => 'raid',
            'fields' => 'ids',
            'posts_per_page' => '1',
            'post_status' => array('publish', 'future'),
            'date_query' => array(
                array(
                    'after' => array(
                        'year' => $begin->format('Y'),
                        'month' => $begin->format('m'),
                        'day' => $begin->format('d'),
                        'hour' => $begin->format('H'),
                        'minute' => $begin->format('i')
                    ),
                    'before' => array(
                        'year' => $end->format('Y'),
                        'month' => $end->format('m'),
                        'day' => $end->format('d'),
                        'hour' => $end->format('H'),
                        'minute' => $end->format('i')
                    ),
                    'inclusive' => true,
                ),
            ),
            'meta_query' => array(
                array(
                    'key' => 'raid_gym',
                    'value' => $this->wpId,
                    'compare' => '=',
                )
            ),
        );

        $raids = get_posts($args );
        
        if( empty( $raids ) ) {
            return false;
        }
        return new POGO_raid($raids[0]);
    }
    
    public function getFutureRaid() {
        
        $begin = new DateTime();
        $begin->setTimezone(new DateTimeZone('Europe/Paris'));
        
        $end = clone $begin;         
        $end->modify('+ '.POGO_config::get('futureRaidDuration').' minutes');

        $args = array(
            'post_type' => 'raid',
            'fields' => 'ids',
            'posts_per_page' => '1',
            'post_status' => array('publish', 'future'),
            'date_query' => array(
                array(
                    'after' => array(
                        'year' => $begin->format('Y'),
                        'month' => $begin->format('m'),
                        'day' => $begin->format('d'),
                        'hour' => $begin->format('H'),
                        'minute' => $begin->format('i')
                    ),
                    'before' => array(
                        'year' => $end->format('Y'),
                        'month' => $end->format('m'),
                        'day' => $end->format('d'),
                        'hour' => $end->format('H'),
                        'minute' => $end->format('i')
                    ),
                    'inclusive' => true,
                ),
            ),
            'meta_query' => array(
                array(
                    'key' => 'raid_gym',
                    'value' => $this->wpId,
                    'compare' => '=',
                )
            ),
        );

        $raids = get_posts($args );
        
        if( empty( $raids ) ) {
            return false;
        }
        return new POGO_raid($raids[0]);
    }
    
    public function isRaidEx() {
        $value = get_field('gym_ex', $this->wpId);
        if( empty($value) ) {
            return false;
        }
        return true;  
    }
}


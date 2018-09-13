<?php

class POGO_community {
    
    function __construct( $id, $discord_id = false ) {
        $this->wpId = $id;
        $this->discordId = ( $discord_id ) ? $discord_id : $this->_getDiscordId() ;
        $this->externalId = $this->discordId;
    }
    
    public static function initFromExternalId( $externalId ) {
        if( !POGO_helpers::getCommunityIdFromExternalId($externalId) ) {
            return false;
        }
        return new POGO_community( POGO_helpers::getCommunityIdFromExternalId($externalId) );
    }
    
    private function _getDiscordId() {
        return get_field('community_discordid', $this->wpId );
    }
    
    public function getNameFr() {
        return html_entity_decode( get_field('community_name_fr', $this->wpId) );
    }
    
    public function getType() {
        return get_field('community_type', $this->wpId );
    }
    
    public function getDefaultRaidsCahnnelId() {
        if( $this->getType() == 'discord' ) {
            return get_field('community_default_raids_channel_id', $this->wpId);
        }
        return false;
    }
    
    public function getUrl( $channelId = false ) {
        if( !$channelId ) $channelId = $this->getDefaultRaidsCahnnelId();
        if( $this->getType() == 'discord' ) {
            return 'https://discordapp.com/channels/'.$this->externalId.'/'.$this->getDefaultRaidsCahnnelId();
        }
        if( $this->getType() == 'messenger' ) {
            return 'http://www.facebook.com/messages/?action=read&tid='.$this->externalId;
            return false;
        }
        return false;
    }
    
    public function getConnectors() {
        $connectors = get_posts( array(
            'post_type' => 'connector',
            'fields' => 'ids',
            'post_status' => array('publish'),
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'connector_community',
                    'value' => $this->wpId,
                    'compare' => '=',
                )
            )
        ) );
        
        if( empty( $connectors ) ) return false;
        
        $return = array();
        foreach( $connectors as $connector_id ) {
            $return[] = new POGO_connector($connector_id);
        }
        return $return;
    }
    
    public function displayNianticNews() {
        $val = get_field('community_news_niantic', $this->wpId);
        if( empty($val) ) {
            return false;
        }
        return true;        
    }
    
    public function getNianticNewsChannel() {
        $val = get_field('community_news_niantic_channel_name', $this->wpId);
        if( empty($val) ) {
            return false;
        }
        return $val;        
    }
    
    public function displaySilphroadNews() {
        $val = get_field('community_news_silphroad', $this->wpId);
        if( empty($val) ) {
            return false;
        }
        return true;        
    }
    
    public function getSilphoradNewsChannel() {
        $val = get_field('community_news_silphroad_channel_name', $this->wpId);
        if( empty($val) ) {
            return false;
        }
        return $val;          
    }
    
     public function displayGohubNews() {
        $val = get_field('community_news_gohub', $this->wpId);
        if( empty($val) ) {
            return false;
        }
        return true;        
    }
    
    public function getGohubNewsChannel() {
        $val = get_field('community_news_gohub_channel_name', $this->wpId);
        if( empty($val) ) {
            return false;
        }
        return $val;          
    }
    
    public function welcomeNewMembers() {
        $val = get_field('community_welcome', $this->wpId);
        if( empty($val) ) {
            return false;
        }
        return true;        
    }
    
    public function getNewMemberChannel() {
        $val = get_field('community_welcome_channel', $this->wpId);
        if( empty($val) ) {
            return 'Bienvenue {member}';
        }
        return $val;          
    }
    
    public function getNewMemberMessage() {
        $val = get_field('community_welcome_message', $this->wpId);
        if( empty($val) ) {
            return 'Bienvenue {member}';
        }
        return $val;          
    }

    
    public function detectRaids() {
        $val = get_field('community_raid_detection', $this->wpId);
        if( empty($val) ) {
            return false;
        }
        return $val;          
    }
    
    public function deleteMessageAfterRaidDetection() {
        $val = get_field('community_raid_detection_delete', $this->wpId);
        if( empty($val) ) {
            return false;
        }
        return $val;          
    }
    
}


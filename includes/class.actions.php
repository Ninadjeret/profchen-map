<?php

class POGO_actions {

    function __construct() {
        add_action('pogo/raid/active', array($this, 'UpdateRaidOnPublish'));
        add_action('pogo/raid/create', array($this, 'SendAnnonceToWebhooks'));
        add_action('pogo/raid/update', array($this, 'SendAnnonceToWebhooks'));
        add_action('rss2_item', array( $this, 'customizeRSS' ));
    }

    function SendAnnonceToWebhooks($raid_id) {
        error_log('_____ before sending to webhook _____');
        $raid = new POGO_raid($raid_id);
        $connectors = $raid->getMatchingConnectors();
        if( !$connectors ) return false;
        
        foreach( $connectors as $connector ) {
            $data = $connector->formatData($raid);
            error_log( print_r($data, true) );
            if( !$data ) return false;
            $connector->sendToWebhook($data);
        }
        
    }
    
    function UpdateRaidOnPublish( $raid_id ) {
        $raid = new POGO_raid($raid_id);
        if( $raid->getEggLevel() ) {
            $bosses = POGO_query::getRaidBosses( $raid->getEggLevel() );
            if( !empty( $bosses ) && count( $bosses ) === 1 ) {
                $source = POGO_helpers::getBotSource();
                $raid->updateRaid($bosses[0]->wpId, $source);
            }
        }
    }
    
    function customizeRSS() {
        global $post;
        if( get_post_type( $post->ID ) != 'bot_update' ) return;
        $news = new POGO_news($post->ID);
        echo '<contentCommunity>'.$news->getContentForCommunity().'</contentCommunity>';
        echo '<contentMap>'.$news->getContentForCommunity().'</contentMap>';
    }

}

new POGO_actions();

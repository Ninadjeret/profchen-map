<?php

class POGO_news {
    
    function __construct( $id ) {
        $this->wpId = $id;
    }
    
    public function getNameFr() {
        return html_entity_decode( get_the_title($this->wpId) );
    }
    
    public function getContentForCommunity() {
        $value = POGO_helpers::getPostContent($this->wpId);
        if( !empty($value) ) {
            return $value;
        }
        return false;        
    }
    
    public function getContentForMap() {
        $value = get_field('content_map', $this->wpId);
        if( !empty($value) ) {
            return $value;
        }
        return false;        
    }
    
    public function getPublishedDate() {
        $date = get_the_date( 'Y-m-d H:i:s', $this->wpId );
        return new DateTime($date);
    }
    
    public function isImportant() {
        $value = get_field('big_news', $this->wpId);
        if( empty($value) ) {
            return false;
        }
        return true;         
    }
    
    public function getPublishDate() {
        return new DateTime( get_the_date( 'Y-m-d H:i:s', $this->wpId ) );
    }
    
}


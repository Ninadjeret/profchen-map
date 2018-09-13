<?php

class POGO_announce {
    
    function __construct( $comment_id ) {
        $this->wpId = $comment_id;
    }
    
    function getType() {
        return get_field('sourceType','comment_'.$this->wpId);
    }
    
    function getCommunity() {
        $value = get_field('sourceCommunity', 'comment_'.$this->wpId);
        if( !empty( $value ) ) {
            return new POGO_community($value);
        }
        return false;
    }
    
    function getAuthor() {
        return get_comment_author( $this->wpId );
    }
    
}


<?php

class POGO_network {
    
    const MAIN_BLOG_ID = 1;
    
    public static function isMainSite() {
        if( get_current_blog_id() == self::MAIN_BLOG_ID ) {
            return true;
        }
        return false;
    }
    
}


<?php

class POGO_user {
    
    function __construct( $user_id ) {
        $this->wpId = $user_id;
        $this->wpData = get_userdata($this->wpId);
    }
    
    public function getAvatarUrl() {
        return 'https://assets.profchen.fr/img/avatar_default.png';
    }
    
    public function getUsername() {
        return $this->wpData->user_firstname;
    }
    
    public function getRole() {
        $role = ($this->getAdminCommunities()) ? 'communityAdmin' : 'user' ;
        return $role;
    }
    
    public function getAdminCommunities() {
        $admin_communities = get_field('admin_communities', 'user_'.$this->wpId);
        if( empty( $admin_communities ) ) return false;
        
        $return = array();
        foreach( $admin_communities as $community_id ) {
            $return[] = new POGO_community($community_id);
        }
        return $return;
    }
    
    public function canEditCommunity( $communityId ) {
        $communties = $this->getAdminCommunities();
        if( empty($communties) ) {
            return false;
        }
        foreach( $communties as $communty ) {
            if( $communty->wpId == $communityId ) {
                return true;
            }
        }
        return false;
    }
    
    public function getSecretKey() {
        $val = get_field('secret_key', 'user_'.$this->wpId);
        if( !empty( $val ) ) {
            return$val;
        }
        return false;        
    }
    
    function isAdmin() {
        if( $this->getRole() == 'communityAdmin' ) {
            return true;
        }
        return false;
    }
    
}

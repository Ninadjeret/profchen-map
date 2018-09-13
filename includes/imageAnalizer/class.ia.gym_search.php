<?php

class POGO_IA_gymSearch {
    
    /**
     * 
     */
    function __construct() {
        $this->debug = false;
        $this->query = false;
        $this->gyms = POGO_helpers::getGyms();
        $this->sanitizedNames = $this->getSanitizedNames();
    }
    
    
    /**
     * 
     * @return type
     */
    function getSanitizedNames() {
        $names = array();
        foreach( $this->gyms as $gym ) { 
            $names[] = sanitize_title($gym->nianticId);            
        }
        return $names;
    }
    
    
    /**
     * 
     * @param type $query
     * @return boolean
     */
    function isBlackListed( $query ) {
        $black_list = array('bonus');
        foreach( $black_list as $pattern ) {
            if( strstr($query, $pattern) ) {
                return true;
            }
        }
        return false;
    }
    
    
    /**
     * 
     * @return type
     */
    function getAllIdentifiers() {
        
        $identifiers = array();
        
        foreach( $this->gyms as $gym ) {
            $name = sanitize_title($gym->nianticId);
            $nb_chars = strlen($name);
            
            //Parcours
            $debut = 0;
            while( $debut < $nb_chars - 1) {
                
                $fin = $nb_chars - $debut;
                while( $fin > 2 ) {
                    $pattern = mb_strimwidth($name, $debut, $fin);
                    //echo $pattern.'<br>';
                    $is_find = 0;
                    foreach( $this->sanitizedNames as $sanitizedName ) {
                        if( strstr($sanitizedName, $pattern) ) { 
                            $is_find++;
                        } 
                    }
                    
                    if( $is_find === 1 ) {
                        //echo 'Identifiant OK<br>';
                        $identifiers[$pattern] = (object) array(
                            'gymId' => $gym->wpId,
                            'percent' => round( strlen($pattern) * 100 / $nb_chars )
                        );$gym->wpId;
                    }
               
                    $fin--;
                }
                
                $debut++;
            }
            //die();
        } 
        $keys = array_map('strlen', array_keys($identifiers));
        array_multisort($keys, SORT_DESC, $identifiers);
        return $identifiers;
    }
    
    
    /**
     * 
     * @param type $query
     * @param type $min
     * @return boolean|\POGO_gym
     */
    function findGym( $query, $min = 50 ) {
        $this->query = $query;
        $sanitizedQuery = sanitize_title($this->query);
        
        if( $this->isBlackListed($sanitizedQuery) ) {
            return false;
        }
        
        foreach( $this->getAllIdentifiers() as $pattern => $data ) {
            if( strstr($sanitizedQuery, $pattern) && $data->percent >= $min ) {
                //var_dump($pattern);
                return new POGO_gym($data->gymId);
            }
        }
        return false;
    }
    
}

